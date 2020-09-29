<?php
/**
** Social Links
** To Do: Call this file in /functions/includes.php
** v 1.0.2
**/
function baumchild_sociales($key = null) {
	$social_links = array(
		'baumchild_contacto_facebook' => array(
			'label' => __('Facebook', 'baumdivi'),
			'input_type' => 'text',
			'section' => 'baumchild_sociales_section',
			'class' => 'fab fa-2x fa-facebook-f',
			'target' => '_blank',
			'link_prefix' => '',
			'show_link' => false
		),
		'baumchild_contacto_linkedin' => array(
			'label' => __('LinkedIn', 'baumdivi'),
			'input_type' => 'text',
			'section' => 'baumchild_sociales_section',
			'class' => 'fab fa-2x fa-linkedin-in',
			'target' => '_blank',
			'link_prefix' => '',
			'show_link' => false
		),
		'baumchild_contacto_youtube' => array(
			'label' => __('Youtube', 'baumdivi'),
			'input_type' => 'text',
			'section' => 'baumchild_sociales_section',
			'class' => 'fab fa-2x fa-youtube',
			'target' => '_blank',
			'link_prefix' => '',
			'show_link' => false
		),
		'baumchild_contacto_whatsapp' => array(
			'label' => __('Whatsapp', 'baumdivi'),
			'input_type' => 'text',
			'section' => 'baumchild_sociales_section',
			'class' => 'fab fa-2x fa-whatsapp',
			'target' => '_blank',
			'link_prefix' => 'https://wa.me/',
			'show_link' => false
		),
		'baumchild_contacto_instagram' => array(
			'label' => __('Instagram', 'baumdivi'),
			'input_type' => 'text',
			'section' => 'baumchild_sociales_section',
			'class' => 'fab fa-2x fa-instagram',
			'target' => '_blank',
			'link_prefix' => '',
			'show_link' => false
		),
		'baumchild_contacto_waze' => array(
			'label' => __('Waze', 'baumdivi'),
			'input_type' => 'text',
			'section' => 'baumchild_sociales_section',
			'class' => 'fab fa-2x fa-waze',
			'target' => '_blank',
			'link_prefix' => '',
			'show_link' => false
		),
		'baumchild_contacto_telefono' => array(
			'label' => __('Telefono', 'baumdivi'),
			'input_type' => 'text',
			'section' => 'baumchild_sociales_section',
			'class' => 'fa fa-phone-square',
			'link_prefix' => 'tel:',
			'show_link' => true,
			'use_field_label' => true
		),
		'baumchild_contacto_email' => array(
			'label' => __('Email', 'baumdivi'),
			'input_type' => 'text',
			'section' => 'baumchild_sociales_section',
			'class' => 'fa fa-envelope',
			'link_prefix' => 'mailto:',
			'show_link' => true,
			'use_field_label' => true
		),
	);

	if (!empty($key)) {
		return $social_links[$key];
	} else {
		return $social_links;
	}
}

/**
** Social link format
**/
function baumchild_social_link_format($social_link) {
	if (preg_match('/email/', $social_link)) {
		$link = sanitize_email(get_theme_mod($social_link));
	} elseif (preg_match('/whatsapp/', $social_link)) {
		$link = '506' . str_replace(array('+', '(', ')', '-', '506'), '', get_theme_mod($social_link));
	} else {
		$link = get_theme_mod($social_link);
	}

	return $link;
}

/**
** Widget wrapper
**/
function baumchild_social_links($exclude = array()) {
	$social_links = baumchild_sociales();
	
	foreach ($social_links as $social_link => $data) {
		if (!(empty(get_theme_mod($social_link))) && !in_array($social_link, $exclude)) {
			?>
			<span class="baumchild_social baumchild_social-<?= str_replace('baumchild_contacto_', '', $social_link) ?>">
				<?php
				$link = baumchild_social_link_format($social_link);
				$target = !empty($data['target']) ? ' target="' . $data['target'] . '"' : '';

				if (isset($data['use_field_label'])) {
					$label = $social_link . '_label';
					$title = apply_filters($label, get_theme_mod($label));
					?>
					<span class="baumchild_social-label"><?= $title ?></span>
					<?php
				}
				?>
				<a class="<?= str_replace('baumchild_', '', $social_link) ?>" href="<?= $data['link_prefix'] . $link ?>" <?= $target ?>>
					<?php if ($data['show_link']) : ?>
						<?= $link ?>
					<?php else : ?>
						<i class="<?= $data['class'] ?>" aria-hidden="true"></i>
					<?php endif; ?>
				</a>
			</span>
			<?php
		}
	}
}

