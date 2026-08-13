@extends('admin.layouts.app')
@section('title', 'Quản lý đơn hàng')
@section('content')
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 mb-4">
        <!-- Tổng đơn hàng -->
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="me-3">
                            <span class="avatar avatar-md p-2 bg-primary">
                                <i class="fa-solid fa-cart-shopping svg-white"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                <h5 class="fw-semibold mb-0 lh-1">{{ number_format($ordersCount['total']) }}</h5>
                            </div>
                            <p class="mb-0 fs-10 op-7 text-muted fw-semibold">TỔNG ĐƠN HÀNG</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng đang chờ xử lý -->
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="me-3">
                            <span class="avatar avatar-md p-2 bg-info">
                                <i class="fa-solid fa-hourglass-half svg-white"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                <h5 class="fw-semibold mb-0 lh-1">{{ number_format($ordersCount['pending']) }}</h5>
                            </div>
                            <p class="mb-0 fs-10 op-7 text-muted fw-semibold">ĐƠN HÀNG CHỜ XỬ LÝ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng đang chạy -->
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="me-3">
                            <span class="avatar avatar-md p-2 bg-warning">
                                <i class="fa-solid fa-clock svg-white"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                <h5 class="fw-semibold mb-0 lh-1">{{ number_format($ordersCount['inprogress']) }}</h5>
                            </div>
                            <p class="mb-0 fs-10 op-7 text-muted fw-semibold">ĐơN HàNG đANG CHạY</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng hoàn thành -->
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="me-3">
                            <span class="avatar avatar-md p-2 bg-success">
                                <i class="fa-solid fa-check-circle svg-white"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                <h5 class="fw-semibold mb-0 lh-1">{{ number_format($ordersCount['completed']) }}</h5>
                            </div>
                            <p class="mb-0 fs-10 op-7 text-muted fw-semibold">ĐƠN HÀNG HOÀN THÀNH</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng hoàn tiền 1 phần -->
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="me-3">
                            <span class="avatar avatar-md p-2 bg-secondary">
                                <i class="fa-solid fa-exclamation-triangle svg-white"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                <h5 class="fw-semibold mb-0 lh-1">{{ number_format($ordersCount['partial']) }}</h5>
                            </div>
                            <p class="mb-0 fs-10 op-7 text-muted fw-semibold">ĐƠN HÀNG HOÀN TIỀN 1 PHẦN</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng hủy -->
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="me-3">
                            <span class="avatar avatar-md p-2 bg-danger">
                                <i class="fa-solid fa-times-circle svg-white"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex mb-1 align-items-top justify-content-between">
                                <h5 class="fw-semibold mb-0 lh-1">{{ number_format($ordersCount['canceled']) }}</h5>
                            </div>
                            <p class="mb-0 fs-10 op-7 text-muted fw-semibold">ĐƠN HÀNG HỦY</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-filter"></i> Bộ Lọc Nâng Cao</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="filter-username" placeholder="Tìm theo username">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mã đơn hàng</label>
                            <input type="text" class="form-control" id="filter-order-id" placeholder="Nhập ID đơn hàng">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dịch vụ</label>
                            <select class="form-select" id="filter-service">
                                <option value="">Tất cả dịch vụ</option>
                                @foreach($services ?? [] as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" id="filter-status">
                                <option value="">Tất cả</option>
                                <option value="Pending">Đang chờ</option>
                                <option value="In progress">Đang chạy</option>
                                <option value="Processing">Đang xử lý</option>
                                <option value="Active">Đang hoạt động</option>
                                <option value="Completed">Hoàn thành</option>
                                <option value="Partial">Hoàn tiền một phần</option>
                                <option value="Canceled">Huỷ</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" class="form-control" id="filter-date-from">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" class="form-control" id="filter-date-to">
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button class="btn btn-primary flex-fill" id="btn-filter">
                                <i class="bx bx-search"></i> Tìm kiếm
                            </button>
                            <button class="btn btn-secondary flex-fill" id="btn-reset-filter">
                                <i class="bx bx-reset"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="card-title me-2">| Danh Sách Đơn Hàng</h4>
                        <div class="btn-group me-2 shadow-sm">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="select-all-btn">
                                <i class="bx bx-check-double"></i> Chọn tất cả
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="deselect-all-btn">
                                <i class="bx bx-x-circle"></i> Bỏ chọn
                            </button>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-success btn-sm dropdown-toggle px-3 shadow-sm" data-bs-toggle="dropdown">
                                <i class="bx bx-download me-1"></i> Xuất file
                            </button>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item py-2" id="btn-export-csv" href="javascript:void(0);"><i class="bx bx-file me-2 text-success"></i>Xuất CSV (.csv)</a></li>
                                <li><a class="dropdown-item py-2" id="btn-export-txt" href="javascript:void(0);"><i class="bx bx-note me-2 text-warning"></i>Xuất TXT (.txt)</a></li>
                            </ul>
                        </div>
                    </div>

   

                    <div class="">
                        <button type="button" class="btn btn-info shadow-sm" onclick="checkAllStatus()">
                            <i class="bx bx-sync"></i> Cập nhật tất cả
                        </button>
                        <button type="button" class="btn btn-warning ms-1" onclick="$('#cleanOrdersModal').modal('show')">
                            <i class="bx bx-brush-alt"></i> Dọn dẹp
                        </button>
                        <button type="button" class="btn btn-danger ms-1" id="btn-delete" disabled onclick="deleteOrder()">
                            <i class="bx bx-trash"></i> Xóa <span id="count-delete">0</span>
                        </button>
                        <button type="button" class="btn btn-danger ms-1" id="btn-delete-all" onclick="deleteAllOrder()">
                            <i class="bx bx-trash-alt"></i> Xóa tất cả
                        </button>
                    </div>
                </div>  
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100" id="table-orders">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th width="1%">
                                        <input type="checkbox" id="check-all" class="form-check-input">
                                    </th>
                                    <th>Thao tác</th>
                                    <th>Đơn hàng</th>
                                    <th>Thông tin</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="mt-3 p-2 rounded d-flex align-items-center" id="order-statistics">
                        <span class="ps-2 fw-bold text-primary" style="border-left: 4px solid var(--primary-color);">
                            Tổng số đơn hàng: <span id="stat-total-orders">0</span> | 
                            Doanh thu: <span id="stat-total-revenue">0đ</span> | 
                            Lợi nhuận: <span id="stat-total-profit">0đ</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            let table = $("#table-orders").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Order']) }}",
                    type: "GET",
                    data: function(d) {
                        // Add filter parameters
                        d.filter_username = $('#filter-username').val();
                        d.filter_order_id = $('#filter-order-id').val();
                        d.filter_service = $('#filter-service').val();
                        d.filter_status = $('#filter-status').val();
                        d.filter_date_from = $('#filter-date-from').val();
                        d.filter_date_to = $('#filter-date-to').val();
                    }
                },
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: "id",
                        name: "id",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="form-check-input check-item" value="${data}">`;
                        },
                    },
                    {
                        data: "id",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" href="${row?.edit_url}"><i class="bx bx-show"></i> Xem</a></li>
                                            <li><a class="dropdown-item copy-link" href="javascript:void(0)" data-link="${row?.link}"><i class="bx bx-copy"></i> Copy Link</a></li>
                                        </ul>
                                    </div>`;
                        },
                    },
                    {
                        data: "id",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <ul class="list-none" style="list-style: none; padding-left: 0;">
                                    <li><strong>Dịch vụ:</strong> <span class="badge bg-primary-transparent">[${row?.service?.id ?? "NULL"}]</span></li>
                                    <li><strong>Tài khoản:</strong> <span class="badge bg-success-transparent">${row?.user?.username ?? ""} [${row?.user?.id ?? "NULL"}]</span></li>
                                    <li><strong>Tạo lúc:</strong> <span class="text-danger">${moment(row?.created_at).format("DD/MM/YYYY HH:mm")}</span></li>
                                    <li><strong>Cập nhật:</strong> <span class="text-muted">${moment(row?.updated_at).format("DD/MM/YYYY HH:mm")}</span></li>
                                </ul>
                            `;
                        },
                    },
                    {
                        data: "id",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let linkDisplay = row?.link?.length > 50 ? row?.link?.substring(0, 50) + '...' : row?.link;
                            return `
                                <ul style="list-style: none; padding-left: 0;">
                                    <li><strong>Link:</strong> <span class="text-primary" style="cursor:pointer" onclick="copyToClipboard('${row?.link}')" title="${row?.link}">${linkDisplay}</span></li>
                                    <li><strong>Quantity:</strong> <span class="badge bg-info">${Intl.NumberFormat().format(row?.quantity)}</span></li>
                                    <li>Rate: <span class="text-danger">${convertAndFormatCurrency(row?.rate, "VND", "VND")} / ${row?.rate_usd}</span></li>
                                    <li>Charge: <span class="text-success">(${row?.total_format})</span> ${row?.status != 'Canceled' ? `- (${row?.profit})` : ''}</li>
                                    <li><strong>Start:</strong> <span class="badge bg-secondary-transparent">${Intl.NumberFormat().format(row?.start_count)}</span> | <strong>Remains:</strong> <span class="badge bg-warning-transparent">${Intl.NumberFormat().format(row?.remains)}</span></li>
                                    <li><strong>Time Start:</strong> <span class="text-muted">${row?.time_start ? moment(row?.time_start).format("DD/MM/YYYY HH:mm") : 'N/A'}</span></li>
                                    <li><strong>Success Time:</strong> <span class="text-success">${row?.success_time ? moment(row?.success_time).format("DD/MM/YYYY HH:mm") : 'N/A'}</span></li>
                                </ul>
                            `;
                        },
                    },
                    {
                        data: "status",
                        name: "status",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <ul style="list-style: none; padding-left: 0;">
                                    <li><strong>Trạng thái:</strong> ${statusOrder(row?.status, true)}</li>
                                    ${row?.dripfeed_status != null ? `
                                            <li><strong>Dripfeed:</strong> ${statusOrder(row?.dripfeed_status, true)}</li>
                                            <li><strong>Đã chạy:</strong> ${row?.run ?? 0} / ${row?.loop_quantity ?? 0}</li>
                                            `
                                     : ""}
                                    <li><strong>Dịch vụ:</strong> <span class="text-primary">${row?.service?.name ?? "NULL"}</span></li>
                                    <li><strong>Note:</strong> <span class="text-muted">${row?.note ?? 'N/A'}</span></li>
                                    <li><strong>Main Order:</strong> ${row?.main_id ?? 'N/A'}</li>
                                </ul>
                            `;
                        }
                    },
                ],
                order: [
                    [0, "desc"]
                ],
            });

            table.on('xhr', function(e, settings, json) {
                if (json && json.meta) {
                    $('#stat-total-orders').text(json.meta.total_orders);
                    $('#stat-total-revenue').text(json.meta.total_revenue);
                    $('#stat-total-profit').text(json.meta.total_profit);
                }
            });

            // Filter button click
            $('#btn-filter').on('click', function() {
                table.ajax.reload();
            });

            // Reset filter button
            $('#btn-reset-filter').on('click', function() {
                $('#filter-username').val('');
                $('#filter-order-id').val('');
                $('#filter-service').val('');
                $('#filter-status').val('');
                $('#filter-date-from').val('');
                $('#filter-date-to').val('');
                table.ajax.reload();
            });

            // Enter key to filter
            $('#filter-username, #filter-order-id').on('keypress', function(e) {
                if (e.which === 13) {
                    table.ajax.reload();
                }
            });

            // Copy link functionality
            $(document).on('click', '.copy-link', function() {
                let link = $(this).data('link');
                copyToClipboard(link);
            });

            // Check all checkbox
            $("#check-all").on("click", function() {
                let checked = $(this).is(":checked");
                $(".check-item").prop("checked", checked);
                updateDeleteButton();
            });

            // Select all button
            $("#select-all-btn").on("click", function() {
                $(".check-item").prop("checked", true);
                $("#check-all").prop("checked", true);
                updateDeleteButton();
            });

            // Deselect all button
            $("#deselect-all-btn").on("click", function() {
                $(".check-item").prop("checked", false);
                $("#check-all").prop("checked", false);
                updateDeleteButton();
            });

            // Check single checkbox
            $("#table-orders").on("click", ".check-item", function() {
                updateDeleteButton();
                if ($(".check-item:checked").length == $(".check-item").length) {
                    $("#check-all").prop("checked", true);
                } else {
                    $("#check-all").prop("checked", false);
                }
            });

            function updateDeleteButton() {
                let count = $(".check-item:checked").length;
                $("#btn-delete").prop("disabled", count == 0);
                $("#count-delete").text(count);
            }
        });

        // Copy to clipboard function
        function copyToClipboard(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    showToast('success', 'Đã copy link vào clipboard!');
                }).catch(function(err) {
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            let textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.top = "-9999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast('success', 'Đã copy link vào clipboard!');
            } catch (err) {
                showToast('error', 'Không thể copy link!');
            }
            document.body.removeChild(textArea);
        }

        function showToast(type, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                alert(message);
            }
        }

        function deleteOrder() {
            let ids = [];
            $(".check-item:checked").each(function() {
                ids.push($(this).val());
            });
            if (ids.length == 0) return;
            
            deleteData(ids, 'Order', $('#table-orders').DataTable());
        }

        function deleteAllOrder() {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Hành động này sẽ xóa tất cả đơn hàng trên trang web này và không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý, xóa tất cả!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.loaddata.delete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            model: 'Order',
                            type: 'all'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Đã xóa!',
                                    response.message,
                                    'success'
                                );
                                $('#table-orders').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Lỗi!',
                                    response.message,
                                    'error'
                                );
                            }
                        }
                    });
                }
            })
        }
        $('#btn-export-csv').on('click', function () {
    exportOrder('csv');
});

