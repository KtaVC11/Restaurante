<?php
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Custom Options by Baum',
		'menu_title'	=> 'Custom Options by Baum',
		'menu_slug' 	=> 'baumchild-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

	// function baumchild_get_datepicker_params() {
	// 	$timezone = date_default_timezone_get();
	// 	$excluir_dias = get_field('excluir_dias', 'option');
	// 	$params = array(
	// 		'hora_limite' => get_field('hora_limite', 'option'),
	// 		'rango_de_dias' => get_field('rango_de_dias', 'option'),
	// 		'excluir_dias' => $excluir_dias,
	// 	);

	// 	if (have_rows('excluir_fechas', 'option')) {
	// 		$excluir_fechas = array();
	// 		while (have_rows('excluir_fechas', 'option')) {
	// 			the_row();
	// 			$excluir_fechas[] = get_sub_field('fecha_excluida', 'option');
	// 		}
	// 		$params['excluir_fechas'] = $excluir_fechas;
	// 	}

	// 	date_default_timezone_set('America/Costa_Rica');
	// 	$current_time = date_i18n('H:i:s'); // '10:55:00';
	// 	$current_date = date('Y-m-d H:i:s');
	// 	if (strtotime($current_time) <= strtotime($params['hora_limite']))
	// 		$params['fecha_inicio'] = date('Y-m-d', strtotime($current_date . ' + 1 days'));
	// 	else
	// 		$params['fecha_inicio'] = date('Y-m-d', strtotime($current_date . ' + 2 days'));

	// 	$fecha_inicio = $params['fecha_inicio'];
	// 	$total_days = 0;
	// 	$_days = $_fechas = array();
	// 	for ($cont = 0; $cont < $params['rango_de_dias']; $cont++) { 
	// 		$day_number = date('w', strtotime($fecha_inicio));
	// 		$_days[] = array(
	// 			'number' => $day_number,
	// 			'date' => $fecha_inicio
	// 		);
	// 		$total_days++;
	// 		if (in_array($fecha_inicio, $excluir_fechas)) {
	// 			$cont--;
	// 		} elseif ($excluir_dias) {
	// 			if (in_array($day_number, $excluir_dias)) {
	// 				$params['excluir_fechas'][] = $fecha_inicio;
	// 				$cont--;
	// 			} else {
	// 				$_fechas[] = $fecha_inicio;
	// 			}
	// 		}

	// 		$fecha_inicio = date('Y-m-d', strtotime($fecha_inicio . ' + 1 days'));
	// 	}
	// 	$params['fecha_inicial'] = (isset($_fechas)) ? date('Y-m-d', strtotime($_fechas[0] . ' + 1 days')) : $params['fecha_inicio'];

	// 	if ($total_days > 0)
	// 		$params['fecha_fin'] = date('Y-m-d', strtotime($params['fecha_inicio'] . ' + ' . ($total_days - 1) . ' days')); // Revisar el -1

	// 	$params['validate'] = array(
	// 		strtotime($current_time) <= strtotime($params['hora_limite']),
	// 		$_days,
	// 		$_fechas,
	// 		date('w', strtotime($params['fecha_fin']))
	// 	);

	// 	date_default_timezone_set($timezone);

	// 	return $params;
	// }


	function baumchild_wc_add_order_item_totals($total_rows, $order, $tax_display) {
		$dates = baumchild_get_estimate_delivery_date('j \d\e F, Y');

		// if ($order->has_shipping_method($this->id)) {
			foreach ($total_rows as $key => $row) {
				$new_rows[$key] = $row;
				if ($key == 'shipping') {
					$new_rows['delivery'] = array(
						'label' => __('Estimado a entregar', 'baumchild'),
						'value' => $dates['fecha_inicio']
					);
				}
			}
			$total_rows = $new_rows;
		// }

		return $total_rows;
	}
	add_filter('woocommerce_get_order_item_totals', 'baumchild_wc_add_order_item_totals', 10, 3);

	/**
	** Add info to Checkout after Shipping options
	**/
	function baumchild_wc_review_order_after_shipping() {
		$dates = baumchild_get_estimate_delivery_date('j \d\e F, Y');

		if (isset($dates['fecha_inicio'])) {
			?>
			<tr class="estimate-delivery">
				<th><?= __('Estimado a entregar', 'baumchild') ?></th>
				<td><?= $dates['fecha_inicio'] ?></td>
			</tr>
			<?php
		}
	}
	add_action('woocommerce_review_order_after_shipping', 'baumchild_wc_review_order_after_shipping');

	function baumchild_get_estimate_delivery_date($return_format = 'Y-m-d') {
		$current_time = date_i18n('H:i:s');
		$current_date = date_i18n('Y-m-d H:i:s');
		$seccion_categoria = get_field('seccion_categoria', 'option');
		$params = array(
			'hora_limite' => get_field('hora_limite', 'option'),
			'excluir_dias' => $excluir_dias
		);

		if (strtotime($current_time) <= strtotime($params['hora_limite']))
			$temp_fecha_inicio = date_i18n('Y-m-d', strtotime($current_date . ' + 1 days'));
		else
			$temp_fecha_inicio = date_i18n('Y-m-d', strtotime($current_date . ' + 2 days'));

		// Check for category in cart
		if (isset($seccion_categoria['categoria'])) {
			$cat_in_cart = false;
			$params['excluir_categorias'] = $seccion_categoria['categoria'];
			$params['entrega_categorias'] = $seccion_categoria['periodo_entrega'];

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( has_term( $params['excluir_categorias'], 'product_cat', $cart_item['product_id'] ) ) {
					$cat_in_cart = true;
					break;
				}
			}

			if ($cat_in_cart) {
				$temp_fecha_inicio = date_i18n('Y-m-d', strtotime($current_date . ' + ' . $params['entrega_categorias'] . ' days'));
			}
		}

		if (have_rows('excluir_fechas_envio', 'option')) {
			// $excluir_fechas = array();
			while (have_rows('excluir_fechas_envio', 'option')) {
				the_row();
				$excluir_fechas = get_sub_field('fecha_excluida', 'option');
				if (strtotime($excluir_fechas) == strtotime($temp_fecha_inicio)) {
					$temp_fecha_inicio = date_i18n('Y-m-d', strtotime($temp_fecha_inicio . ' + 1 days'));
				}
				// var_dump(array(
				// 	// 'date_invalid_count' => $date_invalid_count,
				// 	'excluir_fechas' => $excluir_fechas,
				// 	'temp_fecha_inicio' => $temp_fecha_inicio
				// ));
			}
			// $params['excluir_fechas'] = $excluir_fechas;
		}

		$params['fecha_inicio'] = date_i18n($return_format, strtotime($temp_fecha_inicio));

		// $fecha_inicio = $params['fecha_inicio'];
		// $total_days = 0;
		// $_days = $_fechas = array();
		// for ($cont = 0; $cont < $params['rango_de_dias']; $cont++) { 
		// 	$day_number = date('w', strtotime($fecha_inicio));
		// 	$_days[] = array(
		// 		'number' => $day_number,
		// 		'date' => $fecha_inicio
		// 	);
		// 	$total_days++;
		// 	if (in_array($fecha_inicio, $excluir_fechas)) {
		// 		$cont--;
		// 	} elseif ($excluir_dias) {
		// 		if (in_array($day_number, $excluir_dias)) {
		// 			$params['excluir_fechas'][] = $fecha_inicio;
		// 			$cont--;
		// 		} else {
		// 			$_fechas[] = $fecha_inicio;
		// 		}
		// 	}

		// 	$fecha_inicio = date('Y-m-d', strtotime($fecha_inicio . ' + 1 days'));
		// }
		// $params['fecha_inicial'] = (isset($_fechas)) ? date('Y-m-d', strtotime($_fechas[0] . ' + 1 days')) : $params['fecha_inicio'];
		var_dump($params);

		return $params;
	}
}
