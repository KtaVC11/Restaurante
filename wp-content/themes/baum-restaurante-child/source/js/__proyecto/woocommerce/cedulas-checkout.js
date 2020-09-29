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
