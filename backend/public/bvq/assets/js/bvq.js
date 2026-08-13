$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).on("submit", 'form[data-ajax="true"]', function (e) {
    e.preventDefault();
    let form = $(this);

    let url = form.attr("action");
    let method = form.attr("method") || "GET";
    let data = form.serialize();
    let formData = new FormData(this);

    let btn = form.find("button[type=submit]");
    let btnText = btn.text();

    $.ajax({
        url: url,
        method: method,
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            btn.attr("disabled", true).html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...`
            );
        },
        complete: function () {
            btn.attr("disabled", false).html(btnText);
        },
        success: function (response) {
            if (response.success) {
                if (response.redirect) {
                    Swal.fire({
                        icon: "success",
                        title: "Thành công!",
                        text: response.message,
                        confirmButtonText: 'Tôi Đã Hiểu',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: "success",
                        title: "Thành công!",
                        text: response.message,
                        confirmButtonText: 'Tôi Đã Hiểu',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        window.location.reload();
                    });
                }
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Có lỗi!",
                    text: response.message,
                    confirmButtonText: 'Tôi Đã Hiểu',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            let response = jqXHR.responseJSON;
            if (response && response.message) {
                Swal.fire({
                    icon: "error",
                    title: "Có lỗi!",
                    text: response.message,
                    confirmButtonText: 'Tôi Đã Hiểu',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Có lỗi!",
                    text: "Đã xảy ra lỗi không xác định. Vui lòng thử lại sau.",
                    confirmButtonText: 'Tôi Đã Hiểu',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        },
    });
});

$('select[data-select="select2"]').select2({
    width: "100%",
    placeholder: $(this).data("placeholder"),
});

function levelUser(level, html = false) {
    let levelList = {
        member: ["Thành viên", "bg-primary"],
        silver: ["Bạc", "bg-secondary"],
        gold: ["Vàng", "bg-warning"],
        platinum: ["Bạch kim", "bg-info"],
        diamond: ["Kim cương", "bg-success"],
    };

    if (levelList[level]) {
        if (html) {
            return `<span class="badge ${levelList[level][1]}">${levelList[level][0]}</span>`;
        }
        return levelList[level][0];
    }
    return levelList.member[0];
}

function statusUser(status, html = false) {
    let statusList = {
        active: ["Hoạt động", "bg-success"],
        inactive: ["Ngừng hoạt động", "bg-danger"],
        pending: ["Đang chờ duyệt", "bg-warning"],
        banned: ["Bị cấm", "bg-secondary"],
    };
    if (statusList[status]) {
        if (html) {
            return `<span class="badge ${statusList[status][1]}">${statusList[status][0]}</span>`;
        }
        return statusList[status][0];
    }
    return statusList.active[0];
}

function deleteData(ids = [], model, table = null) {
    Swal.fire({
        title: "Bạn có chắc chắn?",
        text: "Bạn sẽ không thể khôi phục lại dữ liệu này!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Có, xóa nó!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/delete",
                method: "POST",
                data: {
                    ids: ids,
                    model: model,
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Đã xóa!",
                            text: response.message,
                            confirmButtonText: 'Tôi Đã Hiểu',
                        }).then((result) => {
                            if (table) {
                                table.ajax.reload();
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Có lỗi!",
                            text: response.message,
                            confirmButtonText: 'Tôi Đã Hiểu',
                        });
                    }
                },
            });
        }
    });
}

function formatCurrency(value) {
    if (value === null || value === undefined || value === "") {
        return "0";
    }
    value = parseFloat(value);
    if (isNaN(value)) {
        return "0";
    }
    return value.toLocaleString("vi-VN", {
        style: "currency",
        currency: "VND",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
        useGrouping: true,
    });
}

function formatNumber(value) {
    if (value === null || value === undefined || value === "") {
        return "0";
    }
    value = parseFloat(value);
    if (isNaN(value)) {
        return "0";
    }
    return value.toLocaleString("vi-VN", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
        useGrouping: true,
    });
}

function statusOrder(status, html = false) {
    let statusList = {
        Pending: ["Đang chờ", "bg-warning"],
        Processing: ["Đang xử lý", "bg-primary"],
        Active: ["Đang hoạt động", "bg-success"],
        "In progress": ["Đang chạy", "bg-info"],
        Completed: ["Hoàn thành", "bg-success"],
        Partial: ["Hoàn tiền một phần", "bg-danger"],
        Canceled: ["Huỷ", "bg-danger"],
    };
    if (statusList[status]) {
        if (html) {
            return `<span class="badge ${statusList[status][1]}">${statusList[status][0]}</span>`;
        }
        return statusList[status][0];
    }
    return status;
}

function convertCurrency(amount, from, to) {
    // Kiểm tra xem các loại tiền tệ có tồn tại không
    if (!currencies[from] || !currencies[to]) {
        throw new Error("Loại tiền tệ không hợp lệ");
    }

    // Lấy tỷ giá hối đoái
    const fromRate = parseFloat(currencies[from].exchange_rate);
    const toRate = parseFloat(currencies[to].exchange_rate);

    // Chuyển đổi tiền tệ
    return (amount / fromRate) * toRate;
}

function formatCurrency1(amount, currencyCode) {
    if (!currencies[currencyCode]) {
        throw new Error("Loại tiền tệ không hợp lệ");
    }

    const currency = currencies[currencyCode];
    const decimalPlaces = currency.decimal_places;

    // Làm tròn số
    const roundedAmount = parseFloat(amount.toFixed(decimalPlaces));

    // Định dạng phần thập phân và phần nghìn
    const thousandSep = currency.thousand_separator === "dot" ? "." : ",";
    const decimalSep = currency.decimal_separator === "comma" ? "," : ".";

    // Định dạng số
    const formattedNumber = roundedAmount
        .toLocaleString(undefined, {
            minimumFractionDigits: decimalPlaces,
            maximumFractionDigits: decimalPlaces,
        })
        .replace(/\./g, "THOUSAND")
        .replace(/,/g, "DECIMAL")
        .replace(/THOUSAND/g, thousandSep)
        .replace(/DECIMAL/g, decimalSep);

    // Thêm ký hiệu tiền tệ
    if (currency.position === "left") {
        return `${currency.symbol}${formattedNumber}`;
    } else {
        return `${formattedNumber}${currency.symbol}`;
    }
}

function convertAndFormatCurrency(amount, from = "USD", to = "VND") {
    const convertedAmount = convertCurrency(amount, from, to);
    return formatCurrency1(convertedAmount, to);
}

function removeCache() {
    Swal.fire({
        title: "Bạn có chắc chắn?",
        text: "Bạn sẽ không thể khôi phục lại dữ liệu này!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Có, xóa nó!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/remove-cache",
                method: "POST",
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Đã xóa!",
                            text: response.message,
                            confirmButtonText: 'Tôi Đã Hiểu',
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Có lỗi!",
                            text: response.message,
                            confirmButtonText: 'Tôi Đã Hiểu',
                        });
                    }
                },
            });
        }
    });
}
