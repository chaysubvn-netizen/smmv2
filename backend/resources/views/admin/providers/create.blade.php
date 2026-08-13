@extends('admin.layouts.app')
@section('title', 'Thêm API cần kết nối')
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
            <form action="{{ route('admin.providers.store') }}" method="POST" data-ajax="true">
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
                                    <option value="standard">Standard (JAP, Smartpanel,...)</option>
                                    <option value="custom">Custom System</option>
                                </select>
                                <small class="text-muted">API SMMPANEL2 được hỗ trợ miễn phí, API khác tính phí 200.000đ/lần</small>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="api_url" class="form-label text-primary fw-bold">Domain <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-link"></i></span>
                                    <input type="text" class="form-control" id="api_url" name="api_url"
                                        placeholder="VD: https://domain.com/">
                                </div>
                                <small class="text-muted">Nhập đầy đủ URL kèm https:// hoặc http://</small>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="api_key" class="form-label text-primary fw-bold">API Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="api_key" name="api_key" placeholder="Nhập API Key">
                            </div>
                             <div class="col-md-12 form-group mb-3">
                                <label for="name" class="form-label text-primary fw-bold">Tên gợi nhớ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ví dụ: Site A, Site B...">
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
                                    <option value="1">ON - Đồng bộ tự động</option>
                                    <option value="0">OFF - Tắt đồng bộ</option>
                                </select>
                                <small class="text-muted">Hệ thống sẽ tự động đồng bộ tất cả dịch vụ từ API vào website của bạn.</small>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-success">Cập nhật giá bán tự động <span class="text-danger">*</span></label>
                                <select name="auto_update_price" class="form-select">
                                    <option value="1">ON - Cập nhật tự động</option>
                                    <option value="0">OFF - Không cập nhật</option>
                                </select>
                                <small class="text-muted">Khi giá sản phẩm thay đổi ở API, hệ thống sẽ tự động cập nhật.</small>
                            </div>

                            <div class="col-md-3 form-group mb-3">
                                <label class="form-label text-danger">% Tăng giá bán lẻ</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="increase_rate" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Nhập 10 để tăng 10% so với giá vốn</small>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label class="form-label text-warning">% Tăng giá CTV</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="increase_rate_collaborator" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label class="form-label text-info">% Tăng giá Đại lý</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="increase_rate_agency" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label class="form-label text-success">% Tăng giá NPP</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="increase_rate_distributor" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-danger">Tỷ giá tiền tệ quốc tế <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="exchange_rate" value="1">
                                    <span class="input-group-text">VND</span>
                                </div>
                                <small class="text-muted">Nếu giá API là USD, nhập tỷ giá (vd: 25000).</small>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-dark">Định dạng giá bán của API <span class="text-danger">*</span></label>
                                <select name="rate_per" class="form-select">
                                    <option value="1000">Giá của 1000 lượt</option>
                                    <option value="1">Giá của 1 lượt</option>
                                </select>
                                <small class="text-muted">Vui lòng kiểm tra API xem giá của họ là 1 hay 1000.</small>
                            </div>

                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-warning">Cập nhật tên & mô tả tự động <span class="text-danger">*</span></label>
                                <select name="auto_update_name" class="form-select">
                                    <option value="1">ON - Cập nhật tự động</option>
                                    <option value="0">OFF - Giữ nguyên</option>
                                </select>
                                <small class="text-muted">Tự động cập nhật tên và mô tả sản phẩm từ API.</small>
                            </div>
                             <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-info">Lọc HTML trong nội dung API <span class="text-danger">*</span></label>
                                <select name="filter_html" class="form-select">
                                    <option value="1">ON - Kích hoạt bảo vệ</option>
                                    <option value="0">OFF - Tắt</option>
                                </select>
                                <small class="text-muted">Bảo vệ website bằng cách lọc mã HTML độc hại từ API.</small>
                            </div>
                             <div class="col-md-12 form-group mb-3">
                                <label for="note" class="form-label">Ghi chú quan trọng</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Ghi chú nội bộ..."></textarea>
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
                    <div class="alert alert-light-warning">
                        <i class="bx bx-error text-danger me-2"></i> <strong>Lưu ý quan trọng!</strong><br>
                        Trường hợp quý khách cấu hình đúng nhưng không hiện số dư API có thể do máy chủ không thể kết nối với API đích.
                    </div>
                    <a href="#" class="btn btn-warning btn-sm w-100"><i class="bx bx-book-open"></i> Xem hướng dẫn xử lý</a>
                </div>
            </div>

            <div class="card custom-card mt-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-light-primary"><i class="bx bx-globe fs-20 text-primary"></i></span>
                        </div>
                        <div>
                             <h6 class="fw-bold mb-0">API SMMPANEL2</h6>
                             <small class="text-success fw-bold">Miễn phí</small>
                        </div>
                    </div>
                    <hr>
                     <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-light-warning"><i class="bx bx-dollar fs-20 text-warning"></i></span>
                        </div>
                        <div>
                             <h6 class="fw-bold mb-0">API ngoài hệ sinh thái</h6>
                             <small class="text-danger fw-bold">Phí tích hợp: thương lượng</small>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary w-100"><i class="bx bx-support"></i> Liên hệ hỗ trợ kết nối API</button>
                </div>
            </div>
        </div>
    </div>
@endsection
