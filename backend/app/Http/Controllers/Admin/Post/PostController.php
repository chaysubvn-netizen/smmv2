<?php

namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Hiển thị danh sách bài viết
    public function index() {
        return view('admin.post.index');
    }

    // Tạo bài viết mới
    public function create(Request $request){
        if(status_demo()){
            return response()->json([
                'success'=>false,
                'message'=>'Chức năng này không thể sử dụng trên website demo'
            ]);
        }

        $valid = Validator::make($request->all(), [
            'title'=>'required|string',
            'content'=>'required|string',
            'image'=>'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'=>'required|in:active,inactive',
        ]);

        if($valid->fails()){
            return response()->json([
                'success'=>false,
                'message'=>$valid->errors()->first()
            ]);
        }

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/images'), $imageName);
        }

        $post = new Post();
        $post->user_id = auth()->user()->id;
        $post->title = $request->title;
        $post->slug = Str::slug($request->title . '-' . time());
        $post->content = strip_tags($request->content, '<a><b><i><u><strong><em><p><br><ul><li><ol><img><h1><h2><h3><h4><h5><h6><div><span><table><thead><tbody><tr><th><td>');
        $post->image = $imageName;
        $post->status = $request->status;
        $post->domain = $request->getHost();
        $post->save();

        return response()->json([
            'success'=>true,
            'message'=>'Tạo bài viết thành công'
        ]);
    }

    // Cập nhật bài viết
    public function update(Request $request,$id){
        if(status_demo()){
            return response()->json([
                'success'=>false,
                'message'=>'Chức năng này không thể sử dụng trên website demo'
            ]);
        }

        $valid = Validator::make($request->all(), [
            'title'=>'required|string',
            'content'=>'required|string',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'=>'required|in:active,inactive',
        ]);

        if($valid->fails()){
            if(status_demo()){
                return response()->json([
                    'success'=>false,
                    'message'=>'Chức năng này không thể sử dụng trên website demo'
                ]);
            }
            return response()->json([
                'success'=>false,
                'message'=>$valid->errors()->first()
            ]);
        }

        $post = Post::find($id);
        if(!$post){
            return response()->json([
                'success'=>false,
                'message'=>'Bài viết không tồn tại'
            ]);
        }

        if($request->hasFile('image')){
            if($post->image && file_exists(public_path('uploads/images/'.$post->image))){
                unlink(public_path('uploads/images/'.$post->image));
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/images'), $imageName);
            $post->image = $imageName;
        }

        $post->title = $request->title;
        $post->slug = Str::slug($request->title . '-' . time());
        $post->content = strip_tags($request->content, '<a><b><i><u><strong><em><p><br><ul><li><ol><img><h1><h2><h3><h4><h5><h6><div><span><table><thead><tbody><tr><th><td>');
        $post->status = $request->status;
        $post->domain = $request->getHost();
        $post->save();

        return response()->json([
            'success'=>true,
            'message'=>'Cập nhật bài viết thành công'
        ]);
    }

    // Xóa bài viết
    public function delete($id){
        if(status_demo()){
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo'
            ]);
        }

        $post = Post::find($id);
        if(!$post){
            return response()->json([
                'success'=>false,
                'message'=>'Bài viết không tồn tại'
            ]);
        }

        if($post->image && file_exists(public_path('uploads/images/'.$post->image))){
            unlink(public_path('uploads/images/'.$post->image));
        }

        $post->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Xóa bài viết thành công'
        ]);
    }

    // Hiển thị form chỉnh sửa bài viết
    public function edit($id){
        $post = Post::find($id);
        if(!$post){
            return redirect()->back()->with('error','Bài viết không tồn tại');
        }

        return view('admin.post.edit', compact('post'));
    }
}