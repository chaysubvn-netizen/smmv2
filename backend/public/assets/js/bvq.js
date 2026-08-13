// CMSbvq - CMS Laravel Bootstrap
// Version: 1.0.0

// Metronic Select2 Helper Function
function initMetronicSelect2(selector, options = {}) {
    const defaultOptions = {
        dropdownParent: $('body'),
        width: '100%',
        allowClear: false,
        escapeMarkup: function (markup) {
            return markup;
        },
        // Fix dropdown wrapper issues
        dropdownCssClass: 'select2-dropdown-metronic',
        containerCssClass: 'select2-container-metronic'
    };

    const mergedOptions = $.extend({}, defaultOptions, options);
    $(selector).select2(mergedOptions);

    // Ensure proper z-index for dropdown
    $(selector).on('select2:open', function() {
        $('.select2-dropdown').css('z-index', 9999);
    });
}

// ajax setup
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": bvq_config.csrf_token,
        "Api-Key": bvq_config.api_key,
    },
});

const notyf = new Notyf({
    position: {
        x: "right",
        y: "top",
    },
});
// /INIT
function getPlatform() {
    let platforms = [];
    $("#sl-platforms").empty();
    ONLOAD_FETURES.forEach(function (item) {
        platforms.push({
            id: item.id,
            text: item.name,
            icon: item.icon,
            min: item.min,
            max: item.max,
            name: item.name,
            rate: item.rate,
            refill: item.refill,
            cancel: item.cancel,
            warranty: item.warranty,
            average_time: item.average_time,
            description: item.description,
            attributes: item.attributes,
        });
    });
    return platforms;
}

function getCategories(platform) {
    let categories = [];
    $("#sl-categories").empty();
    ONLOAD_FETURES.forEach(function (item) {
        if (item.id == platform) {
            item.categories.forEach(function (category) {
                categories.push({
                    id: category.id,
                    text: category.name,
                    icon: category.icon,
                });
            });
        }
    });
    return categories;
}

function getServices(platform, category) {
    let services = [];
    $("#sl-services").empty();
    ONLOAD_FETURES.forEach(function (item) {
        if (item.id == platform) {
            item.categories.forEach(function (cat) {
                if (cat.id == category) {
                    cat.services.forEach(function (service) {
                        services.push({
                            id: service.id,
                            text: service.name,
                            icon: cat.icon,
                            rate: service.rate,
                            platform: item.id,
                            category: cat.id,
                            service: service.name,
                            min: service.min,
                            max: service.max,
                            refill: service.refill,
                            cancel: service.cancel,
                            dripfeed: service.dripfeed,
                            warranty: service.warranty,
                            average_time: service.average_time,
                            description: service.description,
                            attributes: service.attributes,
                            type: service.type,
                            sold: service.sold,
                            per: service.per,
                            rate_format: service.rate_format,
                        });
                    });
                }
            });
        }
    });
    return services;
}

function quickSearchService() {
    let services = [];
    $("#sl-quick-search").empty();
    ONLOAD_FETURES.forEach(function (item) {
        if (item.categories) {
            item.categories.forEach(function (cat) {
                if (cat.services) {
                    cat.services.forEach(function (service) {
                        services.push({
                            id: service.id,
                            text: service.name,
                            icon: item.image || item.icon, // Support both
                            rate: service.rate,
                            platform: item.id,
                            category: cat.id,
                            service: service.name,
                            min: service.min,
                            max: service.max,
                            refill: service.refill,
                            cancel: service.cancel,
                            warranty: service.warranty,
                            average_time: service.average_time,
                            description: service.description,
                            attributes: service.attributes,
                            type: service.type,
                            sold: service.sold,
                            per: service.per,
                        });
                    });
                }
            });
        }
    });
    return services;
}

