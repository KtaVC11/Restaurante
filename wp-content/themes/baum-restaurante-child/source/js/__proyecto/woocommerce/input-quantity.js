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
	