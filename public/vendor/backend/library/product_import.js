(function ($) {
    "use strict";
    var HT = {};
    var globalReadyData = [];
    var globalReviewData = [];
    var catalogueOptions = '';
    var _token = $('meta[name="csrf-token"]').attr('content');

    HT.importJson = () => {
        $('#import-json-btn').on('click', function () {
            $('#import-json-input').click();
        });

        $('#import-json-input').on('change', function (e) {
            let file = e.target.files[0];
            if (!file) return;

            let reader = new FileReader();
            reader.onload = function (e) {
                try {
                    let jsonData = JSON.parse(e.target.result);
                    HT.analyzeImport(jsonData);
                } catch (err) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error("File JSON không hợp lệ.");
                    } else {
                        alert("File JSON không hợp lệ.");
                    }
                }
                // Reset input
                $('#import-json-input').val('');
            };
            reader.readAsText(file);
        });
    }

    HT.analyzeImport = (jsonData) => {
        $('#import-loading').show();
        $.ajax({
            url: 'product/analyze-import', // Giữ nguyên theo style của product_crawl.js
            type: 'POST',
            data: {
                products: jsonData,
                _token: _token
            },
            dataType: 'json',
            success: function (res) {
                // Phân tách dữ liệu trả về
                const status = res.status;
                const ready = res.ready;
                const review = res.review;
                const dropdown = res.dropdown;

                globalReadyData = ready;
                globalReviewData = review;

                if (status === 'direct') {
                    if (confirm(`Tìm thấy ${ready.length} sản phẩm hợp lệ. Tiến hành import ngay?`)) {
                        HT.executeImport(ready);
                    }
                } else {
                    HT.renderReviewTable(review, dropdown);
                    $('#import-modal').modal('show');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Lỗi phân tích:', textStatus, errorThrown);
                if (typeof toastr !== 'undefined') {
                    toastr.error("Không thể phân tích dữ liệu. Vui lòng kiểm tra console.");
                }
            },
            complete: function () {
                $('#import-loading').hide();
            }
        });
    }

    HT.renderReviewTable = (items, dropdown) => {
        catalogueOptions = '<option value="">-- Chọn danh mục --</option>';
        if (dropdown) {
            Object.entries(dropdown).forEach(([id, name]) => {
                catalogueOptions += `<option value="${id}">${name}</option>`;
            });
        }

        let html = items.map((item, index) => {
            let image = (item.album && item.album.length > 0) ? item.album[0] : '';
            return `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td class="text-center">
                        <img src="${image}" class="img-review">
                    </td>
                    <td>
                        <span class="product-info-name">${item.name}</span>
                        <div class="product-info-price">
                            Giá: ${HT.formatNumber(item.price)}đ &nbsp;&nbsp; 
                            KM: ${HT.formatNumber(item.price_discount)}đ &nbsp;&nbsp;
                            Đã bán: ${item.sold || 0}
                        </div>
                    </td>
                    <td>
                        <select class="form-control select-mapping" data-index="${index}">
                            ${catalogueOptions}
                        </select>
                    </td>
                </tr>
            `;
        }).join('');
        $('#import-review-body').html(html);
        $('#select-all-catalogue').html(catalogueOptions);

        // Khởi tạo select2 cho các select mới thêm vào
        if ($.fn.select2) {
            // Hủy select2 cũ nếu đã tồn tại để tránh lỗi re-init
            $('.select-mapping, #select-all-catalogue').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });

            $('.select-mapping, #select-all-catalogue').select2({
                width: '100%',
                placeholder: '-- Chọn danh mục --',
                dropdownParent: $('#import-modal') // Đảm bảo dropdown hiển thị đúng trên modal
            });
        }
    }

    HT.changeAllCatalogue = () => {
        $(document).on('change', '#select-all-catalogue', function () {
            let val = $(this).val();
            if (val !== '') {
                $('.select-mapping').val(val).trigger('change');
            }
        });
    }

    HT.formatNumber = (num) => {
        if (!num) return 0;
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }

    HT.executeImport = (products) => {
        $('#import-loading').show();
        if (typeof toastr !== 'undefined') {
            toastr.info("Đang xử lý import, vui lòng chờ...");
        }

        $.ajax({
            url: 'product/execute-import',
            type: 'POST',
            data: {
                products: products,
                _token: _token
            },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    if (typeof toastr !== 'undefined') {
                        toastr.success("Đã import sản phẩm vào hệ thống.");
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message || "Có lỗi xảy ra khi import");
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Lỗi import:', textStatus, errorThrown);
                if (typeof toastr !== 'undefined') {
                    toastr.error("Có lỗi xảy ra trong quá trình import.");
                }
            },
            complete: function () {
                $('#import-loading').hide();
            }
        });
    }

    HT.confirmImport = () => {
        $('#confirm-import-btn').on('click', function () {
            let mappingValid = true;
            $('.select-mapping').each(function () {
                const index = $(this).data('index');
                const selectedId = $(this).val();
                if (!selectedId) {
                    mappingValid = false;
                    $(this).parent().addClass('has-error');
                } else {
                    $(this).parent().removeClass('has-error');
                    globalReviewData[index].product_catalogue_id = selectedId;
                }
            });

            if (!mappingValid) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning("Vui lòng gán danh mục cho tất cả sản phẩm.");
                }
                return;
            }

            const finalData = [...globalReadyData, ...globalReviewData];
            $('#import-modal').modal('hide');
            HT.executeImport(finalData);
        });
    }

    $(document).ready(function () {
        HT.importJson();
        HT.confirmImport();
        HT.changeAllCatalogue();
    });

})(jQuery);
