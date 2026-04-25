(function($) {
	"use strict";
	var HT = {}; 

	HT.filterOption = () => {
		var filterOption = {
			'attributes' : {},
			'price' : {
				'price_min' : $('.price_min').val(),
				'price_max' : $('.price_max').val()
			},
			'productCatalogueId' : $('.product_catalogue_id').val(),
			'sortType' : $('select[name=sortType]').val(),
		};
		
		$('.filterAttribute:checked').each(function() {
			let group = $(this).attr('data-group')
			if(!filterOption.attributes.hasOwnProperty(group)) {
				filterOption.attributes[group] = []
			}
			filterOption.attributes[group].push($(this).val())
		})
		
		$('.filter-price:checked').each(function() {
			let min = $(this).attr('data-min')
			let max = $(this).attr('data-max')
			if(filterOption.price.price_min == '' || min < filterOption.price.price_min) {
				filterOption.price.price_min = min
			}
			if(filterOption.price.price_max == '' || max > filterOption.price.price_max) {
				filterOption.price.price_max = max
			}
		})

		return filterOption;
	}

	HT.sendDataToFilter = () => {
		let option = HT.filterOption();
		console.log('Sending filter request:', option);
		
		$.ajax({
			url: 'ajax/product/filter', 
			type: 'GET', 
			data: option, 
			dataType: 'json', 
			beforeSend: function() {
				$('#product-list').addClass('loading');
			},
			success: function(res) {
				$('#product-list').removeClass('loading');
				let html = res.data
				
				console.log('Received data, length:', html.length);
				
				// Target the ROW inside the container
				let target = $('#product-list .row');
				if(target.length) {
					target.html(html);
					console.log('Grid updated');
				} else {
					$('#product-list').html('<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4 g-3">' + html + '</div>');
				}
				
				if(res.countProduct !== undefined) {
					$('.caption strong').html(`${res.countProduct} sản phẩm`);
				}
				
				// Scroll to top of list
				$('html, body').animate({
					scrollTop: $("#product-list").offset().top - 150
				}, 300);
			},
			error: function(err) {
				$('#product-list').removeClass('loading');
				console.error('AJAX Error:', err);
			}
		});
	}

	HT.filterInput = () => {
		$(document).on('change', '.filtering', function() {
			HT.sendDataToFilter();
		})
	}

	HT.sortProduct = () => {
		$(document).on('change', 'select[name=sortType]', function() {
			HT.sendDataToFilter();
		})
	}

	$(document).ready(function() {
		HT.filterInput();
		HT.sortProduct();
	});

})(jQuery);
