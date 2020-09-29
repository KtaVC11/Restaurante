<?php
require get_stylesheet_directory(). '/functions/widgets/widget_baum-sociales.php';
// require get_stylesheet_directory(). '/functions/widgets/widget_baum-product_cat.php';
//require get_stylesheet_directory(). '/functions/widgets/widget_wc-account-links.php';

/**
** Register and load the widget
**/
function baumchild_load_widgets() {
	if (class_exists('Baum_Sociales_Widget'))
		register_widget('Baum_Sociales_Widget');

	/*if (class_exists('WC_Baum_Product_Categories'))
		register_widget('WC_Baum_Product_Categories');
	
	if (class_exists('Woo_Account_Links_Widget'))
		register_widget('Woo_Account_Links_Widget');*/
}
add_action('widgets_init', 'baumchild_load_widgets');

/**
** Override widgets
**/
function baumchild_hidden_options($widget, $return, $instance) {
	$baum_classes = isset( $instance['baum_classes'] ) ? esc_attr( $instance['baum_classes'] ) : '';

	// Are we dealing with a media image widget?
	if ( 'media_image' == $widget->id_base ) {
		// Display the description option.
		$hide_xs = isset( $instance['hide_xs'] ) ? $instance['hide_xs'] : '';
		$hide_sm = isset( $instance['hide_sm'] ) ? $instance['hide_sm'] : '';
		$hide_md = isset( $instance['hide_md'] ) ? $instance['hide_md'] : '';
		$hide_lg = isset( $instance['hide_lg'] ) ? $instance['hide_lg'] : '';
		?>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id('hide_xs'); ?>" name="<?php echo $widget->get_field_name('hide_xs'); ?>" <?php checked( true , $hide_xs ); ?> />
			<label for="<?php echo $widget->get_field_id('hide_xs'); ?>">
				<?php _e('Hide < 768 (xs)', 'baumchild'); ?>
			</label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id('hide_sm'); ?>" name="<?php echo $widget->get_field_name('hide_sm'); ?>" <?php checked( true , $hide_sm ); ?> />
			<label for="<?php echo $widget->get_field_id('hide_sm'); ?>">
				<?php _e('Hide >= 768 (sm)', 'baumchild'); ?>
			</label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id('hide_md'); ?>" name="<?php echo $widget->get_field_name('hide_md'); ?>" <?php checked( true , $hide_md ); ?> />
			<label for="<?php echo $widget->get_field_id('hide_md'); ?>">
				<?php _e('Hide >= 992 (md)', 'baumchild'); ?>
			</label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id('hide_lg'); ?>" name="<?php echo $widget->get_field_name('hide_lg'); ?>" <?php checked( true , $hide_lg ); ?> />
			<label for="<?php echo $widget->get_field_id('hide_lg'); ?>">
				<?php _e('Hide >= 1200 (lg)', 'baumchild'); ?>
			</label>
		</p>
		<?php
	}
	?>
	<p>
		<label for="<?php echo $widget->get_field_id('baum_classes'); ?>"><?php _e('Widget Classes by Baum', 'baumchild'); ?></label>
		<input class="input-text widefat" type="text" id="<?php echo $widget->get_field_id('baum_classes'); ?>" name="<?php echo $widget->get_field_name('baum_classes'); ?>" value="<?= $baum_classes ?>" />
	</p>
	<?php
}
add_filter('in_widget_form', 'baumchild_hidden_options', 10, 3);

function baumchild_widget_update($instance, $new_instance) {
	$instance['hide_xs'] = (!empty($new_instance['hide_xs'])) ? 1 : '';
	$instance['hide_sm'] = (!empty($new_instance['hide_sm'])) ? 1 : '';
	$instance['hide_md'] = (!empty($new_instance['hide_md'])) ? 1 : '';
	$instance['hide_lg'] = (!empty($new_instance['hide_lg'])) ? 1 : '';
	$instance['baum_classes'] = sanitize_text_field( $new_instance['baum_classes'] );

	return $instance;
}
add_filter('widget_update_callback', 'baumchild_widget_update', 10, 2);

function baumchild_dynamic_sidebar_params($params) {
	// https://gist.github.com/CEscorcio/5669905
	global $wp_registered_widgets;
	$widget_id    = $params[0]['widget_id'];
	$widget_obj    = $wp_registered_widgets[$widget_id];
	$widget_opt    = get_option($widget_obj['callback'][0]->option_name);
	$widget_num    = $widget_obj['params'][0]['number'];

	$classes = array('widget');
	if ( isset($widget_opt[$widget_num]['hide_xs']) && !empty($widget_opt[$widget_num]['hide_xs']) )
		$classes[] = "hidden-xs";
	if ( isset($widget_opt[$widget_num]['hide_sm']) && !empty($widget_opt[$widget_num]['hide_sm']) )
		$classes[] = "hidden-sm";
	if ( isset($widget_opt[$widget_num]['hide_md']) && !empty($widget_opt[$widget_num]['hide_md']) )
		$classes[] = "hidden-md";
	if ( isset($widget_opt[$widget_num]['hide_lg']) && !empty($widget_opt[$widget_num]['hide_lg']) )
		$classes[] = "hidden-lg";

	if ( isset($widget_opt[$widget_num]['baum_classes']) && !empty($widget_opt[$widget_num]['baum_classes']) )
		$classes[] = $widget_opt[$widget_num]['baum_classes'];
	
	if(!empty($classes))
		$params[0] = array_replace($params[0], array('before_widget' => str_replace('widget ', implode(' ', $classes) . ' ', $params[0]['before_widget'])));

	return $params;
}
add_filter('dynamic_sidebar_params', 'baumchild_dynamic_sidebar_params');
