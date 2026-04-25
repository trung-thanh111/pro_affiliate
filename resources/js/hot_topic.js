$(document).ready(function() {
    $('#hot-topic-input').on('keyup', function() {
        let keyword = $(this).val().toLowerCase();
        $('.topic-item').each(function() {
            let name = $(this).data('name');
            if (name.includes(keyword)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Hide columns if no items visible
        $('.topic-column').each(function() {
            if ($(this).find('.topic-item:visible').length === 0) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });

    $('#hot-topic-btn').on('click', function() {
        let keyword = $('#hot-topic-input').val();
        if (keyword.length > 0) {
            window.location.href = '/tim-kiem-bai-viet?keyword=' + encodeURIComponent(keyword);
        }
    });

    $('#hot-topic-input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#hot-topic-btn').trigger('click');
        }
    });
});
