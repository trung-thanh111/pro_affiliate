(function($) {
    "use strict";
    var HT = {};

    HT.productTab = () => {
        $(document).on('click', '.tab-title', function(e) {
            e.preventDefault();
            let _this = $(this);
            let catalogueId = _this.data('id');
            
            // Find the closest parent section to locate the grid container
            let parentSection = _this.closest('.uk-grid');
            let gridContainer = parentSection.find('.product-grid-container');
            
            if(gridContainer.length === 0) return;

            // UI Update
            _this.closest('.category-list-sidebar').find('.tab-title').removeClass('active');
            _this.addClass('active');

            // Ajax Load
            $.ajax({
                url: '/ajax/product/getProducts',
                type: 'GET',
                data: {
                    catalogue_id: catalogueId
                },
                beforeSend: function() {
                    gridContainer.addClass('loading-opacity');
                },
                success: function(res) {
                    if (res.code == 10) {
                        gridContainer.html(res.html);
                        gridContainer.removeClass('loading-opacity');
                        
                        // Re-trigger wow animations if present
                        if(typeof WOW !== 'undefined') {
                            new WOW().init();
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching products:', textStatus);
                    gridContainer.removeClass('loading-opacity');
                }
            });
        });
    }

    $(document).ready(function() {
        HT.productTab();
    });

})(jQuery);
