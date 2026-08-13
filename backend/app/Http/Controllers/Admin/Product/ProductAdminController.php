<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductAdminController extends Controller
{
    public function index()
    {
        $products = Product::where('domain', request()->getHost())->orderBy('sort_order', 'asc')->get();
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::where('status', 'active')
            ->where('domain', request()->getHost())
            ->orderBy('sort_order', 'asc')
            ->get();
        return view('admin.product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'category_id' => 'required|exists:product_categories,id',
            'sort_order' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'data_input' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($valid->fails()) {
            return response()->json(['success' => false, 'message' => $valid->errors()->first()]);
        }

        $category = ProductCategory::where('domain', request()->getHost())->where('id', $request->category_id)->first();
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Danh mục không tồn tại']);
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/images'), $imageName);
        }

        $product = new Product();
        $product->category_id = $request->category_id;
        $product->sort_order = $request->sort_order;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name . '-' . time());
        $product->price = $request->price;
        $product->status = $request->status;
        $product->description = strip_tags($request->description, '<a><b><i><u><strong><em><p><br><ul><li><ol><img><h1><h2><h3><h4><h5><h6><div><span><table><thead><tbody><tr><th><td>');
        $product->short_description = strip_tags($request->short_description, '<a><b><i><u><strong><em><p><br><ul><li><ol><img>');
        $product->data_input = $request->data_input;
        $product->image = $imageName;
        $product->domain = request()->getHost();
        $product->save();

        return response()->json(['success' => true, 'message' => 'Thêm sản phẩm thành công']);
    }

    public function edit($id)
    {
        $product = Product::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Sản phẩm không tồn tại');
        }

        $categories = ProductCategory::where('status', 'active')
            ->where('domain', request()->getHost())
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('admin.product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'category_id' => 'required|exists:product_categories,id',
            'sort_order' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'data_input' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($valid->fails()) {
            return response()->json(['success' => false, 'message' => $valid->errors()->first()]);
        }

        $product = Product::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        }

        $category = ProductCategory::where('domain', request()->getHost())->where('id', $request->category_id)->first();
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Danh mục không tồn tại']);
        }

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path('uploads/images/' . $product->image))) {
                unlink(public_path('uploads/images/' . $product->image));
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/images'), $imageName);
            $product->image = $imageName;
        }

        $product->category_id = $request->category_id;
        $product->sort_order = $request->sort_order;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name . '-' . time());
        $product->price = $request->price;
        $product->status = $request->status;
        $product->description = strip_tags($request->description, '<a><b><i><u><strong><em><p><br><ul><li><ol><img><h1><h2><h3><h4><h5><h6><div><span><table><thead><tbody><tr><th><td>');
        $product->short_description = strip_tags($request->short_description, '<a><b><i><u><strong><em><p><br><ul><li><ol><img>');
        $product->data_input = $request->data_input;
        $product->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật sản phẩm thành công']);
    }

    public function delete($id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $product = Product::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        }

        try {
            if ($product->image && file_exists(public_path('uploads/images/' . $product->image))) {
                unlink(public_path('uploads/images/' . $product->image));
            }
            $product->delete();

            return response()->json(['success' => true, 'message' => 'Xóa sản phẩm thành công']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Xóa sản phẩm thất bại']);
        }
    }

    public function stock($id)
    {
        $product = Product::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Sản phẩm không tồn tại');
        }
        $product->load('stocks');

        return view('admin.product.stock', compact('product'));
    }

    public function stockCreate(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'data' => 'required|string',
        ]);

        if ($valid->fails()) {
            return response()->json(['success' => false, 'message' => $valid->errors()->first()]);
        }

        $product = Product::where('domain', request()->getHost())->where('id', $request->product_id)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        }

        $data = array_unique(array_filter(array_map('trim', explode(PHP_EOL, $request->data))));
        foreach ($data as $item) {
            $stock = $product->stocks()->where('data', $item)->first();
            if (!$stock) {
                $product->stocks()->create([
                    'product_id' => $product->id,
                    'data' => $item,
                    'status' => 'available',
                    'domain' => request()->getHost(),
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Thêm stock thành công']);
    }

    public function orders()
    {
        $products = Product::where('domain', request()->getHost())->get();
        return view('admin.product.orders', compact('products'));
    }
}