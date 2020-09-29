<?php
/**
Plugin Name: WC Provincia-Canton-Distrito
Plugin URI: https://keylormendoza.com/woocommerce/wc-provincia-canton-distrito/
Description: This plugin allows you to populate your custom states, cities, and postcodes for WooCommerce. It started working only for Costa Rica but now it is compatible with multi countries.
Version: 1.2.4
Requires at least: 4.7
Tested up to: 5.4.2
WC requires at least: 3.0
WC tested up to: 4.3.1
Author: Keylor Mendoza A.
Author URI: https://www.keylormendoza.com
License: GPLv2
Text Domain: wc-prov-cant-dist
*/

if (! defined('ABSPATH')) { exit; }

if (!defined('WPCD_PLUGIN_FILE')) 
	define('WPCD_PLUGIN_FILE', plugin_basename(__FILE__));

/**
** Check if WooCommerce is active
**/
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	add_action('admin_notices', 'wcpcd_inactive_notice');
	return;
}

function wcpcd_inactive_notice() {
	if ( current_user_can( 'activate_plugins' ) ) :
		if ( !class_exists( 'WooCommerce' ) ) :
			?>
			<div id="message" class="error">
				<p>
					<?php
					printf(
						__('%s requires %sWooCommerce%s to be active.', 'wc-prov-cant-dist'),
						'<strong>WC Provincia-Canton-Distrito</strong>',
						'<a href="http://wordpress.org/plugins/woocommerce/" target="_blank" >',
						'</a>'
					);
					?>
				</p>
			</div>		
			<?php
		endif;
	endif;
}

include plugin_dir_path(__FILE__) . '/includes/wcpcd-class.php';

register_activation_hook(__FILE__, 'wcpcd_activation');
register_deactivation_hook(__FILE__, 'wcpcd_deactivation');


function wcpcd_activation() {
	// Set San Jose - CR as a default country/region on activation
	update_option('woocommerce_default_country', 'CR:SJ');
}

function wcpcd_deactivation() {
	// Set CR as a default country/region on deactivation
	update_option('woocommerce_default_country', 'CR');
}

/**
** WPCD Init
**/
function wcpcd_init() {
	$wc_prov_cant_dist = new WC_PROV_CANT_DIST();
}
add_action('init', 'wcpcd_init');

/**
** Language
**/
function wcpcd_language_init() {
	load_plugin_textdomain('wc-prov-cant-dist', false, dirname( plugin_basename(__FILE__) ) . '/languages/');
}
add_action('plugins_loaded', 'wcpcd_language_init');
