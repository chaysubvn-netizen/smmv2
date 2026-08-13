@extends('admin.layouts.app')
@section('title', 'Cập nhật hệ thống')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card custom-card">
            <div class="card-header">
                <h4 class="card-title text-primary"><i class="ti ti-history me-1"></i> Cập Nhật Hệ Thống</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-start" role="alert">
                    <i class="ti ti-info-circle me-2 fs-4"></i>
                    <div>
                        <h5 class="alert-heading fw-bold mb-1">Hướng dẫn cập nhật</h5>
                        <p class="mb-0">Hệ thống sẽ kiểm tra phiên bản mới từ máy chủ. Nếu có bản cập nhật, bạn có thể thực hiện tải xuống và cài đặt tự động. Vui lòng không tắt trình duyệt khi đang diễn ra quá trình cập nhật.</p>
                    </div>
                </div>

                <div class="row mt-4 mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light border-0 text-center p-4">
                            <p class="text-muted mb-1 text-uppercase fw-bold small">Phiên bản hiện tại</p>
                            <h2 class="fw-bold text-dark">v{{ $currentVersion }}</h2>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-primary text-white text-center p-4">
                            <p class="text-white-50 mb-1 text-uppercase fw-bold small">Phiên bản mới nhất</p>
                            <h2 class="fw-bold" id="latest-version-text">v{{ $currentVersion }}</h2>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-5">
                    <button type="button" class="btn btn-primary btn-lg" id="btn-check-update">
                        <i class="ti ti-search me-1"></i> KIỂM TRA PHIÊN BẢN
                    </button>
                    <button type="button" class="btn btn-success btn-lg d-none" id="btn-upgrade-now">
                        <i class="ti ti-download me-1"></i> CẬP NHẬT NGAY
                    </button>
                </div>

                <div class="mt-4">
                    <h5 class="fw-bold mb-3">Thông tin bổ sung</h5>
                    <ul class="list-group list-group-flush border-top">
                        <li class="list-group-item d-flex align-items-center bg-transparent py-3">
                            <i class="ti ti-check bg-success text-white rounded-circle p-1 me-3 fs-6"></i>
                            <span>Tự động sao lưu dữ liệu cũ (Khuyên dùng thực hiện thủ công trước khi chạy).</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center bg-transparent py-3">
                            <i class="ti ti-check bg-success text-white rounded-circle p-1 me-3 fs-6"></i>
                            <span>Khớp các Migration cơ sở dữ liệu mới nhất (Auto SQL Migration).</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center bg-transparent py-3">
                            <i class="ti ti-check bg-success text-white rounded-circle p-1 me-3 fs-6"></i>
                            <span>Xóa cache hệ thống sau khi hoàn tất.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateLogModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="ti ti-terminal me-1"></i> Progress Log</h5>
            </div>
            <div class="modal-body bg-dark text-light p-4">
                <div id="log-content" style="font-family: 'Courier New', Courier, monospace; height: 300px; overflow-y: auto;">
                    <div class="mb-1 text-info">> Khởi tạo trình cập nhật...</div>
                </div>
                <div class="progress mt-4" style="height: 10px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="update-progress"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        let updateData = null;

        $('#btn-check-update').on('click', function() {
            const btn = $(this);
            const originalHtml = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang kiểm tra...').attr('disabled', true);

            $.post("{{ route('admin.system.update.check') }}", {
                _token: "{{ csrf_token() }}"
            }, function(res) {
                console.log('Update check response:', res);
                btn.html(originalHtml).attr('disabled', false);
                
                if (res.ok || res.status === 'success') {
                    const version = res.latest_version || (res.data ? res.data.version : 'Unknown');
                    const hasUpdate = res.update_available || res.has_update;
                    const changelog = res.changelog || (res.data ? res.data.changelog : '');
                    
                    $('#latest-version-text').text('v' + version);
                    updateData = res.data || { version: version, download_url: res.url, changelog: changelog };

                    if (hasUpdate) {
                        Swal.fire({
                            title: 'Bản cập nhật mới!',
                            html: `Phát hiện phiên bản <b>v${version}</b>.<br><br><small class="text-muted">Changelog: ${changelog || 'Không có thông tin mô tả'}</small>`,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Cập nhật ngay',
                            cancelButtonText: 'Lần sau'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                startUpgrade();
                            }
                        });
                        $('#btn-upgrade-now').removeClass('d-none');
                    } else {
                        Swal.fire('Thông báo', 'Hệ thống đã ở phiên bản mới nhất.', 'success');
                    }
                } else {
                    Swal.fire('Lỗi', res.message || 'Lỗi không xác định', 'error');
                }
            }).fail(function() {
                btn.html(originalHtml).attr('disabled', false);
                Swal.fire('Lỗi', 'Không thể kết nối với máy chủ.', 'error');
            });
        });

        $('#btn-upgrade-now').on('click', function() {
            startUpgrade();
        });

        function startUpgrade() {
            if (!updateData) return;

            $('#updateLogModal').modal('show');
            addLog('Đang tải bản cập nhật...', 'text-info');
            setProgress(20);

            $.post("{{ route('admin.system.update.upgrade') }}", {
                _token: "{{ csrf_token() }}",
                url: updateData.download_url,
                version: updateData.version
            }, function(res) {
                if (res.status === 'success') {
                    setProgress(100);
                    addLog('Cập nhật mã nguồn thành công!', 'text-success');
                    addLog('Xoá cache thành công!', 'text-success');
                    addLog('Hệ thống sẽ tải lại sau 3 giây...', 'text-white');
                    
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                } else {
                    addLog('LỖI: ' + res.message, 'text-danger');
                    Swal.fire('Lỗi cập nhật', res.message, 'error');
                    $('#updateLogModal').find('.modal-header').removeClass('bg-dark').addClass('bg-danger');
                    $('.progress-bar').removeClass('bg-primary').addClass('bg-danger');
                }
            }).fail(function() {
                addLog('LỖI: Mất kết nối server.', 'text-danger');
                Swal.fire('Lỗi', 'Mất kết nối server trong quá trình cập nhật.', 'error');
            });
        }

        function addLog(msg, colorClass = '') {
            const time = new Date().toLocaleTimeString();
            $('#log-content').append(`<div class="mb-1 ${colorClass}">[${time}] > ${msg}</div>`);
            $('#log-content').scrollTop($('#log-content')[0].scrollHeight);
        }

        function setProgress(percent) {
            $('#update-progress').css('width', percent + '%');
        }
    });
</script>
@endsection
