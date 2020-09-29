<?php
/**
** Change GreenPay Payment Gateway title
**/
function baumchild_wc_baum_greenpay_gateway_title($title, $gateway_id) {
	if ($gateway_id == 'baum_greenpay')
		return __('Tarjeta de crédito o débito', 'baumchild');

	return $title;
}
add_filter('woocommerce_gateway_title', 'baumchild_wc_baum_greenpay_gateway_title', 10, 2);

/**
** Payment section title in Checkout page
**/
function baumchild_payment_section_title() {
	?>
	<h3 id="order_payment_heading" class="checkout-payment-title"><?php echo __('Forma de pago', 'baumchild'); ?></h3>
	<?php
}
add_action('woocommerce_checkout_order_review', 'baumchild_payment_section_title');

/**
** Subscribe link after email field in Checkout form
**/
function baumchild_account_checkout_form_inline() {
	$label = __('Quiero recibir ofertas y noticias en mi correo.', 'baumchild');
	?>
	<script type="text/javascript">
		jQuery(function($) {
			<?php if(class_exists('MC4WP_MailChimp')) : ?>
				$('.woocommerce-checkout #billing_email_field').append('<span class="subscribe-field"><label class="checkbox"><input type="checkbox" name="mc4wp-subscribe" value="1" /><?php echo $label ?></label></span>');
			<?php endif; ?>
		});
	</script>
	<style type="text/css">
		.woocommerce .form-row[id*="cedula"] .woocommerce-input-wrapper label:first-of-type {
			margin-right: 10px;
		}

		.woocommerce #cedula_razon_social_field {
			display: none;
		}
	</style>
	<?php
}
add_action('woocommerce_after_checkout_form', 'baumchild_account_checkout_form_inline');
add_action('woocommerce_after_edit_account_address_form', 'baumchild_account_checkout_form_inline');

/**
** Enable/Disable field in shipping calculator & checkout
**/
add_filter('woocommerce_enable_order_notes_field', '__return_false');
add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');

/**
** Manage billing fields in checkout page
**/
function baumchild_woocommerce_billing_fields($fields) {
	// Unset billing fields - Set hidden class
	$fields['billing_country']['class'] = (!baumchild_check_countries_quantity()) ? array('d-none') : array('');
	$fields['billing_company']['class'] = array('d-none');
	$fields['billing_postcode']['required'] = baumchild_input_is_require();

		
	// Order billing fields
	$fields['billing_phone'] = array(
		'class' => array('form-row', 'form-row-first'),
		'clear' => true,
		'label' => __('Phone', 'woocommerce'),
		'priority' => 25,
		'placeholder' => '8888-8888',
		'required' => true,
		'type' => 'tel',
		'maxlength' => 8
	);
	
	$fields['billing_email'] = array(
		'class' => array('form-row', 'form-row-last'),
		'clear' => true,
		'label' => __('Email', 'woocommerce'),
		'placeholder' => 'Email',
		'priority' => 26,
		'required' => true
	);

	// New field
	// $fields['billing_entrega'] = array(
	// 	'id' => 'datepicker',
	// 	'class' => array('form-row', 'form-row-first', 'datepicker'),
	// 	'clear' => true,
	// 	'label' => __('Fecha de entrega', 'baumchild'),
	// 	'placeholder' => __('Día Mes, año', 'woocommerce'),
	// 	'priority' => 27,
	// 	'type' => 'text',
	// 	'required' => true
	// );

	$fields['_shipping_waze'] = array(
		'id' => 'text',
		'class' => array('form-row', 'form-row-wide'),
		'clear' => true,
		'label' => sprintf(__('Link de Waze %sBuscar%s', 'baumchild'), '<a href="https://www.waze.com/es/livemap/" class="font-italic btn-link" target="_blank">', '</a>'),
		// 'placeholder' => __('Día Mes, año', 'woocommerce'),
		'priority' => 55,
		'type' => 'text',
		'required' => false
	);

	return $fields;
}
add_filter('woocommerce_billing_fields', 'baumchild_woocommerce_billing_fields');

