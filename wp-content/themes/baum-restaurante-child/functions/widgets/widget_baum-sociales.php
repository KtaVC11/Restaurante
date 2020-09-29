<?php
/**
** Widget Baum Sociales
** To Do: Call this file in /functions/wp-widgets.php
** then add register_widget('Baum_Sociales_Widget') to action widgets_init
**/
class Baum_Sociales_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'baumchild_sociales_widget',
			__('Baum Sociales', 'baumdivi'),
			array(
				'description' => __('Muestra los links a redes sociales.', 'baumdivi')
			)
		);
	}

	private function sociales_items() {
		$sociales = array();

		foreach (baumchild_sociales() as $social => $data) {
			if(preg_match('/baumchild_sociales_/', $social)) {
				$sociales[] = str_replace('baumchild_sociales_', 'exclude_soc_', $social);
			} elseif(preg_match('/baumchild_contacto_/', $social)) {
				$sociales[] = str_replace('baumchild_contacto_', 'exclude_soc_', $social);
			}
		}

		return $sociales;
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', $instance['title']);
		$exclude_sociales = array();

		foreach ($this->sociales_items() as $key => $field) :
			if(!empty($instance[$field])) 
				$exclude_sociales[] = str_replace('exclude_soc_', 'baumchild_contacto_', $field);
		endforeach;

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		if (!empty($instance['title']))
			echo $args['before_title'] . $title . $args['after_title'];
		
		if(function_exists('baumchild_social_links')) :
			?>
			<div class="sociales-wrapper">
				<?php echo baumchild_social_links($exclude_sociales); ?>
			</div>
			<?php 
		endif;

		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		$attr = array();
		$title = apply_filters('widget_title', $instance['title']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'baumdivi'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php 
		foreach ($this->sociales_items() as $key => $field) :
			$attr[$field] = (!empty($instance[$field])) ? $instance[$field] : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Exclude ' . str_replace('exclude_soc_', '', $field) . '?', 'baumdivi'); ?>
			<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1" <?php checked($attr[$field], '1'); ?>">
			</label>
		</p>
		<?php 
		endforeach;
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

		foreach ($this->sociales_items() as $key => $field) {
			$instance[$field] = (!empty($new_instance[$field])) ? strip_tags($new_instance[$field]) : '';
		}

		return $instance;
	}
}