<form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true">
    @csrf
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <label for="silver_rank" class="form-label">Rank Bạc</label>
            <input type="text" class="form-control" id="silver_rank" name="silver_rank" value="{{ s('silver_rank') }}">
        </div>
        <div class="col-md-6 col-lg-3">
            <label for="gold_rank" class="form-label">Rank Vàng</label>
            <input type="text" class="form-control" id="gold_rank" name="gold_rank" value="{{ s('gold_rank') }}">
        </div>
        <div class="col-md-6 col-lg-3">
            <label for="platinum_rank" class="form-label">Rank Bạch Kim</label>
            <input type="text" class="form-control" id="platinum_rank" name="platinum_rank" value="{{ s('platinum_rank') }}">
        </div>
        <div class="col-md-6 col-lg-3">
            <label for="diamond_rank" class="form-label">Rank Kim Cương</label>
            <input type="text" class="form-control" id="diamond_rank" name="diamond_rank" value="{{ s('diamond_rank') }}">
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3">
        <i class="bx bx-save"></i> Lưu thay đổi
    </button>
</form>
