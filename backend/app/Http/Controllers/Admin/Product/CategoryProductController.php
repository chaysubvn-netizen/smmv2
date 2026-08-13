<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryProductController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::where('domain', request()->getHost())->get();
        return view('admin.product.category.index', compact('categories'));
    }

    public function create(Request $request)
    {
        // ✅ Kiểm tra chế độ demo
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'sort_order' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        // Handle file upload
        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('/uploads/images'), $imageName);
        }

        $category = new ProductCategory();
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->slug = Str::slug($request->input('name') . '-' . time());
        $category->image = $imageName;
        $category->status = $request->input('status');
        $category->sort_order = $request->input('sort_order');
        $category->domain = $request->getHost();
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Tạo danh mục thành công',
            'data' => $category,
        ]);
    }

    public function edit($id)
    {
        $category = ProductCategory::where('id', $id)->where('domain', request()->getHost())->first();
        if (!$category) {
            return redirect()->back()->with('error', 'Danh mục không tồn tại');
        }

        return view('admin.product.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        // ✅ Kiểm tra chế độ demo
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'sort_order' => 'required|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        $category = ProductCategory::where('id', $id)->where('domain', request()->getHost())->first();
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => "Danh mục không tồn tại",
            ]);
        }

        if ($request->hasFile('image')) {
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('/uploads/images'), $imageName);
            $category->image = $imageName;
        }

        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->status = $request->input('status');
        $category->sort_order = $request->input('sort_order');
        $category->slug = Str::slug($request->input('name') . '-' . time());
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật danh mục thành công',
            'data' => $category,
        ]);
    }

    public function delete($id)
    {
        // ✅ Kiểm tra chế độ demo
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $category = ProductCategory::where('id', $id)->where('domain', request()->getHost())->first();
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Danh mục không tồn tại',
            ]);
        }

        try {
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa danh mục thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa danh mục thất bại',
            ]);
        }
    }
}