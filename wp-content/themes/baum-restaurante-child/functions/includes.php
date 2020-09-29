<?php
/**
** Custom & override functions
**/
require get_stylesheet_directory() . '/functions/wc/wc-custom-functions.php';
require get_stylesheet_directory() . '/functions/wp-fonts.php';
require get_stylesheet_directory() . '/functions/wp-sidebars.php';
require get_stylesheet_directory() . '/functions/wp-customizer.php';
require get_stylesheet_directory() . '/functions/wp-baum-sociales.php';
require get_stylesheet_directory() . '/functions/wp-misc.php';
//require get_stylesheet_directory() . '/functions/wc/wc-product-details.php';

/**
** Partial
**/
//require get_stylesheet_directory() . '/functions/partials/sidebar-panels.php';

/**
** Checkout page
**/
//require get_stylesheet_directory() . '/functions/wc/wc-checkout-options.php';
// 
/**
** WP & WC Performance
**/
//require get_stylesheet_directory() . '/functions/wc/wc-performance.php';


/**
** Share products
**/
require get_stylesheet_directory() . '/functions/wp-share-posts.php';

/**
** Notifications
**/
//require get_stylesheet_directory() . '/functions/wc/wc-notifications.php';

/**
** Custom widgets
**/
require get_stylesheet_directory() . '/functions/wp-widgets.php';

/**
** Visual Composer customization
**/
/*require get_stylesheet_directory(). '/functions/vc-elements/custom-elements.php';

if( function_exists('vc_set_shortcodes_templates_dir') ){ 
	vc_set_shortcodes_templates_dir( get_stylesheet_directory() . '/functions/vc-elements/vc-templates' );
}

/**
** wishlist Customization
**/
//require get_stylesheet_directory() . '/functions/wc/wishlist-customizations.php';