/**
** Check allowed countries quantity
**/
function baumchild_check_countries_quantity() {
	if(count(get_option('woocommerce_specific_allowed_countries')) > 1) {
		return true;
	}

	return false;
}

/**
** Manage address field in checkout page
**/
function baumchild_woocommerce_default_address_fields($fields) {
	$fields['first_name']['priority'] = 10;
	$fields['last_name'] = array(
		'class' => array('form-row', 'form-row-last'),
		'label' => __('Apellidos (Si es persona física)', 'baumchild'),
		'priority' => 20,
		'required' => (!isset($_POST['cedula_tipo']) || $_POST['cedula_tipo'] == 'cedula_fisica') ? true : false
	);

	$fields['address_1'] = array(
		'class' => array('form-row', 'form-row-wide'),
		'clear' => true,
		'label' => __('Dirección exacta', 'baumchild'),
		'priority' => 50,
		'required' => baumchild_input_is_require(),
		'type' => 'textarea',
	);
	
	$fields['address_2']['class'] = array('d-none');
	$fields['state']['required'] = baumchild_input_is_require();
	$fields['state']['class'][] = 'form-row-first';
	$fields['state']['priority'] = 40;
	$fields['city']['required'] = baumchild_input_is_require();
	$fields['city']['class'][] = 'form-row-last';
	$fields['city']['priority'] = 42;

	if(!baumchild_check_countries_quantity()) {
		$fields['country']['class'] = array('d-none');
		$fields['postcode']['class'] = array('d-none');
	}

	return $fields;
}
add_filter('woocommerce_default_address_fields', 'baumchild_woocommerce_default_address_fields', 30);

/**
** Checkout form fields
**/
function baumchild_tipo_cedulas($key = '') {
	$cedulas = array(
		'cedula_fisica' => __('Cédula Física', 'baumchild'),
		'cedula_juridica' => __('Cédula Jurídica', 'baumchild')
	);

	if(!empty($key)) {
		return $cedulas[$key];
	} else {
		return $cedulas;
	}
}

function baumchild_checkout_fields($checkout_fields) {
	// "billing", "shipping", "account", "order"
	global $woocommerce;

	/**
	** New checkout element to array: "cedula"
	**/
	$checkout_fields['cedula'] = array(
		'cedula_tipo' => array(
			'class' => array('form-row', 'form-row-first'),
			'label' => __('Tipo de cédula', 'baumchild'),
			'priority' => 10,
			'type' => 'select',
			'default' => 'cedula_juridica',
			'options' => baumchild_tipo_cedulas(),
			'required' => true,
		),
		'cedula_numero' => array(
			'class' => array('form-row', 'form-row-last'),
			'clear' => true,
			'label' => __('Número de cédula', 'baumchild'),
			'placeholder' => __('102340567', 'baumchild'),
			'priority' => 20,
			'type' => 'text',
			'required' => true,
		),
		'cedula_razon_social' => array(
			'class' => array('form-row', 'form-row-wide'),
			'clear' => true,
			'label' => __('Razón Social', 'baumchild'),
			'placeholder' => __('', 'baumchild'),
			'priority' => 30,
			'type' => 'text',
			'required' => (!isset($_POST['cedula_tipo']) || $_POST['cedula_tipo'] == 'cedula_juridica') ? true : false
		)
	);

	/**
	** Order field
	** Removed on Red Rubies - 18-08-2020 - KMA
	**/

	/** Remove field validation on wc_pickup_store shipping **/
	$shipping_methods = array('wc_pickup_store');
	$chosen_methods = WC()->session->get('chosen_shipping_methods');
	$chosen_shipping = $chosen_methods[0];

	if(in_array($chosen_shipping, $shipping_methods)) {
		unset($checkout_fields['billing']['billing_address_1']['validate']);
		unset($checkout_fields['billing']['billing_city']['validate']);
		unset($checkout_fields['billing']['billing_state']['validate']);
	}

	if ( get_option( 'woocommerce_registration_generate_password' ) == 'no' ) {
		$checkout_fields['account']['account_password'] = array(
			'class' => array('form-row', 'form-row-first'),
			'label' => __('Contraseña', 'baumchild'),
			'placeholder' => _x('Contraseña', 'placeholder', 'baumchild'),
			'required' => false,
			'type' => 'password',
		);

		$checkout_fields['account']['account_confirm_password'] = array(
			'class' => array('form-row', 'form-row-last'),
			'label' => __('Confirmar Contraseña', 'baumchild'),
			'placeholder' => _x('Confirmar Contraseña', 'placeholder', 'baumchild'),
			'required' => false,
			'type' => 'password',
		);
	}

	return $checkout_fields;
}
add_filter('woocommerce_checkout_fields', 'baumchild_checkout_fields', 10, 1);