function templateQuickService(service) {
    var originalOption = service;
    if (!service || !service.id) {
        return service?.text || "";
    }

    let attributeHtml = "";
    if (service.attributes && service.attributes.length > 0) {
        service.attributes.forEach(function (attribute) {
            if (attributesCode[attribute]) {
                attributeHtml += `<span class="badge fw-semibold py-1 bg-${attributesCode[attribute][1]} me-1" style="font-size: 10px;">${attributesCode[attribute][0]}</span>`;
            }
        });
    }

    if (service.refill == false) {
        attributeHtml += `<span class="badge fw-semibold py-1 bg-danger text-white me-1" style="font-size: 10px;">${trans.no_warranty}</span>`;
    } else {
        attributeHtml += `<span class="badge fw-semibold py-1 bg-warning text-dark me-1" style="font-size: 10px;">${trans.warranty}</span>`;
    }

    var $option = $(
        `
        <div class="d-flex align-items-center justify-content-between w-100 py-2 ps-1">
            <div class="d-flex align-items-center flex-grow-1 me-3">
                <div class="flex-shrink-0 me-3 d-flex align-items-center justify-content-center" style="width: 24px;">
                    <img src="${service.icon}" alt="" width="24" height="24" style="object-fit: contain;">
                </div>
                <div class="d-flex flex-column justify-content-center flex-grow-1">
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <span class="badge badge-light-primary fw-bold me-1 py-1 px-2" style="font-size: 11px; line-height: 1;">Sv ${service.id}</span>
                            <span class="text-white fw-bold fs-7 me-1" style="white-space: normal !important; word-break: break-word !important; line-height: 1.2;">${service.text}</span>
                            <span class="badge badge-light-success text-success fw-bold py-1 px-2" style="font-size: 11px; line-height: 1;">
                                ${convertCurrency(originalOption.rate)}
                            </span>

                            <span class="badge fw-bold py-1 px-2 d-flex align-items-center" style="cursor: pointer; font-size: 11px; line-height: 1; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.2); color: #06b6d4;" onclick="event.stopPropagation(); showServiceStats(${service.id})">
                                <iconify-icon icon="solar:chart-square-bold" class="me-1" style="font-size: 12px;"></iconify-icon>
                                ${trans.view_details}
                            </span>
                            ${attributeHtml}
                        </div>
                </div>
            </div>
        </div>
    `
    );
    return $option;
}

function templatePlatform(platform) {
    var originalOption = platform;
    if (!platform || !platform.id) {
        return platform?.text || "";
    }

    var $option = $(
        `
    <div class="d-flex align-items-center ps-1">
        <img src="${platform.icon}" alt="" class="me-2" width="30" height="30">
        <span class="fw-bold text-gray-700">${platform.text}</span>
    </div>
    `
    );
    return $option;
}

function templateCategory(category) {
    var originalOption = category;
    if (!category || !category.id) {
        return category?.text || "";
    }

    var $option = $(
        `
    <div class="d-flex align-items-center ps-1">
        <img src="${category.icon}" alt="" class="me-2" width="30" height="30">
        <span class="fw-bold text-gray-700">${category.text}</span>
    </div>
    `
    );
    return $option;
}

function changeCurrency(code = "USD") {
    $.ajax({
        url: "/request-action",
        type: "POST",
        data: {
            action: "change-currency",
            code: code,
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                notify(response.message, "success");
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                notify(response.message, "error");
            }
        },
        error: function (xhr, status, error) {
            notify(
                "Đã xảy ra lỗi không xác định. Vui lòng thử lại sau.",
                "error"
            );
        },
    });
}

function changeLanguage(code = "en") {
    $.ajax({
        url: "/request-action",
        type: "POST",
        data: {
            action: "change-language",
            code: code,
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                notify(response.message, "success");
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                notify(response.message, "error");
            }
        },
        error: function (xhr, status, error) {
            notify(
                "Đã xảy ra lỗi không xác định. Vui lòng thử lại sau.",
                "error"
            );
        },
    });
}

function templateService(service) {
    var originalOption = service;
    if (!service || !service.id) {
        return service?.text || "";
    }

    let attributeHtml = "";

    if (service.attributes && service.attributes.length > 0) {
        service.attributes.forEach(function (attribute) {
            if (attributesCode[attribute]) {
                attributeHtml += `<span class="badge fw-semibold py-1 bg-${attributesCode[attribute][1]} me-1" style="font-size: 10px;">${attributesCode[attribute][0]}</span>`;
            }
        });
    }

    if (service.refill == false) {
        attributeHtml += `<span class="badge fw-semibold py-1 bg-danger text-white me-1" style="font-size: 10px;">${trans.no_warranty}</span>`;
    } else {
        attributeHtml += `<span class="badge fw-semibold py-1 bg-warning text-dark me-1" style="font-size: 10px;">${trans.warranty}</span>`;
    }

    var $state = `
        <div class="d-flex align-items-center justify-content-between w-100 py-2 ps-1">
            <div class="d-flex align-items-center flex-grow-1 me-3">
                <div class="flex-shrink-0 me-3 d-flex align-items-center justify-content-center" style="width: 30px;">
                    <img src="${service.icon}" alt="" width="30" height="30" style="object-fit: contain;">
                </div>
                <div class="d-flex flex-column justify-content-center flex-grow-1">
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <span class="badge badge-light-primary fw-bold me-1 py-1 px-2" style="font-size: 11px; line-height: 1;">Sv ${service.id}</span>
                            <span class="text-white fw-bold fs-7 me-1" style="white-space: normal !important; word-break: break-word !important; line-height: 1.2;">${service.text}</span>
                            <span class="badge badge-light-success text-success fw-bold py-1 px-2" style="font-size: 11px; line-height: 1;">
                                ${convertCurrency(originalOption.rate)}
                            </span>

                            <span class="badge fw-bold py-1 px-2 d-flex align-items-center" style="cursor: pointer; font-size: 11px; line-height: 1; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.2); color: #06b6d4;" onclick="event.stopPropagation(); showServiceStats(${service.id})">
                                <iconify-icon icon="solar:chart-square-bold" class="me-1" style="font-size: 12px;"></iconify-icon>
                                ${trans.view_details}
                            </span>
                            ${attributeHtml}
                        </div>
                </div>
            </div>
        </div>
    `;
    return $state;
}

