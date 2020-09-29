jQuery(function($) {
	var inputs = [
		{
			obj: '#baum_greenpay-card-number',
			required: true
		},
		{
			obj: '#baum_greenpay-card-cvc',
			required: true
		},
		{
			obj: '#baum_greenpay-card-expiry',
			required: true
		},
		{
			obj: '#baum_greenpay-card-holder',
			required: true
		}
	];

	var the_field_by_id = function(id, value) {
		var the_field = $('.payment_box.payment_method_baum_greenpay').find('#' + id);
		if (value !== undefined && value != '') {
			if (the_field.length) {
				the_field.val(value);
			}
		} else {
			return false;
		}

		return true;
	}

	var success_callback = function(data, form) {
		var error = 0;
		var checkout_form = $('form.woocommerce-checkout, form#add_payment_method');
		
		if (form.length)
			checkout_form = form;

		if (!data.use_token) {
			// add a token to our hidden input field
			if (data.result != undefined && data.keyPairs != undefined) {
				if (!the_field_by_id('token_ld', data.result))
					error++;

				if (!the_field_by_id('token_lk', data.keyPairs))
					error++;
			}

			if (error == 0) {
				set_inputs_required();
			}
		}

		checkout_form.submit();
	};

	var token_request = function(form) {
		var card_number = $(inputs[0].obj),
			card_cvc = $(inputs[1].obj),
			card_expiry = $(inputs[2].obj),
			card_holder = $(inputs[3].obj),
			expiration_month = card_expiry.val().substring(0, 2),
			expiration_year = card_expiry.val().substring(5),
			encrypted_card = {
				use_token: true
			};

		$('#is-valid-cc').val('processing');
		if (
			$('.wc-saved-payment-methods').length == 0 || 
			( $('#wc-baum_greenpay-payment-token-new').length && $('#wc-baum_greenpay-payment-token-new').prop('checked') )
		) {
			var is_cc_valid = validate_card_fields([card_number, card_cvc, card_expiry, card_holder]);
			
			if (is_cc_valid) {
				var the_card = {
					cardName: card_holder.val(),
					cardExpirationMonth: {
						month: Number(expiration_month),
						year: expiration_year
					},
					cardNumber: card_number.val().split(' ').join(''),
					cardExpirationCVV: card_cvc.val()
				};
				var card_format = formatCard(the_card);

				if (JSON.parse(card_format).card.cardNumber != '') {
					encrypted_card = encryptCard(JSON.parse(bgp_params.get_public_key), card_format);
					encrypted_card.use_token = false;
				} else {
					$('#is-valid-cc').val();
				}
			} else {
				return false;
			}
		}

		success_callback(encrypted_card, form);
		
		return false;
	};

	var validate_card_fields = function(_inputs) {
		var error = 0;

		$.each(_inputs, function(index) {
			if ($(this).val() == '') {
				$(this).closest('.form-row').addClass('validate-required woocommerce-invalid woocommerce-invalid-required-field');
				error++;
			} else {
				$(this).closest('.form-row').removeClass('validate-required woocommerce-invalid woocommerce-invalid-required-field');
			}
		});

		return (error == 0) ? true : false;
	}

	$(document).on('click', '#place_order', function() {
		if ( 
			( $('#wc-baum_greenpay-payment-token-new').length && $('#wc-baum_greenpay-payment-token-new').prop('checked') ) || 
			( $('#payment_methods').length && $('#payment_methods').val() == 'baum_greenpay' ) || 
			( $('#payment_method_baum_greenpay').length && $('#payment_method_baum_greenpay').prop('checked') ) || 
			$('#add_payment_method').length ) {
			var form = $(this).closest('form');
			token_request(form);

			return false;
		}
	});

	function set_inputs_required() {
		$.each(inputs, function(index, input) {
			var field = $(input.obj);
			if (input.required && field.val() == '') {
				field.closest('.form-row').removeClass('woocommerce-validated').addClass('validate-required');
			}
		});
	}
	// set_inputs_required();

	/**
	** Toggle CC form or use a saved CC in the Checkout
	**/
	$(document).on('change', 'input.woocommerce-SavedPaymentMethods-tokenInput', function() {
		var _this = $(this);
		if (_this.prop('checked') && _this.val() == 'new') {
			$('#collapse-cc-form').slideDown();
		} else {
			$('#collapse-cc-form').slideUp();
		}
	});

	/**
	** Show CC form on Add payment method page
	**/
	if ($('#add_payment_method').length) {
		$('#wc-baum_greenpay-payment-token-new').prop('checked', true).trigger('change');
	}
});