/**
** Check required fields before allow checkout to proceed.
**/
function baumchild_checkout_custom_validation($posted, $errors) {
	$checkout = WC()->checkout;
	if (!is_user_logged_in() && ( $checkout->must_create_account || ! empty( $posted['createaccount']))) {
		if(strcmp( $posted['account_password'], $posted['account_confirm_password']) !== 0) {
			wc_add_notice(__('Contraseñas no coinciden.', 'baumchild'), 'error');
		}
	}

	if(!empty($_POST['cedula_numero']) && strlen($_POST['cedula_numero']) < 9) {
		wc_add_notice(__('<strong>Número de cédula</strong> debe contener mínimo 9 dígitos.', 'baumchild'), 'error');
	}

	if(!(preg_match('/^[0-9]{4}-[0-9]{4}$/', $_POST['billing_phone']))) {
		$errors->add('validation', __('Teléfono debe contener 8 dígitos', 'baumchild'));
		// wc_add_notice(__('Teléfono debe contener 8 dígitos.', 'baumchild'), 'error');
	}
	
	if(!empty($_POST['billing_celular']) && !(preg_match('/^[0-9]{4}-[0-9]{4}$/', $_POST['billing_celular']))) {
		wc_add_notice(__('Celular debe contener 8 dígitos.', 'baumchild'), 'error');
	}
	
	if(empty($_POST['_shipping_waze'])) {
		wc_add_notice(__('El enlace de Waze es requerido para el envío.', 'baumchild'), 'error');
	}
}
add_action('woocommerce_after_checkout_validation', 'baumchild_checkout_custom_validation', 10, 2);

/**
** Set order by fields key
** This function solves an error in checkout fields order, WC 3.5
** Function baumchild_set_order_fields_by_key unused since Baum WC Starter 1.0.3 and WC 3.5.3
** 09-01-2018
**/

/**
** Validate Shipping methods for required fields
**/
function baumchild_input_is_require() {
	global $woocommerce;

	$shipping_methods = array('wc_pickup_store');
	$chosen_methods = WC()->session->get('chosen_shipping_methods');
	$chosen_shipping = $chosen_methods[0];

	if (in_array($chosen_shipping, $shipping_methods) && is_checkout()) {		
		return false;
	}

	return true;
}

/**
** Message before password fields in checkout page
**/
function baumchild_account_message($checkout) {
	if ( $checkout->get_checkout_fields( 'account' ) ) :
		$create_password_message = !empty(get_theme_mod('baumchild_create_password_message')) ? '<p>' . get_theme_mod('baumchild_create_password_message') . '</p>' : '';
		?>
		<div class="create-account-message">
			<strong><?php echo sprintf(__('Create una cuenta y recordaremos estos datos para tu próxima compra.', 'baumchild'), get_bloginfo( 'name', 'display' )) ?></strong>
			<?php echo $create_password_message; ?>
		</div>
		<?php
	endif;
}
add_action('woocommerce_before_checkout_registration_form', 'baumchild_account_message');

