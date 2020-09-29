<?php

/**
 * * Add custom rules to htaccess
 * */
function baumchild_custom_htaccess_rules($rules)
{
    $custom_rules = "\n
	# BEGIN Baumchild
	<IfModule mod_rewrite.c>";

    $custom_rules .= "\n
	# Force HTTPS
	RewriteEngine on
	RewriteCond %{HTTP:X-Forwarded-Proto} !https
	RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]";

    $custom_rules .= "\n</IfModule>
	# END Baumchild";

    if (!is_baum_localhost()) :
        return $rules . $custom_rules;
    else :
        return $rules;
    endif;
}

// add_filter('mod_rewrite_rules', 'baumchild_custom_htaccess_rules', 5);

/**
 * * Update image sizes
 * * disable 768px image generation
 * */
function baumchild_wc_customize_image_sizes($sizes)
{
    unset($sizes['medium_large']);
    unset($sizes['large']);
    unset($sizes['post-thumbnail']);
    unset($sizes['thumbnail']);

    return $sizes;
}

add_filter('intermediate_image_sizes_advanced', 'baumchild_wc_customize_image_sizes');

/**
 * * Default logo
 * */
function baumchild_default_logo($position = null, $alt = false, $ext = 'svg')
{
    $logo_id = get_theme_mod('custom_logo');
    $logo_url = wp_get_attachment_image_url($logo_id, 'full', false);
    $is_alt = ($alt) ? '-alt' : '';

    if (!empty($position))
    {
        $logo_url = get_theme_mod('baumchild_logo_' . $position);
    }

    if (!$logo_url)
        $logo_url = baumchild_dir_uri() . '/assets/images/logo-default' . $is_alt . '.' . $ext;
    ?>
    <a class="logo" data-position="<?= $position ?>" href="<?php echo home_url('/') ?>" rel="home" itemprop="url">
        <img src="<?php echo $logo_url ?>" alt="<?php echo get_bloginfo('name', 'display') ?>" itemprop="logo" />
    </a>
    <?php
}

/**
 * * Admin login logo
 * */
function baumchild_admin_logo()
{
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full', false);

    if (!$logo_url)
        $logo_url = baumchild_dir_uri() . '/assets/images/logo-default.svg';
    ?> 
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?= $logo_url ?>);
            background-size: contain; 
            height: 120px;
            width: auto;
        }

        body.login div#login #wp-submit {
            -webkit-transition: all 120ms ease-in-out;
            -moz-transition: all 120ms ease-in-out;
            -ms-transition: all 120ms ease-in-out;
            -o-transition: all 120ms ease-in-out;
            transition: all 120ms ease-in-out;
            background: #f5f5f5;
            border: 2px solid #BF1931;
            color: #BF1931;
            box-shadow: none;
            height: auto;
            line-height: 1.5;
            padding-bottom: 5px;
            padding-top: 5px;
            text-shadow: none;
        }

        body.login div#login #wp-submit:hover {
            background: #BF1931;
            border: 2px solid #BF1931;
            color: #FFF;
        }

        /* CUSTOM THEME */
        body.login {
            background: #f5f5f5;
        }

        .login #backtoblog, .login #nav {
            text-align: center;
        }
        #loginform {
            border-radius: 5px;
        }
        .login #login_error, .login .message, .login .success{
            border-left: 4px solid #BF1931 !important;
        }

        .login #backtoblog a,
        .login #nav a {
            color: #BF1931 !important;
        }
        .login #backtoblog a:hover,
        .login #nav a:hover {
            color: #BF1931 !important;
        }
    </style>
    <?php
}

add_action('login_enqueue_scripts', 'baumchild_admin_logo');

/**
 * * Baum Logo
 * */
function baum_logo_branding()
{
    ?>
    <!-- Baum Logo -->
    <div class="logo-baum">
        <a href="https://www.baumdigital.com">
            <figure>
                <?php echo baumchild_get_styles_data(get_stylesheet_directory_uri() . '/assets/images/logo-baum-animado.svg'); ?>
            </figure>
        </a>
    </div>
    <?php
}

add_action('baum_logo_branding', 'baum_logo_branding');

/**
 * * Favicon
 * */
function baumchild_favicon_image()
{
    $favicon = baumchild_dir_uri() . '/assets/images/favicon.png';
    if (!has_site_icon()):
        ?>
        <link rel="shortcut icon" href="<?php echo esc_attr($favicon); ?>" > 
        <?php
    endif;
}

add_action('wp_head', 'baumchild_favicon_image');
add_action('admin_head', 'baumchild_favicon_image');

/**
 * * Manage header slider
 * */
function baumchild_set_slider()
{
    if (class_exists('RevSlider'))
    {
        $slider_alias = get_theme_mod('baumchild_site_slider');
        if (!empty($slider_alias) && is_front_page()) :
            ?>
            <div class="site-header-slider">
                <?php putRevSlider($slider_alias, "homepage"); ?>
            </div>
            <?php
        endif;
    }
}

add_action('baumchild_slider_header', 'baumchild_set_slider');

/**
 * * Change upload folder to root based on custom post type
 * */
function baumchild_post_type_upload_dir($upload)
{
	if (!isset($_REQUEST['post_id']))
		return $upload;

    $accepted_cpt = array(
        'product' => array(
            'folder_name' => 'products'
        )
    );

    $id = $_REQUEST['post_id'];
    $parent = get_post($id)->post_parent;

    foreach ($accepted_cpt as $cpt => $value)
    {
        // Check the post-type of the current post
        if ($cpt == get_post_type($id) || $cpt == get_post_type($parent))
            $upload['subdir'] = '/' . $value['folder_name'] . $upload['subdir'];
    }

    $upload['path'] = $upload['basedir'] . $upload['subdir'];
    $upload['url'] = $upload['baseurl'] . $upload['subdir'];

    return $upload;
}

add_filter('upload_dir', 'baumchild_post_type_upload_dir');

/**
 * * Defer parsing of Javascript
 * */
if (!is_admin())
{

    function baumchild_defer_parsing_js($url)
    {
        // Expresión regular que valida si la url termina en .js
        // Si no termine en ello, devuelve la url tal cual
        if (!preg_match("/([a-zA-Z0-9\s_\\.\-\(\):])+(.js)$/i", $url))
        {
            return $url;
        }
        // Expresión regular que valida si la url termina en jquery.js
        // Si no termine en ello, devuelve la url tal cual
        /**
         * * Fix: 13-11-19 - KMA
         * * No valida URLs con subdominio
         * */
        // if ( preg_match("/([a-zA-Z0-9\s_\\.\-\(\):]*)+(jquery.js)$/i", $url) ){
        if (preg_match("/([a-zA-Z0-9\s_\\:]*)+(jquery.js)$/i", $url))
        {
            return $url;
        }
        return "$url' defer='defer";
    }

    add_filter('clean_url', 'baumchild_defer_parsing_js', 11, 1);
}

/**
** Body classes
**/
function baumchild_custom_body_classes($classes) {
	if (get_option('woocommerce_enable_myaccount_registration') === 'yes' && !is_user_logged_in() && is_account_page()) {
		$classes[] = 'woocommerce-login-register';
	}

    if (baumchild_has_header_fixed()) {
        $classes[] = 'baumchild-header-fixed';
    }

	return $classes;
}
add_filter('body_class', 'baumchild_custom_body_classes');

/**
** Validate if header is fixed
** Return true for theme using header fixed
**/
function baumchild_has_header_fixed() {
    return !is_baum_localhost();
}