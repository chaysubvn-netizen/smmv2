<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $platforms = Platform::where('status', 'active')->orderBy('sort_order', 'asc')->get();
        $categories = Category::orderBy('sort_order', 'asc')->get();

        return view('admin.services.categories.index', compact('platforms', 'categories'));
    }

    public function create(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'platform_id' => 'required|exists:platforms,id',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'required|integer|min:0',
            'name' => 'required|string|max:255',
            'is_discount' => 'required|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return response()->json(['success' => false, 'message' => $valid->errors()->first()]);
        }

        $platform = Platform::find($request->platform_id);
        if (!$platform) {
            return response()->json(['success' => false, 'message' => 'Nền tảng không tồn tại']);
        }

        try {
            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $filename = time() . "-" . Str::random(10) . "." . $file->extension();
                
                $uploadPath = public_path('uploads/categories');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $file->move($uploadPath, $filename);
            } else {
                $filename = null;
            }

            $category = new Category();
            $category->platform_id = $platform->id;
            $category->icon = $filename;
            $category->sort_order = $request->sort_order;
            $category->code = Str::random(10);
            $category->name = $request->name;
            $category->is_discount = $request->is_discount;
            $category->status = $request->status;
            $category->save();

            return response()->json(['success' => true, 'message' => 'Thêm danh mục thành công']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->back()->with('error', 'Danh mục không tồn tại');
        }

        $platforms = Platform::where('status', 'active')->orderBy('sort_order', 'asc')->get();
        return view('admin.services.categories.edit', compact('category', 'platforms'));
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
            'platform_id' => 'required|exists:platforms,id',
            'icon' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'required|integer|min:0',
            'name' => 'required|string|max:255',
            'is_discount' => 'required|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return response()->json(['success' => false, 'message' => $valid->errors()->first()]);
        }

        $platform = Platform::find($request->platform_id);
        if (!$platform) {
            return response()->json(['success' => false, 'message' => 'Nền tảng không tồn tại']);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Danh mục không tồn tại']);
        }

        if ($request->hasFile('icon')) {
            try {
                if ($category->icon && file_exists(public_path('uploads/categories/' . $category->icon))) {
                    unlink(public_path('uploads/categories/' . $category->icon));
                }
            } catch (\Exception $e) {
                // Không làm gì nếu xóa thất bại
            }

            $file = $request->file('icon');
            $filename = time() . "-" . Str::random(10) . "." . $file->extension();
            $file->move(public_path('uploads/categories'), $filename);
            $category->icon = $filename;
        }

        try {
            $category->platform_id = $platform->id;
            $category->sort_order = $request->sort_order;
            $category->name = $request->name;
            $category->is_discount = $request->is_discount;
            $category->status = $request->status;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công',
                'redirect' => route('admin.categories.index')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Danh mục không tồn tại']);
        }

        try {
            if ($category->icon && file_exists(public_path('uploads/categories/' . $category->icon))) {
                unlink(public_path('uploads/categories/' . $category->icon));
            }
            $category->delete();

            return response()->json(['success' => true, 'message' => 'Xóa danh mục thành công']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}