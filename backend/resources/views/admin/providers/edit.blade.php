@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa API Provider')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <a href="{{ route('admin.providers.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Quay lại
                </a>
            </div>
        </div>
        <div class="col-lg-8">
            <form action="{{ route('admin.providers.update', $provider->id) }}" method="POST" data-ajax="true">
                @csrf
                <div class="card custom-card">
                    <div class="card-header">
                        <h4 class="card-title text-primary"><i class="bx bx-plug me-2"></i>Thông tin kết nối API</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="type" class="form-label text-primary fw-bold">Loại API <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select">
                                    <option value="standard" {{ $provider->type == 'standard' ? 'selected' : '' }}>Standard (JAP, Smartpanel,...)</option>
                                    <option value="custom" {{ $provider->type == 'custom' ? 'selected' : '' }}>Custom System</option>
                                </select>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="api_url" class="form-label text-primary fw-bold">Domain <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-link"></i></span>
                                    <input type="text" class="form-control" id="api_url" name="api_url" value="{{ $provider->api_url }}"
                                        placeholder="VD: https://domain.com/">
                                </div>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="api_key" class="form-label text-primary fw-bold">API Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="api_key" name="api_key" value="{{ $provider->api_key }}" placeholder="Nhập API Key">
                            </div>
                             <div class="col-md-12 form-group mb-3">
                                <label for="name" class="form-label text-primary fw-bold">Tên gợi nhớ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $provider->name }}" placeholder="Ví dụ: Site A, Site B...">
                            </div>
                             <div class="col-12 form-group mb-3">
                                <label for="status" class="form-label text-primary fw-bold">Trạng thái</label>
                                <select name="status" id="form_status" class="form-select">
                                    <option value="active" {{ $provider->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ $provider->status == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card custom-card mt-3">
                    <div class="card-header">
                        <h4 class="card-title text-success"><i class="bx bx-slider me-2"></i>Cài đặt đồng bộ dữ liệu</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-primary">Đồng bộ dịch vụ từ API <span class="text-danger">*</span></label>
                                <select name="sync_services" class="form-select">
                                    <option value="1" {{ $provider->sync_services ? 'selected' : '' }}>ON - Đồng bộ tự động</option>
                                    <option value="0" {{ !$provider->sync_services ? 'selected' : '' }}>OFF - Tắt đồng bộ</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-success">Cập nhật giá bán tự động <span class="text-danger">*</span></label>
                                <select name="auto_update_price" class="form-select">
                                    <option value="1" {{ $provider->auto_update_price ? 'selected' : '' }}>ON - Cập nhật tự động</option>
                                    <option value="0" {{ !$provider->auto_update_price ? 'selected' : '' }}>OFF - Không cập nhật</option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group mb-3">
                                <label class="form-label text-danger">% Tăng giá bán lẻ</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="increase_rate" value="{{ $provider->increase_rate }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label class="form-label text-warning">% Tăng giá CTV</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="increase_rate_collaborator" value="{{ $provider->increase_rate_collaborator }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label class="form-label text-info">% Tăng giá Đại lý</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="increase_rate_agency" value="{{ $provider->increase_rate_agency }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label class="form-label text-success">% Tăng giá NPP</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="increase_rate_distributor" value="{{ $provider->increase_rate_distributor }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-danger">Tỷ giá tiền tệ quốc tế <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="exchange_rate" value="{{ number_format($provider->exchange_rate, 0, '', '') }}">
                                    <span class="input-group-text">VND</span>
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-dark">Định dạng giá bán của API <span class="text-danger">*</span></label>
                                <select name="rate_per" class="form-select">
                                    <option value="1000" {{ $provider->rate_per == '1000' ? 'selected' : '' }}>Giá của 1000 lượt</option>
                                    <option value="1" {{ $provider->rate_per == '1' ? 'selected' : '' }}>Giá của 1 lượt</option>
                                </select>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-warning">Cập nhật tên & mô tả tự động <span class="text-danger">*</span></label>
                                <select name="auto_update_name" class="form-select">
                                    <option value="1" {{ $provider->auto_update_name ? 'selected' : '' }}>ON - Cập nhật tự động</option>
                                    <option value="0" {{ !$provider->auto_update_name ? 'selected' : '' }}>OFF - Giữ nguyên</option>
                                </select>
                            </div>
                             <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-info">Lọc HTML trong nội dung API <span class="text-danger">*</span></label>
                                <select name="filter_html" class="form-select">
                                    <option value="1" {{ $provider->filter_html ? 'selected' : '' }}>ON - Kích hoạt bảo vệ</option>
                                    <option value="0" {{ !$provider->filter_html ? 'selected' : '' }}>OFF - Tắt</option>
                                </select>
                            </div>
                             <div class="col-md-12 form-group mb-3">
                                <label for="note" class="form-label">Ghi chú quan trọng</label>
                                <textarea class="form-control" id="note" name="note" rows="3">{{ $provider->note }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-save"></i> Lưu cấu hình API</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-4">
             <div class="card custom-card">
                <div class="card-header">
                     <h5 class="card-title text-info"><i class="bx bx-info-circle me-2"></i>Lưu Ý</h5>
                </div>
                <div class="card-body">
                   <div class="alert alert-light-info">
                        <i class="bx bx-bulb text-warning me-2"></i> <strong>Mục đích:</strong> Chức năng này cho phép bạn bán lại sản phẩm của website khác trên chính website của bạn.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
