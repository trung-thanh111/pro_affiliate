import Echo from 'laravel-echo';
import io from 'socket.io-client';

// import '../vendor/frontend/resources/library/js/jquery.js';

// window.jQuery = jQuery;
// window.$ = jQuery;

// import '../vendor/backend/js/plugins/toastr/toastr.min.js';
// // import '../vendor/frontend/resources/plugins/wow/dist/wow.min.js';
// import '../vendor/frontend/resources/uikit/js/uikit.min.js';
// import '../vendor/frontend/resources/uikit/js/components/sticky.min.js';
// import '../vendor/frontend/resources/uikit/js/components/accordion.min.js';
// import '../vendor/frontend/resources/uikit/js/components/accordion.min.js';
// import '../vendor/frontend/resources/uikit/js/components/lightbox.min.js';
// import '../vendor/frontend/resources/uikit/js/components/sticky.min.js';
// import '../vendor/frontend/core/plugins/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js';
import '../vendor/frontend/resources/function.js';
import '../vendor/frontend/core/library/product.js';
import '../vendor/frontend/core/library/filter.js';
import '../vendor/frontend/core/library/compare.js';
// import '../vendor/frontend/core/library/cart.js';
// import 'https://unpkg.com/swiper/swiper-bundle.min.js';



// window.io = io;

// window.Echo = new Echo({
//     broadcaster: 'socket.io',
//     host: 'http://laravelversion1.com:6001'
// });

window.HT = window.HT || {};

HT.productTab = () => {
    $(document).on('click', '.tab-title', function (e) {
        e.preventDefault();
        let _this = $(this);
        let catalogueId = _this.data('id');

        // Find the closest parent section to locate the grid container
        let parentSection = _this.closest('section');
        let gridContainer = parentSection.find('.product-grid-container');

        if (gridContainer.length === 0) return;

        // UI Update
        parentSection.find('.tab-title').removeClass('active');
        _this.addClass('active');

        // Ajax Load
        $.ajax({
            url: '/ajax/product/getProducts',
            type: 'GET',
            data: {
                catalogue_id: catalogueId
            },
            beforeSend: function () {
                gridContainer.addClass('loading-opacity');
            },
            success: function (res) {
                if (res.code == 10) {
                    gridContainer.html(res.html);
                    gridContainer.removeClass('loading-opacity');

                    // Re-trigger wow animations if present
                    if (typeof WOW !== 'undefined') {
                        new WOW().init();
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error fetching products:', textStatus);
                gridContainer.removeClass('loading-opacity');
            }
        });
    });
}

HT.loadMoreReview = () => {
    $(document).on('click', '#load-more-review', function () {
        let _this = $(this);
        let page = parseInt(_this.attr('data-page')) + 1;
        let url = 'ajax/post/load-review'; // or use a global config for routes

        _this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang tải...');
        _this.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'GET',
            data: { page: page },
            success: function (res) {
                if (res.html) {
                    $('#review-list').append(res.html);
                    _this.attr('data-page', page);
                    _this.html('Xem thêm');
                    _this.prop('disabled', false);
                    if (!res.hasMore) {
                        _this.parent().hide();
                    }
                }
            },
            error: function () {
                _this.html('Xem thêm');
                _this.prop('disabled', false);
            }
        });
    });
}

$(document).ready(function () {
    HT.productTab();
    HT.loadMoreReview();

    let searchTimeout;

    function loadHotTopics() {
        let suggestionsContainer = $('.search-suggestions');
        $.ajax({
            url: '/ajax/dashboard/getHotTopics',
            type: 'GET',
            success: function (res) {
                if (res && res.length > 0) {
                    let html = `
                        <div class="suggestion-header">
                            <i class="bi bi-fire"></i> Hot Topics
                        </div>
                        <div class="hot-topics-list">
                    `;
                    res.forEach(item => {
                        let name = item.languages[0].pivot.name;
                        let canonical = item.languages[0].pivot.canonical;
                        html += `<a href="/${canonical}.html" class="hot-topic-item">${name}</a>`;
                    });
                    html += `</div>`;
                    suggestionsContainer.html(html).show();
                }
            }
        });
    }

    $('.input-search').on('focus', function () {
        if ($(this).val().length === 0) {
            loadHotTopics();
        }
    });

    $('.input-search').on('input', function () {
        let keyword = $(this).val();
        let suggestionsContainer = $('.search-suggestions');

        clearTimeout(searchTimeout);

        if (keyword.length === 0) {
            loadHotTopics();
            return;
        }

        if (keyword.length < 2) {
            suggestionsContainer.hide().empty();
            return;
        }

        searchTimeout = setTimeout(function () {
            $.ajax({
                url: '/ajax/dashboard/findPost',
                type: 'GET',
                data: { keyword: keyword },
                success: function (res) {
                    if (res && res.length > 0) {
                        let html = '';
                        res.forEach(item => {
                            let name = item.languages[0].pivot.name;
                            let image = item.image || '/user-default.png';
                            let canonical = item.languages[0].pivot.canonical;

                            html += `
                                <a href="/${canonical}" class="suggestion-item">
                                    <img src="${image}" class="img" alt="${name}">
                                    <div class="info">
                                        <span class="name">${name}</span>
                                    </div>
                                </a>
                            `;
                        });
                        suggestionsContainer.html(html).show();
                    } else {
                        suggestionsContainer.hide().empty();
                    }
                },
                error: function () {
                    suggestionsContainer.hide().empty();
                }
            });
        }, 300);
    });

    // Close suggestions when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.header-search').length) {
            $('.search-suggestions').hide();
        }
    });

    // Hot Topic Section Filtering
    $('#hot-topic-input').on('keyup', function () {
        let keyword = $(this).val().toLowerCase();
        $('.topic-item').each(function () {
            let name = $(this).data('name');
            if (name.includes(keyword)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Hide columns if no items visible
        $('.topic-column').each(function () {
            if ($(this).find('.topic-item:visible').length === 0) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });

    $('#hot-topic-btn').on('click', function () {
        let keyword = $('#hot-topic-input').val();
        if (keyword.length > 0) {
            window.location.href = '/tim-kiem-bai-viet?keyword=' + encodeURIComponent(keyword);
        }
    });

    $('#hot-topic-input').on('keypress', function (e) {
        if (e.which === 13) {
            $('#hot-topic-btn').trigger('click');
        }
    });
});
