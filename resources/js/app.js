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

$(document).ready(function() {
    let searchTimeout;
    $('.input-search').on('input', function() {
        let keyword = $(this).val();
        let suggestionsContainer = $('.search-suggestions');
        
        clearTimeout(searchTimeout);
        
        if (keyword.length < 2) {
            suggestionsContainer.hide().empty();
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '/ajax/dashboard/findProduct',
                type: 'GET',
                data: { keyword: keyword },
                success: function(res) {
                    if (res && res.length > 0) {
                        let html = '';
                        res.forEach(item => {
                            // Map model data to suggestion item
                            // We might need to handle the image path and price format
                            let name = item.name || (item.languages ? item.languages[0].pivot.name : '');
                            let image = item.image || '/user-default.png';
                            let canonical = item.canonical || (item.languages ? item.languages[0].pivot.canonical : '');
                            let price = item.price ? new Intl.NumberFormat('vi-VN').format(item.price) + 'đ' : '';

                            html += `
                                <a href="/${canonical}.html" class="suggestion-item">
                                    <img src="${image}" class="img" alt="${name}">
                                    <div class="info">
                                        <span class="name">${name}</span>
                                        <span class="price">${price}</span>
                                    </div>
                                </a>
                            `;
                        });
                        suggestionsContainer.html(html).show();
                    } else {
                        suggestionsContainer.hide().empty();
                    }
                },
                error: function() {
                    suggestionsContainer.hide().empty();
                }
            });
        }, 300);
    });

    // Close suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.header-search').length) {
            $('.search-suggestions').hide();
        }
    });
});