/**
** Cedula fields to checkout form
** 26-12-2018
**/
function baumchild_before_checkout_billing_form() {
	$fields = WC()->checkout->get_checkout_fields('cedula');

	foreach($fields as $key => $field) {
		woocommerce_form_field($key, $field, get_user_meta(get_current_user_id(), $key, true));
	}
}
add_action('woocommerce_before_checkout_billing_form', 'baumchild_before_checkout_billing_form');
add_action('woocommerce_before_edit_address_form_billing', 'baumchild_before_checkout_billing_form');

/**
** Checkout new fields array
**/
function baumchild_checkout_new_fields($key = '') {
	// "billing", "shipping", "account", "order"
	$new_fields = array(
		'billing' => array(
			'billing_celular' => __('Celular', 'baumchild')
		),
		'shipping' => array(
			'_shipping_waze' => __('Link de Waze', 'baumchild')
		),
		'cedula' => array(
			'cedula_tipo' => __('Tipo de Cédula', 'baumchild'),
			'cedula_numero' => __('Número de Cédula', 'baumchild'),
			'cedula_razon_social' => __('Razón Social', 'baumchild')
		),
		'others' => array(
			'_billing_entrega' => __('Fecha de entrega', 'baumchild')
		)
	);

	if(!empty($key) && !empty($new_fields[$key])) {
		return $new_fields[$key];
	} else {
		return $new_fields;
	}
}

/**
** Manage new order meta fields
**/
function baumchild_update_wc_custom_fields($order_id) {
	$user_id = (is_user_logged_in()) ? get_current_user_id() : false;
	foreach (baumchild_checkout_new_fields() as $field_key => $fields) {
		switch ($field_key) {
			case 'cedula':
				foreach ($fields as $key => $field) {
					if (!empty($_POST[$key])) {
						update_post_meta($order_id, $key, sanitize_text_field($_POST[$key]));

						if ($user_id)
							update_user_meta($user_id, $key, sanitize_text_field($_POST[$key]));
					}					
				}
				break;
			
			default:
				foreach ($fields as $key => $field) {
					if (!empty($_POST[$key])) {
						update_post_meta($order_id, $key, sanitize_text_field($_POST[$key]));
					}

					if ($key == '_shipping_waze' && $user_id) {
						update_user_meta($user_id, $key, sanitize_text_field($_POST[$key]));
					}
				}
				break;
		}
	}
}
add_action('woocommerce_checkout_update_order_meta', 'baumchild_update_wc_custom_fields');

/**
** Adding new fields to billing details, admin page
**/
function baumchild_custom_field_admin_order($order) {
	$order_id = $order->get_id();
	foreach (baumchild_checkout_new_fields() as $key => $fields) {
		foreach ($fields as $key => $field) {
			$value = get_post_meta($order_id, $key, true);
			switch ($key) {
				case 'billing_celular':
					$data = '<a href="tel:' . $value . '">' . get_post_meta($order_id, $key, true) . '</a>';
					break;

				case 'cedula_tipo':
					$data = baumchild_tipo_cedulas($value);
					break;
				
				default:
					$data = $value;
					break;
			}

			if(!empty($value)) :
				?>
				<p>
					<strong class="title"><?php echo $field . ':' ?></strong>
					<span class="data"><?= $data ?></span>
				</p>
				<?php
			endif;
		}
	}
}
add_action('woocommerce_admin_order_data_after_billing_address', 'baumchild_custom_field_admin_order', 20, 1);

