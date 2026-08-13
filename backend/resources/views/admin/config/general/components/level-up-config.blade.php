<form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true">
    @csrf
    <div class="row">
        <div class="col-md-6 col-lg-3 form-group mb-3">
            <label for="up_silver" class="form-label">Mức Rank Bạc</label>
            <input type="text" class="form-control" id="up_silver" name="up_silver" value="{{ s('up_silver') }}">
        </div>
        <div class="col-md-6 col-lg-3 form-group mb-3">
            <label for="up_gold" class="form-label">Mức Rank Vàng</label>
            <input type="text" class="form-control" id="up_gold" name="up_gold" value="{{ s('up_gold') }}">
        </div>
        <div class="col-md-6 col-lg-3 form-group mb-3">
            <label for="up_platinum" class="form-label">Mức Rank Bạch Kim</label>
            <input type="text" class="form-control" id="up_platinum" name="up_platinum" value="{{ s('up_platinum') }}">
        </div>
        <div class="col-md-6 col-lg-3 form-group mb-3">
            <label for="up_diamond" class="form-label">Mức Rank Kim Cương</label>
            <input type="text" class="form-control" id="up_diamond" name="up_diamond" value="{{ s('up_diamond') }}">
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">
        <i class="bx bx-save"></i> Lưu thay đổi
    </button>
</form>
