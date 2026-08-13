@extends('admin.layouts.app')
@section('title', 'Cập nhật giá dịch vụ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Chỉnh giá</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.services.update-rate', $service->id) }}" method="POST" data-ajax="true">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Tên dịch vụ</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $service->name }}"
                                readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="rate" class="form-label">Giá hiện tại</label>
                            <input type="text" class="form-control" id="rate" name="rate" value="{{ $service->rate }}"
                                readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="rate_new" class="form-label">Giá mới</label>
                            <input type="text" class="form-control" id="rate_new" name="rate_new"
                                placeholder="Nhập giá mới">
                        </div>
                        <div class="form-group mb-3">
                            <label for="type" class="form-label">Tăng/Giảm</label>
                            <select class="form-select" id="type" name="type">
                                <option value="increase">Tăng</option>
                                <option value="decrease">Giảm</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bxs-save"></i>
                            Cập nhật
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
