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
