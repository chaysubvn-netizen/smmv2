@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa bài viết')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Chỉnh sửa bài viết</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề bài viết</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $post->title }}">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung bài viết</label>
                            <textarea class="form-control" id="content" name="content" rows="5">{{ $post->content }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Ảnh bìa</label>
                            <input type="file" class="form-control" id="image" name="image">
                            @if ($post->image)
                                <img src="{{ asset($post->image) }}" alt="Post Image" class="img-thumbnail mt-2" style="max-width: 200px;">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" {{ $post->status == 'active' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="inactive" {{ $post->status == 'inactive' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Lưu
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary-gradient">
                            <i class="bx bx-x"></i> Hủy
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('content');
            //update content CKEditor
            CKEDITOR.instances['content'].on('change', function() {
                this.updateElement();
            });
        }
    </script>
@endsection
