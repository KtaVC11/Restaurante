<?php
/**
** Add custom field to product detail
**/
function baumchild_output_custom_product_field() {
	global $product;
	$note_cost = get_field('additional_note_cost', 'option');
	// $note_cost = get_theme_mod('baumchild_additional_note_cost');
	$additional_notes = (function_exists('get_field') && !is_null(get_field('notas_adicionales'))) ? get_field('notas_adicionales') : false;

	if (empty($note_cost) || !$additional_notes) {
	    return;
	}
	?>
	<div class="badge-light col mb-3 p-3 product_note-field">
		<label for="add-note" class="custom-checkbox mb-0">
			<input type="checkbox" id="add-note" name="add_product_note" data-toggle="collapse" data-target="#collapse_message"><?= sprintf(__('Agregá una tarjeta con un mensaje personalizado para tu pedido por %s', 'baumchild'), '<strong>' . wc_price($note_cost) . '</strong>'); ?>
		</label>
		<div id="collapse_message" class="form-group collapse">
			<textarea name="product_note" class="form-control" cols="30" rows="5" placeholder="<?= __('Escribí el mensaje personalizado', 'baumchild') ?>"></textarea>
		</div>
	</div>
	<?php
}
add_action('woocommerce_after_add_to_cart_quantity', 'baumchild_output_custom_product_field', 20);

/**
** Showing additional product field
**/
function baumchild_add_custom_product_text_to_cart_item($cart_item_data, $product_id, $variation_id) {
	$custom_product_note = filter_input(INPUT_POST, 'product_note');
	$add_product_note = filter_input(INPUT_POST, 'add_product_note');
	if (!empty($custom_product_note) && isset($add_product_note)) {
		$cart_item_data['product_note'] = $custom_product_note;
	}

	return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'baumchild_add_custom_product_text_to_cart_item', 10, 3);

/**
** Display text in cart item line
**/
function baumchild_get_custom_product_text_to_cart_item($item_data, $cart_item) {
	if (!empty($cart_item['product_note'])) {
		$item_data[] = array(
			'key'     => '<strong>' . __('Mensaje personalizado', 'baumchild') . '</strong>',
			'value'   => wc_clean($cart_item['product_note']),
			'display' => '',
		);
	}

	return $item_data;
}
add_filter('woocommerce_get_item_data', 'baumchild_get_custom_product_text_to_cart_item', 10, 2);

/**
** Checkout save extra order line
**/
function baumchild_custom_product_field_to_order_items($item, $cart_item_key, $values, $order) {
	if (empty($values['product_note'])) {
		return;
	}
	$item->add_meta_data(__('Mensaje personalizado', 'baumchild'), $values['product_note']);
}
add_action('woocommerce_checkout_create_order_line_item', 'baumchild_custom_product_field_to_order_items', 10, 4);

/**
** Add cart fee cost based on notes
**/
function baumchild_calculate_notes_costs($cart) {
	// $custom_price = 400; // This will be your custom price  
	$note_cost = get_field('additional_note_cost', 'option');
	// $note_cost = get_theme_mod('baumchild_additional_note_cost');
	$notes = 0;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		if (isset($cart_item['product_note'])) {
			$notes++;
		}
	}

	if ($notes > 0 && !empty($note_cost)) {
		WC()->cart->add_fee(
			sprintf(
				_n(
					'Tarjeta con mensaje personalizado',
					'Tarjetas con mensaje personalizado x%d',
					$notes,
					'baumchild'
				),
				number_format_i18n($notes)
			),
			($note_cost * $notes),
			true,
			''
		);
	}
}
add_action('woocommerce_cart_calculate_fees', 'baumchild_calculate_notes_costs');
