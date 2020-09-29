<?php

/**
** Replace image flipper plugin with custom functions
**/
function woocommerce_template_loop_secondary_product_thumbnail() {
	global $product;

	$attachment_ids = get_gallery_image_ids($product);

	if($attachment_ids) {
		$attachment_id = $attachment_ids[0];
		// var_dump($attachment_ids);

		for ($cont=0; $cont < 5; $cont++) { 
			if($attachment_ids[$cont] != 0) {
				$attachment_id = $attachment_ids[$cont];
				break;
			}
		}

		if($attachment_id != 0) {
			echo wp_get_attachment_image(
				$attachment_id,
				'shop_catalog',
				'',
				array(
					'class' => 'secondary-image attach_' . $attachment_id . ' attachment-shop-catalog wp-post-image wp-post-image--secondary'
				)
			);
		}
	}
}

function baumchild_check_product_gallery($classes) {
	global $product;

	$attachment_ids = get_gallery_image_ids($product);

	if($attachment_ids) {
		$classes[] = 'has-second-image';
	}

	return $classes;
}

function baumchild_desktop_only() {
	if(!wp_is_mobile()) {
		add_action('woocommerce_before_shop_loop_item_title' , 'woocommerce_template_loop_secondary_product_thumbnail');

		add_filter('post_class', 'baumchild_check_product_gallery');
	} else {
		remove_theme_support('wc-product-gallery-zoom');
		remove_theme_support('wc-product-gallery-lightbox');
		remove_theme_support('wc-product-gallery-slider');
	}
}
add_action('init', 'baumchild_desktop_only');

/** WooCommerce Compatibility Functions **/
function get_gallery_image_ids( $product ) {
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return;
	}

	if ( is_callable( 'WC_Product::get_gallery_image_ids' ) ) {
		return $product->get_gallery_image_ids();
	} else {
		return $product->get_gallery_attachment_ids();
	}
}