/**
** Adding new fields to profile page
**/
function baumchild_custom_fields_user_profile($user) {
	?>
	<h2><?= __('Campos Personalizados', 'baumchild') ?></h2>
	<table class="form-table">
		<?php foreach (baumchild_checkout_new_fields() as $field_key => $fields) : ?>
			<?php foreach ($fields as $key => $field) : $field_value = get_user_meta($user->ID, $key, true); ?>
				<tr>
					<th><label for="<?= $key ?>"><?= $field; ?> </label></th>
					<td>
						<?php if($key == 'cedula_tipo') : ?>
							<select name="<?= $key ?>">
								<?php foreach(baumchild_tipo_cedulas() as $key => $tipo_cedula) : ?>
									<option value="<?= $key ?>" <?php selected($key, $field_value) ?>><?= $tipo_cedula ?></option>
								<?php endforeach; ?>
							</select>
						<?php else : ?>
							<input type="text" name="<?= $key ?>" value="<?php echo esc_attr($field_value) ?>" class="regular-text" />
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>
	<?php
}
add_action('show_user_profile', 'baumchild_custom_fields_user_profile');
add_action('edit_user_profile', 'baumchild_custom_fields_user_profile');

function baumchild_save_extra_custom_fields($user_id) {
	foreach (baumchild_checkout_new_fields('cedula') as $key => $field) {
		if (!empty($_POST[$key])) {
			update_user_meta($user_id, $key, sanitize_text_field($_POST[$key]));
		}
	}

	if(isset($_POST['billing_phone']))
		update_user_meta($user_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ));
}
add_action('personal_options_update', 'baumchild_save_extra_custom_fields');
add_action('edit_user_profile_update', 'baumchild_save_extra_custom_fields');
add_action('woocommerce_save_account_details', 'baumchild_save_extra_custom_fields');
add_action('woocommerce_customer_save_address', 'baumchild_save_extra_custom_fields');

/**
** Remove password strength validation
**/
function baumchild_remove_password_strength() {
	if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
		wp_dequeue_script( 'wc-password-strength-meter' );
	}
}
add_action('wp_print_scripts', 'baumchild_remove_password_strength', 100);

/**
** Adding custom data to billing_emails before order table
**/
function baumchild_email_before_order_table($order) {
	$order_id = $order->get_id();
	$customer_details = array(
		array(
			'label' => __('Nombre', 'baumchild'),
			'value' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()
		),
		array(
			'label' => __('Email', 'baumchild'),
			'value' => $order->get_billing_email()
		),
	);

	$cedula_tipo = get_post_meta($order_id, 'cedula_tipo', true);
	$cedula_razon_social = get_post_meta($order_id, 'cedula_razon_social', true);

	if (!empty(get_post_meta($order_id, 'cedula_numero', true))) {
		$customer_details[] = array('label' => __('N° de cédula', 'baumchild'), 'value' => get_post_meta($order_id, 'cedula_numero', true));
	}

	if (!empty($cedula_razon_social) && $cedula_tipo == 'cedula_juridica') {
		$customer_details[] = array('label' => __('Razón Social', 'baumchild'), 'value' => $cedula_razon_social);
	}

	if (!empty(get_post_meta($order_id, 'billing_celular', true))) {
		$customer_details[] = array('label' => __('Celular', 'baumchild'), 'value' => get_post_meta($order_id, 'billing_celular', true)); 
	}

	if (!empty(get_post_meta($order_id, '_billing_entrega', true))) {
		$customer_details[] = array('label' => __('Fecha de entrega', 'baumchild'), 'value' => date_i18n('j \d\e F, Y', strtotime(get_post_meta($order_id, '_billing_entrega', true)))); 
	}

	if (!empty(get_post_meta($order_id, '_shipping_waze', true))) {
		$customer_details[] = array('label' => __('Link de Waze', 'baumchild'), 'value' => get_post_meta($order_id, '_shipping_waze', true)); 
	}
	?>
	<div style="margin-bottom: 20px;">		
		<?php 
		if(!empty($customer_details)) :
			foreach ($customer_details as $key => $detail) :
				?>
				<span><strong><?php echo $detail['label'] ?><span class="colon">:</span></strong> <?php echo esc_html($detail['value']); ?></span><br/>
				<?php
			endforeach;
		endif;
		?>
	</div>

	<?php
		if ($order->needs_payment() && !$sent_to_admin) :
			if($order->get_payment_method() == 'bacs') {
				?>
					<p><a href="<?= esc_url( $order->get_view_order_url() ) ?>"><?= __('Enviar número de transferencia', 'baumchild') ?></a></p>
				<?php
			} else {
				?>
					<p><a href="<?= esc_url( $order->get_checkout_payment_url() ) ?>"><?= __('Pagar Pedido', 'baumchild') ?></a></p>
				<?php
			}
		?>
	<?php endif;
}
add_action('woocommerce_email_before_order_table', 'baumchild_email_before_order_table');

