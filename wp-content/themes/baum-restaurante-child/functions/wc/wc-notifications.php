<?php
/**
** Override Added to cart notification
** /wp-content/plugins/woocommerce/includes/wc-cart-functions.php
**/
function wc_add_to_cart_message_html_filter($message, $products) {
	foreach($products as $product_id => $quantity) {
		$product = wc_get_product( $product_id );
		$titles[] = $product->get_title();

		$titles = array_filter( $titles );
		$added_text = sprintf( __('%s ha sido agregado al carrito con éxito.', '%s ha sido agregado al carrito.', sizeof( $titles ), 'woocommerce'), wc_format_list_of_items($titles));

		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			$return_to = apply_filters( 'woocommerce_continue_shopping_redirect', wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false ) : wc_get_page_permalink( 'shop' ) );
			$message   = sprintf( '%s <a href="%s" class="button wc-forward">%s</a>', esc_html( $added_text ), esc_url( $return_to ), esc_html__( 'Continue shopping', 'woocommerce' ) );
		} else {
			$message = sprintf( '%s <a href="%s" class="button wc-forward">%s</a>',
				esc_html( $added_text ),
				esc_url( wc_get_page_permalink( 'cart' ) ),
				esc_html__( 'View Cart', 'woocommerce' ));
		}
	}

	return $message;
}
add_filter('wc_add_to_cart_message_html', 'wc_add_to_cart_message_html_filter', 10, 2);