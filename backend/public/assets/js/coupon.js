// ===== COUPON CODE VALIDATION =====
// This file handles coupon/discount code validation on the order page

$(document).ready(function () {
    // Reset discount state
    window.currentCouponCode = null;
    window.currentDiscountAmount = 0;

    // Apply coupon button click handler
    $("#btn-apply-coupon").on('click', function () {
        let code = $("#coupon_code").val().trim();

        if (!code) {
            $("#discount-error").html('<i class="fas fa-exclamation-circle me-1"></i> Vui lòng nhập mã giảm giá');
            $("#discount-message").text('');
            return;
        }

        $("#discount-error").text('');
        $("#discount-message").text('');

        let btn = $(this);
        let originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        // Show loading message
        $("#discount-message")
            .text('Đang kiểm tra mã giảm giá...')
            .removeClass('text-success')
            .addClass('text-info');

        // Get current service and calculate amount
        let service = $("#sl-services").select2("data")[0];
        if (!service) {
            $("#discount-error").html('<i class="fas fa-exclamation-circle me-1"></i> Vui lòng chọn dịch vụ trước');
            btn.prop('disabled', false).html(originalText);
            $("#discount-message").text('');
            return;
        }

        let rate = service.rate;
        let per = service.per || 1000;
        let quantity = parseFloat($("#quantity").val()) || 0;

        if (service.type == "Custom Comments") {
            let comments = $("#comments").val();
            let commentsArray = comments.split("\n").filter(c => c.trim() !== "");
            quantity = commentsArray.length;
        }

        let amount = (rate * quantity) / per;

        $.ajax({
            url: "/api/check-discount",
            type: "POST",
            timeout: 10000, // 10 seconds timeout
            data: {
                coupon_code: code,
                code: code,
                amount: amount
            },
            success: function (res) {
                if (res.success) {
                    window.currentCouponCode = res.code;
                    window.currentDiscountAmount = parseFloat(res.discount);

                    // Show success message with green color
                    $("#discount-message")
                        .removeClass('text-info')
                        .addClass('text-success')
                        .html('<i class="fas fa-check-circle me-1"></i>' + res.message + " (-" + convertCurrency(parseFloat(res.discount)) + ")");

                    $("#discount-error").text('');

                    // Update Total Display
                    let newTotal = amount - parseFloat(res.discount);
                    if (newTotal < 0) newTotal = 0;

                    $("#payment").text(convertCurrency(newTotal));

                    // Show success notification
                    if (typeof notify !== 'undefined') {
                        notify('Áp dụng mã giảm giá thành công!', 'success');
                    }
                } else {
                    $("#discount-message").text('');
                    $("#discount-error").html('<i class="fas fa-times-circle me-1"></i>' + res.message);
                    resetCouponDiscount();
                }
            },
            error: function (xhr, status, error) {
                $("#discount-message").text('');

                let msg = '';
                if (status === 'timeout') {
                    msg = '<i class="fas fa-clock me-1"></i> Yêu cầu quá lâu! Vui lòng thử lại.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = '<i class="fas fa-exclamation-triangle me-1"></i> ' + xhr.responseJSON.message;
                } else if (xhr.status === 0) {
                    msg = '<i class="fas fa-wifi me-1"></i> Không có kết nối mạng!';
                } else {
                    msg = '<i class="fas fa-exclamation-circle me-1"></i> Có lỗi xảy ra! Vui lòng thử lại.';
                }

                $("#discount-error").html(msg);
                resetCouponDiscount();

                if (typeof notify !== 'undefined') {
                    notify('Không thể áp dụng mã giảm giá', 'error');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Reset discount function
    function resetCouponDiscount() {
        window.currentCouponCode = null;
        window.currentDiscountAmount = 0;
        $("#coupon_code").val('');
    }

    // When service or quantity changes, recalculate if discount is applied
    $("#sl-services, #quantity, #comments").on('change input', function () {
        if (window.currentCouponCode && window.currentDiscountAmount > 0) {
            // Re-trigger coupon validation
            $("#btn-apply-coupon").trigger('click');
        }
    });
});
