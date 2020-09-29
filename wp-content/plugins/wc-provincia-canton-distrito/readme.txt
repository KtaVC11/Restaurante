=== WC Provincia Canton Distrito ===
Contributors: keylorcr
Donate link: https://www.paypal.me/keylorcr
Tags: eCommerce, e-commerce, woocommerce, costa rica, costa rica states, provincias, canton, distrito, central america
Requires at least: 4.7
Tested up to: 5.4.1
Stable tag: 1.1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to populate your custom states, cities, and postcodes for WooCommerce. It started working only for Costa Rica but now it is compatible with multi countries.

== Description ==
Manage your custom states, cities, and postcodes by countries from a .json file that can be moved from the plugin to your custom location.

Available into My account, Shipping calculator form, and the Checkout.


### Features And Options:
* Multicountry compatible.
* Postcode loaded from the selected location.
* Filters and actions are available.
* Also available for admin orders edition

== Installation ==

= Requieres WooCommerce =

1. Upload the plugin files to the `/wp-content/plugins/wc-prov-cant-dist` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Optional, go to the settings page from plugin listing with Settings link or go to Menu > Settings > WC Provincia-Canton-Distrito
4. Done.

== Frequently Asked Questions ==

= How do I set up the plugin? =
No configuration required. All locations are automatically loaded to state, city, and postcode fields. However, it has three configurations: The first one removes the plugin priority for fields state, city, and postcode and keeps using the WooCommerce priority. The second one hides the postcode on the Checkout page, shipping calculator form, and billing and shipping form pages. And the last one allows you to debug the js plugin file printing the .js instead of .min.js.

= Where is the data origin? =
The distribution of Country > State > City-District > Postcode is loaded from a .json file located on /assets/js/prov-cant-dist.json.

= Is it possible to override the data origin? =
Yes, it can be overridden from your custom theme directory using the filter wcpcd_prov_cant_dist_json.

= Is it possible to make it compatible with another country?
Now it is possible, simply override the prov-cant-dist.json file in your custom theme. Then you can add your country (based on WC country code) followed by states, cities-districts and postcodes.
```php
/**
** Adding custom json file locations from child theme
** WC Provincia-Canton-Distrito
**/
function kmchild_prov_cant_dist_json($json_file) {
    $json_file = get_stylesheet_directory_uri() . '/assets/js/prov-cant-dist.json';
    
    return $json_file;
}
add_filter('wcpcd_prov_cant_dist_json', 'kmchild_prov_cant_dist_json');
```


== Screenshots ==
1. Shipping calculator
2. Checkout
3. Provincias
4. Distritos búsqueda
5. Editar dirección
6. Detalle dirección
6. Opciones en Administrador de órdenes
7. Formato .json para país, estado, ciudad y código postal

== Changelog ==
= 1.2.4 =
* Fix: include_once instead of include for admin option in includes/wcpcd-class.php.
* Improvement: JS validation for selected Country/State/City in prov-cant-dist.js. Reported using theme stephanie-king.
* New: Settings options separated to includes/admin/wcpcd-settings.php file
* New: Testing JSON file locations in settings page
* Review: WC 4.3.1 compatibility

= 1.2.3 =
* Update: Filter wcpcd_city_field_placeholder in city_first_option in includes/wcpcd-class.php
* New: JS to enable provincia-canton-distrito-zipcode to admin order panel
* New: Admin order options in includes/wcpcd-admin.php
* New: Provincia, Cant. y Dist. for manual orders created
* Fix: JS functions and performance
* Update: Main json format using country key
* New: Compatibility with multi countries from .json and .js
* Move to EN lang

= 1.2.2 =
* URGENT Fix: Validation for deprecated function wpcd_get_provincias
* Update: Validation compatibility for WP 5.4 and WC 4.0.x

= 1.2.1 =
* Fix update

= 1.2 =
* Remove: WPCD_TEXTDOMAIN
* New: .pot file
* New: en_US translation
* Update: Prefix wpcd_ to wcpcd_

= 1.1.20 =
* Update: WC compatibility
* New: WPCD_TEXTDOMAIN
* New: Empty index.php to the plugin
* Update: Custom css class .hide-zipcode to hide zipcode in the Checkout
* New: Hook to wp_head to add custom styles to hide zipcode fields
* Remove: Action woocommerce_before_shipping_calculator to the method wpcd_shipping_calculator_styles
* New: Action and method wpcd_locations_allowed to check for valid locations for the plugin

= 1.1.19 =
* Fix: wpcd_file_exists now uses wp_remote_fopen to get data from json file

= 1.1.18 =
* New filter wpcd_prov_cant_dist_json to load json data file

= 1.1.17 =
* Trigger to update_checkout after city change

= 1.1.16 =
* Update in function wpcd_get_provincia_canton_distrito to eliminate file_get_contents function

= 1.1.15 =
* Update domain name in class wpcd-class.php

= 1.1.14 =
* Latest stable version

== Upgrade Notice ==
= 1.2.3 =
* Fix: JS functions and performance
* New: Compatibility with multi countries from .json and .js
* Others

= 1.1.20 =
* WC Compatibility

= 1.1.19 =
* Fix: wpcd_file_exists now uses wp_remote_fopen to get data from json file

= 1.1.18 =
* New filter wpcd_prov_cant_dist_json to load json data file

= 1.1.17 =
* Trigger to update_checkout after city change

= 1.1.16 =
* Update in function wpcd_get_provincia_canton_distrito to eliminate file_get_contents function

= 1.1.15 =
* Update domain name in class wpcd-class.php

= 1.1.14 =
* Latest stable version