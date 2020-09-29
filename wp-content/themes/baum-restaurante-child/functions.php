<?php
/**
** Declare child theme from twentynineteen
**/
function baumchild_child_enqueue_styles() {
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'baumchild_child_enqueue_styles');

/**
** Theme directory URI
**/
function baumchild_dir_uri() {
	return get_stylesheet_directory_uri();
}

/**
** Custom functions
**/
require get_stylesheet_directory() . '/functions/includes.php';

/**
** Manage menus
** Tweentynineteen menus added
**/
function baumchild_manage_menu_locations() {
	add_theme_support('custom-logo', array(
		'width' => 250,
		'height' => 250,
		'flex-width' => true,
		'flex-height' => true,
	));

	$menus = array('menu-1', 'primary', 'footer', 'social');

	foreach ($menus as $key => $menu) {
		unregister_nav_menu($menu);
	}

	register_nav_menus(array(
		'primary' => __('Menú Principal', 'baumchild'),
		'secondary' => __('Menú Secundario', 'baumchild'),
	));
}
add_action('after_setup_theme', 'baumchild_manage_menu_locations', 20);

/**
** Enqueue and dequeue CSS and JS
** Updated - 31-01-2020 - KMA
**/
function baumchild_enqueue_styles() {
	$min = (!is_baum_localhost() && !is_user_logged_in()) ? '.min' : '';

	$arrays_css = array('select2', 'bootstrap_css', 'font_awesome', 'mmenu');
	$arrays_js = array('jquery', 'jquery_mask', 'mmenu', 'bootstrap_js', 'select2', 'matchHeight', 'jquery_easing', 'litepicker');

	wp_enqueue_style('bootstrap_css', '//stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
	wp_enqueue_style('font_awesome', '//use.fontawesome.com/releases/v5.6.3/css/all.css');
	wp_enqueue_style('mmenu', '//cdnjs.cloudflare.com/ajax/libs/jQuery.mmenu/8.5.13/mmenu.min.css');
    wp_enqueue_style('baumchild-fonts', baumchild_fonts_url(), $arrays_css, null);
	
	
	/* Adding inline styles */
	wp_add_inline_style('bootstrap_css', baumchild_print_inline_css());
    wp_register_script('jquery_easing', '//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js', array('jquery'));
    wp_register_script('jquery_mask', '//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.13/jquery.mask.min.js', array('jquery'));
    wp_register_script('mmenu', '//cdnjs.cloudflare.com/ajax/libs/jQuery.mmenu/8.5.13/mmenu.min.js', array('jquery'));    
	wp_register_script('matchHeight', '//cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.2/jquery.matchHeight-min.js', array('jquery'));
	wp_enqueue_script('bootstrap_js', '//stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js');
    wp_enqueue_script('litepicker', '//cdn.jsdelivr.net/npm/litepicker/dist/js/main.js', array('jquery')); // litepicker -> datepicker
	wp_enqueue_script('baumchild-script', baumchild_dir_uri() . '/assets/js/all' . $min . '.js', $arrays_js, null, true);
    
    if (file_exists(get_stylesheet_directory() . '/assets/js/plugin.js'))
        wp_enqueue_script('baumchild-plugins', baumchild_dir_uri() . '/assets/js/plugin' . $min . '.js', array('jquery'), null, true);

	// Declare it to use ajax
	$localize_script = array(
		'ajax_url' => admin_url('admin-ajax.php')
	);
	
	if (function_exists('baumchild_get_datepicker_params')) {
		$localize_script = wp_parse_args(baumchild_get_datepicker_params(), $localize_script);
	}

	wp_localize_script('baumchild-script', 'baumchild_ajax', $localize_script);
}
add_action('wp_enqueue_scripts', 'baumchild_enqueue_styles');

function baumchild_dequeue_styles() {
	$styles = array('font-awesome', 'tinvwl-font-awesome', 'woocommerce-smallscreen', 'woof', 'genericons', 'turbotabs', 'twentynineteen-print-style', 'wps_bootstrap', 'wps_fontawesome', 'twentynineteen-style', 'woocommerce-general');

	foreach ($styles as $style) {
		wp_dequeue_style($style);
		wp_deregister_style($style);
	}

	if(is_baum_localhost()) {
		wp_enqueue_style('baumchild-style', baumchild_dir_uri() . '/assets/css/theme.css', array(), null);
	}
    
	wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'baumchild_dequeue_styles', 99);

function baumchild_dequeue_script() {
	$scripts = array('turbotabs');

	foreach ($scripts as $script) {
		wp_dequeue_script($script);
	}
}
add_action('wp_print_scripts', 'baumchild_dequeue_script', 100);

/**
** CSS Inline
**/
function baumchild_print_inline_css() {
	$theme_uri = get_stylesheet_directory_uri();
	$css_path = $theme_uri . '/assets/css/theme.min.css';

	$css = baumchild_get_inline_styles();

	if(!is_baum_localhost()) {
		$css .= baumchild_get_styles_data($css_path);
	}
	
	return $css;
}

function baumchild_get_styles_data($file) {
	// wp_remote_fopen replaces old curl structure to get custom data from file
	$data = wp_remote_fopen($file);

	return $data;
}

/**
** Check URL from localhost
**/
function is_baum_localhost() {
    if (preg_match('/localhost/', site_url())) {
        return true; 
    } else {
        return false; 
    }
}
