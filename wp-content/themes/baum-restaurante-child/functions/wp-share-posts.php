<?php
/**
** Share options single product
** To Do: Call this file in /functions/includes.php
** and use these share options with action, shortcode or custom actions in WooCommerce (woocommerce_share)
**
** Extension to show custom share options for posts (post, product, cpt, etc)
** v1.0
**/
function baumchild_product_sharing() {
	$prod_share_options = baumchild_posts_share_options_data();
	
	if(!empty($prod_share_options)) :
		?>
		<div class="align-items-center d-flex post-sharing">
			<?php if(!empty($share_title = apply_filters('get_option_baumchild_share_title', get_theme_mod('baumchild_share_title')))) : ?>
				<span class="post-sharing-title"><?= $share_title ?><span class="colon">:</span></span>
			<?php endif; ?>

			<ul class="list-inline post-share-options">
				<?php
					foreach ($prod_share_options as $prod_share_option => $prod_share_data) {
						?>
						<li class="align-middle list-inline-item post-share-option share-<?= strtolower($prod_share_data['label']) ?>">
							<a title="<?= esc_attr($prod_share_data['label']) ?>" href="<?= $prod_share_data['value']; ?>" <?= (isset($prod_share_data['target'])) ? ' target="' . $prod_share_data['target'] . '"' : '' ?>><i class="<?= $prod_share_data['icon'] ?>"></i></a>
						</li>
						<?php
					}
				?>
			</ul>
		</div>
		<?php
	endif;
}
add_action('woocommerce_share', 'baumchild_product_sharing');
add_action('baumchild_share', 'baumchild_product_sharing');

/**
** Share options array with data
**/
function baumchild_posts_share_options_data() {
	$prod_share_options = baumchild_posts_share_options();
	unset($prod_share_options['baumchild_share_title']);
	foreach ($prod_share_options as $prod_share_option => $prod_share_data) {
		if(!empty(get_theme_mod($prod_share_option))) {
			$prod_share_options[$prod_share_option]['value'] = baumchild_share_links(get_the_ID(), strtolower($prod_share_data['label']));
		} else {
			unset($prod_share_options[$prod_share_option]);
		}
	}	

	return $prod_share_options;
}

/**
** Share options data
**/
function baumchild_posts_share_options() {
	$settings = array(
		'baumchild_share_title' => array(
			'label' => __('Título', 'baumchild'),
			'description' => 'A mostrarse en la lista de opciones a compartir',
			'input_type' => 'text',
		),
		'baumchild_share_facebook' => array(
			'label' => __('Facebook', 'baumchild'),
			'description' => '',
			'input_type' => 'checkbox',
			'icon' => 'fab fa-facebook-f',
			'target' => '_blank',
		),
		'baumchild_share_twitter' => array(
			'label' => __('Twitter', 'baumchild'),
			'description' => '',
			'input_type' => 'checkbox',
			'icon' => 'fab fa-twitter',
			'target' => '_blank',
		),
		'baumchild_share_pinterest' => array(
			'label' => __('Pinterest', 'baumchild'),
			'description' => '',
			'input_type' => 'checkbox',
			'icon' => 'fab fa-pinterest-p',
			'target' => '_blank',
		),
		'baumchild_share_whatsapp' => array(
			'label' => __('Whatsapp', 'baumchild'),
			'description' => '',
			'input_type' => 'checkbox',
			'icon' => 'fab fa-whatsapp',
			'target' => '_blank',
			'mobile_only' => false
		),
		'baumchild_share_email' => array(
			'label' => __('Email', 'baumchild'),
			'description' => '',
			'input_type' => 'checkbox',
			'icon' => 'fas fa-envelope'
		),
	);

	return $settings;
}

function baumchild_customize_share_options($wp_customize) {
    $wp_customize->add_section('baumchild_share_products_section', array(
        'title' => __('Compartir', 'baumchild'),
        'priority' => 2,
        'capability' => 'edit_theme_options',
        'description' => __('Opciones disponibles en el detalle de artículo o producto', 'baumchild'),
        'panel' => 'baumchild_panel'
    ));

    foreach (baumchild_posts_share_options() as $prod_share_option => $prod_share_data) {
		$wp_customize->add_setting($prod_share_option, array(
			'default' => '',
			'capability' => 'edit_theme_options',
			'type' => 'theme_mod',
			'transport' => 'refresh',
		));

		$wp_customize->add_control(new WP_Customize_Control($wp_customize, $prod_share_option, array(
			'label' => $prod_share_data['label'],
			'type' => $prod_share_data['input_type'],
			'settings' => $prod_share_option,
			'description' => $prod_share_data['description'],
			'section' => 'baumchild_share_products_section',
		)));
    }
}
add_action('customize_register', 'baumchild_customize_share_options');

/**
** Get share link data
**/
function baumchild_share_links($post_id, $social) {
	$data = array();
	$post_url = esc_url(get_permalink($post_id));
	switch ($social) {
		case 'facebook':
			$data['u'] = $post_url;
			$link = esc_url('https://www.facebook.com/sharer/sharer.php?' . http_build_query($data));
			break;
		case 'twitter':
			$data['url'] = $post_url;
			$link = esc_url('https://twitter.com/share?' . http_build_query($data));
			break;
		case 'pinterest':
			$data['url'] = $post_url;
			$data['media'] = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full')[0];
			$link = esc_url('http://pinterest.com/pin/create/button/?' . http_build_query($data));
			break;
		case 'google':
			$data['url'] = $post_url;
			$link = esc_url('https://plus.google.com/share?' . http_build_query($data));
			break;
		case 'email':
			$data['body'] = $post_url;
			$link = 'mailto:?' . http_build_query($data);
			break;
		case 'whatsapp':
			$data['text'] = $post_url;

			if(wp_is_mobile()) {
				$link = 'whatsapp://send?' . http_build_query($data);
			} else {
				$link = 'https://wa.me/?' . http_build_query($data);
			}
			break;
		
		default:
			break;
	}

	return $link;
}
