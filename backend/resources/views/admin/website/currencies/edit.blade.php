
<form action="{{ route('admin.currencies.update', $currency->id) }}" method="POST" data-ajax="true" enctype="multipart/form-data">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Chỉnh sửa tiền tệ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 col-lg-4 form-group mb-3">
                <label for="name" class="form-label">Tên quốc gia</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Tên quốc gia"
                    value="{{ $currency->name }}">
            </div>
            <div class="col-md-6 col-lg-4 form-group mb-3">
                <label for="code" class="form-label">Mã tiền tệ</label>
                <input type="text" class="form-control" id="code" name="code" placeholder="Mã tiền tệ"
                    value="{{ $currency->code }}">
            </div>
            <div class="col-md-6 col-lg-4 form-group mb-3">
                <label for="symbol" class="form-label">Ký hiệu tiền tệ</label>
                <input type="text" class="form-control" id="symbol" name="symbol" value="{{ $currency->symbol }}"
                    placeholder="Ký hiệu tiền tệ">
            </div>
            <div class="col-md-6 col-lg-6 form-group mb-3">
                <label for="position" class="form-label">Vị trí hiển thị</label>
                <select class="form-select" id="position" name="position">
                    <option value="right" {{ $currency->position == 'right' ? 'selected' : '' }}>Phía bên phải</option>
                    <option value="left" {{ $currency->position == 'left' ? 'selected' : '' }}>Phía bên trái</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-6 form-group mb-3">
                <label for="thousand_separator" class="form-label">Dấu phân cách hàng nghìn</label>
                <select class="form-select" id="thousand_separator" name="thousand_separator">
                    <option value="comma" {{ $currency->thousand_separator == 'comma' ? 'selected' : '' }}>, (Comma)</option>
                    <option value="dot" {{ $currency->thousand_separator == 'dot' ? 'selected' : '' }}>. (Dot)</option>
                    <option value="space" {{ $currency->thousand_separator == 'space' ? 'selected' : '' }}> (Space)</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-6 form-group mb-3">
                <label for="decimal_separator" class="form-label">Dấu phân cách thập phân</label>
                <select class="form-select" id="decimal_separator" name="decimal_separator">
                    <option value="dot" {{ $currency->decimal_separator == 'dot' ? 'selected' : '' }}>. (Dot)</option>
                    <option value="comma" {{ $currency->decimal_separator == 'comma' ? 'selected' : '' }}>, (Comma)</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-6 form-group mb-3">
                <label for="decimal_places" class="form-label">Số thập phân</label>
                <select name="decimal_places" id="decimal_places" class="form-control">
                    <option value="0" {{ $currency->decimal_places == 0 ? 'selected' : '' }}>0</option>
                    <option value="1" {{ $currency->decimal_places == 1 ? 'selected' : '' }}>0.0</option>
                    <option value="2" {{ $currency->decimal_places == 2 ? 'selected' : '' }}>0.00</option>
                    <option value="3" {{ $currency->decimal_places == 3 ? 'selected' : '' }}>0.000</option>
                    <option value="4" {{ $currency->decimal_places == 4 ? 'selected' : '' }}>0.0000</option>
                </select>
            </div>
            <div class="col-12 form-group mb-3">
                <label for="exchange_rate" class="form-label">Tỷ giá quy đổi (Bao nhiêu VND cho 1 đơn vị tiền tệ này)</label>
                <input type="text" class="form-control" id="exchange_rate" name="exchange_rate"
                    placeholder="Tỷ giá quy đổi" value="{{ $currency->exchange_rate }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary">Lưu lại</button>
    </div>
</form>
