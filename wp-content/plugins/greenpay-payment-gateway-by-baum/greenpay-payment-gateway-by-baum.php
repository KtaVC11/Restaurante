<?php
/**
 * Plugin Name: GreenPay Payment Gateway by Baum
 * Plugin URI:  https://baumdigital.com/
 * Description: Take card payments on your store using GreenPay. This is based on GreenPay Payment Gateway plugin and other resources
 * Version:     1.0.7
 * Requires at least: 4.4
 * Tested up to: 5.4.2
 * WC requires at least: 3.0
 * WC tested up to: 4.2.2
 * Author:      Keylor Mendoza A.
 * Author URI:  https://baumdigital.com/
 * Text Domain: baum-greenpay-payment
 * Domain Path: /languages
 * License:     GPLv2
 */
if (!defined('ABSPATH')) exit;

if (!defined('WC_BAUM_GREENPAY_VERSION'))
	define('WC_BAUM_GREENPAY_VERSION', '1.0.7');

if (!defined('WC_BAUM_GREENPAY_ID'))
	define('WC_BAUM_GREENPAY_ID', 'baum_greenpay');

if (!defined('WC_BAUM_GREENPAY_PLUGIN_FILE'))
	define('WC_BAUM_GREENPAY_PLUGIN_FILE', plugin_basename(__FILE__));

if (!defined('WC_BAUM_GREENPAY_PLUGIN_PATH'))
	define('WC_BAUM_GREENPAY_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));

if (!defined('WC_BAUM_GREENPAY_PLUGIN_URL'))
	define('WC_BAUM_GREENPAY_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('WC_BAUM_GREENPAY_TEXTDOMAIN'))
	define('WC_BAUM_GREENPAY_TEXTDOMAIN', 'baum-greenpay-payment');

register_activation_hook(__FILE__, 'baum_greenpay_class_init');

/**
** Load misc. and other files
**/
include WC_BAUM_GREENPAY_PLUGIN_PATH . '/includes/baum-greenpay-endpoints.php';
include WC_BAUM_GREENPAY_PLUGIN_PATH . '/includes/baum-greenpay-misc.php';

/**
** Baum Greenpay Payment Gateway init
**/
function baum_greenpay_class_init() {
	include WC_BAUM_GREENPAY_PLUGIN_PATH . '/includes/class-baum-greenpay.php';
	if (! class_exists('WC_Baum_Greenpay')) return;

	add_filter('woocommerce_payment_gateways', 'baum_greenpay_class');
	function baum_greenpay_class($methods) {
		$methods[] = 'WC_Baum_Greenpay';

		return $methods;
	}

	$site_option_version = get_site_option('baum_greenpay_version');
	if ($site_option_version != WC_BAUM_GREENPAY_VERSION || empty($site_option_version)) {
		baum_greenpay_install();
		baum_greenpay_table_tokens();
	}
}
add_action('plugins_loaded', 'baum_greenpay_class_init');

/**
** Create table and save payment gateway version
** Table to save all processed transactions
**/
function baum_greenpay_install() {
	global $wpdb;

	$table_bitacora = $wpdb->prefix . 'bgp_transacciones';

	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE IF NOT EXISTS " . $table_bitacora . " (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`persona` int(11) NOT NULL DEFAULT '1',
		`monto` decimal(11,2) NOT NULL DEFAULT '0.00',
		`orden` int(11) DEFAULT NULL,
		`metodo_pago` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
		`authcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		`transactionid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		`error` longtext COLLATE utf8_unicode_ci,
		`fecha_creacion` datetime DEFAULT NULL,
		`estado` int(11) DEFAULT NULL,
		UNIQUE (`id`)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);

	update_option('baum_greenpay_version', WC_BAUM_GREENPAY_VERSION);
}

/**
** Token extra table
** Save all tokens used in the Checkout. Not all tokens could be stored in WC by the User
**/
function baum_greenpay_table_tokens() {
	global $wpdb;

	$table_bitacora = $wpdb->prefix . 'bgp_tokens';

	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE IF NOT EXISTS " . $table_bitacora . " (
		`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`user_id` int(11) NOT NULL DEFAULT '1',
		`token` varchar(255) DEFAULT NULL,
		`description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		`estado` varchar(45) DEFAULT NULL,
		`fecha_creacion` datetime DEFAULT NULL,
		UNIQUE (`id`)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

/**
** Add setting links to WC
**/
function baum_greenpay_admin_submenu() {
	add_submenu_page(
		'woocommerce', __('Greenpay Payment Gateway by Baum', WC_BAUM_GREENPAY_TEXTDOMAIN), __('Greenpay Payment Gateway by Baum', WC_BAUM_GREENPAY_TEXTDOMAIN), 'manage_options', admin_url('admin.php?page=wc-settings&tab=checkout&section=' . WC_BAUM_GREENPAY_ID)
	);
}
add_action('admin_menu', 'baum_greenpay_admin_submenu');

/**
** Add Settings action links
**/
function baum_greenpay_links($links) {
	$plugin_links = array(
		'<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=' . WC_BAUM_GREENPAY_ID) . '">' . __('Settings', WC_BAUM_GREENPAY_TEXTDOMAIN) . '</a>',
	);

	// Merge our new link with the default ones
	return array_merge($plugin_links, $links);    
}
add_filter('plugin_action_links_' . WC_BAUM_GREENPAY_PLUGIN_FILE, 'baum_greenpay_links');
