(function($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr('content');

    HT.setupCrawl = () => {
        $(document).on('click', '#crawl-data-btn', function() {
            $('#crawl-modal').modal('show');
        });

        $(document).on('click', '#start-crawl-btn', function() {
            let url = $('#crawl-url').val();
            if (url == '') {
                toastr.error('Vui lòng nhập link sản phẩm');
                return false;
            }

            $('#crawl-preview').hide();
            $('#save-crawled-btn').hide();
            $('#crawl-loading').show();

            $.ajax({
                url: 'product/crawl',
                type: 'POST',
                data: {
                    url: url,
                    _token: _token
                },
                dataType: 'json',
                success: function(res) {
                    $('#crawl-loading').hide();
                    if (res.status === 'success') {
                        let data = res.data;
                        let mainImage = data.images && data.images.length > 0 ? data.images[0] : '';
                        
                        $('#preview-image').attr('src', mainImage);
                        $('#preview-name').val(data.name);
                        $('#preview-price').val(addCommas(data.original_price));
                        $('#preview-price-sale').val(addCommas(data.price));
                        $('#preview-description').val(data.description);

                        $('#crawl-preview').show();
                        $('#save-crawled-btn').show();
                        toastr.success('Quét dữ liệu thành công');
                    } else {
                        toastr.error(res.message || 'Không thể lấy dữ liệu từ link này');
                    }
                },
                error: function() {
                    $('#crawl-loading').hide();
                    toastr.error('Có lỗi xảy ra trong quá trình quét dữ liệu');
                }
            });
        });

        $(document).on('click', '#save-crawled-btn', function() {
            let data = {
                image: $('#preview-image').attr('src'),
                name: $('#preview-name').val(),
                original_price: $('#preview-price').val(),
                price: $('#preview-price-sale').val(),
                description: $('#preview-description').val(),
                _token: _token
            };

            $.ajax({
                url: 'product/save-crawled',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success('Lưu sản phẩm thành công');
                        $('#crawl-modal').modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(res.message || 'Lưu sản phẩm thất bại');
                    }
                },
                error: function() {
                    toastr.error('Có lỗi xảy ra khi lưu sản phẩm');
                }
            });
        });
    }

    $(document).ready(function() {
        HT.setupCrawl();
    });

})(jQuery);
