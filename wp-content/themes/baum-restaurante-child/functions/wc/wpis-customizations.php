<?php
/**
** Customizations by Baum
** Advanced WooCommerce Product Gallery Slider
**/
// function baumchild_wpis_reorder_scripts() {
//     if (is_product_category() || is_shop() || is_front_page()) {
//         wp_enqueue_script('wpis-slick-js', plugins_url('woo-product-images-slider/assets/js/slick.min.js'), array('jquery'), '1.6.0', false);
//         wp_enqueue_style('wpis-front-css', plugins_url('woo-product-images-slider/assets/css/wpis-front.css'), '1.0', true);
//     }
// }
// add_action('wp_enqueue_scripts', 'baumchild_wpis_reorder_scripts');

/**
** Template override
**/
// function baumchild_wc_product_image() {
// 	require get_stylesheet_directory() . '/woocommerce/product-image.php';
// }
// add_action('woocommerce_before_single_product_summary', 'baumchild_wc_product_image', 5);
// remove_action('woocommerce_before_single_product_summary', 'wpis_show_product_image', 10);

// add_action('xoo-qv-images', 'baumchild_wc_product_image', 20);
// remove_action('xoo-qv-images', 'xoo_qv_product_image', 20);

// function baumchild_wc_product_thumbnail() {
// 	require get_stylesheet_directory() . '/woocommerce/product-thumbnails.php';
// }
// add_action('woocommerce_product_thumbnails', 'baumchild_wc_product_thumbnail', 20);
// remove_action('woocommerce_product_thumbnails', 'wpis_show_product_thumbnails', 20);