(function initAdminPanel() {
    if (typeof jQuery === 'undefined') {
        setTimeout(initAdminPanel, 50);
        return;
    }

    const $ = jQuery;
    const config = document.getElementById('child-panel-config');
    const baseUrl = config?.dataset.baseUrl || '/admin/child-panel';
    const settingsUrl = config?.dataset.settingsUrl || (baseUrl + '/settings');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    function formatNumber(num) {
        return new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(Math.round(num || 0));
    }

    function formatVND(num) {
        return formatNumber(num) + '₫';
    }

    // Toggle password visibility
    $(document).on('click', '[data-action="toggle-password"]', function () {
        const targetId = $(this).data('target');
        const input = document.getElementById(targetId);
        if (!input) return;
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('ti-eye', 'ti-eye-off');
        } else {
            input.type = 'password';
            icon.classList.replace('ti-eye-off', 'ti-eye');
        }
    });

    // Save settings
    const settingsForm = document.getElementById('settingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const enabledCheckbox = document.getElementById('child_panel_enabled');
            formData.append('child_panel_enabled', (enabledCheckbox && enabledCheckbox.checked) ? '1' : '0');

            $.ajax({
                url: settingsUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.fire({ icon: 'success', title: 'Thành công', text: response.message });
                },
                error: function (xhr) {
                    Swal.fire({ icon: 'error', title: 'Lỗi', text: xhr.responseJSON?.message || 'Có lỗi xảy ra' });
                }
            });
        });
    }

    // Update status (event delegation on anchors with data-action="update-status")
    $(document).on('click', '[data-action="update-status"]', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');

        Swal.fire({
            title: 'Xác nhận?', text: 'Thay đổi trạng thái cho Child Panel này.',
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Đồng ý', cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseUrl + '/' + id + '/update-status',
                    method: 'POST',
                    data: { status, _token: csrfToken },
                    success: function (response) {
                        Swal.fire('Thành công', response.message, 'success').then(() => location.reload());
                    },
                    error: function (xhr) {
                        Swal.fire('Lỗi', xhr.responseJSON?.message || 'Có lỗi xảy ra', 'error');
                    }
                });
            }
        });
    });

    // Delete panel
    $(document).on('click', '[data-action="delete-panel"]', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const domain = $(this).data('domain');

        Swal.fire({
            title: 'Xóa sạch Child Panel?',
            html: `<div class="text-start">
                <p class="mb-2">Bạn đang xóa <strong>${domain}</strong>. Hành động này sẽ:</p>
                <ul class="small text-muted">
                    <li>Xóa Zone trên <strong>Cloudflare</strong></li>
                    <li>Xóa domain trên <strong>cPanel</strong></li>
                    <li>Xóa tất cả <strong>Users</strong> của site con</li>
                    <li>Xóa bản ghi <strong>Site</strong> trong database</li>
                    <li>Xóa bản ghi <strong>Child Panel</strong></li>
                </ul>
                <div class="alert alert-danger py-2 small mt-2 mb-0">
                    <i class="ti ti-alert-triangle me-1"></i>Không thể hoàn tác!
                </div>
            </div>`,
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Xóa vĩnh viễn', cancelButtonText: 'Hủy',
            confirmButtonColor: '#d33', showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: baseUrl + '/' + id,
                    method: 'DELETE',
                    data: { _token: csrfToken }
                }).then(response => response)
                  .catch(xhr => { Swal.showValidationMessage(xhr.responseJSON?.message || 'Có lỗi xảy ra'); });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const r = result.value;
                let icon = 'success', title = 'Đã xóa!', html = r.message;
                if (r.warnings && r.warnings.length > 0) {
                    icon = 'warning';
                    title = 'Đã xóa (có cảnh báo)';
                    html += '<br><br><small class="text-muted">Cảnh báo:<br>' + r.warnings.join('<br>') + '</small>';
                }
                Swal.fire({ icon, title, html }).then(() => location.reload());
            }
        });
    });

    // Show statistics modal
    $(document).on('click', '[data-action="show-statistics"]', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('statisticsModal'));
        document.getElementById('statsLoading').style.display = 'block';
        document.getElementById('statsContent').style.display = 'none';
        modal.show();

        $.ajax({
            url: baseUrl + '/' + id + '/statistics',
            method: 'GET',
            success: function (response) {
                if (response.status === 'success') populateStats(response.data);
            },
            error: function () {
                document.getElementById('statsLoading').innerHTML =
                    '<div class="text-center py-4"><i class="ti ti-alert-triangle f-40 text-danger"></i><p class="text-muted mt-2">Không thể tải thống kê</p></div>';
            }
        });
    });

    function populateStats(d) {
        document.getElementById('statsDomain').textContent = d.domain;
        const statusEl = document.getElementById('statsStatus');
        statusEl.textContent = d.status;
        statusEl.className = 'badge bg-light-' + d.status_color + ' text-' + d.status_color;
        document.getElementById('statsOwner').textContent = d.owner;
        document.getElementById('statsAdmin').textContent = d.admin_username;
        document.getElementById('statsEmail').textContent = d.admin_email;
        document.getElementById('statsPrice').textContent = formatVND(d.monthly_price) + '/tháng';
        document.getElementById('statsCreated').textContent = d.created_at;
        document.getElementById('statsExpires').textContent = d.expires_at;
        const daysEl = document.getElementById('statsDays');
        daysEl.textContent = (d.days_remaining ?? 0) + ' ngày';
        daysEl.className = 'badge ' + ((d.days_remaining ?? 0) > 7 ? 'bg-light-success text-success' : 'bg-light-danger text-danger');
        const cfEl = document.getElementById('statsCF');
        const cfStatus = d.cloudflare_status || 'unknown';
        cfEl.textContent = cfStatus;
        cfEl.className = 'badge ' + (cfStatus === 'active' ? 'bg-light-success text-success' : 'bg-light-warning text-warning');
        document.getElementById('statsNS').innerHTML = [d.nameserver1, d.nameserver2].filter(Boolean).join('<br>') || 'N/A';
        document.getElementById('statsOrdersTotal').textContent = formatNumber(d.orders.total);
        document.getElementById('statsDeposits').textContent = formatVND(d.finance.total_deposits);
        document.getElementById('statsSpent').textContent = formatVND(d.finance.total_spent);
        document.getElementById('statsUsersTotal').textContent = formatNumber(d.users.total);
        document.getElementById('statsOrdersCompleted').textContent = formatNumber(d.orders.completed);
        document.getElementById('statsOrdersPending').textContent = formatNumber(d.orders.pending);
        document.getElementById('statsOrdersCancelled').textContent = formatNumber(d.orders.cancelled);
        document.getElementById('statsOrdersRecent').textContent = formatNumber(d.orders.recent_7d);
        document.getElementById('statsPendingDeposits').textContent = formatVND(d.finance.pending_deposits);
        document.getElementById('statsTotalBalance').textContent = formatVND(d.finance.total_balance);
        document.getElementById('statsRecentRevenue').textContent = formatVND(d.finance.recent_revenue_7d);
        const profitEl = document.getElementById('statsProfit');
        const profit = d.finance.profit_estimate || 0;
        profitEl.textContent = formatVND(profit);
        profitEl.className = 'fw-bold small ' + (profit >= 0 ? 'text-success' : 'text-danger');
        document.getElementById('statsUsersAdmin').textContent = formatNumber(d.users.admins);
        document.getElementById('statsUsersClient').textContent = formatNumber(d.users.clients);
        document.getElementById('statsLoading').style.display = 'none';
        document.getElementById('statsContent').style.display = 'block';
    }
})();