function initQuickSearchServiceSelect2() {
    let data = quickSearchService();
    $("#sl-quick-search").val(null).trigger("change");
    $("#sl-quick-search").empty();

    // Destroy existing select2 if it exists
    if ($("#sl-quick-search").hasClass("select2-hidden-accessible")) {
        $("#sl-quick-search").select2('destroy');
    }

    // Initialize with Metronic select2
    initMetronicSelect2("#sl-quick-search", {
        placeholder: "-- Tìm và chọn dịch vụ nhanh --",
        templateResult: templateQuickService,
        templateSelection: templateQuickService,
        data: data,
        minimumResultsForSearch: 0,
        minimumInputLength: 2,
        language: {
            inputTooShort: function () {
                return "Gõ ít nhất 2 ký tự để tìm kiếm";
            },
            noResults: function () {
                return "Không tìm thấy dịch vụ nào.";
            },
            searching: function () {
                return "Đang tìm kiếm...";
            }
        },
        matcher: function (params, data) {
            if ($.trim(params.term) === "") {
                return data;
            }
            if (typeof data.text === "undefined") {
                return null;
            }
            let term = params.term.toLowerCase();
            let text = data.text.toLowerCase();
            let id = String(data.id || "").toLowerCase();
            if (text.indexOf(term) > -1 || id.indexOf(term) > -1) {
                return data;
            }
            if (data.children) {
                let filteredChildren = [];
                for (let i = 0; i < data.children.length; i++) {
                    let child = data.children[i];
                    if (child.text.toLowerCase().indexOf(term) > -1) {
                        filteredChildren.push(child);
                    }
                }
                if (filteredChildren.length > 0) {
                    return {
                        id: data.id,
                        text: data.text,
                        children: filteredChildren,
                    };
                }
            }
            return null;
        }
    });

    // Add placeholder to the search box when it opens
    $("#sl-quick-search").on('select2:open', function() {
        let searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) {
            searchField.placeholder = "Gõ tên/ID dịch vụ để tìm kiếm...";
        }
    });
}

function iniPlatformSelect2() {
    let data = getPlatform();

    // Destroy existing select2 if it exists
    if ($("#sl-platforms").hasClass("select2-hidden-accessible")) {
        $("#sl-platforms").select2('destroy');
    }

    initMetronicSelect2("#sl-platforms", {
        placeholder: "Nền tảng",
        data: data,
        templateResult: templatePlatform,
        templateSelection: templatePlatform,
        language: {
            noResults: function () {
                return "Không tìm thấy nền tảng nào.";
            }
        }
    });
    
    $("#sl-platforms").on('select2:open', function() {
        let searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) {
            searchField.placeholder = "Tìm tên nền tảng...";
        }
    });

    if (data.length == 0) {
        $("#sl-platforms").val(null).trigger("change");
        $("#sl-categories").val(null).trigger("change");
        $("#sl-services").val(null).trigger("change");
        updateInput(null);
        return;
    }

    initCategorySelect2(data[0].id);
}

function initCategorySelect2(id) {
    let data = getCategories(id);
    $("#sl-categories").empty();

    // Destroy existing select2 if it exists
    if ($("#sl-categories").hasClass("select2-hidden-accessible")) {
        $("#sl-categories").select2('destroy');
    }

    initMetronicSelect2("#sl-categories", {
        placeholder: "Phân loại",
        data: data,
        templateResult: templateCategory,
        templateSelection: templateCategory,
        language: {
            noResults: function () {
                return "Không tìm thấy phân loại nào.";
            }
        }
    });

    $("#sl-categories").on('select2:open', function() {
        let searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) {
            searchField.placeholder = "Tìm tên phân loại...";
        }
    });

    if (data.length == 0) {
        $("#sl-categories").val(null).trigger("change");
        $("#sl-services").val(null).trigger("change");
        updateInput(null);
        return;
    }
    $("#sl-categories").val(data[0].id).trigger("change");
    initServiceSelect2(id, data[0].id);
}

