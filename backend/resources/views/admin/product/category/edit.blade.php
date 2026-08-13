@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa danh mục')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card custom-card">
            <div class="card-header">
                <h4 class="card-title">Chỉnh sửa danh mục</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.categories.update', $category->id) }}" method="POST" data-ajax="true">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Tên danh mục" value="{{ $category->name }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="sort_order" class="form-label">Thứ tự</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                            placeholder="Thứ tự" value="{{ $category->sort_order }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Ghi chú">{{ $category->description }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        @if ($category->image)
                            <img src="{{ asset($category->image) }}" alt="Hình ảnh danh mục" class="mt-2" style="max-width: 200px;">
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="form_status" class="form-control">
                            <option value="active" {{ $category->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ $category->status == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                        </select>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Lưu
                        </button>
                        <a href="{{ route('admin.products.categories.index') }}" class="btn btn-secondary-gradient">
                            <i class="bx bx-x"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
