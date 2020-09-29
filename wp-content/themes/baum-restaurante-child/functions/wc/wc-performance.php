<?php
/**
** WC Custom image sizes
**/
function baumchild_wc_image_size_single($size) {
	return array(
		'width'  => 550,
		'height' => 420,
		'crop'   => 0,
	);
}
add_filter('woocommerce_get_image_size_single', 'baumchild_wc_image_size_single');

function baumchild_wc_image_size_thumbnail($size) {
	return array(
		'width'  => 350,
		'height' => 320,
		'crop'   => 1,
	);
}
add_filter('woocommerce_get_image_size_thumbnail', 'baumchild_wc_image_size_thumbnail');

function baumchild_wc_image_size_gallery_thumbnail($size) {
	return array(
		'width'  => 150,
		'height' => 150,
		'crop'   => 1,
	);
}
add_filter('woocommerce_get_image_size_gallery_thumbnail', 'baumchild_wc_image_size_gallery_thumbnail');

/**
** Remove image srcset
**/
// add_filter('max_srcset_image_width', create_function('', 'return 1;'));

/**
** WooCommerce script cleaner
** wc-cart-fragments removed to show minicart on all pages
** 05-2020 - KMA
**/
function baumchild_wc_script_cleaner() {
	if (!is_woocommerce() && !is_cart() && !is_checkout()) {
		$dequeue_scripts = array('fancybox', 'jquery-blockui', 'jquery-payment', 'jquery-placeholder', 'jqueryui', 'prettyPhoto', 'prettyPhoto-init', 'selectWoo', 'wc-add-payment-method', 'wc-add-to-cart', 'wc-add-to-cart-variation', 'wc-cart', 'wc-checkout', 'wc-chosen', 'wc-credit-card-form', 'wc-lost-password', 'wc-single-product', 'wc-single-product', 'wc_price_slider', 'woocommerce');
		$dequeue_style = array('woocommerce-smallscreen', 'woocommerce_chosen_styles', 'woocommerce_fancybox_styles', 'woocommerce_prettyPhoto_css');

		foreach ($dequeue_scripts as $script) {
			wp_dequeue_script($script);
		}

		foreach ($dequeue_style as $style) {
			wp_dequeue_style($style);
		}

		wp_deregister_script( 'selectWoo' );
	}
}
add_action('wp_enqueue_scripts', 'baumchild_wc_script_cleaner', 11);

/**
** WooCommerce style cleaner
**/
function baumchild_wc_style_cleaner() {
	if (is_product()) {
		$dequeue_style = array('berocket_lmp_style', 'chosen-drop-down', 'contact-form-7', 'flash_sale_shortcodes', 'flipclock-master-cssss', 'pw-gift-grid-style', 'pw-gift-layout-style', 'pw-gift-lightbox-css', 'pw-gift-slider-style', 'woof', 'woof_color_html_items');

		foreach ($dequeue_style as $style) {
			wp_dequeue_style($style);
		}
	}
}
add_action('wp_enqueue_scripts', 'baumchild_wc_style_cleaner', 11);