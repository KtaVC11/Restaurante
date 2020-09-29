<?php
/**
** Declare WC env variables
**/


if(!defined('WC_LIST_INTERVAL')) {
	define('WC_LIST_INTERVAL', !empty(get_theme_mod( 'baumchild_products_per_page' )) ? get_theme_mod( 'baumchild_products_per_page' ) : 12);
}


/**
** Remove WC Product Tabs, Cross Sell (Cart Page), Upsell & default Related Products
**/
remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs');
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);


/**
** Listing product structure
**/
function baumchild_open_product_image_container() {
	echo '<div class="product-wrapper">';
    echo '<div class="product-image-container">';
    echo '<span class="d-flex align-items-center justify-content-center">';
}
add_action('woocommerce_before_shop_loop_item', 'baumchild_open_product_image_container', 5);

function baumchild_close_a_tag() {
	echo "</a>";
}
add_action('woocommerce_before_shop_loop_item_title', 'baumchild_close_a_tag', 11);

function baumchild_close_div_tag() {
	echo "</span></div><!-- .product-image-container -->";
}
add_action('woocommerce_before_shop_loop_item_title', 'baumchild_close_div_tag', 12);

function baumchild_open_new_a_tag() {
	global $product;
	$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
	echo '<div class="product-title-container">';
}
add_action('woocommerce_shop_loop_item_title', 'baumchild_open_new_a_tag', 5);

function baumchild_close_product_title_tag() {
	echo "</div><!-- .product-title-container -->";
}
add_action('woocommerce_after_shop_loop_item', 'baumchild_close_product_title_tag', 10);

function baumchild_close_product_wrapper_tag() {
	echo "</div><!-- .product-wrapper -->";
}
add_action('woocommerce_after_shop_loop_item', 'baumchild_close_product_wrapper_tag', 12);

/**
** CRC Currency
**/
function baumchild_currency_symbol( $currency_symbol, $currency ) {
	switch( $currency ) {
		case 'CRC':
			$currency_symbol = '¢';
		break;
	}
	return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'baumchild_currency_symbol', 10, 2);

/**
** Remove button add to cart
**/

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

/**
** Add container to WC Pages
**/
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);


/**
** Remove sku
**/

add_filter( 'wc_product_sku_enabled', '__return_false' );

add_filter('woocommerce_sale_flash', 'hide_sale_flash');
function hide_sale_flash() {
	return false;
}

/* Remove WooCommerce Styles */
// add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
** Remove Showing X Results
**/
add_action( 'init', 'bbloomer_delay_remove_result_count' );
  
function bbloomer_delay_remove_result_count() {
   remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
   remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
}


/**
** Remove the sorting button
**/

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


/**
 **Remove Breadcrumbs-La jerarquia del menu */
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);


function disable_site_header( $return ) {
    if ( is_search() ) {
        $return = false;
    }
    return $return;
}
add_filter( 'ocean_display_header', 'disable_site_header' );


/*function disable_site_header( $return ) {
    if ( is_content_product() ) {
        $return = false;
    }
    return $return;
}
add_filter( 'ocean_display_header', 'disable_site_header' );*/

//remove_action( 'woocommerce_before_content-product_loop', 'storefront_woocommerce_pagination', 30 );



