$(document).ready(function() {
    $(document).on('click', '.js-news-item', function(e) {
        if ($(e.target).closest('.no-redirect, [class*="btn-load-more"], .no-global-redirect').length) return;

        const affiliate = $(this).data('affiliate');
        
        if (affiliate) {
            const href = $(this).data('href') || $(this).attr('href');
            
            // 1. Mở affiliate ở tab mới (Hành động trực tiếp giúp tránh bị chặn popup)
            window.open(affiliate, '_blank');
            
            // 2. Chuyển hướng tab hiện tại đến bài viết
            if (href) {
                window.location.href = href;
            }
            
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
});