$('#btn-export-txt').on('click', function () {
    exportOrder('txt');
});

function exportOrder(type) {
    let params = {
        filter_username: $('#filter-username').val(),
        filter_order_id: $('#filter-order-id').val(),
        filter_service: $('#filter-service').val(),
        filter_status: $('#filter-status').val(),
        filter_date_from: $('#filter-date-from').val(),
        filter_date_to: $('#filter-date-to').val(),
        type: type
    };

    let query = $.param(params);
    window.location.href = `{{ route('admin.data.orders.export') }}?` + query;
}

function showStep2CleanOrders() {
    const days = $('#clean-days').val();
    if (!days || days < 1) {
        showToast('error', 'Vui lòng nhập số ngày hợp lệ');
        return;
    }
    $('#clean-days-display').text(days);
    $('#cleanOrdersModal').modal('hide');
    $('#confirmCleanModal').modal('show');
}

$(document).on('input', '#confirm-text', function() {
    const text = $(this).val().toLowerCase().trim();
    if (text === 'toi dong y') {
        $('#btnFinalClean').prop('disabled', false).removeClass('opacity-50');
    } else {
        $('#btnFinalClean').prop('disabled', true).addClass('opacity-50');
    }
});

function executeCleanOrders() {
    const days = $('#clean-days').val();
    const confirmText = $('#confirm-text').val().toLowerCase().trim();
    
    if (confirmText !== 'toi dong y') return;

    const btn = $('#btnFinalClean');
    const originalText = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...');

    $.ajax({
        url: '{{ route("admin.data.orders.clean") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            days: days
        },
        success: function(res) {
            $('#confirmCleanModal').modal('hide');
            btn.prop('disabled', false).html(originalText);
            
            if(res.success) {
                Swal.fire('Thành công!', res.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Lỗi!', res.message, 'error');
            }
        },
        error: function(xhr) {
            $('#confirmCleanModal').modal('hide');
            btn.prop('disabled', false).html(originalText);
            Swal.fire('Lỗi!', 'Không thể thực hiện yêu cầu. Vui lòng thử lại sau!', 'error');
        }
    });
}