/**
** Adding cedula to order details page and thank you page
**/
function baumchild_order_details_after_customer_details($order) {
	$order_id = $order->get_id();
	$cedula = array();
	$cedula['tipo'] = get_post_meta($order_id, 'cedula_tipo', true);
	$cedula['numero'] = get_post_meta($order_id, 'cedula_numero', true);
	$cedula['razon_social'] = get_post_meta($order_id, 'cedula_razon_social', true);

	if(!empty($cedula['tipo'])) :
		?>
		<section class="woocommerce-cedula-details">
			<h2><?= __('Detalles de Cédula', 'baumchild') ?></h2>
			<div class="cedula-details">
				<?= baumchild_cedula_formatting($cedula) ?>
			</div>
		</section>
		<?php
	endif;
}
add_action('woocommerce_order_details_after_customer_details', 'baumchild_order_details_after_customer_details');
add_action('wcdn_after_addresses', 'baumchild_order_details_after_customer_details');

/**
** Adding cedula details to my-address template in account page
**/
function baumchild_after_address_details($customer_id) {
	$cedula = array();
	$cedula['tipo'] = get_user_meta($customer_id, 'cedula_tipo', true);
	$cedula['numero'] = get_user_meta($customer_id, 'cedula_numero', true);
	$cedula['razon_social'] = get_user_meta($customer_id, 'cedula_razon_social', true);

	if(!empty($cedula['tipo'])) :
		?>
		<div class="woocommerce-cedula-details">
			<?= baumchild_cedula_formatting($cedula) ?>
		</div>
		<?php
	endif;
}
add_action('baumchild_details_before_address_details', 'baumchild_after_address_details');

function baumchild_cedula_formatting($cedula) {
	ob_start();

	if(!empty($cedula)) {
		?>
		<p><strong><?= __('N° de cédula', 'baumchild') ?><span class="colon">:</span></strong> <?= $cedula['numero'] ?></p>
		<?php if(!empty($cedula['razon_social']) && $cedula['tipo'] == 'cedula_juridica') : ?>
			<p><strong><?= __('Razón Social', 'baumchild') ?><span class="colon">:</span></strong> <?= $cedula['razon_social'] ?></p>
		<?php
	endif;
	}

	return ob_get_clean();
} 

/**
** Showing addresses details to non logged in users
** This is hidden by default
**/
function baumchild_adding_customers_details_to_thankyou($order_id) {
	// Only for non logged in users
	if ( ! $order_id || is_user_logged_in() ) return;

	$order = wc_get_order($order_id);

	wc_get_template( 'order/order-details-customer.php', array('order' => $order ));
}
add_action('woocommerce_thankyou', 'baumchild_adding_customers_details_to_thankyou');

/**
** GreenPay Payment Method by Baum - Custom Description
**/
function baumchild_baum_greenpay_method_description($description) {
	return __('Pagá con tarjeta de crédito o débito por medio de GreenPay', 'baumchild');
}
add_filter('baum_greenpay_method_description', 'baumchild_baum_greenpay_method_description');

/**
** Checkout message after Billing form
**/
function baumchild_wc_after_checkout_billing_form() {
	ob_start();
	?>
	<p class="mb-4 small"><?= '*' . __('Despachamos únicamente para Costa Rica.', 'baumchild') ?></p>
	<?php
	echo ob_get_clean();
}
add_action('woocommerce_after_checkout_billing_form', 'baumchild_wc_after_checkout_billing_form');