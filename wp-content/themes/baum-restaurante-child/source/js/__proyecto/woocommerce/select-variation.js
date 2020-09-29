	/**
	** Reset variations
	**/
	$(document).on('click', '.reset_variations', function() {
		$(this).parents('table.variations').find('select.woo-variation-raw-type-info option.enabled').prop('selected', true).trigger('change');
		
		return false;
	});
	