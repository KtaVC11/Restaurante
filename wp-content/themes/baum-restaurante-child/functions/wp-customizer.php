<?php
/**
** Customizer Options
**/
function baumchild_customize_options($wp_customize) {
	// Remove WP unused sections
	$wp_customize->remove_section('colors');
	$wp_customize->remove_section('header_image');
	$wp_customize->remove_section('background_image');
	$baumchild_options = array(
		'baumchild_tracking_before_head_close' => array(
			'label' => __('Before </head>', 'baumchild'),
			'input_type' => 'textarea',
			'description' => 'Inserta antes de ' . htmlspecialchars('</head>'),
			'section' => 'baumchild_tracking_section',
		),
		'baumchild_tracking_after_body_open' => array(
			'label' => __('After <body>', 'baumchild'),
			'input_type' => 'textarea',
			'description' => 'Inserta después de ' . htmlspecialchars('<body>'),
			'section' => 'baumchild_tracking_section',
		),
		'baumchild_tracking_before_body_close' => array(
			'label' => __('Before </body>', 'baumchild'),
			'input_type' => 'textarea',
			'description' => 'Inserta antes de ' . htmlspecialchars('</body>'),
			'section' => 'baumchild_tracking_section',
		),
		'baumchild_header_columns' => array(
			'label' => __('Columnas para el contenido del header', 'baumchild'),
			'input_type' => 'select',
			'choices' => array(
				'none' => 'N/A',
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4'
			),
			'description' => __('Cantidad de columnas a mostrarse en el header dentro del área de Widget <strong>Header Columns</strong>. Use N/A para desactivar esta área de Widget', 'baumchild'),
			'section' => 'baumchild_miscellaneous_section'
		),
		'baumchild_footer_columns' => array(
			'label' => __('Columnas para el footer', 'baumchild'),
			'input_type' => 'select',
			'choices' => array(
				'1' =>'1',
				'2' => '2',
				'3' => '3',
				'4' => '4'
			),
			'description' => __('Cantidad de columnas a mostrarse en el footer como secciones de Widget <strong>Footer First, Footer Second, etc</strong>', 'baumchild'),
			'section' => 'baumchild_miscellaneous_section'
		),
		'baumchild_products_per_page' => array(
			'label' => __('Productos por página', 'baumchild'),
			'input_type' => 'select',
			'choices' => array(
				'12' => '12',
				'16' => '16',
				'20' => '20',
				'24' => '24',
				'-1' => __('Todos', 'baumchild'),
			),
			'description' => 'Cantidad de productos a mostrar por página',
			'panel' => 'woocommerce',
			'section' => 'baumchild_wc_section'
		),
		'baumchild_create_password_message' => array(
			'label' => __('Mensaje crear contraseña', 'baumchild'),
			'input_type' => 'textarea',
			'description' => 'Aparece en el checkout para usuarios que no tienen cuenta',
			'panel' => 'woocommerce',
			'section' => 'baumchild_wc_section'
		)
	);

	if (class_exists('RevSlider')) {
		$rev_slider = new RevSlider();
		$sliders[] = __('Sin Slider', 'baumchild');

		foreach ($rev_slider->getAllSliderForAdminMenu() as $key => $slider) {
			$sliders[$slider['alias']] = $slider['title'];
		}

		$baumchild_options['baumchild_site_slider'] = array(
			'label' => __('Slider', 'baumchild'),
			'input_type' => 'select',
			'choices' => $sliders,
			'description' => 'Seleccione el slider principal',
			'section' => 'baumchild_miscellaneous_section'
		);
	}

	$baumchild_sections = array(
		'baumchild_tracking_section' => array(
			'title' => __('Códigos de seguimiento', 'baumchild'),
			'description' => __('Analytics, FB Pixel, etc. Recuerde agregar las etiquetas "script"', 'baumchild'),			
		),
		'baumchild_miscellaneous_section' => array(
			'title' => __('Miscellaneous', 'baumchild'),
			'description' => __('Logos extra y configuraciones en el sitio', 'baumchild'),
		),
        'baumchild_wc_section' => array(
            'title' => __('WC by Baum', 'baumchild'),
            'description' => __('Extra configuraciones para WC por Baum', 'baumchild'),
            'panel' => 'woocommerce'
        )
	);

	// Create a custom panel
	$wp_customize->add_panel('baumchild_panel', array(
		'priority' => 10,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => __('Theme Options by Baum', 'baumchild'),
		'description' => __('Custom settings to this theme', 'baumchild'),
	));

	// Create a custom section and added on top of options
	foreach ($baumchild_sections as $baumchild_section => $section_data) {
		$wp_customize->add_section($baumchild_section, array(
			'title' => $section_data['title'],
			'capability' => 'edit_theme_options',
			'priority' => (!empty($section_data['priority'])) ? $section_data['priority'] : 1,
			'description' => (!empty($section_data['description'])) ? $section_data['description'] : '',
			'panel' => (!empty($section_data['panel'])) ? $section_data['panel'] : 'baumchild_panel'
		));
	}

	// Create a custom options and controls
	foreach ($baumchild_options as $baumchild_option => $social_data) {
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

		if($social_data['input_type'] == 'select') {
			$options['choices'] = $social_data['choices'];
		}

		$wp_customize->add_control(new WP_Customize_Control($wp_customize, $baumchild_option, $options));
	}
}
add_action('customize_register', 'baumchild_customize_options');

/**
** Tracking codes
** Use wp_body_open available since WP 5.2 instead of baumchild_after_body_open
**/
function baumchild_tracking_code_head_close() {
	$before_head_close = get_theme_mod('baumchild_tracking_before_head_close');

	if (!empty($before_head_close) && !is_admin())
		echo $before_head_close;
}
add_action('wp_head', 'baumchild_tracking_code_head_close', 9999);

function baumchild_tracking_code_body_open() {
	$after_body_open = get_theme_mod('baumchild_tracking_after_body_open');

	if (!empty($after_body_open) && !is_admin())
		echo $after_body_open;
}
add_action('wp_body_open', 'baumchild_tracking_code_body_open');

function baumchild_tracking_code_body_close() {
	$before_body_close = get_theme_mod('baumchild_tracking_before_body_close');

	if (!empty($before_body_close) && !is_admin())
		echo $before_body_close;
}
add_action('wp_footer', 'baumchild_tracking_code_body_close', 9999);