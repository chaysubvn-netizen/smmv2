<form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12 col-lg-6 form-group mb-3">
            <label for="title" class="form-label">Tiêu đề Website</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ s('title') }}">
        </div>
        <div class="col-md-12 col-lg-6 form-group mb-3">
            <label for="keywords" class="form-label">Từ khoá</label>
            <input type="text" class="form-control" id="keywords" name="keywords" value="{{ s('keywords') }}">
        </div>
        <div class="col-md-12 col-lg-12 form-group mb-3">
            <label for="description" class="form-label">Mô tả Website</label>
            <textarea class="form-control" id="description" name="description"
                rows="3">{{ s('description') }}</textarea>
        </div>
        <div class="col-md-12 col-lg-4 form-group mb-3">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" class="form-control" id="logo" name="logo">
            @if (s('logo'))
            <div class="mt-2">
                <img src="{{ s('logo') }}" alt="Logo" class="img-fluid" style="max-width: 200px;">
            </div>
            @endif
        </div>
        <div class="col-md-12 col-lg-4 form-group mb-3">
            <label for="favicon" class="form-label">Favicon</label>
            <input type="file" class="form-control" id="favicon" name="favicon">
            @if (s('favicon'))
            <div class="mt-2">
                <img src="{{ s('favicon') }}" alt="favicon" class="img-fluid" style="max-width: 200px;">
            </div>
            @endif
        </div>
        <div class="col-md-12 col-lg-4 form-group mb-3">
            <label for="og_image" class="form-label">Og Image</label>
            <input type="file" class="form-control" id="og_image" name="og_image">
            @if (s('og_image'))
            <div class="mt-2">
                <img src="{{ s('og_image') }}" alt="og_image" class="img-fluid" style="max-width: 200px;">
            </div>
            @endif
        </div>
       

      
        <!-- Colors & Language -->
        <!-- <div class="col-md-4">
            <label class="form-label">Màu chủ đạo</label>
            <input type="color" class="form-control form-control-color w-100" style="height: 42px" name="color_primary"
                value="{{ s('color_primary') }}">
        </div> -->
        <div class="col-md-6">
            <label class="form-label">Trạng thái Demo (1: Bật, 0: Tắt)</label>
            <input type="text" name="status_demo" class="form-control" value="{{ s('status_demo') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Bật cập nhật tự động (Mỗi khi có bản Release mới)</label>
            <div class="form-check form-switch mt-1">
                <input class="form-check-input" type="checkbox" id="auto_update" name="auto_update" value="1" {{ s('auto_update') == 1 ? 'checked' : '' }}>
                <label class="form-check-label" for="auto_update">Kích hoạt</label>
            </div>
            <small class="text-muted">Lưu ý: Khi bật tính năng này, hệ thống sẽ tự động tải và cài đặt bản cập nhật mới nhất và chạy SQL Migration.</small>
        </div>




        <div class="col-md-6 form-group mb-3">
            <label for="facebook_link" class="form-label">Link Facebook Support</label>
            <input type="text" class="form-control" id="facebook_link" name="facebook_link"
                value="{{ s('facebook_link') }}" placeholder="Nhập link facebook support">
        </div>
        <div class="col-md-6 form-group mb-3">
            <label for="zalo_link" class="form-label">Link Zalo Support</label>
            <input type="text" class="form-control" id="zalo_link" name="zalo_link" value="{{ s('zalo_link') }}"
                placeholder="Nhập link zalo support">
        </div>
        <div class="col-md-6 form-group mb-3">
            <label for="telegram_link" class="form-label">Link Telegram</label>
            <input type="text" class="form-control" id="telegram_link" name="telegram_link"
                value="{{ s('telegram_link') }}" placeholder="Nhập link telegram">
        </div>
        <div class="col-md-6 form-group mb-3">
            <label for="email_link" class="form-label">Địa chỉ Email</label>
            <input type="email" class="form-control" id="email_link" name="email_link" value="{{ s('email_link') }}"
                placeholder="Nhập địa chỉ email">
        </div>

    </div>


    <button type="submit" class="btn btn-primary">
        <i class="bx bx-save"></i> Lưu thay đổi
    </button>
</form>
