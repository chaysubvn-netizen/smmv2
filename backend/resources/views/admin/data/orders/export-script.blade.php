<script>
function exportOrders() {
    // Get current filter values
    let params = new URLSearchParams({
        export: 'excel',
        filter_username: $('#filter-username').val() || '',
        filter_order_id: $('#filter-order-id').val() || '',
        filter_service: $('#filter-service').val() || '',
        filter_status: $('#filter-status').val() || '',
        filter_date_from: $('#filter-date-from').val() || '',
        filter_date_to: $('#filter-date-to').val() || ''
    });

    // Show loading toast
    Swal.fire({
        title: 'Đang xuất dữ liệu...',
        text: 'Vui lòng đợi',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Open export URL in new window
    window.open('{{ route("admin.data.orders.export") }}?' + params.toString(), '_blank');
    
    // Close loading after 1 second
    setTimeout(() => {
        Swal.close();
    }, 1000);
}
</script>
