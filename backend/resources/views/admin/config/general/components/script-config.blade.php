<form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12 form-group mb-3">
            <label for="script_header" class="form-label">Script Header</label>
            <textarea class="form-control" id="script_header" name="script_header" rows="5" placeholder="Script Header">{{ s('script_header') }}</textarea>
        </div>
        <div class="col-md-12 form-group mb-3">
            <label for="script_footer" class="form-label">Script Footer</label>
            <textarea class="form-control" id="script_footer" name="script_footer" rows="5" placeholder="Script Footer">{{ s('script_footer') }}</textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="ri-save-line"></i> Lưu thay đổi
    </button>
</form>
