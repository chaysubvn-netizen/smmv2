@extends('admin.layouts.app')
@section('title', 'Quản lý vé hỗ trợ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Danh sách ticket</h4>
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="autoLoadToggle" checked>
                            <label class="form-check-label" for="autoLoadToggle">Auto Load</label>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="intervalDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span id="intervalText">1x giây</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="intervalDropdown">
                                <li><a class="dropdown-item" href="#" data-seconds="1">1x giây</a></li>
                                <li><a class="dropdown-item" href="#" data-seconds="3">3x giây</a></li>
                                <li><a class="dropdown-item" href="#" data-seconds="5">5x giây</a></li>
                                <li><a class="dropdown-item" href="#" data-seconds="10">10 giây</a></li>
                                <li><a class="dropdown-item" href="#" data-seconds="30">30 giây</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" data-seconds="0">Tắt</a></li>
                            </ul>
                        </div>
                        <span class="badge bg-info" id="countdownBadge">1</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table class="table w-100" id="tabale-tickets">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Tiêu đề</th>
                                    <th>Người gửi</th>
                                    <th>Loại</th>
                                    <th>Order ID</th>
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
        $(document).ready(function() {
            let table = $('#tabale-tickets').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Ticket']) }}",
                    type: 'GET',
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
                        render: function(data, type, row) {
                            return `
                            <a href="${row.detail_url}" class="btn btn-primary btn-sm" title="Xem chi tiết">
                                <i class="bx bx-show"></i>
                            </a>
                            <a href="${row.edit_url}" class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                <i class="bx bx-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm" title="Xoá" onclick="đeleteData([${row.id}], 'Ticket')">
                                <i class="bx bx-trash"></i>
                            </button>
                            `;
                        }
                    }, {
                        data: 'title',
                        name: 'title',
                        render: function(data, type, row) {
                            return `<a href="${row.show_url}">${data}</a>`;
                        }
                    },
                    {
                        data: 'user',
                        name: 'user',
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary-gradient">${data.username}</span>`;
                        }
                    }, {
                        data: 'type',
                        name: 'type',
                        render: function(data, type, row) {
                            if (data == 'refill') {
                                return `<span class="badge bg-warning-gradient">Bảo hành</span>`;
                            } else if (data == 'cancel') {
                                return `<span class="badge bg-danger-gradient">Huỷ đơn</span>`;
                            } else {
                                return `<span class="badge bg-info-gradient">Khác</span>`;
                            }
                        }
                    },
                    {
                        data: 'order_id',
                        name: 'order_id',
                        render: function(data, type, row) {
                            return data ? `${data}` : 'N/A';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data == 'pending') {
                                return `<span class="badge bg-warning-gradient">Chờ xử lý</span>`;
                            } else if (data == 'processing') {
                                return `<span class="badge bg-info-gradient">Đang xử lý</span>`;
                            } else if (data == 'completed') {
                                return `<span class="badge bg-success-gradient">Đã hoàn thành</span>`;
                            } else {
                                return `<span class="badge bg-danger-gradient">Đã đóng</span>`;
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
            });

            // Auto-reload functionality
            let autoReloadInterval;
            let countdownInterval;
            let currentSeconds = 1; // Default 1 second
            let countdown = 1;
            let isAutoLoadEnabled = true;

            function updateCountdown() {
                $('#countdownBadge').text(countdown);
            }

            function startAutoReload() {
                clearInterval(autoReloadInterval);
                clearInterval(countdownInterval);
                
                if (!isAutoLoadEnabled || currentSeconds === 0) {
                    $('#countdownBadge').text('--');
                    return;
                }

                // Reset countdown
                countdown = currentSeconds;
                updateCountdown();

                // Countdown timer (updates every second)
                countdownInterval = setInterval(function() {
                    countdown--;
                    if (countdown <= 0) {
                        countdown = currentSeconds;
                    }
                    updateCountdown();
                }, 1000);

                // Auto reload
                autoReloadInterval = setInterval(function() {
                    table.ajax.reload(null, false);
                    countdown = currentSeconds; // Reset countdown after reload
                }, currentSeconds * 1000);
            }

            function stopAutoReload() {
                clearInterval(autoReloadInterval);
                clearInterval(countdownInterval);
                $('#countdownBadge').text('--');
            }

            // Handle toggle switch
            $('#autoLoadToggle').on('change', function() {
                isAutoLoadEnabled = $(this).is(':checked');
                if (isAutoLoadEnabled) {
                    startAutoReload();
                } else {
                    stopAutoReload();
                }
            });

            // Handle dropdown interval selection
            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                const seconds = parseInt($(this).data('seconds'));
                const text = $(this).text();
                
                currentSeconds = seconds;
                $('#intervalText').text(text);
                
                if (seconds === 0) {
                    // Turn off auto-load when "Tắt" is selected
                    $('#autoLoadToggle').prop('checked', false);
                    isAutoLoadEnabled = false;
                    stopAutoReload();
                } else {
                    // Restart with new interval
                    if (isAutoLoadEnabled) {
                        startAutoReload();
                    }
                }
            });

            // Start auto-reload by default
            startAutoReload();
        })
    </script>
@endsection
