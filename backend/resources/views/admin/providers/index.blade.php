@extends('admin.layouts.app')
@section('title', 'Quản Lý Kết Nối API')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách API Providers</h4>
                    <div class="card-tool">
                        <a href="{{ route('admin.providers.create') }}" class="btn btn-primary btn-primary-gradient btn-wave">
                            <i class="bx bx-plus"></i>
                            <span>Thêm mới</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100 table-bordered" id="table-providers">
                            <thead>
                                <tr>
                                    <th>Website</th>
                                    <th>Type</th>
                                    <th>Số dư</th>
                                    <th>Thống kê</th>
                                    <th>Chi tiết</th>
                                    <th>Trạng thái</th>
                                    <th data-sortable="false" width="100">Thao tác</th>
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
        $(document).ready(function() {
            let table = $('#table-providers').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'ApiProvider']) }}",
                    type: "GET",
                },
                columns: [{
                        data: 'api_url',
                        name: 'api_url',
                        render: function(data, type, row) {
                            return `
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bx bx-link me-2 text-primary"></i>
                                        <a href="${data}" target="_blank" class="fw-bold">${data}</a>
                                    </div>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bx bx-key me-2"></i>
                                        <span>${row.api_key.substring(0, 10)}...${row.api_key.substring(row.api_key.length - 5)}</span>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'type',
                        name: 'type',
                        render: function(data) {
                            return `<span class="badge bg-primary-transparent">${data.toUpperCase()}</span>`;
                        }
                    },
                    {
                        data: 'balance_format',
                        name: 'balance',
                         render: function(data, type, row) {
                            return `<span class="fw-bold text-success">${data}</span>`;
                        }
                    },
                    {
                        data: 'id', 
                        name: 'id', // Placeholder for stats
                        render: function(data, type, row) {
                            return `
                                <div class="small">
                                    <div class="mb-1"><i class="bx bx-category text-info me-1"></i> Chuyên mục: <span class="badge bg-info">${row.count_categories}</span></div>
                                    <div class="mb-1"><i class="bx bx-list-ul text-warning me-1"></i> Dịch vụ: <span class="badge bg-warning text-dark">${row.count_services}</span></div>
                                    <div class="mb-1"><i class="bx bx-cart text-success me-1"></i> Đơn hàng: <span class="badge bg-success">${row.count_orders}</span></div>
                                    <div class="mb-1"><i class="bx bx-money text-primary me-1"></i> Doanh thu: <span class="text-primary fw-bold">${row.revenue}</span></div>
                                    <div><i class="bx bx-trending-up text-danger me-1"></i> Lợi nhuận: <span class="text-danger fw-bold">${row.profit}</span></div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'id',
                        name: 'id', // Placeholder for details
                        render: function(data, type, row) {
                             let syncServices = row.sync_services ? '<span class="badge bg-success">ON</span>' : '<span class="badge bg-secondary">OFF</span>';
                             let autoPrice = row.auto_update_price ? '<span class="badge bg-success">ON</span>' : '<span class="badge bg-secondary">OFF</span>';
                             let updateName = row.auto_update_name ? '<span class="badge bg-success">ON</span>' : '<span class="badge bg-secondary">OFF</span>';
                             let filterHtml = row.filter_html ? '<span class="badge bg-success">ON</span>' : '<span class="badge bg-secondary">OFF</span>';

                            return `
                                <div class="small">
                                    <div class="mb-1 d-flex justify-content-between"><span>Đồng bộ dịch vụ:</span> ${syncServices}</div>
                                    <div class="mb-1 d-flex justify-content-between"><span>Cập nhật giá:</span> ${autoPrice}</div>
                                    <div class="mb-1 d-flex justify-content-between"><span>Cập nhật tên:</span> ${updateName}</div>
                                    <div class="mb-1 d-flex justify-content-between"><span>Lọc HTML:</span> ${filterHtml}</div>
                                    <div class="mb-1">
                                        <span>% Tăng giá: </span>
                                        <span class="badge bg-outline-primary" title="Thành viên">${row.increase_rate}%</span>
                                        <span class="badge bg-outline-warning" title="Cộng tác viên">${row.increase_rate_collaborator}%</span>
                                        <span class="badge bg-outline-info" title="Đại lý">${row.increase_rate_agency}%</span>
                                        <span class="badge bg-outline-success" title="Nhà phân phối">${row.increase_rate_distributor}%</span>
                                    </div>
                                    <div>
                                        <span>Tỷ giá: </span> <span class="badge bg-light text-dark border">${row.exchange_rate_format}</span>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                             let checked = data === 'active' ? 'checked' : '';
                             return `
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="switch-${row.id}" ${checked} onclick="updateStatus(${row.id}, '${data}')">
                                </div>
                             `;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-cog"></i> Thao tác
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="${row.edit_url}"><i class="bx bx-edit me-2"></i>Chỉnh sửa</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="updateBalance(${row.id})"><i class="bx bx-dollar me-2"></i>Cập nhật số dư</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="updatePrices(${row.id})"><i class="bx bx-refresh me-2"></i>Tính lại giá dịch vụ</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteData([${row.id}], 'ApiProvider')"><i class="bx bx-trash me-2"></i>Xóa</a></li>
                                    </ul>
                                </div>
                            `;
                        }
                    },
                ]
            });
        });

        function updateBalance(id) {
             let url = "{{ route('admin.providers.update-balance', ':id') }}";
            url = url.replace(':id', id);
            Swal.fire({
                icon: 'warning',
                title: 'Thông báo',
                text: "Bạn có chắc chắn muốn cập nhật số dư không?",
                showCancelButton: true,
                confirmButtonText: 'Cập nhật',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                     $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                             _token: "{{ csrf_token() }}"
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.showLoading();
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Thành công', response.message, 'success');
                                $('#table-providers').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Thất bại', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Lỗi', 'Có lỗi xảy ra', 'error');
                        }
                    });
                }
            });
        }

        function updatePrices(id) {
             let url = "{{ route('admin.providers.update-prices', ':id') }}";
            url = url.replace(':id', id);
            Swal.fire({
                icon: 'warning',
                title: 'Thông báo',
                text: "Bạn có chắc chắn muốn tính toán lại giá cho toàn bộ dịch vụ của nhà cung cấp này dựa trên % đã cài đặt không?",
                showCancelButton: true,
                confirmButtonText: 'Tính lại ngay',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                     $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.showLoading();
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Thành công', response.message, 'success');
                                $('#table-providers').DataTable().ajax.reload();
                            } else {
                                Swal.fire('Thất bại', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Lỗi', 'Có lỗi xảy ra', 'error');
                        }
                    });
                }
            });
        }
        
        function updateStatus(id, currentStatus) {
            let newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            // Logic to update status via AJAX if needed, usually handled by a generic route
            // For now, let's just show a toast or message
            // Ideally we should have a route for status update
             $.ajax({
                url: "{{ route('admin.loaddata.update-field') }}",
                type: "POST",
                data: {
                    id: id,
                    model: 'ApiProvider',
                    field: 'status',
                    value: newStatus,
                     _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                         $('#table-providers').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    </script>
@endsection
