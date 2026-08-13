<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductClientController extends Controller
{
    public function index(Request $request)
    {
        $domain = request()->getHost();
        $query = ProductCategory::where('status', 'active')
            ->where('domain', $domain)
            ->with(['products' => function($q) use ($request, $domain) {
                $q->where('status', 'active')
                  ->where('domain', $domain)
                  ->withSum('order_products', 'quantity')
                  ->withAvg('reviews', 'rating');
                
                if ($request->has('keyword') && $request->keyword != '') {
                    $q->where(function($subQ) use ($request) {
                        $subQ->where('name', 'like', '%' . $request->keyword . '%')
                             ->orWhere('description', 'like', '%' . $request->keyword . '%');
                    });
                }

                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }

                if ($request->filled('sort')) {
                    switch ($request->sort) {
                        case 'price_asc':
                            $q->orderBy('price', 'asc');
                            break;
                        case 'price_desc':
                            $q->orderBy('price', 'desc');
                            break;
                        default:
                            $q->orderBy('sort_order', 'asc');
                            break;
                    }
                } else {
                     $q->orderBy('sort_order', 'asc');
                }
            }]);

        if ($request->has('category') && $request->category != 'all') {
            $query->where('id', $request->category);
        }

        $categories = $query->get();
        
        // Fetch all categories for the dropdown filter (unfiltered by selection)
        $allCategories = ProductCategory::where('status', 'active')
            ->where('domain', $domain)
            ->get();

        // If searching, only show categories that have matching products in the grid
        if ($request->has('keyword') && $request->keyword != '') {
            $categories = $categories->filter(function($category) {
                return $category->products->count() > 0;
            });
        }

        return view('client.products.index', [
            'categories' => $categories,
            'allCategories' => $allCategories,
        ]);
    }

    public function detail(Request $request, $slug)
    {
        if (status_demo()) {
           /*return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);*/
        }
        
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->where('domain', request()->getHost())
            ->withSum('order_products', 'quantity')
            ->firstOrFail();

        // Fetch only top-level reviews (parent_id is null) with their replies
        $reviews = $product->reviews()
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'order_product'])
            ->latest()
            ->get();

        // Fetch user's purchased orders for this product to allow selecting in review modal
        $purchasedOrders = [];
        if (Auth::check()) {
            $purchasedOrders = \App\Models\OrderProduct::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->where('status', 'success')
                ->latest()
                ->get();
        }

        return view('client.products.detail', [
            'product' => $product,
            'reviews' => $reviews,
            'purchasedOrders' => $purchasedOrders,
            'rating_summary' => [
                'average' => $product->reviews()->where('status', 'active')->avg('rating') ?? 0,
                'count' => $product->reviews()->where('status', 'active')->count(),
                'stars' => [
                    5 => $product->reviews()->where('status', 'active')->where('rating', 5)->count(),
                    4 => $product->reviews()->where('status', 'active')->where('rating', 4)->count(),
                    3 => $product->reviews()->where('status', 'active')->where('rating', 3)->count(),
                    2 => $product->reviews()->where('status', 'active')->where('rating', 2)->count(),
                    1 => $product->reviews()->where('status', 'active')->where('rating', 1)->count(),
                ]
            ]
        ]);
    }

    public function storeReview(Request $request) 
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:reviews,id',
            'order_product_id' => 'nullable|exists:order_products,id',
        ]);

        $rating = $request->rating;
        $comment = $request->comment;
        $parentId = $request->parent_id;
        $orderProductId = $request->order_product_id;

        // If it's a reply (has parent_id), check if user is admin
        if ($parentId && Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Chỉ quản trị viên mới có thể phản hồi đánh giá.'], 403);
        }

        // If it's a review (no parent_id), require order selection (except for admin)
        if (!$parentId) {
            if (!$orderProductId && Auth::user()->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Vui lòng chọn đơn hàng đã mua để đánh giá.'], 400);
            }
            
            if ($orderProductId) {
                // Verify ownership and product match
                $owned = \App\Models\OrderProduct::where('id', $orderProductId)
                    ->where('user_id', Auth::id())
                    ->where('product_id', $request->product_id)
                    ->where('status', 'success')
                    ->exists();
                if (!$owned) {
                    return response()->json(['success' => false, 'message' => 'Đơn hàng không hợp lệ hoặc bạn chưa mua sản phẩm này.'], 403);
                }
            }
            
            if (!$rating) $rating = 5;

            // Restore default comment logic
            if (empty($comment)) {
                $defaultComments = [
                    1 => 'Tệ',
                    2 => 'Không hài lòng',
                    3 => 'Bình thường',
                    4 => 'Hài lòng',
                    5 => 'Tuyệt vời'
                ];
                $comment = $defaultComments[$rating] ?? 'Hài lòng';
            }
        }

        // If it's a reply (has parent_id), rating is not needed
        if ($parentId) {
            $rating = 0;
            $orderProductId = null; // Replies don't link directly to an order record
        }

        \App\Models\Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'order_product_id' => $orderProductId,
            'parent_id' => $parentId,
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'active', 
            'domain' => request()->getHost()
        ]);

        return response()->json(['success' => true, 'message' => $parentId ? 'Phản hồi thành công!' : 'Đánh giá thành công!']);
    }


    public function orders(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = OrderProduct::where('user_id', Auth::user()->id)
            ->where('domain', request()->getHost());
        
        // Get counts for tabs
        $counts = [
            'all' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'processing' => (clone $query)->where('status', 'processing')->count(),
            'success' => (clone $query)->where('status', 'success')->count(),
            'cancel' => (clone $query)->where('status', 'cancel')->count(),
        ];

        // Apply filters
        $order_products = $query->search($search);
        
        if ($status && $status != 'all') {
            $order_products = $order_products->where('status', $status);
        }

        $order_products = $order_products->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('client.products.orders', [
            'order_products' => $order_products,
            'counts' => $counts,
            'current_status' => $status ?? 'all'
        ]);
    }
}