function checkAllStatus() {
    Swal.fire({
        title: 'Đang cập nhật...',
        text: 'Hệ thống đang kiểm tra trạng thái đơn hàng từ nguồn API. Vui lòng đợi!',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: "{{ route('cron.order.status') }}",
        type: "GET",
        success: function(response) {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: response.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                $('#table-orders').DataTable().ajax.reload(null, false);
            });
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire('Lỗi!', 'Không thể kết nối đến máy chủ cập nhật!', 'error');
        }
    });
}
    </script>

    <!-- Modal Step 1 -->
    <div class="modal fade" id="cleanOrdersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="background: #18191f; border-radius: 20px;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-800 text-white d-flex align-items-center">
                        <i class="bx bx-brush-alt me-2 text-warning"></i> Dọn dẹp đơn hàng cũ
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert bg-warning-transparent border-warning mb-4" style="background: rgba(245, 158, 11, 0.05);">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-exclamation-triangle text-warning me-2 fs-4"></i>
                            <h6 class="text-warning mb-0 fw-bold">THÔNG TIN</h6>
                        </div>
                        <ul class="text-muted small mb-0 ps-3">
                            <li>Xác định khoảng thời gian để dọn dẹp dữ liệu.</li>
                            <li>Hệ thống sẽ giữ lại đơn hàng trong số ngày này.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-white fw-bold"><i class="bx bx-calendar me-1"></i> Số ngày cần giữ lại</label>
                        <input type="number" class="form-control bg-dark border-0 text-white p-3" id="clean-days" placeholder="Ví dụ: 30" style="background: rgba(0,0,0,0.2) !important; border-radius: 12px;">
                        <div class="form-text text-muted">Nhập số ngày. Các đơn hàng cũ hơn số ngày này sẽ bị xóa.</div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="button" class="btn btn-secondary flex-fill py-3 fw-bold rounded-3" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.05); border: 0;">Huỷ bỏ</button>
                        <button type="button" class="btn btn-primary flex-fill py-3 fw-bold rounded-3" onclick="showStep2CleanOrders()">Tiếp tục <i class="bx bx-right-arrow-alt"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Step 2 (Xác nhận theo ảnh mẫu) -->
    <div class="modal fade" id="confirmCleanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-body p-5 text-center">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px; background: rgba(245, 158, 11, 0.1);">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 50px;"></i>
                        </div>
                    </div>
                    
                    <h2 class="fw-bold mb-3" style="color: #2871d8ff;">Xác nhận xóa đơn hàng cũ</h2>
                    <i class="fas fa-exclamation-triangle text-danger fs-3 mb-2"></i>
                    
                    <p class="mb-4 fw-bold" style="color: #ef4444;">Bạn sắp XÓA VĨNH VIỄN tất cả các đơn hàng cũ hơn <span id="clean-days-display">0</span> ngày</p>
                    
                    <div class="alert text-start mb-4" style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px;">
                        <div class="fw-bold text-danger mb-2"><i class="fas fa-exclamation-circle me-1"></i> Lưu ý:</div>
                        <ul class="text-danger small mb-0 ps-3" style="list-style-type: '• ';">
                            <li>Hành động này <span class="text-decoration-underline">KHÔNG THỂ HOÀN TÁC</span></li>
                            <li>Tất cả dữ liệu đơn hàng sẽ bị xóa vĩnh viễn</li>
                            <li>Chỉ giữ lại đơn hàng trong <span id="clean-days-display" class="fw-bold text-dark"></span> ngày gần nhất</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <p class="text-muted small mb-2">Để xác nhận, vui lòng nhập: <span class="fw-bold text-danger">toi dong y</span></p>
                        <input type="text" class="form-control text-center py-3" id="confirm-text" placeholder="Nhập tại đây..." style="border-radius: 10px; border: 1px solid #e2e8f0;">
                    </div>

                    @if(status_demo())
                    <div class="mb-4 d-flex align-items-center justify-content-center gap-2 p-2 rounded" style="background: #fff1f2; color: #e11d48;">
                        <i class="fas fa-exclamation-circle"></i> 
                        <span class="small fw-bold">Chức năng này không thể sử dụng trên website demo</span>
                    </div>
                    @endif

                    <div class="d-flex gap-3 justify-content-center">
                        <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center opacity-50" id="btnFinalClean" disabled onclick="executeCleanOrders()" style="background: #6366f1; border: 0; padding: 12px 30px; border-radius: 10px; min-width: 140px;">
                            <i class="fas fa-trash-alt me-2"></i> Xóa ngay
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="padding: 12px 30px; border-radius: 10px; min-width: 140px;">
                            <i class="fas fa-times me-2"></i> Huỷ bỏ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    #confirmCleanModal .btn-primary:hover:not(:disabled) {
        background: #4f46e5 !important;
    }
    #confirmCleanModal .modal-content {
        animation: modalScaleIn 0.3s ease-out;
    }
    @keyframes modalScaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    </style>
 @endsection
