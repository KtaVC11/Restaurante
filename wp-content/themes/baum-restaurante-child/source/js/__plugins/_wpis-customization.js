	/**
	** Gallery on success_modal
	** Flex classes added to single page and quick view modal - 05-2020 - KMA
	**/
	var flex_classes = 'd-flex justify-content-center';
	$(window).on('load', function() {
		if (jQuery('.single-product').length) {
			setTimeout(function() {
				jQuery('.shop-wrapper .wpis-slider-for .slick-track').addClass(flex_classes + ' align-items-center');
				jQuery('.shop-wrapper .wpis-slider-nav .slick-track').addClass(flex_classes + ' align-items-stretch');
			}, 3500);
		}
	});
    
    $(document).on('close_modal', function () {
        $('body').removeClass('body-overflow');
    });

	$(document).on('success_modal', function () {
		var select = $('.xoo-qv-summary .variations select').first();
		var name = select.attr('name');
        
        $('body').addClass('body-overflow');

		if (select.hasClass('select_active'))
			select.removeClass('select_active');

		select.addClass('select_listing_active');

		$('.xoo-qv-images .wpis-slider-for').slick({
			fade: true,
			arrows: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			infinite: true,
			prevArrow: object_name.wpis_arrow_prev,
			nextArrow: object_name.wpis_arrow_next,
			asNavFor: '.wpis-slider-nav'
		});

		$('.xoo-qv-images .wpis-slider-nav').slick({
			dots: false,
			arrows: true,
			centerMode: false,
			focusOnSelect: true,
			infinite:false,
			slidesToShow: 4,
			slidesToScroll: 1,
			infinite: true,
			lazyLoad: 'ondemand',
			prevArrow: object_name.wpis_arrow_prev,
			nextArrow: object_name.wpis_arrow_next,
			asNavFor: '.wpis-slider-for'
		});

		$('.xoo-qv-images .wpis-slider-for .slick-track').addClass('woocommerce-product-gallery__image single-product-main-image align-items-center ' + flex_classes);
		$('.xoo-qv-images .wpis-slider-nav .slick-track').addClass('flex-control-nav align-items-stretch ' + flex_classes);
		$('.xoo-qv-images .wpis-slider-nav .slick-track li img').removeAttr('srcset');

		$('.variations select').change(function() {
			$('.xoo-qv-images .wpis-slider-nav').slick('slickGoTo', 0);
		});

		if(object_name.wpis_show_lightbox != 1) { 
			$('.xoo-qv-images a.wpis-popup').remove();
		}
	});