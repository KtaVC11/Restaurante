(function($) {
	/**
	** Mega menu breakpoint
	**/
	var mega_menu_breakpoint = $('#mega-menu-primary').data('breakpoint');
    var ww = window.innerWidth;

	/**
	** Close mobile menu
	**/
	function close_mobile_menu() {
		if ($('.menu-toggle').hasClass('toggle-open')) {
			$('.menu-toggle.toggle-open').trigger('click');
		}
	}

	/**
	** Validate fields only numbers
	**/
	function numbersOnly(s) {
		$(s).on("keydown", function(e) {
			-1 !== $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) ||
			(/65|67|86|88/.test(e.keyCode) &&
				(e.ctrlKey === true || e.metaKey === true) &&
				(!0 === e.ctrlKey || !0 === e.metaKey)) ||
			(35 <= e.keyCode && 40 >= e.keyCode) ||
			((e.shiftKey || 48 > e.keyCode || 57 < e.keyCode) &&
				(96 > e.keyCode || 105 < e.keyCode) &&
				e.preventDefault());
		});
	}
	
    /**
     ** litepicker
     **/
    if ($('#datepicker').length > 0) {
        // Date Picker config
        // var date =
        //     typeof baumdivi_ajax != 'undefined' &&
        //     typeof baumdivi_ajax.litepicker_today != 'undefined' && baumdivi_ajax.litepicker_today != '' ? new Date(baumdivi_ajax.litepicker_today) : new Date();

        var options = {
            element: document.getElementById('datepicker'),
            format: 'DD MMMM, YYYY',
            lang: 'es-CR',
            mobileFriendly: true,
            onShow: function () {},
        };
        
        if (baumchild_ajax.excluir_fechas != undefined) 
            options.lockDays = baumchild_ajax.excluir_fechas;

        if (baumchild_ajax.fecha_inicio != undefined) {
            options.minDate = baumchild_ajax.fecha_inicio;
        }

        if (baumchild_ajax.fecha_inicial != undefined) {
            options.startDate = baumchild_ajax.fecha_inicial;
        }

        if (baumchild_ajax.fecha_fin != undefined)
            options.maxDate = baumchild_ajax.fecha_fin;

        // if (baumchild_ajax.exclude_weekend != undefined)
        //     options.disableWeekends = baumchild_ajax.exclude_weekend;

        var picker = new Litepicker(options);
    }

/**
 ** mmenu init
 **/
var mmenu_args = {
    extensions: [
        "fx-panels-slide-100",
        "pagedim-black",
        "position-right",
        "position-front",
    ],
    close: false,
    navbar: false,
    onClick: {
        setSelected: true
    },
    keyboardNavigation: {
        enable: true,
        enhance: true
    }
};

// declaracion de cada instancia:  [nombre] - [selector jquery] - [argumentos*]
setTimeout(function () {
    sidebar_contacto = $('#sidebar-contacto').mmenu(mmenu_args).data('mmenu'); // Contacto
}, 5);




// Inserta el icono de cerrar el mmenu .mmenu-close-button
$(".mm-menu").each(function () {
    $(this).find(".mm-panel").append('<span class="mmenu-close-button"><i class="fas fa-times"></i></span>');
});


// Cierra el mmenu mmenu-sidebar-contacto
$(document).on("click", "#sidebar-contacto .mmenu-close-button", function () {
    sidebar_contacto.close();
    return false;
});


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
    /** 
     ** Custom anchor menu 
     **/
    /*
    $('.menu-menu-principal-container li a[href*=#]:not([href=#])').click(function () {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top - $('.site-header').height()
                }, 1200);
                return false;
            }
        }
    });
    */

    /**
     ** Print click loader
     **/
    $(document).on("printLinkComplete", function () {
        $("body").removeClass("bm-loader");
    });

    /**
     ** Custom checked checkboxes
     **/
    $(document).on("click", 'input[type="checkbox"]', function () {
        if ($(this).is(":checked")) {
            $(this)
                .parent('label[class*="checkbox"]')
                .addClass("checked");
        } else {
            $(this)
                .parent('label[class*="checkbox"]')
                .removeClass("checked");
        }
    });

    /**
     ** Allow only numbers in input type text
     **/
    numbersOnly(".input-container input.input-text");

    /**
     ** Custom checked checkboxes
     **/
    $(document).on('change', 'input[type="checkbox"]', function () {
        if ($(this).prop('checked')) {
            $(this).closest('label[class*="checkbox"]').addClass('checked');
        } else {
            $(this).closest('label[class*="checkbox"]').removeClass('checked');
        }
    });

    function check_checkboxes() {
        $('input[type="checkbox"]').each(function () {
            if ($(this).prop('checked') && !$(this).closest('.checkbox').hasClass('checked')) {
                $(this).closest('.checkbox').addClass('checked')
            }
        });
    }
    check_checkboxes();

    /**
     ** Check for Cedula tipo
     **/
    function check_cedula_tipo() {
        if ($('#cedula_tipo_field').length && !$('input[name="cedula_tipo"]').is(':checked')) {
            $('#cedula_tipo_cedula_fisica').prop('checked', true);
        }
    }
    check_cedula_tipo();

    /**
    ** Fix issue with check_checkboxes
    ** 04-09-2020 - KMA
    **/
    $('#collapse_message').on('shown.bs.collapse', function() {
        $('.product_note-field').find('.custom-checkbox').addClass('checked');
    });

    $('#collapse_message').on('hidden.bs.collapse', function() {
        $('.product_note-field').find('.custom-checkbox').removeClass('checked');
    });

    /**
    ** Fix Checkout field
    ** 04-09-2020 - KMA
    **/
    $(window).on('load resize', resize_email_field);

    function resize_email_field() {
        if ($('#billing_phone_field.form-row-first').length) {
            if ($(window).width() > 600) {
                $('#billing_phone_field.form-row-first').height($('#billing_email_field.form-row-last').height());
            } else {
                $('#billing_phone_field.form-row-first').height('auto');
            } 
        }
    }
    resize_email_field();


    // encierra el div row-cta en un <nav>
//    $(document).ready(function () {
//        if ($('.section-contacto').length > 0) {
//            $(this).parent('entry-content').addClass('section-contact-main');
//        }
//    });


var left = $('.main-navigation').innerWidth;

// Abre el menu principal
$('.toggleButton').on('click', function () {
    menu_mobile_open();
});

// Cierra el menu principal
$('.close-nav, #nav-first li').on('click', function () {
    if ($('.menu-is-open').length > 0) {
        menu_mobile_close();
    }
});



/*
 ** function para ABRIR el menu
 **/
function menu_mobile_open() {
    $('.close-nav').addClass('open');
    $('body').addClass('body-overflow');
    $('.site-header-menu').addClass('menu-is-open');
    $('.main-navigation').animate({
        "left": "0px"
    }, 800, function () {
        console.log('%c El Menu ha sido abierto', 'background: #ffcc00; color: #000000');
    });
}


/*
 ** function para CERRAR el menu principal
 */
function menu_mobile_close() {
    $('.close-nav').removeClass('open');
    $('.main-navigation').animate({
        "left": "-450px"
    }, 500, function () {
        $('body').removeClass('body-overflow');
        $('.site-header-menu').removeClass('menu-is-open');
        console.log('%c El Menu ha sido cerrado', 'background: #222; color: #bada55');
    });

}



// Cierra el menu al darle clic al overlay
$(document).on('click', '.site-header-menu', function (e) {
    ww = window.innerWidth;
    if (ww < 768) {
        if ($('body').hasClass('body-overflow')) {
            if (!$(e.target).closest('.main-navigation').length) {
                menu_mobile_close();
            }
        }
    }
});


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

/**
 ** Add class to add overlay in quick view next/prev
 **/
/*
$(document).ready(function () {
    $('.xoo-cp-modal, .xoo-cp-opac, .xoo-qv-panel, .xoo-qv-opac').addClass('Fixed');
});
*/
	/**
	** Order message wrapper
	**/
	if ($('#send_order_message').length) {
		$(document).on('change', '#send_order_message', check_for_message_wrapper);
		$(window).on('load', function() {
			$('#order_comments_field label').remove();
			$('#order_comments_field .woocommerce-input-wrapper').addClass('w-100');
			$('#send_order_message').siblings('span').remove();
			check_for_message_wrapper();
		});

		function check_for_message_wrapper() {
			if ($('#send_order_message').prop('checked')) {
				$('#order_comments_field').slideDown();
			} else {
				$('#order_comments_field').slideUp();
				$('#order_comments').val('');
			}
		}
	}

	/**
	** Add to Cart loader
	**/
	$(document).on('click', '.single_add_to_cart_button', function() {
		$(this).addClass('disabled');
	});

	$(document).on('added_to_cart', function() {
		$('.single_add_to_cart_button').removeClass('disabled');
	});

	/**
	** Toggle checkout actions
	**/
	$(".woocommerce-checkout-notifications .woocommerce-info a").click(function() {
		var parent = $(this)
		.parent()
		.parent();
		remove_coupon_message(0);

		$('[class*="woocommerce-form-"]')
		.not(parent)
		.removeClass("open");

		if (parent.hasClass("open")) {
			parent.removeClass("open");
		} else {
			parent.addClass("open");
		}

		if (parent.hasClass("open")) {
			$("article").addClass("overlay overlay-checkout");
		} else {
			$("article").removeClass("overlay overlay-checkout");
		}
	});

	/**
	** Toggle overlay
	**/
	$(document).on("click", "body.overlay", function(e) {
		if (!$(e.target).closest(".open + form").length) {
			remove_checkout_info_overlay();
		}
	});
	$(document).on("click", ".woocommerce-checkout-notifications .close", function(
		e
		) {
		remove_checkout_info_overlay();
	});

	function remove_checkout_info_overlay() {
		$("body").removeClass("overlay overlay-checkout");
		$('[class*="woocommerce-form-"]').removeClass("open");
	}

	function remove_coupon_message(time) {
		var elements =
		".woocommerce-checkout-notifications .woocommerce-error, .woocommerce-checkout .woocommerce-message";
		setTimeout(function() {
			$(elements).fadeOut("slow", function() {
				setTimeout(function() {
					$(elements).remove();
				}, 60);
			});
		}, time);
	}

	/**
	** Habilitar el boton en formulario cupones
	**/
	$(document).on("change keyup keypress blur", "#coupon_code", function() {
		if ($(this).val() != "") {
			$(this)
			.parent()
			.find(".button")
			.removeAttr("disabled");
		} else {
			$(this)
			.parent()
			.find(".button")
			.attr("disabled", "disabled");
		}
	});

	/**
	** Activar check para crear cuenta en checkout
	**/
	$("#account_password, #account_confirm_password").on("blur", function() {
		var _account = $("#account_password");
		var _account_conf = $("#account_confirm_password");

		if (_account.val() != "" || _account_conf.val() != "") {
			$("#createaccount").prop("checked", true);
		} else {
			$("#createaccount").prop("checked", false);
		}
	});

	/**
	** Checkout fields validation
	** Show payment box on load in Add payment method page
	**/
	if ($('.woocommerce-Payment .payment_box').length)
		$('.woocommerce-Payment').find('.payment_box').show();

	$(document).on("change", "#payment_methods", function() {
		$(".payment_box")
		.not(".payment_method_" + $(this).val())
		.slideUp();
		$(".payment_box.payment_method_" + $(this).val()).slideDown();
	});

	$("#billing_phone, #billing_celular").mask("0000-0000", {
		placeholder: "8888-8888"
	});

	$("#account_email, #billing_email").blur(function() {
		$(this).val(
			$(this)
			.val()
			.split(" ")
			.join("")
			);
	});

	/**
	** Shipping calculator as dropdown
	**/
	$(document).on("change", ".shipping_methods", function() {
		var _this = $(this).val();
		if (_this == "calculate_shipping") {
			$(".shipping-calculator-button").trigger("click");
		} else {
			if ($(".shipping-calculator-form").is(":visible")) {
				$(".shipping-calculator-button").trigger("click");
			}
			$('select.shipping_method option[value="' + _this + '"]').prop(
				"selected",
				true
				);
			$("select.shipping_method").trigger("change");
		}
	});

	/**
	** After update checkout
	**/
	$(document.body).on("update_checkout, updated_checkout", function() {
		validate_checkout_required_fields();
		remove_checkout_info_overlay();
		remove_coupon_message(1500);
	});

	function validate_checkout_required_fields() {
		if ($("select.shipping_method").val() == "wc_pickup_store") {
			$("#billing_state_field, #billing_city_field, #billing_address_1_field")
			.find(".required")
			.hide();
			$(
				"#billing_state_field, #billing_city_field, #billing_address_1_field"
				).removeClass(
				"validate-required woocommerce-validated woocommerce-invalid woocommerce-invalid-required-field"
				);
			} else {
				if (
					$(
						"#billing_state_field .required, #billing_city_field .required, #billing_address_1_field .required"
						).length < 1
					) {
					$(
						"#billing_state_field label, #billing_city_field label, #billing_address_1_field label"
						).append('<abbr class="required" title="obligatorio">*</abbr>');
			} else {
				$("#billing_state_field, #billing_city_field, #billing_address_1_field")
				.find(".required")
				.show();
			}
			$(
				"#billing_state_field, #billing_city_field, #billing_address_1_field"
				).addClass("validate-required");
		}
	}

	/* Toggle for input cedula_juridica */
	if ($('select[name="cedula_tipo"]').length) {
		if($('.form-row[id*="cedula"] select[name="cedula_tipo"]').val() == 'cedula_juridica') {
			is_cedula_juridica();
		}

		$(document).on('change', '.form-row[id*="cedula"] select[name="cedula_tipo"]', check_for_cedulas);
		$(window).on('load', function() {
				$('select[name="cedula_tipo"]').trigger('change');
		});

		function check_for_cedulas() {
			var cedula = $(this).val();
			if (cedula == 'cedula_juridica') {
				is_cedula_juridica();
			} else {
				$('#cedula_numero').removeClass().addClass('input-text ' + cedula);
				$('#cedula_razon_social_field').removeClass("validate-required woocommerce-validated woocommerce-invalid woocommerce-invalid-required-field");
				$('#billing_last_name_field').addClass("validate-required");
				$('#cedula_razon_social_field').slideUp();
				$('#billing_last_name_field .required').show();
			}
		}

		function is_cedula_juridica() {
			$('#cedula_numero').removeClass().addClass('input-text cedula_juridica');
			$('#billing_last_name_field').removeClass("validate-required woocommerce-validated woocommerce-invalid woocommerce-invalid-required-field");
			$('#cedula_razon_social_field').addClass("validate-required");
			$('#cedula_razon_social_field').slideDown();
			$('#cedula_razon_social_field .optional, #billing_last_name_field .required').hide();
		}

		$("#cedula_numero.cedula_fisica").mask("0-0000-0000", {
			placeholder: '1-2345-6789',
			selectOnFocus: true
		});

		$("#cedula_numero.cedula_juridica").mask("0-000-000000", {
			placeholder: '1-234-567890',
			selectOnFocus: true
		});
	}

	/**
	** Custom WC input quantity functionality
	**/
	$(window).on('load', function() {		
		if($('.woocommerce-cart-form input.qty').length) {
			check_input_qty();
		}

		if($('.single-product').length) {
			check_stock($(".quantity input.qty"));
			check_max_attr($('.single-product input.qty'));
		}
	});

	$(document).on('click', '.quantity-up', function (e) {
		input_qty = $(this).parents('.input-container').find('.qty');

		if (check_stock(input_qty)) {
			input_qty.val(function (i, cur_val) {
				return ++cur_val;
			});

			input_qty.change();
		}

		return false;
	});

	$(document).on('click', '.quantity-down', function (e) {
		input_qty = $(this).parents('.input-container').find('.qty');
		input_qty.val(function (i, cur_val) {
			return cur_val > 1 ? --cur_val : 0;
		});

		input_qty.change();

		return false;
	});

	$(document).on('focus', 'input.qty', function() {
		$(this).select();
	});

	$(document).on('change blur keyup', 'input.qty', function() {
		var _this = $(this);
		setTimeout(function() {
			check_stock(_this);
		}, 50);
	});

	function enable_qty_input(pos, input) {
		input.parent().find('.quantity-' + pos).removeClass('quantity-disabled');
	}

	function disable_qty_input(pos, input) {
		input.parent().find('.quantity-' + pos).addClass('quantity-disabled');
	}

	function check_stock(_this) {
		var val = parseFloat(_this.val());
		var max_stock = parseFloat(_this.attr('max'));
		var min_stock = (_this.attr('min') > 0) ? parseFloat(_this.attr('min')) : 1;
		var current = parseFloat(_this.data('qty'));

		if(val <= max_stock || val > min_stock) {
			enable_qty_input('down', _this);
			enable_qty_input('up', _this);
		}		

		if(val == max_stock || isNaN(max_stock)) {
			disable_qty_input('up', _this);
		}

		if(val == 1) {
			disable_qty_input('down', _this);
		} else if(val > max_stock) {
			_this.val(min_stock);
			_this.trigger('change');
			_this.select();
		}

		return true;
	}

	/**
	** Update cart amount
	**/
	$(document.body).on('updated_cart_totals', function () {
		check_input_qty();
	});
	check_input_qty();

	$(document).on("woocommerce_variation_select_change", ".variations_form", function () {
		setTimeout(function() {
			check_stock($(".quantity input.qty"));
		}, 80);
	});

	$(document).on('success_modal', function () {
		$(".xoo-qv-images .wpis-popup").remove();
		check_stock($(".xoo-qv-summary .quantity input.qty"));
	});

	function check_input_qty() {
		$(".woocommerce-cart-form input.qty").each(function () {
			var _this = $(this);

			_this.data("qty", _this.val());
			check_stock(_this);
			check_max_attr(_this);
		});
	}

	function check_max_attr(_this) {
		if(_this.attr('max') == "") {
			_this.attr('max', _this.val());
		}
	}
	

/**
 ** Login / Register
 **/


// Mostar formulario de Regsitro
$(document).on('click', '#registro-btn', function (e) {
    e.preventDefault();
    cuenta_registro(this);
});

// Mostar formulario de Login
$(document).on('click', '#login-btn', function (e) {
    e.preventDefault();
    cuenta_login(this);
});


/*
 **  Menu desplegable de mi cuenta logeado
 **/
$(document).on('click', '.toggle-account', function () {
    $(this).toggleClass('open');
    $(".menu-edit-account").toggle();
});


$(document).on('click', 'body', function (e) {
    if (!$(e.target).closest('.cuenta-wrapper').length && $('.menu-edit-account').is(':visible')) {
        $('.menu-edit-account').hide();
        $(".toggle-account").removeClass('open');
    }
});


// LOGIN
function cuenta_login(target) {
    $('#register-woocommerce').fadeOut("slow", function () {
        $('#login-woocommerce').fadeIn("slow", function () {
            $("html, body").animate({scrollTop: 0}, 300);
        });
    });
}
// REGISTRO
function cuenta_registro(target) {
    $('#login-woocommerce').fadeOut("slow", function () {
        $('#register-woocommerce').fadeIn("slow", function () {
            $("html, body").animate({scrollTop: 0}, 300);
        });
    });
}
	/**
	** Reset variations
	**/
	$(document).on('click', '.reset_variations', function() {
		$(this).parents('table.variations').find('select.woo-variation-raw-type-info option.enabled').prop('selected', true).trigger('change');
		
		return false;
	});
	
    /**
     ** Internal selects to select2
     **/
    function runSelect2(selector) {
        $(selector).select2({
            width: '100%',
            minimumResultsForSearch: Infinity
        });
    }
    runSelect2("select:not(.hide, .hidden, .country_select)");

    /**
     ** Select2 methods select
     **/
    function change_select_shipping_to_select2() {
        $(
            ".select-select2, select.shipping_methods, .woocommerce-checkout select.shipping_method, select#shipping-pickup-store-select"
            ).select2({
            width: '100%',
            minimumResultsForSearch: Infinity
        });
    }
    change_select_shipping_to_select2();

    /**
     ** After update checkout
     **/
    $(document.body).on("update_checkout, updated_checkout", function () {
        change_select_shipping_to_select2();
    });

    /**
     ** Update cart amount
     **/
    $(document.body).on("updated_cart_totals", function () {
        change_select_shipping_to_select2();
    });

    /**
     ** Placeholder to select2 input search
     **/
    function placeholder_input_search(selector) {
        setTimeout(function () {
            $(selector).on("select2:open", function () {
                $(".select2-search__field").attr(
                    "placeholder",
                    $(this)
                    .find("option:first")
                    .text()
                    );
            });
            $(selector).on("select2:close", function () {
                $(".select2-search__field").attr("placeholder", null);
            });
        }, 20);
    }
    placeholder_input_search(
        "select.country_to_state, select.state_select, select.city_select"
        );

    /**
     ** Ejecuta el Select2 en el Quickview
     **/
    $(document).on("success_modal", function () {
        if (!$(".xoo-qv-panel").hasClass("current-modal")) {
            runSelect2(".xoo-qv-summary select:not(.hide)");
        }
    });

}(jQuery));