/**
** Shortcode wrapper - Visual Composer
**/
function baumchild_social_shortcode($args = array()) {
	$args = shortcode_atts(array(
		'id' => 'whatsapp',
		'is_link' => 0,
		'title' => '',
		'is_right' => 'left',
		'show_icon' => false
	), $args);

	$id = 'baumchild_contacto_' . $args['id'];
	$is_link = (bool) ($args['is_link'] == 'true') ? 1 : 0;
	$is_right = (bool) ($args['is_right'] == 'true') ? 1 : 0;
	$show_icon = (bool) ($args['show_icon'] == 'true') ? 1 : 0;
	$data = baumchild_sociales($id);
	$link = baumchild_social_link_format($id);
	$target = ' target="' . $data['target'] . '"';

	if ($data['use_field_label'] && empty($args['title'])) {
		$args['title'] = get_theme_mod($id . '_label');
	}
	ob_start();

	if(!empty($link)) :
		$title = apply_filters('baumchild_social_title', $args['title'], $id);
		?>
		<span class="baumchild_social baumchild_social-<?= $args['id'] ?>">
			<?php if ($show_icon && !empty($data['class'])) : ?>
				<a href="<?= $data['link_prefix'] . $link ?>" class="contacto_<?= $args['id'] ?>" <?= !empty($target) ? $target : '' ?>><i class="<?= $data['class'] ?>"></i> <?= $title ?></a>
			<?php elseif ($data['use_field_label']) : ?>
				<span class="baumchild_social-label"><?= $title ?></span>
				<a href="<?= $data['link_prefix'] . $link ?>" <?= !empty($target) ? $target : '' ?>><?= $link ?></a>
			<?php elseif ($is_link) : ?>
				<a href="<?= $data['link_prefix'] . $link ?>" <?= !empty($target) ? $target : '' ?>><?= $link  . $title ?></a>
			<?php else : ?>
				<?= $link ?>
			<?php endif; ?>
		</span>
		<?php
	endif;

	$html = ob_get_clean();

	echo $html;
}
add_shortcode('baumchild_social', 'baumchild_social_shortcode');

/**
** Customizer Options
**/
function baumchild_baum_sociales_customize_options($wp_customize) {
	$baumchild_sections = array(
		'baumchild_sociales_section' => array(
			'title' => __('Baum Sociales', 'baumdivi'),
			'description' => __('Opciones para redes sociales. Use [baumchild_social id="facebook" is_link=true title="Facebook"] para imprimir el link en un bloque de texto. Ej: Facebook: https://www.facebook.com/', 'baumdivi'),
		)
	);

	// Create a custom section and added on top of options
	foreach ($baumchild_sections as $baumchild_section => $section_data) {
		$wp_customize->add_section($baumchild_section, array(
			'title' => $section_data['title'],
			'capability' => 'edit_theme_options',
			'priority' => (!empty($section_data['priority'])) ? $section_data['priority'] : 1,
			'description' => (!empty($section_data['description'])) ? $section_data['description'] : '',
			'panel' => 'baumchild_panel'
		));
	}

	// Create a custom options and controls
	foreach (baumchild_sociales() as $baumchild_option => $social_data) {
		// Add custom label to social field
		if ($social_data['use_field_label']) {
			$new_social_data = wp_parse_args(array(
				'label' => $social_data['label'] . ' Label',
				'description' => $social_data['label'] . ' Label',
			), $social_data);
			baumchild_social_input_control($wp_customize, $baumchild_option . '_label', $new_social_data);
		}
		
		baumchild_social_input_control($wp_customize, $baumchild_option, $social_data);
	}
}
add_action('customize_register', 'baumchild_baum_sociales_customize_options');

function baumchild_social_input_control($wp_customize, $baumchild_option, $social_data) {
	$wp_customize->add_setting($baumchild_option, array(
		'default' => '',
		'capability' => 'edit_theme_options',
		'type' => 'theme_mod',
		'transport' => 'refresh',
	));

	$options = array(
		'label' => $social_data['label'],
		'type' => $social_data['input_type'],
		'settings' => $baumchild_option,
		'description' => (!empty($social_data['description'])) ? $social_data['description'] : '',
		'section' => $social_data['section'],
	);

	$wp_customize->add_control(new WP_Customize_Control($wp_customize, $baumchild_option, $options));
}
