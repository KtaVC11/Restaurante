<?php
/**
** Add a new column to payment methods table
** woocommerce/includes/wc-account-functions.php
**/
function baum_greenpay_wc_account_payment_methods_columns($columns) {
	$new_columns = array(
		'card_holder'  => __('Titular de la tarjeta', WC_BAUM_GREENPAY_TEXTDOMAIN)
	);

	return array_merge($new_columns, $columns);
}
add_filter('woocommerce_account_payment_methods_columns', 'baum_greenpay_wc_account_payment_methods_columns');

/**
** Get card holder from token
** woocommerce_account_payment_methods_column_$column_id
** woocommerce/templates/myaccount/payment-methods.php
**/
function baum_greenpay_wc_account_payment_methods_column_card_holder($method) {
	if (isset($method['token_id'])) {
		$token = WC_Payment_Tokens::get($method['token_id']);
		echo (!empty($card_holder = $token->get_meta('card_holder'))) ? $card_holder : apply_filters('baum_greenpay_default_card_holder', 'N/A');
	}
}
add_action('woocommerce_account_payment_methods_column_card_holder', 'baum_greenpay_wc_account_payment_methods_column_card_holder');

/**
** Add token_id to payment method list item in account page
** woocommerce/includes/wc-account-functions.php
**/
function baum_greenpay_wc_payment_methods_list_item($list_by_type_key, $payment_token) {
	return array_merge(array(
		'token_id' => $payment_token->get_id()
	), $list_by_type_key);
}
add_filter('woocommerce_payment_methods_list_item', 'baum_greenpay_wc_payment_methods_list_item', 10, 2);

/**
** Add Pay button in thank you page in case of order has Pending payment status
**/
function baum_greenpay_wc_thankyou($order_id) {
	$wcbgp = new WC_Baum_Greenpay();
	$order = wc_get_order($order_id);
	$transaction_id = $order->get_transaction_id();

	if ($order->needs_payment() && $order->get_payment_method() == $wcbgp->id) {
		?>
		<div class="baum-greenpay-order-actions">
			<span><?= sprintf(__('Su orden se encuentra en estado: %s', WC_BAUM_GREENPAY_TEXTDOMAIN), '<strong>' . esc_html(wc_get_order_status_name($order->get_status())) . '</strong>'); ?></span>
			<div class="order-actions">
				<?php
					$actions = wc_get_account_orders_actions( $order );
					if ( ! empty( $actions ) ) {
						foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
							echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
						}
					}
				?>
			</div>
		</div>
		<?php
	}
}
add_action('woocommerce_thankyou_baum_greenpay', 'baum_greenpay_wc_thankyou');