function initServiceSelect2(platform, category, service = null) {
    let services = getServices(platform, category);
    $("#sl-services").empty();

    // Destroy existing select2 if it exists
    if ($("#sl-services").hasClass("select2-hidden-accessible")) {
        $("#sl-services").select2('destroy');
    }

    initMetronicSelect2("#sl-services", {
        placeholder: "Dịch vụ",
        data: services,
        templateResult: templateService,
        templateSelection: templateService,
        matcher: function (params, data) {
            if ($.trim(params.term) === "") {
                return data;
            }
            if (typeof data.text === "undefined") {
                return null;
            }
            let term = params.term.toLowerCase();
            let text = data.text.toLowerCase();
            let id = String(data.id || "").toLowerCase();
            if (text.indexOf(term) > -1 || id.indexOf(term) > -1) {
                return data;
            }
            return null;
        },
        minimumResultsForSearch: 0,
        minimumInputLength: 0,
        language: {
            noResults: function () {
                return "Không tìm thấy dịch vụ nào.";
            }
        }
    });

    $("#sl-services").on('select2:open', function() {
        let searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) {
            searchField.placeholder = "Gõ tên/ID dịch vụ để tìm kiếm...";
        }
    });

    if (services.length === 0) {
        $("#sl-services").val(null).trigger("change");
        updateInput(null);
        return;
    }

    if (service) {
        let selectedService = services.find((s) => s.id == service);
        if (!selectedService) {
            selectedService = services[0];
        }
        $("#sl-services").val(selectedService.id).trigger("change");
        updateInput(selectedService);
    } else {
        console.log(services);
        $("#sl-services").val(services[0].id).trigger("change");
        updateInput(services[0]);
    }
}

function updateInput(service) {
    if (!service) {
        $("#service-seleted").text("");
        $("#name-service").text("Không có dịch vụ");
        $("#refill-mode").text("");
        $("#cancel-mode").text("");
        $("#is_dripfeed").hide();
        $("#average_time").text("");
        $("#description-service").html(`
            <div class="d-flex align-items-center justify-content-center py-4">
                <span class="text-muted">-- Chọn dịch vụ để xem lưu ý --</span>
            </div>
        `);
        $("#qt_min").text("0");
        $("#qt_max").text("0");
        $("#s_name").text("");
        if (typeof trans !== 'undefined') {
            $("#time-service").html(`<iconify-icon icon="solar:clock-circle-bold"></iconify-icon> ${trans.estimate || 'Ước tính'}: --`);
            $("#refund-service").html(`<iconify-icon icon="solar:round-transfer-horizontal-bold"></iconify-icon> ${trans.refund || 'Hoàn tiền'}: --`);
            $("#warranty-service").html(`<iconify-icon icon="solar:shield-check-bold"></iconify-icon> ${trans.warranty || 'Bảo hành'}: --`);
        } else {
            $("#time-service").html(`<iconify-icon icon="solar:clock-circle-bold"></iconify-icon> Ước tính: --`);
            $("#refund-service").html(`<iconify-icon icon="solar:round-transfer-horizontal-bold"></iconify-icon> Hoàn tiền: --`);
            $("#warranty-service").html(`<iconify-icon icon="solar:shield-check-bold"></iconify-icon> Bảo hành: --`);
        }
        $("#order-value").text(convertCurrency(0));
        $("#quantity_limit").text("Tối thiểu: 0 - Tối đa: 0");
        $("#payment").text(convertCurrency(0));
        changeInput("Default");
        idKeyInput("link,quantity");
        return;
    }
    $("#service-seleted").text(service.id);
    $("#name-service").text(service.text);
    $("#refill-mode").text(
        service.refill == true ? trans.warranty : trans.no_warranty
    );
    // if(service?.refill == true){
    // tìm nếu  refill trong attributes thì hiện với key và hiển thị một cái
    // let attributesRefill = service.attributes.find(
    //     (item) =>
    //         item == "refill" ||
    //         item == "refill_7_days" ||
    //         item == "refill_30_days" ||
    //         item == "refill_15_days" ||
    //         item == "refill_90_days" ||
    //         item == "refill_365_days" ||
    //         item == "refill_1_year" ||
    //         item == "refill_lifetime"
    // );
    // if (service?.refill == true) {
    //     $("#refill-mode").text("Có bảo hành");
    // } else {
    //     $("#refill-mode").text("Không bảo hành");
    // }
    // }
    $("#cancel-mode").text(service.cancel == true ? trans.refund + ": " + trans.yes : trans.refund + ": " + trans.no);
    if (service.dripfeed == true) {
        $("#is_dripfeed").show();
    } else {
        $("#is_dripfeed").hide();
    }
    console.log(service);
    if (service.average_time != null) {
        $("#average_time").text(service.average_time);
    } else {
        $("#average_time").text("NO DATA");
    }
    $("#description-service").html(service.description);
    $("#qt_min").text(formatNumber(service.min));
    $("#qt_max").text(formatNumber(service.max));

    $("#s_name").text(service.text);
    
    // Cập nhật các badge trạng thái (Premium UI)
    const timeVal = service.average_time || "00:00:00";
    $("#time-service").html(`<iconify-icon icon="solar:clock-circle-bold"></iconify-icon> ${trans.estimate}: ${timeVal}`);
    
    const refundVal = service.cancel == true ? trans.yes : trans.no;
    $("#refund-service").html(`<iconify-icon icon="solar:round-transfer-horizontal-bold"></iconify-icon> ${trans.refund}: ${refundVal}`);
    
    // Determine specific warranty text from attributes
    let warrantyVal = service.refill == true ? trans.yes : trans.no;
    if (service.attributes && service.attributes.length > 0) {
        const warrantyKeys = ["refill_7_days", "refill_15_days", "refill_30_days", "refill_60_days", "refill_90_days", "refill_365_days", "refill_1_year", "refill_lifetime"];
        const foundWarranty = service.attributes.find(attr => warrantyKeys.includes(attr));
        if (foundWarranty && attributesCode[foundWarranty]) {
            warrantyVal = attributesCode[foundWarranty][0];
        }
    }
    $("#warranty-service").html(`<iconify-icon icon="solar:shield-check-bold"></iconify-icon> ${trans.warranty}: ${warrantyVal}`);

    $("#order-value").text(convertCurrency(service.rate));
    $("#quantity_limit").text(`Tối thiểu: ${formatNumber(service.min)} - Tối đa: ${formatNumber(service.max)}`);

    $("#payment").text(convertCurrency(service.rate));
    changeInput(service.type);

    switch (service.type) {
        case "Default":
            idKeyInput("link,quantity");
            break;
        case "Package":
            idKeyInput("link");
            break;
        case "SEO":
            idKeyInput("link,quantity,keywords");
            break;
        case "Custom Comments":
            idKeyInput("link,quantity,comments");
            break;
        case "Mentions with Hashtags":
            idKeyInput("link,quantity,usernames,hastags");
            break;
        case "Mentions Custom List":
            idKeyInput("link,quantity,usernames");
            break;
        case "Mentions Hashtag":
            idKeyInput("link,quantity,hastag");
            break;
        case "Mentions User Followers":
            idKeyInput("link,quantity,username");
            break;
        case "Mentions Media Likers":
            idKeyInput("link,quantity,media");
            break;
        case "Custom Comments Package":
            idKeyInput("link,comments");
            break;
        case "Comment Likes":
            idKeyInput("link,quantity,username");
            break;
        case "Poll":
            idKeyInput("link,quantity,answer_number");
            break;
        case "Comment Replies":
            idKeyInput("link,quantity,username,comments");
            break;
        case "Invites from Groups":
            idKeyInput("link,quantity,groups");
            break;
        case "Subscriptions":
            idKeyInput("username,min,max,posts,old_posts,delay,expiry");
            break;
        default:
            idKeyInput("link,quantity");
            break;
    }
}

