@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa nền tảng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form action="{{ route('admin.platforms.update', ['id' => $platform->id]) }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <input type="file" class="form-control" id="icon" name="icon" placeholder="Nhập icon">
                            <img src="{{ asset($platform->icon) }}" alt="Icon" class="mt-2" style="width: 100px; height: 100px;">
                        </div>
                        <div class="form-group mb-3">
                            <label for="sort_order" class="form-label">Thứ tự</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                value="{{ $platform->sort_order }}" placeholder="Nhập thứ tự">
                        </div>
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Tên nền tảng</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $platform->name }}" placeholder="Nhập tên nền tảng">
                        </div>
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="form_status" class="form-control">
                                <option value="1" {{ $platform->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ $platform->status == 0 ? 'selected' : '' }}>Ngừng hoạt động</option>
                            </select>
                        </div>
                        <div class="">
                            <a href="{{ route('admin.platforms.index') }}" class="btn btn-secondary btn-wave">
                                <i class="bx bx-arrow-back"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary btn-primary-gradient btn-wave">
                                <i class="bx bx-save"></i> Lưu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
