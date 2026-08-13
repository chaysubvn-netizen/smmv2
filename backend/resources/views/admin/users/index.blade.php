@extends('admin.layouts.app')
@section('title', 'Danh sách người dùng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row">
                                <div
                                    class="col-xxl-3 col-xl-2 col-lg-3 col-md-3 col-sm-4 col-4 d-flex align-items-center justify-content-center ecommerce-icon success px-0">
                                    <span class="rounded p-3 bg-success-transparent"> <svg xmlns="http://www.w3.org/2000/svg"
                                            class="svg-white success" height="24px" viewBox="0 0 24 24" width="24px"
                                            fill="#000000">
                                            <path d="M0 0h24v24H0V0z" fill="none"></path>
                                            <path
                                                d="M12 6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2m0 10c2.7 0 5.8 1.29 6 2H6c.23-.72 3.31-2 6-2m0-12C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                                            </path>
                                        </svg> </span>
                                </div>
                                <div class="col-xxl-9 col-xl-10 col-lg-9 col-md-9 col-sm-8 col-8 px-0">
                                    <div class="mb-2">Tổng thành viên</div>
                                    <div class="text-muted mb-1 fs-12"> <span
                                            class="text-dark fw-semibold fs-20 lh-1 vertical-bottom">
                                            {{ number_format($totalUsers) }} </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row">
                                <div
                                    class="col-xxl-3 col-xl-2 col-lg-3 col-md-3 col-sm-4 col-4 d-flex align-items-center justify-content-center ecommerce-icon success px-0">
                                    <span class="rounded p-3 bg-primary-transparent">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="svg-white primary"
                                            enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                            width="24px" fill="#000000">
                                            <path d="M0,0h24v24H0V0z" fill="none"></path>
                                            <g>
                                                <path
                                                    d="M19.5,3.5L18,2l-1.5,1.5L15,2l-1.5,1.5L12,2l-1.5,1.5L9,2L7.5,3.5L6,2v14H3v3c0,1.66,1.34,3,3,3h12c1.66,0,3-1.34,3-3V2 L19.5,3.5z M15,20H6c-0.55,0-1-0.45-1-1v-1h10V20z M19,19c0,0.55-0.45,1-1,1s-1-0.45-1-1v-3H8V5h11V19z">
                                                </path>
                                                <rect height="2" width="6" x="9" y="7"></rect>
                                                <rect height="2" width="2" x="16" y="7"></rect>
                                                <rect height="2" width="6" x="9" y="10"></rect>
                                                <rect height="2" width="2" x="16" y="10"></rect>
                                            </g>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col-xxl-9 col-xl-10 col-lg-9 col-md-9 col-sm-8 col-8 px-0">
                                    <div class="mb-2">Tổng số dư còn lại</div>
                                    <div class="text-muted mb-1 fs-12"> <span
                                            class="text-dark fw-semibold fs-20 lh-1 vertical-bottom">
                                            {{ convert_currency($totalBalance, Auth::user()->currency, false) }} </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row">
                                <div
                                    class="col-xxl-3 col-xl-2 col-lg-3 col-md-3 col-sm-4 col-4 d-flex align-items-center justify-content-center ecommerce-icon success px-0">
                                    <span class="rounded p-3 bg-warning-transparent">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="svg-white warning"
                                            enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24"
                                            width="24px" fill="#000000">
                                            <path d="M0,0h24v24H0V0z" fill="none"></path>
                                            <g>
                                                <path
                                                    d="M19.5,3.5L18,2l-1.5,1.5L15,2l-1.5,1.5L12,2l-1.5,1.5L9,2L7.5,3.5L6,2v14H3v3c0,1.66,1.34,3,3,3h12c1.66,0,3-1.34,3-3V2 L19.5,3.5z M15,20H6c-0.55,0-1-0.45-1-1v-1h10V20z M19,19c0,0.55-0.45,1-1,1s-1-0.45-1-1v-3H8V5h11V19z">
                                                </path>
                                                <rect height="2" width="6" x="9" y="7"></rect>
                                                <rect height="2" width="2" x="16" y="7"></rect>
                                                <rect height="2" width="6" x="9" y="10"></rect>
                                                <rect height="2" width="2" x="16" y="10"></rect>
                                            </g>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col-xxl-9 col-xl-10 col-lg-9 col-md-9 col-sm-8 col-8 px-0">
                                    <div class="mb-2">Tổng đã nạp</div>
                                    <div class="text-muted mb-1 fs-12"> <span
                                            class="text-dark fw-semibold fs-20 lh-1 vertical-bottom">
                                            {{ convert_currency($totalDeposit, Auth::user()->currency, false) }} </span>
                                    </div>
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
                    <h4 class="card-title">Danh sách thành viên</h4>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="level" class="form-label">Tìm theo cấp bậc</label>
                                <select name="level" id="level" class="form-control" onchange="filterData()">
                                    <option value="">Tất cả</option>
                                    <option value="silver">Rank bạc</option>
                                    <option value="gold">Rank vàng</option>
                                    <option value="platinum">Rank bạch kim</option>
                                    <option value="diamond">Rank kim cương</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="status" class="form-label">Tìm theo trạng thái</label>
                                <select name="status" id="form_status" class="form-control" onchange="filterData()">
                                    <option value="all">Tất cả</option>
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Ngừng hoạt động</option>
                                    <option value="blocked">Đã khoá</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table w-100 table-bordered" id="table-users">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Tài khoản</th>
                                    <th>Email</th>
                                    <th>Số dư</th>
                                    <th>Tổng nạp</th>
                                    <th>Cấp bậc</th>
                                    <th>Chức vụ</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đăng kí</th>
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
        function filterData() {
            let level = $('#level').val();
            let status = $('#status').val();
            $("#table-users").DataTable().ajax.url("{{ route('admin.loaddata.index', ['model' => 'User']) }}&level=" +
                level + "&status=" + status).load();
        }
    </script>
    <script>
        $(document).ready(function() {
            const table = $("#table-users").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'User']) }}",
                    type: "GET",
                },
                columns: [{
                        data: "id",
                        name: "id",
                        className: "text-center",
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                        render: function(data, type, row) {
                            return `
                            <a href="${row?.edit_url}" class="btn btn-primary btn-sm">
                                <i class="bx bx-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="deleteData([${row?.id}], 'User')">
                                <i class="bx bx-trash"></i>
                            </button>
                        `;
                        }
                    },
                    {
                        data: "username",
                        name: "username",
                    },
                    {
                        data: "email",
                        name: "email",
                    },
                    {
                        data: "balance",
                        name: "balance",
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary">${row?.balance_format}</span>`;
                        }
                    },
                    {
                        data: "total_deposit",
                        name: "total_deposit",
                        render: function(data, type, row) {
                            return `<span class="badge bg-success">${row?.total_deposit_format}</span>`;
                        }
                    },
                    {
                        data: "level",
                        name: "level",
                        render: function(data, type, row) {
                            return levelUser(data, true);
                        }
                    },
                    {
                        data: "role",
                        name: "role",
                    },
                    {
                        data: "status",
                        name: "status",
                        render: function(data, type, row) {
                            return statusUser(data, true);
                        }
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        render: function(data, type, row) {
                            return moment(data).format("DD/MM/YYYY HH:mm:ss");
                        }
                    }
                ],
                order: [
                    [0, "desc"]
                ],
            });
            $('#level').select2({
                minimumResultsForSearch: Infinity,
                placeholder: "Tìm theo cấp bậc",
                allowClear: true,
            });
            $('#status').select2({
                minimumResultsForSearch: Infinity,
                placeholder: "Tìm theo trạng thái",
                allowClear: true,
            });

        })
    </script>
@endsection
