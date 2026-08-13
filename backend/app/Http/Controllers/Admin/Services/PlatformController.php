<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PlatformController extends Controller
{
    /**
     * Hiển thị danh sách nền tảng
     */
    public function index()
    {
        return view('admin.services.platforms.index');
    }

    /**
     * Tạo nền tảng mới
     */
    public function create(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng không thể sử dụng trên demo',
            ]);
        }
        $validator = Validator::make($request->all(), [
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'required|integer',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $filename = time() . "-" . Str::random(10) . "." . $file->extension();
                
                $uploadPath = public_path('uploads/platforms');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $file->move($uploadPath, $filename);
            } else {
                $filename = null;
            }

            $platform = new Platform();
            $platform->icon = $filename;
            $platform->sort_order = $request->sort_order;
            $platform->code = Str::random(10);
            $platform->name = $request->name;
            $platform->status = $request->status;
            $platform->save();

            return response()->json([
                'success' => true,
                'message' => 'Tạo nền tảng mới thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Hiển thị form chỉnh sửa nền tảng
     */
    public function edit($id)
    {
        
        $platform = Platform::find($id);
        if (!$platform) {
            return redirect()->back()->with('error', 'Nền tảng không tồn tại!');
        }

        return view('admin.services.platforms.edit', compact('platform'));
    }

    /**
     * Cập nhật nền tảng
     */
    public function update(Request $request, $id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng không thể sử dụng trên demo',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'icon' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'required|integer',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $platform = Platform::find($id);
        if (!$platform) {
            return response()->json([
                'success' => false,
                'message' => 'Nền tảng không tồn tại!'
            ]);
        }

        // Xử lý upload icon mới
        if ($request->hasFile('icon')) {
            try {
                if ($platform->icon && file_exists(public_path($platform->icon))) {
                    unlink(public_path($platform->icon));
                }
            } catch (\Exception $e) {
                // Không làm gì nếu xóa lỗi
            }

            $file = $request->file('icon');
            $filename = time() . "-" . Str::random(10) . "." . $file->extension();
            $file->move(public_path('uploads/platforms'), $filename);
            $platform->icon = $filename;
        }

        try {
            $platform->sort_order = $request->sort_order;
            $platform->name = $request->name;
            $platform->status = $request->status;
            $platform->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nền tảng thành công!',
                'redirect' => route('admin.platforms.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