function changeInput(type) {
    if (type == "Custom Comments") {
        $("#quantity").prop("readonly", true);
    } else {
        $("#quantity").prop("readonly", false);
    }
}

function handleDataInput() {
    let serviceData = $("#sl-services").select2("data");
    if (!serviceData || serviceData.length === 0) return;
    let service = serviceData[0];

    let type = service.type;
    let rate = service.rate;
    let comments = $("#comments").val() || "";

    if (type == "Custom Comments") {
        let commentsArray = comments.split("\n").filter(comment => comment.trim() !== "");
        $("#quantity").val(commentsArray.length);
    }

    let quantity = $("#quantity").val() || 0;
    let mode = $('input[name="mode"]:checked').val();
    let linkMultiplier = 1;

    if (mode === 'many') {
        let links = $("#link_many").val();
        let linksArray = links.split("\n").filter(c => c.trim() !== "");
        linkMultiplier = linksArray.length || 1; 
        $("#line-count").text(linksArray.length); // Use the correct ID from HTML
    }

    let totalPayment = (rate * quantity * linkMultiplier) / (service.per || 1000);
    
    // Xử lý nạp lặp lại (Drip-feed)
    if ($("#is_loop").is(":checked")) {
        let runs = $("#runs").val() || 2;
        totalPayment = totalPayment * runs;
        $("#total-drip-quantity").text(quantity * runs);
    } else {
        $("#total-drip-quantity").text(quantity);
    }

    $("#payment").text(convertCurrency(totalPayment));
    $("#order-value").text(convertCurrency((rate * quantity) / (service.per || 1000)));
}

function idKeyInput(hiddenKeys = null) {
    let listKey = [
        "link",
        "quantity",
        "keywords",
        "comments",
        "usernames",
        "hastags",
        "hastag",
        "media",
        "username",
        "answer_number",
        "groups",
        "min",
        "max",
        "posts",
        "old_posts",
        "delay",
        "expiry",
        "runs",
        "interval",
        "country",
        "device",
        "type_of_traffic",
        "google_keyword",
        "referring_url",
    ];

    if (!hiddenKeys) {
        return;
    }

    hiddenKeys = hiddenKeys.split(",");
    hiddenKeys.forEach(function (key) {
        if (listKey.includes(key)) {
            $("#div-" + key).show();
        }
    });
    listKey.forEach(function (key) {
        if (!hiddenKeys.includes(key)) {
            $("#div-" + key).hide();
        }
    });
}

