@extends('admin.layouts.app')
@section('title', 'Quản lý tiền thưởng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách tiền thưởng</h4>
                    <div class="">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modal-add-bonus">
                            <i class="bx bx-plus"></i> Thêm mới
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100" id="table-bonus">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Loại nạp</th>
                                    <th>Số tiền</th>
                                    <th>Thưởng thêm</th>
                                    <th>Thời gian tạo</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal  --}}
    <div class="modal fade" id="modal-add-bonus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.banks.bonus.create') }}" data-ajax="true">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Thêm tiền thưởng</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="type" class="form-label">Thưởng cho</label>
                        <select class="form-select" name="type" id="type">
                            <option value="banking">Nạp ngân hàng</option>
                            <option value="usdt">USDT</option>
                            <option value="binance">Binance Pay</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="min_amount" class="form-label">Số tiền nạp tối thiểu USD</label>
                        <input type="number" class="form-control" name="min_amount" id="min_amount" value="1"
                            placeholder="Nhập số tiền nạp tối thiểu">
                        <small class="text-muted">Đơn vị mặc định là USD</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="bonus" class="form-label">Thưởng thêm (%)</label>
                        <input type="number" class="form-control" id="bonus" name="bonus" value="0"
                            placeholder="Nhập số % thưởng thêm">
                    </div>
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" name="status" id="status">
                            <option value="active">Kích hoạt</option>
                            <option value="inactive">Không kích hoạt</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm tiền thưởng</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            let table = $('#table-bonus').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'RechargeBonus']) }}",
                    type: "GET",
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row) => {
                            return `
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteData([${row.id}], 'RechargeBonus')">
                                <i class="bx bx-trash"></i>
                            </button>
                            `;
                        }
                    },
                    {
                        data: 'type',
                        name: 'type',
                        render: function(data, type, row) {
                            if (data == 'banking') {
                                return '<span class="badge bg-primary">Nạp ngân hàng</span>';
                            } else if (data == 'usdt') {
                                return '<span class="badge bg-success">Nạp USDT</span>';
                            } else if (data == 'binance') {
                                return '<span class="badge bg-warning text-dark">Binance Pay</span>';
                            }
                        }
                    },
                    {
                        data: 'min_amount',
                        name: 'min_amount',
                        render: function(data, type, row) {
                            return "$"+data;
                        }
                    },
                    {
                        data: 'bonus',
                        name: 'bonus',
                        render: function(data, type, row) {
                            return data + "%";
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    },
                ],
            });
        })
    </script>
@endsection
