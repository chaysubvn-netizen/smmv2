@extends('admin.layouts.app')
@section('title', 'Cấu hình chung')
@section('content')
    <div class="row">
        <div class="col-12 col-lg-3">
            <nav class="nav nav-pills flex-column" role="tablist">
                <a class="nav-link active mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#home-vertical-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fas fa-cog me-2 align-middle d-inline-block"></i>Cấu hình chung
                </a>
                <a class="nav-link mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#config-google-oauth-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fab fa-google me-2 align-middle d-inline-block"></i>Google OAuth Login
                </a>
                <a class="nav-link mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#config-level-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fas fa-percentage me-2 align-middle d-inline-block"></i>Cấu hình giảm giá cấp bậc
                </a>
                {{-- mốc lên cấp --}}
                <a class="nav-link mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#config-level-up-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fas fa-level-up-alt me-2 align-middle d-inline-block"></i>Cấu hình mốc lên cấp
                </a>
                {{-- Nạp tiền --}}
                <a class="nav-link mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#config-recharge-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fas fa-wallet me-2 align-middle d-inline-block"></i>Cấu hình nạp tiền
                </a>
                {{-- Tiếp thị liên kết --}}
                <a class="nav-link mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#config-affiliate-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fas fa-users me-2 align-middle d-inline-block"></i>Cấu hình tiếp thị liên kết
                </a>
                {{-- script --}}
                <a class="nav-link mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#config-script-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fas fa-code me-2 align-middle d-inline-block"></i>Cấu hình script
                </a>
                {{-- Theme Customizer --}}
                <a class="nav-link mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#config-theme-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fas fa-paint-brush me-2 align-middle d-inline-block"></i>Cấu hình giao diện
                </a>
                {{-- Anti DDoS --}}
                <a class="nav-link mb-2" data-bs-toggle="pill" role="tab" aria-current="page" href="#config-antiddos-link"
                    aria-selected="false" tabindex="-1">
                    <i class="fas fa-shield-alt me-2 align-middle d-inline-block"></i>Cấu hình Anti-DDoS
                </a>
            </nav>
        </div>
        <div class="col-12 col-lg-9">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="home-vertical-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình chung</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.general-config')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-google-oauth-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình Google OAuth Login</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.google-oauth-config')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-level-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình cấp bậc giảm giá</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.level-config')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-level-up-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình mốc lên cấp</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.level-up-config')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-recharge-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình nhạp tiền</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.recharge-config')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-affiliate-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình tiếp thị</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.affiliate-config')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-script-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình script</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.script-config')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-theme-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình giao diện</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.theme-config')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="config-antiddos-link" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h4 class="card-title">Cấu hình Anti-DDoS & Block IP</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.config.general.components.antiddos-config')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/monokai.min.css">
    <style>
        .CodeMirror {
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlmixed/htmlmixed.min.js"></script>
    <script>
        $(document).ready(function() {
            loadBlockedIps();

            $('#add-ip-form').on('submit', function(e) {
                e.preventDefault();
                const data = $(this).serialize();
                $.ajax({
                    url: '{{ route("admin.system.ip-block.store") }}',
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Thành công', res.message, 'success');
                            $('#addIpModal').modal('hide');
                            $('#add-ip-form').trigger('reset');
                            loadBlockedIps();
                        } else {
                            Swal.fire('Lỗi', res.message, 'error');
                        }
                    },
                    error: function(err) {
                        let msg = 'Đã có lỗi xảy ra';
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        Swal.fire('Lỗi', msg, 'error');
                    }
                });
            });
        });

        function loadBlockedIps() {
            $.ajax({
                url: '{{ route("admin.system.ip-block.index") }}',
                type: 'GET',
                success: function(res) {
                    if(res.success) {
                        const tbody = $('#ip-block-table tbody');
                        tbody.empty();
                        res.data.forEach(item => {
                            tbody.append(`
                                <tr>
                                    <td>${item.ip}</td>
                                    <td>${item.reason || ''}</td>
                                    <td>${new Date(item.created_at).toLocaleString()}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" onclick="deleteIp(${item.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                }
            });
        }

        function deleteIp(id) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "IP này sẽ được bỏ chặn!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/config/system/ip-block/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Đã xóa!', res.message, 'success');
                                loadBlockedIps();
                            } else {
                                Swal.fire('Lỗi', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Lỗi', 'Không thể xóa IP này', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
 