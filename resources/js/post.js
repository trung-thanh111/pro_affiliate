$(document).ready(function() {
    $(document).on('click', '#load-more-review', function() {
        let _this = $(this);
        let page = parseInt(_this.attr('data-page')) + 1;
        let url = 'ajax/post/load-review'; // or use a global config for routes
        
        _this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang tải...');
        _this.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'GET',
            data: { page: page },
            success: function(res) {
                if(res.html) {
                    $('#review-list').append(res.html);
                    _this.attr('data-page', page);
                    _this.html('Xem thêm');
                    _this.prop('disabled', false);
                    if(!res.hasMore) {
                        _this.parent().hide();
                    }
                }
            },
            error: function() {
                _this.html('Xem thêm');
                _this.prop('disabled', false);
            }
        });
    });
});
