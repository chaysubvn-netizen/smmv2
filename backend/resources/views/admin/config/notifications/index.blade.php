@extends('admin.layouts.app')
@section('title', 'Cấu hình thông báo')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Thông báo Modal Dịch vụ</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}"  method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="notice_modal" class="form-label">Thông báo Modal</label>
                            <textarea name="notice_modal" id="notice_modal" class="form-control" rows="5">{{ s('') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Lưu
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Thông báo Dịch vụ</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="notice_service" class="form-label">Thông báo</label>
                            <textarea name="notice_service" id="notice_service" class="form-control" rows="5">{{ s('') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Lưu
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Thông báo Nạp tiền</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="notice_recharge" class="form-label">Nội dung thông báo (Trang nạp tiền)</label>
                            <textarea name="notice_recharge" id="notice_recharge" class="form-control" rows="5">{{ s('') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Lưu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        CKEDITOR.replace('notice_modal');
        CKEDITOR.replace('notice_service');

        CKEDITOR.instances['notice_modal'].on('change', function() {
            this.updateElement();
        });
        CKEDITOR.instances['notice_service'].on('change', function() {
            this.updateElement();
        });
        CKEDITOR.replace('notice_recharge');
        CKEDITOR.instances['notice_recharge'].on('change', function() {
            this.updateElement();
        });
    </script>
@endsection
