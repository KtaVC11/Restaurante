<?php 
/*
Plugin Name: Woo Product Images Slider edited by Baum
Plugin URI: https://baumdigital.com
Description: This plugin creates a attractive responsive slider/carousel for WooCommerce product gallery images with nicely lightbox effect. Edited by Baum based on DesirePress plugin: https://desirepress.com
Version: 1.1.0
Requires at least: 4.7
Tested up to: 5.4.1
WC requires at least: 3.0
WC tested up to: 4.1.0
Author: DesirePress & Keylor Mendoza A.
Author URI: https://baumdigital.com
License: GPL2
-------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

include_once('inc/settings.php');    // Include required settings

/**
 * Include JS Files 
 */
function wpis_enqueue_scripts() {
	if (!is_admin()) {	
		if ( class_exists( 'WooCommerce' ) && (is_product() || is_product_category() || is_shop() || is_front_page()) ) {
			// wp_enqueue_script('wpis-fancybox-js', plugins_url('assets/js/jquery.fancybox.js', __FILE__),array('jquery'),'1.0', true);
			// wp_enqueue_style('wpis-fancybox-css', plugins_url('assets/css/fancybox.css', __FILE__),'1.0', true);
			wp_enqueue_script('wpis-fancybox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js');
			wp_enqueue_style('wpis-fancybox-css', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css');

			// wp_enqueue_script('wpis-zoom-js', plugins_url('assets/js/jquery.zoom.min.js', __FILE__),array('jquery'),'1.0', true);
			wp_enqueue_script('wpis-zoom', '//cdnjs.cloudflare.com/ajax/libs/jquery-zoom/1.7.21/jquery.zoom.min.js');

			// wp_enqueue_script('wpis-slick-js', plugins_url('assets/js/slick.min.js', __FILE__),array('jquery'),'1.6.0', false);
			wp_enqueue_script('wpis-slick-js', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array('jquery'), '', false);

			// wp_enqueue_style('wpis-fontawesome-css', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css','1.0', true);
			wp_enqueue_style('wpis-front-css', plugins_url('assets/css/wpis-front.css', __FILE__),'1.0', true);
			wp_register_script('wpis-front-js', plugins_url('assets/js/wpis.front.js', __FILE__),array('jquery'),'1.0', true);
			
			$options = get_option('wpis_options');
			
			$wpis_options = array(
				'wpis_arrow' => $options['arrow'],
				'wpis_carrow' => $options['carrow'],
				'wpis_zoom' => $options['zoom'],
				'wpis_popup' => $options['popup'],
				'wpis_autoplay' => $options['autoplay'],
				'wpis_arrow_prev' => '<i class="slick-prev dashicons dashicons-arrow-left-alt2"></i>',
				'wpis_arrow_next' => '<i class="slick-next dashicons dashicons-arrow-right-alt2"></i>'
			);
			
			wp_localize_script( 'wpis-front-js', 'object_name', apply_filters('wpis_options_object', $wpis_options) );
			
			// Enqueued script with localized data.
			wp_enqueue_script( 'wpis-front-js' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'wpis_enqueue_scripts' ); 

// Call plugin loaded action
add_action('plugins_loaded','wpis_remove_woo_hooks');
function wpis_remove_woo_hooks() {
	remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	add_action('woocommerce_product_thumbnails', 'wpis_show_product_thumbnails', 20);
	add_action('woocommerce_before_single_product_summary', 'wpis_show_product_image', 5);

	if ( in_array('quick-view-woocommerce/xoo-quickview-main.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
		/* Quick view locations */
		add_action('xoo-qv-images', 'wpis_show_product_image', 20);
		remove_action('xoo-qv-images', 'xoo_qv_product_image', 20);
	}

	wpis_plugin_settings();
}

// Single Product Image
function wpis_show_product_image() {
	// Woocmmerce 3.0+ Slider Fix 
	require_once 'inc/product-image.php';
}

// Single Product Thumbnails 
function wpis_show_product_thumbnails() {
	// Woocmmerce 3.0+ Slider Fix 
	require_once 'inc/product-thumbnails.php';	
}

// get plugin version
function wpis_get_version(){
	if ( ! function_exists( 'get_plugins' ) )
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}