    /**
     ** Select first variation
     **/
    
     function select_first_variation() {
	     if($('.variations').length) {
		    setTimeout(function() {
		     	$('.variations select').each(function() {
			     var _this = $(this);
			     if(_this.val() == '') {
			     	_this.find('option.attached').first().prop('selected', true);
			    	_this.trigger('change');
			     }
			    });
		     }, 300);
	     }
     }
     $(document).on('ready', select_first_variation);

	/**
	** Clase ready al ul en listado y detalle de producto
	**/
	if ($('body.post-type-archive-product ul.products > li, body.tax-product_cat ul.products > li, body.single-product ul.products  li.product').length > 1) {
		$('ul.products').addClass('ready');
	}
	
	if ($('body.single-product').length > 0) {
		$(window).on('load', function () {
			if($('#wpis-gallery .slick-slide').length > 1) {
				$('#wpis-gallery').addClass('ready');
			}
		});
	}

	/**
	** Aplica clase .quick-view-hover en el hover al li.product
	**/
	/*
    $('a.xoo-qv-button').on('mouseenter, mouseover ', function () {
		$(this).closest('li.product').addClass('quick-view-hover');
	});
    */

	/**
	** Remueve clase .quick-view-hover en el hover al li.product
	**/
    /*
	$('a.xoo-qv-button').on('mouseleave', function () {
		$(this).closest('li.product').removeClass('quick-view-hover');
	});
    */

	/**
	** Muestra las images del carrusel
	**/
	if ($('.product div.images').length > 0) {
		$(window).on('load', function () {
			$('.woocommerce .product div.images').animate({
				opacity: 1
			}, 800, function () {
				$(this).addClass('slick-loaded');
			});
		});

		$(document).on('change', '.div.xoo-qv-panel', function () {
			$('.woocommerce .product div.images').animate({
				opacity: 1
			}, 800, function () {
				$(this).addClass('slick-loaded');
			});
		});
	}

    /**
    ** Select per page
    **/
    /*
    $('.woocommerce-posts-per-page').on('change', 'select.posts-per-page', function () {
        $(this).closest('form').submit();
    });
    */

	/**
	** Fix paginación en mobile
	**/
   /*
	$(window).on('load', function() {
		if ($(window).width() < 480) {
			$(window).trigger('resize'); // Fix matchHeight
		}
	});
    */

	/*
    $(window).on('load resize', function() {
		if ($(window).width() < 480) {
			$('.woocommerce-pagination').find('a.page-numbers').not('.prev, .next').closest('li').hide();
		} else {
			$('.woocommerce-pagination').find('a.page-numbers').closest('li').show();
		}
	});
    */

	/**
	** Fake link triggered to Quick View in product listing
	**/
   /*
	$(document).on('click', '.woocommerce-LoopProduct-link', function() {
		if ($(this).attr('href') == '#qv') {
			$(this).closest('.product').find('.xoo-qv-button').trigger('click');
		}
		
		return false;
	});
    */
