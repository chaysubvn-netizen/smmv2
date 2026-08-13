@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa vé hỗ trợ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Chỉnh sửa vé hỗ trợ</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" data-ajax="true">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="title" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Nhập tiêu đề" value="{{ $ticket->title }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="type" class="form-label">Loại hỗ trợ</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="refill" {{ $ticket->type == 'refill' ? 'selected' : '' }}>Bảo hành
                                    </option>
                                    <option value="cancel" {{ $ticket->type == 'cancel' ? 'selected' : '' }}>Huỷ đơn
                                    </option>
                                    <option value="other" {{ $ticket->type == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="order_id" class="form-label">Mã đơn hàng</label>
                                <input type="text" class="form-control" id="order_id" name="order_id"
                                    placeholder="Nhập mã đơn hàng" value="{{ $ticket->order_id }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="form_status" class="form-control">
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Mở</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Đóng</option>
                                    <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Chờ trả lời</option>
                                </select>
                            </div>
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Lưu
                            </button>
                            <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary-gradient">
                                <i class="bx bx-arrow-back"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