function formatNumber(value) {
    value = parseFloat(value);
    if (isNaN(value)) {
        return null;
    }
    return Intl.NumberFormat().format(value);
}

function convertCurrency(
    amountVND,
    targetCurrency = null,
    maxDecimalPlaces = 6
) {
    const currencyCode = targetCurrency || bvq_config.currency.code;
    const targetCurrencyInfo = bvq_config.currencies[currencyCode];
    const vndCurrencyInfo = bvq_config.currencies["VND"];

    if (!targetCurrencyInfo || !vndCurrencyInfo) {
        return amountVND.toString(); // Trả về nguyên gốc nếu không tìm thấy currency
    }

    // 1. CHUYỂN ĐỔI TIỀN TỆ (VND → targetCurrency)
    let convertedAmount = amountVND;
    if (currencyCode !== "VND") {
        convertedAmount = amountVND / parseFloat(vndCurrencyInfo.exchange_rate);
    }

    // 2. XỬ LÝ ĐỊNH DẠNG THEO CHUẨN VIỆT NAM
    if (currencyCode === "VND") {
        // Định dạng VND: 1.234.567đ (dấu . phân cách hàng nghìn, không có thập phân)
        const formattedNumber = amountVND.toLocaleString("vi-VN", {
            style: "decimal",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        });
        return `${formattedNumber}₫`;
        
    } else {
        // Định dạng tiền tệ khác (USD, EUR...): 1.234,56 USD
        const formattedNumber = convertedAmount.toLocaleString("vi-VN", {
            style: "decimal",
            minimumFractionDigits: 0,
            maximumFractionDigits: maxDecimalPlaces,
        });
        if (currencyCode === "USD") {
            return `$${formattedNumber}`;
        }
        return `${formattedNumber} ${currencyCode}`;
    }
}

function copyToClipboard(text) {
    let $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();

    notify("Đã sao chép vào clipboard", "success");
}

function actionOrder(ids = [], action = "") {
    if (ids.length == 0) {
        notify("Vui lòng chọn đơn hàng để thực hiện hành động.", "error");
        return;
    }
    let text = "action";
    if (action == "refill") {
        text = "Bạn có chắc chắn muốn bảo hành đơn hàng này?";
    }
    if (action == "cancel") {
        text = "Bạn có chắc chắn muốn gửi yêu cầu HỦY tới đơn hàng này?";
    }

    Swal.fire({
        title: "Xác nhận",
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Có",
        cancelButtonText: "Không",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/api/request-action",
                type: "POST",
                data: {
                    action: action,
                    ids: ids,
                },
                dataType: "json",
                beforeSend: function () {
                    Swal.fire({
                        title: "Đang xử lý...",
                        html: "Vui lòng đợi trong giây lát.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Thành công!",
                            text: response.message,
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Có lỗi!",
                            text: response.message,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Có lỗi!",
                        text:
                            xhr.responseJSON.message ||
                            "Đã xảy ra lỗi không xác định. Vui lòng thử lại sau.",
                    });
                },
            });
        }
    });
}

function notify(message, type = "success") {
    // Đảm bảo có container cho toast
    let $toastContainer = $('#fui-toast');
    if ($toastContainer.length === 0) {
        $('body').append('<div id="fui-toast"></div>');
        $toastContainer = $('#fui-toast');
    }

    // Định nghĩa icon cho từng loại thông báo
    const icons = {
        success: 'solar:check-circle-bold',
        error: 'solar:danger-bold',
        warning: 'solar:info-circle-bold',
        info: 'solar:bell-bold'
    };

    const icon = icons[type] || icons.info;

    // Tạo HTML cho toast
    const toastHtml = `
        <div class="fui-toast fui-toast-${type}">
            <div class="fui-toast-icon">
                <iconify-icon icon="${icon}"></iconify-icon>
            </div>
            <div class="fui-toast-content">
                <p class="fui-toast-msg">${message}</p>
            </div>
        </div>
    `;

    const $toast = $(toastHtml);
    $toastContainer.append($toast);

    // Tự động xóa toast sau 3 giây
    setTimeout(() => {
        $toast.fadeOut(500, function() {
            $(this).remove();
        });
    }, 3000);
}

