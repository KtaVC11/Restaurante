<?php
/**
** Including the url of fonts googleapis 
**/
function baumchild_fonts_url() {
	$fonts_url = '';
	$fonts = array();
	$subsets = 'latin,latin-ext';

	if ('off' !== _x('on', 'Google fonts: on or off', 'baumchild')) {
		$fonts = array('Montserrat:wght@300;400;600;700','Sofia'); 
	}

	if ($fonts) {
		$fonts_url = add_query_arg(array(
			'family' => implode('&family=', $fonts),
			'display' => 'swap',
			'subset' => urlencode($subsets),
		), 'https://fonts.googleapis.com/css2');
	}

	return $fonts_url;
}

/**
** Custom theme fonts
**/
function baumchild_enqueue_fonts() {}
add_action('wp_enqueue_scripts', 'baumchild_enqueue_fonts');

/**
** Font face and inline styles
**/
function baumchild_get_inline_styles() {
	$theme_uri = get_stylesheet_directory_uri();
	$ajax_loader = baumchild_dir_uri() . '/assets/images/preloader.svg';

	if (isset($_SERVER['HTTP_USER_AGENT']) && ( (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') !== false) || (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))) {
		$ajax_loader = baumchild_dir_uri() . '/assets/images/preloader.gif';
	}
	
	$css = ' ';

    

	if (file_exists(get_stylesheet_directory() . '/assets/fonts/font-baum/style.css'))
		$css .= wp_remote_fopen(baumchild_dir_uri() . '/assets/fonts/font-baum/style.css');
    
	return $css;
}
