@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa người dùng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Chỉnh sửa thông tin</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" data-ajax="true">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="username" class="form-label">Tài khoản</label>
                                <input type="text" class="form-control" id="username" name="username" readonly
                                    placeholder="Nhập họ và tên" value="{{ $user->username }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="email" class="form-label">Địa chỉ Email</label>
                                <input type="email" class="form-control" id="email" name="email" readonly
                                    placeholder="Nhập địa chỉ email" value="{{ $user->email }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="created_at" class="form-label">Thời gian đăng kí</label>
                                <input type="text" class="form-control" id="created_at" name="created_at" readonly
                                    placeholder="Nhập họ và tên" value="{{ $user->created_at }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="api_key" class="form-label">Api Key</label>
                                <input type="text" class="form-control" id="api_key" name="api_key" readonly
                                    placeholder="Nhập họ và tên" value="{{ $user->api_key }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="balance" class="form-label">Số dư</label>
                                <input type="text" class="form-control" id="balance" name="balance" readonly
                                    placeholder="Nhập họ và tên" value="{{ $user->balance_format }}đ">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="balance" class="form-label">Tổng nạp</label>
                                <input type="text" class="form-control" id="balance" name="balance" readonly
                                    placeholder="Nhập họ và tên" value="{{ $user->total_deposit_format }}đ">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="level" class="form-label">Cấp bậc thành viên</label>
                                <select name="level" id="level" class="form-control">
                                    <option value="member" {{ $user->level == 'member' ? 'selected' : '' }}>Thành viên</option>
                                    <option value="silver" {{ $user->level == 'silver' ? 'selected' : '' }}>Rank Bạc</option>
                                    <option value="gold" {{ $user->level == 'gold' ? 'selected' : '' }}>Rank Vàng</option>
                                    <option value="platinum" {{ $user->level == 'platinum' ? 'selected' : '' }}>Rank Bạch Kim</option>
                                    <option value="diamond" {{ $user->level == 'diamond' ? 'selected' : '' }}>Rank Kim Cương</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="form_status" class="form-control">
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                    <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Tạm ngưng</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="commission" class="form-label">Hoa hồng giới thiệu</label>
                                <input type="text" class="form-control" id="commission" name="commission"
                                    placeholder="Nhập hoa hồng giới thiệu" value="{{ $user->commission ?? 0 }}" readonly>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="total_commission" class="form-label">Tổng số hoa hồng</label>
                                <input type="text" class="form-control" id="total_commission" name="total_commission" value="{{ $user->total_commission }}" />
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                placeholder="Nhập mật khẩu mới nếu muốn thay đổi">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Cộng tiền tài khoản</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update-balance', $user->id) }}" method="POST" data-ajax="true">
                        @csrf
                        <input type="hidden" name="action" value="add">
                        <div class="form-group mb-3">
                            <label for="balace" class="form-label">Số tiền</label>
                            <input type="text" class="form-control" id="balance" name="balance"
                                placeholder="Nhập số tiền muốn cộng vào tài khoản">
                        </div>
                        <div class="form-group mb-3">
                            <label for="reason" class="form-label">Lý do</label>
                            <textarea class="form-control" id="reason" name="reason"
                                placeholder="Nhập lý do cộng tiền nếu cộng tiền"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Cộng tiền
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Trừ tiền tài khoản</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update-balance', $user->id) }}" method="POST" data-ajax="true">
                        @csrf
                        <input type="hidden" name="action" value="sub">
                        <div class="form-group mb-3">
                            <label for="balace" class="form-label">Số tiền</label>
                            <input type="text" class="form-control" id="balance" name="balance"
                                placeholder="Nhập số tiền muốn Trừ vào tài khoản">
                        </div>
                        <div class="form-group mb-3">
                            <label for="reason" class="form-label">Lý do</label>
                            <textarea class="form-control" id="reason" name="reason"
                                placeholder="Nhập lý do Trừ tiền nếu Trừ tiền"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Trừ tiền
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Lịch sử hoạt động gần đây</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100 table-bordered" id="table-transactions">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã giao dịch</th>
                                    <th>Số dư trước</th>
                                    <th>Số tiền</th>
                                    <th>Số dư sau</th>
                                    <th>Nội dung</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->transaction_code }}</td>
                                        <td>
                                            {{ number_format($transaction->balance_before, 0, ',', '.') }}đ
                                        </td>
                                        <td>
                                            @if ($transaction->type == 'add')
                                                +{{ number_format($transaction->amount, 0, ',', '.') }}đ
                                            @else
                                                -{{ number_format($transaction->amount, 0, ',', '.') }}đ
                                            @endif
                                        </td>
                                        <td>
                                            {{ number_format($transaction->balance_after, 0, ',', '.') }}đ
                                        </td>
                                        <td>
                                            <textarea class="form-control" readonly rows="2">{{ $transaction->description }}</textarea>
                                        </td>
                                        <td>
                                            @if ($transaction->status == 'success')
                                                <span class="badge bg-success">Thành công</span>
                                            @elseif ($transaction->status == 'pending')
                                                <span class="badge bg-warning">Đang xử lý</span>
                                            @else
                                                <span class="badge bg-danger">Thất bại</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#table-transactions').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi",
                    "zeroRecords": "Không tìm thấy bản ghi nào",
                    "info": "Đang hiển thị trang _PAGE_ trên _PAGES_",
                    "infoEmpty": "Không có bản ghi nào",
                    "infoFiltered": "(được lọc từ _MAX_ tổng số bản ghi)",
                    "search": "Tìm kiếm:",
                    "paginate": {
                        "first": "Đầu tiên",
                        "last": "Cuối cùng",
                        "next": "Tiếp theo",
                        "previous": "Trước"
                    }
                }
            });
        });
    </script>
@endsection