$("form[bvq-request]").on("submit", function (e) {
    e.preventDefault();
    const form = $(this);
    const btn = form.find('button[type="submit"]');
    const btnHtml = btn.html();

    // --- LOGIC CHO ĐẶT HÀNG (Dịch vụ) ---
    if (form.attr('id') === 'order-form') {
        const mode = $('input[name="mode"]:checked').val();
        if (mode === 'many') {
            const links = $('#link_many').val().split('\n').filter(l => l.trim() !== "");
            if (links.length === 0) {
                notify("Vui lòng nhập ít nhất một liên kết", "error");
                return;
            }
        } else {
            if (!$('#link').val() || $('#link').val().trim() === '') {
                notify("Vui lòng nhập liên kết hoặc UID", "error");
                return;
            }
        }

        if (!$('#quantity').val() || $('#quantity').val() <= 0) {
            notify("Vui lòng nhập số lượng hợp lệ", "error");
            return;
        }
        
        // Populate Modal Data
        const serviceName = $('#sl-services').select2('data')[0]?.text || $('#name-service').text();
        const link = mode === 'many' ? `${$('#link_many').val().split('\n').filter(l => l.trim() !== "").length} links` : $('#link').val();
        const quantity = $('#quantity').val();
        const total = $('#payment').text();
        const isSchedule = $('#is_schedule').is(':checked') ? $('#schedule-time').val() : 'Không';

        $('#confirm-service-name').text(serviceName);
        $('#confirm-service-link').text(link);
        $('#confirm-service-quantity').text(quantity);
        $('#confirm-service-schedule').text(isSchedule);
        $('#confirm-service-value').text(total);
        $('#confirm-service-total').text(total);

        if (typeof openOrderConfirmModal === 'function') {
            openOrderConfirmModal();
        } else {
            $('#orderConfirmModal').modal('show');
        }
        return; // Dừng lại để đợi confirm từ modal
    }

    // --- LOGIC CHO CÁC FORM KHÁC (Nạp tiền, Đổi mật khẩu, v.v.) ---
    const url = form.attr("action");
    const method = form.attr("method") || "POST";
    const formData = new FormData(form[0]);

    $.ajax({
        url: url,
        method: method,
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'Api-Key': bvq_config.api_key
        },
        beforeSend: function () {
            btn.attr("disabled", true).html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...`
            );
        },
        complete: function () {
            btn.attr("disabled", false).html(btnHtml);
        },
        success: function (response) {
            if (response.success) {
                notify(response.message, "success");
                if (response.redirect) {
                    setTimeout(() => window.location.href = response.redirect, 1500);
                } else if (form.attr("reload") == "true") {
                    setTimeout(() => window.location.reload(), 1500);
                }
            } else {
                notify(response.message || "Đã xảy ra lỗi.", "error");
            }
        },
        error: function (xhr) {
            const res = xhr.responseJSON || {};
            notify(res.message || "Lỗi kết nối máy chủ.", "error");
        }
    });
});

function showServiceStats(id = null) {
    const serviceId = id || $("#sl-services").val();
    if (!serviceId) {
        notify("Vui lòng chọn dịch vụ để xem chi tiết", "warning");
        return;
    }

    // Show modal with loading state
    $('#stats-service-name').text("Đang tải...");
    $('#stats-status, #stats-cancel, #stats-refill, #stats-newest, #stats-highest, #stats-lowest').text("...");
    $('#serviceStatsModal').fadeIn(200).css('display', 'flex').addClass('active');

    $.ajax({
        url: `/service/stats/${serviceId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#stats-service-name').text(data.name);
                if (data.icon) {
                    $('#stats-service-icon').attr('src', data.icon).show();
                    $('#stats-service-default-icon').hide();
                } else {
                    $('#stats-service-icon').hide();
                    $('#stats-service-default-icon').show();
                }
                $('#stats-status').text(data.status);
                $('#stats-cancel').text(data.cancel);
                $('#stats-refill').text(data.refill);
                $('#stats-newest').text(data.newest);
                $('#stats-highest').text(data.highest);
                $('#stats-lowest').text(data.lowest);
            } else {
                notify(response.message, "error");
                closeServiceStatsModal();
            }
        },
        error: function() {
            notify("Không thể tải dữ liệu thống kê", "error");
            closeServiceStatsModal();
        }
    });
}

function closeServiceStatsModal() {
    $('#serviceStatsModal').removeClass('active').fadeOut(200);
}

