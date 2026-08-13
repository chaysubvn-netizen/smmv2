<form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6 col-lg-6 form-group mb-3">
            <label for="affiliate_percent" class="form-label">Tỉ lệ hoa hồng (%)</label>
            <input type="number" class="form-control" id="affiliate_percent" name="affiliate_percent" value="{{ s('affiliate_percent') }}" placeholder="Tỉ lệ hoa hồng (%)">
        </div>
        <div class="col-md-6 col-lg-6 form-group mb-3">
            <label for="affiliate_min" class="form-label">Hạn mức trả tưởng (VND)</label>
            <input type="text" class="form-control" id="affiliate_min" name="affiliate_min" value="{{ s('affiliate_min') }}" placeholder="Số tiền tối thiểu để rút">
        </div>
        <div class="col-md-6 col-lg-3 form-group mb-3">
            <label for="affiliate_max" class="form-label">Số tiền tối đa để rút</label>
            <input type="text" class="form-control" id="affiliate_max" name="affiliate_max" value="{{ s('affiliate_max') }}" placeholder="Số tiền tối đa để rút">
        </div>
        <div class="col-md-6 col-lg-3 form-group mb-3">
            <label for="affiliate_status" class="form-label">Trạng thái rút tiền</label>
            <select class="form-select" id="affiliate_status" name="affiliate_status">
                <option value="active" {{ s('affiliate_status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ s('affiliate_status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="ri-save-3-line"></i> Lưu thay đổi
    </button>
</form>
