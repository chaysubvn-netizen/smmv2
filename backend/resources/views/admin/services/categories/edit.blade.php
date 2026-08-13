@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa phân loại')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Chỉnh sửa phân loại</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', ['id' => $category->id]) }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="platform_id" class="form-label">Nền tảng</label>
                            <select name="platform_id" id="platform_id" class="form-control">
                                <option value="">Chọn nền tảng</option>
                                @foreach ($platforms as $platform)
                                    <option value="{{ $platform->id }}" {{ $category->platform_id == $platform->id ? 'selected' : '' }}>{{ $platform->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <input type="file" class="form-control" id="icon" name="icon" placeholder="Nhập icon">
                            @if ($category->icon)
                                <img src="{{ asset($category->icon) }}" alt="Icon" class="mt-2" style="width: 50px; height: 50px;">
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <label for="sort_order" class="form-label">Thứ tự</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ $category->sort_order }}" placeholder="Nhập thứ tự">
                        </div>
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Tên phân loại</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" placeholder="Nhập tên phân loại">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3 form-group">
                                <label for="is_discount" class="form-label">Giảm giá</label>
                                <select name="is_discount" id="is_discount" class="form-control">
                                    <option value="0" {{ $category->is_discount == 0 ? 'selected' : '' }}>Tắt</option>
                                    <option value="1" {{ $category->is_discount == 1 ? 'selected' : '' }}>Bật</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="form_status" class="form-control">
                                <option value="active" {{ $category->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ $category->status == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                            </select>
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-primary btn-primary-gradient btn-wave">
                                <i class="bx bx-save"></i> Lưu
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-secondary-gradient btn-wave">
                                <i class="bx bx-x"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
