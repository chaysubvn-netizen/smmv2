@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa tài khoản ngân hàng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Chỉnh sửa tài khoản ngân hàng</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.banks.accounts.update', $accountBank->id) }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="icon" class="form-label">Ảnh/Icon</label>
                            <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                            @if ($accountBank->icon)
                                <img src="{{ $accountBank->icon }}" alt="Icon" class="mt-2" style="width: 100px;">
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <label for="bank_name" class="form-label">Tên ngân hàng</label>
                            <select name="bank_name" id="bank_name" class="form-control">
                                <option value="mbbank" {{ $accountBank->bank_name == 'mbbank' ? 'selected' : '' }}>Mbbank</option>
                                <option value="vietcombank" {{ $accountBank->bank_name == 'vietcombank' ? 'selected' : '' }}>Vietcombank</option>
                                <option value="acb" {{ $accountBank->bank_name == 'acb' ? 'selected' : '' }}>ACB</option>
                                <option value="viettinbank" {{ $accountBank->bank_name == 'viettinbank' ? 'selected' : '' }}>Viettinbank</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bank_code" class="form-label">Mã ngân hàng (VietQR)</label>
                            <input type="text" class="form-control" id="bank_code" name="bank_code" value="{{ $accountBank->bank_code }}"
                                placeholder="Ví dụ: MB, VCB, ICB, ACB...">
                            <small class="text-muted">Xem mã tại vietqr.io/danh-sach-bin-ngan-hang</small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="account_number" class="form-label">Số tài khoản</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" value="{{ $accountBank->account_number }}"
                                placeholder="Nhập số tài khoản">
                        </div>
                        <div class="form-group mb-3">
                            <label for="account_name" class="form-label">Tên tài khoản</label>
                            <input type="text" class="form-control" id="account_name" name="account_name" value="{{ $accountBank->account_name }}"
                                placeholder="Nhập tên tài khoản">
                        </div>
                        <div class="form-group mb-3">
                            <label for="branch" class="form-label">Chi nhánh</label>
                            <input type="text" class="form-control" id="branch" name="branch" value="{{ $accountBank->branch }}"
                                placeholder="Nhập chi nhánh">
                        </div>
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="form_status" class="form-control">
                                <option value="active" {{ $accountBank->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ $accountBank->status == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Lưu
                        </button>
                        <a href="{{ route('admin.banks.accounts.index') }}" class="btn btn-secondary-gradient">
                            <i class="bx bx-x"></i> Quay lại
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
