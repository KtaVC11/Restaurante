<?php
/**
** Remove sidebars from parent theme
**/
function baumchild_remove_parent_sidebars() {
	unregister_sidebar('sidebar-1');
	unregister_sidebar('sidebar-2');
	unregister_sidebar('sidebar-3');
}
add_action('init', 'baumchild_remove_parent_sidebars', 11); 

/**
** Add new sidebars
**/
function baumchild_custom_widgets_init() {
	$footer_columns = (!empty(get_theme_mod('baumchild_footer_columns'))) ? get_theme_mod('baumchild_footer_columns') : 3;

	/**
	** Shop sidebar - sidebar-principal
	** No aplica en este tema - KMA - 04-2020
	**/

	// Single Product Carousels
	register_sidebar(array(
		'name' => __('Single Product Carousels', 'baumchild'),
		'id' => 'sidebar-single-product-carousels',
		'description' => __('Aparece abajo del detalle de producto.', 'baumchild'),
		'before_widget' => '<section id="%1$s" class="widget widget-single-product-carousels %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
	
	if (($header_columns = get_theme_mod('baumchild_header_columns')) != 'none') {
		// Header widgets
		register_sidebar(array(
			'name' => __('Header Columns', 'baumchild'),
			'id' => 'sidebar-header-columns',
			'description' => sprintf(__('Área para agregar en el header del sitio. Disponible %s columna(s).', 'baumchild'), $header_columns),
			'before_widget' => '<section id="%1$s" class="widget widget-header-columns ' . baumchild_get_widget_classes('header') . ' %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	}

	// Footer widgets
	foreach (baumchild_footer_widgets() as $pos => $footer_widget) {
		register_sidebar(array(
			'name' => __('Footer ' . ucfirst($pos), 'baumchild'),
			'id' => 'sidebar-footer-' . $pos,
			'before_widget' => '<section id="%1$s" class="widget widget-footer-' . $footer_widget['id'] . ' %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	}

	// Footer Copyright text
	register_sidebar(array(
		'name' => __('Footer Copyright', 'baumchild'),
		'id' => 'sidebar-footer-copy',
		'description' => __('', 'baumchild'),
		'before_widget' => '<div class="copyright %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	// Contacto
    register_sidebar(array(
        'name' => __('Contacto', 'baumchild'),
        'id' => 'sidebar-contacto',
        'description' => __('', 'baumchild'),
        'before_widget' => '<div class="widget widget-contacto %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'baumchild_custom_widgets_init');

/**
** Generate footer sidebars
**/
function baumchild_footer_widgets() {
	$footer_columns = (!empty(get_theme_mod('baumchild_footer_columns'))) ? get_theme_mod('baumchild_footer_columns') : 3;

	$widgets = array(
		'first' => array('id' => 1),
		'second' => array('id' => 2),
		'third' => array('id' => 3),
		'fourth' => array('id' => 4)
	);

	return array_slice($widgets, 0, $footer_columns);
}

function baumchild_get_widget_classes($position = 'footer') {
	$classes = array();
	$columns = (!empty(get_theme_mod('baumchild_' . $position . '_columns'))) ? get_theme_mod('baumchild_' . $position . '_columns') : 4;

	switch ($columns) {
		case '1':
			$classes = 'col-sm-12';
			break;
		case '2':
			$classes = 'col-sm-6';
			break;
		case '3':
			$classes = ($position == 'footer') ? 'col-md-4 col-sm-6' : 'col-sm-4 col-6';
			break;		
		default:			
			$classes = ($position == 'footer') ? 'col-md-3 col-sm-6' : 'col-md-3 col-6';
			break;
	}


	return $classes;
}

/**
** Display header sidebars using action baumchild_before_main_content
**/
function baumchild_top_content_columns() {
	ob_start();
	if (is_active_sidebar('sidebar-header-columns')) :
		?>
		<div class="container site-header-columns columns-<?= get_theme_mod('baumchild_header_columns') ?>">
			<div class="row">
				<?php dynamic_sidebar('sidebar-header-columns'); ?>
			</div>
		</div>
		<?php
		echo ob_get_clean();
	endif;
}
add_action('baumchild_before_main_content', 'baumchild_top_content_columns');
