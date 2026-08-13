@extends('admin.layouts.app')
@section('title', 'Lịch sử nạp tiền')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách đã nạp tiền</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 form-group">
                            <label for="type" class="form-label">Phương thức nạp</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="bank">Chuyển khoản</option>
                                <option value="usdt">Nạp USDT</option>
                                <option value="binance">Nạp Binance</option>
                                <option value="manual">Cộng tay</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table w-100" id="table-deposits">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tài khoản</th>
                                    <th>Phương thức</th>
                                    <th>Mã giao dịch</th>
                                    <th>Số tiền</th>
                                    <th>Khuyến mãi</th>
                                    <th>Thực nhận</th>
                                    <th>Nội dung</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $("#type").select2({
            placeholder: "Chọn phương thức nạp",
            allowClear: true
        });
    </script>
    <script>
        $(document).ready(function(){
            let table = $('#table-deposits').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Recharge']) }}",
                    data: function (d) {
                        d.type = $('#type').val();
                    }
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'user.username',
                        name: 'user.username',
                    },
                    {
                        data: 'method',
                        name: 'method',
                        render: function(data, type, row) {
                            if (data == 'Cộng tay') {
                                return 'Cộng tay';
                            } else if (row.type == 'bank' || row.method == 'banking') {
                                return 'Chuyển khoản';
                            } else if (row.method == 'binance') {
                                return 'Nạp Binance';
                            } else {
                                return 'Nạp USDT';
                            }
                        }
                    },
                    {
                        data: 'transaction_id',
                        name: 'transaction_id',
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        render: function(data, type, row) {
                            return `<span class="text-success">${(row?.amount_format)}</span>`;
                        }
                    },
                    {
                        data: 'bonus',
                        name: 'bonus',
                        render: function(data, type, row) {
                            return `<span class="text-success">${formatCurrency(data)}</span>`;
                        }
                    },
                    {
                        data: 'real_amount',
                        name: 'real_amount',
                        render: function(data, type, row) {
                            return `<span class="text-success">${(row?.real_amount_format)}</span>`;
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data, type, row) {
                            return `<textarea class="form-control" rows="2" readonly>${data}</textarea>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            return data == 'completed' ? '<span class="badge badge-success">Thành công</span>' : '<span class="badge badge-danger">Thất bại</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    },
                    {
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row) {
                            if (row.status != 'completed' && row.status != 'failed') {
                                return `
                                    <button class="btn btn-sm btn-primary" onclick="approveRecharge(${data})">Duyệt</button>
                                    <button class="btn btn-sm btn-danger" onclick="cancelRecharge(${data})">Hủy</button>
                                `;
                            }
                            return '';
                        }
                    }
                ],
                order: [[0, 'desc']],
            });

            $('#type').on('change', function() {
                table.draw();
            });

            window.approveRecharge = function(id) {
                Swal.fire({
                    title: 'Xác nhận duyệt nạp tiền?',
                    text: "Số tiền sẽ được cộng vào tài khoản người dùng!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.data.deposits.approve') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire('Thành công', res.message, 'success');
                                    table.draw();
                                } else {
                                    Swal.fire('Lỗi', res.message, 'error');
                                }
                            }
                        });
                    }
                })
            }
            window.cancelRecharge = function(id) {
                Swal.fire({
                    title: 'Xác nhận hủy nạp tiền?',
                    text: "Giao dịch này sẽ bị đánh dấu là thất bại!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.data.deposits.cancel') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire('Thành công', res.message, 'success');
                                    table.draw();
                                } else {
                                    Swal.fire('Lỗi', res.message, 'error');
                                }
                            }
                        });
                    }
                })
            }
        });
    </script>
@endsection
