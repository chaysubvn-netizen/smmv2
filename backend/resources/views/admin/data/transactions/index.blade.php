@extends('admin.layouts.app')
@section('title', "Lịch sử giao dịch")
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Lịch sử giao dịch</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100" id="table-transactions">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tài khoản</th>
                                    <th>Mã giao dịch</th>
                                    <th>Nhận</th>
                                    <th>Trước khi</th>
                                    <th>Sau khi</th>
                                    <th>Ghi chú</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
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
        $(document).ready(function(){
            let table = $("#table-transactions").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Transaction']) }}",
                    data: function (d) {
                        d.type = $('#type').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'user.username', name: 'user.username'},
                    {data: 'transaction_code', name: 'transaction_code'},
                    {
                        data: 'amount',
                        name: 'amount',
                        render: function (data, type, row) {
                            if(row.type == 'add') {
                                return `<span class="text-success">+${(row?.amount_format)}</span>`;
                            } else {
                                return `<span class="text-danger">-${(row?.amount_format)}</span>`;
                            }
                        }
                    },
                    {
                        data: 'balance_before',
                        name: 'balance_before',
                        render: function (data, type, row) {
                            return `<span class="text-success">${(row?.balance_before_format)}</span>`;
                        }
                    },
                    {
                        data: 'balance_after',
                        name: 'balance_after',
                        render: function (data, type, row) {
                            return `<span class="text-success">${(row?.balance_after_format)}</span>`;
                        }
                    },
                    {data: 'description', name: 'description'},
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data, type, row) {
                            return data == 'success' ? '<span class="text-success">Hoàn thành</span>' : '<span class="text-danger">Đang xử lý</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function (data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    }
                ],
                order: [[0, "desc"]],
            });
        })
    </script>
@endsection
