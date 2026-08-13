@extends('admin.layouts.app')
@section('title', 'Quản lý Affiliate')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="card custom-card border-top-card border-top-primary rounded-0">
                        <div class="card-body">
                            <div class="text-center">
                                <p class="fs-14 fw-semibold mb-2">Tổng đã mời</p>
                                <div class="d-flex align-items-center justify-content-center flex-wrap">
                                    <h4 class="mb-0 fw-semibold">{{ $affiliates->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card custom-card border-top-card border-top-primary rounded-0">
                        <div class="card-body">
                            <div class="text-center">
                                <p class="fs-14 fw-semibold mb-2">Tổng đã nạp</p>
                                <div class="d-flex align-items-center justify-content-center flex-wrap">
                                    @php
                                        $totalDeposit = 0;
                                        foreach ($affiliates->get() as $affiliate) {
                                            $totalDeposit += $affiliate->user->total_deposit;
                                        }
                                    @endphp
                                    <h4 class="mb-0 fw-semibold">{{ number_format($totalDeposit) }}đ</h4>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card custom-card border-top-card border-top-primary rounded-0">
                        <div class="card-body">
                            <div class="text-center">
                                <p class="fs-14 fw-semibold mb-2">Tổng hoa hồng nhận</p>
                                <div class="d-flex align-items-center justify-content-center flex-wrap">
                                    <h4 class="mb-0 fw-semibold">{{ number_format($affiliates->sum('commission')) }}đ</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Danh Sách Người Được Giới Thiệu - Hiện có <span
                            class="fw-bold text-success">{{ $affiliates->count() }}</span> Người</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100" id="table-affiliates">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tài khoản</th>
                                    <th>Giới thiệu</th>
                                    <th>Tổng tiền nạp</th>
                                    <th>Tổng hoa hồng nhận</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($affiliates->orderBy('id', 'desc')->get() as $affiliate)
                                    <tr>
                                        <td>{{ $affiliate->id }}</td>
                                        <td>{{ $affiliate->user->username }}</td>
                                        <td>{{ $affiliate->refUser->username }}</td>
                                        <td>{{ number_format($affiliate->user->total_deposit, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($affiliate->commission, 0, ',', '.') }} đ</td>
                                        <td>{{ $affiliate->created_at->format('d/m/Y H:i:s') }}</td>
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
            $('#table-affiliates').DataTable({
                "order": [
                    [0, "desc"]
                ],
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi",
                    "zeroRecords": "Không tìm thấy bản ghi nào",
                    "info": "Đang hiển thị _PAGE_ trên _PAGES_ trang",
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
