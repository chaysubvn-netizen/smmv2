@extends('admin.layouts.app')
@section('title', 'Thêm mới sản phẩm')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Thêm sản phẩm mới</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="category_id" class="form-label">Tên danh mục</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Chọn danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="sort_order" class="form-label">Thứ tự</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="0"
                                placeholder="Nhập thứ tự sản phẩm">
                        </div>
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Nhập tên sản phẩm">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="price" class="form-label">Giá</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="price" name="price"
                                        placeholder="Nhập giá sản phẩm" value="">
                                    <span class="input-group-text">₫</span>
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="form_status" class="form-control">
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Ngừng hoạt động</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="short_description" class="form-label">Chi tiết gói (Mô tả ngắn)</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Nhập chi tiết gói"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Mô tả đầy đủ</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Nhập mô tả sản phẩm"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="data_input" class="form-label">Dữ liệu đơn hàng</label>
                            <input type="text" class="form-control" id="data_input" name="data_input"
                                placeholder="Nhập thông tin cần của nguời dùng">
                            <small class="form-text text-muted">Ví dụ: Tên, Số điện thoại, Địa chỉ, hoặc nếu tắt thì không nhập</small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="image" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <small class="form-text text-muted">Chọn hình ảnh sản phẩm</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i>
                            <span>Lưu lại</span>
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary-gradient">
                            <i class="bx bx-arrow-back"></i>
                            <span>Quay lại</span>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        CKEDITOR.replace('short_description');
        CKEDITOR.instances['short_description'].on('change', function() {
            this.updateElement();
        });
        CKEDITOR.replace('description');
        CKEDITOR.instances['description'].on('change', function() {
            this.updateElement();
        });
        $(document).ready(function() {
            $('#category_id').select2({
                placeholder: 'Chọn danh mục',
                allowClear: true
            });
        });
    </script>
@endsection