// Final Confirmation Handler
$('#btn-confirm-order-final').on('click', async function() {
    const form = $("#order-form");
    const modal = $('#orderConfirmModal');
    const btn = $(this);
    const btnHtml = btn.html();

    const url = form.attr("action");
    const method = form.attr("method") || "POST";
    
    let mode = $('input[name="mode"]:checked').val();

    if (mode === "many") {
        let links = $("#link_many").val().split("\n").filter(l => l.trim() !== "");
        if (links.length === 0) {
            notify("Vui lòng nhập ít nhất một liên kết", "error");
            return;
        }

        btn.attr("disabled", true);
        $.blockUI({
            message: `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`,
            css: { border: "none", backgroundColor: "transparent", color: "#fff" },
            overlayCSS: { backgroundColor: "#000", opacity: 0.5, cursor: "wait" },
        });

        let successCount = 0;
        let errorCount = 0;

        for (let i = 0; i < links.length; i++) {
            let currentLink = links[i].trim();
            btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý ${i + 1}/${links.length}...`);

            let formData = new FormData(form[0]);
            formData.set('link', currentLink); 
            formData.delete('link_many'); 

            try {
                const response = await $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Api-Key': bvq_config.api_key
                    }
                });

                if (response.success) {
                    successCount++;
                } else {
                    errorCount++;
                }
            } catch (err) {
                errorCount++;
            }
        }

        $.unblockUI();
        btn.attr("disabled", false).html(btnHtml);
        
        if (typeof closeOrderConfirmModal === 'function') {
            closeOrderConfirmModal();
        } else {
            modal.modal('hide');
        }

        if (successCount > 0) {
            notify(`Đã tạo thành công ${successCount} đơn hàng!`, "success");
            form[0].reset();
            if (form.attr("reload") == "true") {
                setTimeout(() => window.location.reload(), 2000);
            }
        }
        if (errorCount > 0) {
            notify(`Có ${errorCount} đơn hàng gặp lỗi. Vui lòng kiểm tra lại.`, "error");
        }

    } else {
        // Single order mode (One)
        let formData = new FormData(form[0]);
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Api-Key': bvq_config.api_key
            },
            beforeSend: function () {
                btn.attr("disabled", true).html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...`
                );
            },
            complete: function () {
                btn.attr("disabled", false).html(btnHtml);
                if (typeof closeOrderConfirmModal === 'function') {
                    closeOrderConfirmModal();
                } else {
                    modal.modal('hide');
                }
            },
            success: function (response) {
                if (response.success) {
                    notify(response.message, "success");
                    form[0].reset();
                    if (response.redirect) {
                        setTimeout(() => { window.location.href = response.redirect; }, 2000);
                    } else if (form.attr("reload") == "true") {
                        setTimeout(() => { window.location.reload(); }, 2000);
                    }
                } else {
                    notify(response.message || "Đã xảy ra lỗi không xác định.", "error");
                }
            },
            error: function (jqXHR) {
                let response = jqXHR.responseJSON || {};
                notify(response.message || "Đã xảy ra lỗi không xác định.", "error");
            },
        });
    }
});

$(document).ready(function () {
    let url = window.location.href;
    if (url.includes("/new")) {
        initQuickSearchServiceSelect2();
        $("#sl-quick-search").val(null).trigger("change");
        iniPlatformSelect2();
        $("#sl-quick-search").on("select2:select", function (e) {
            $("#service-loader").css("display", "flex").hide().fadeIn(200);
            let data = e.params.data;
            let platform = data.platform;
            let category = data.category;
            let service = data.id;
            
            setTimeout(() => {
                try {
                    $("#sl-platforms").val(platform).trigger("change");
                    initCategorySelect2(platform);
                    $("#sl-categories").val(category).trigger("change");
                    initServiceSelect2(platform, category, service);
                    $("#sl-services").val(service).trigger("change");
                    updateInput(data);
                    $("#sl-quick-search").val(null).trigger("change");
                } finally {
                    $("#service-loader").fadeOut(200);
                }
            }, 500);
        });

        $("#sl-platforms").on("select2:select", function (e) {
            $("#service-loader").css("display", "flex").hide().fadeIn(200);
            let data = e.params.data;
            setTimeout(() => {
                try {
                    initCategorySelect2(data.id);
                } finally {
                    $("#service-loader").fadeOut(200);
                }
            }, 500);
        });
        $("#sl-categories").on("select2:select", function (e) {
            $("#service-loader").css("display", "flex").hide().fadeIn(200);
            let data = e.params.data;
            let platform = $("#sl-platforms").val();
            setTimeout(() => {
                try {
                    initServiceSelect2(platform, data.id);
                    $("#sl-services").trigger("change");
                } finally {
                    $("#service-loader").fadeOut(200);
                }
            }, 500);
        });

        $("#sl-services").on("select2:select", function (e) {
            $("#service-loader").css("display", "flex").hide().fadeIn(200);
            let data = e.params.data;
            setTimeout(() => {
                try {
                    updateInput(data);
                    handleDataInput(); // Calculate on service select
                } finally {
                    $("#service-loader").fadeOut(200);
                }
            }, 500);
        });
    }

    // Attach listeners for real-time calculation
    $(document).on("input", "#quantity, #link_many, #comments, #loop-quantity", function() {
        handleDataInput();
    });

    $(document).on("change", 'input[name="mode"], #loop', function() {
        handleDataInput();
    });

    $("#loop").change(function () {
        if ($(this).is(":checked")) {
            $("#loop-content").show();
        } else {
            $("#loop-content").hide();
        }
    });

    $("#is_schedule-time").change(function () {
        if ($(this).is(":checked")) {
            $("#is-schedule-time-content").show();
        } else {
            $("#is-schedule-time-content").hide();
        }
    });
});
