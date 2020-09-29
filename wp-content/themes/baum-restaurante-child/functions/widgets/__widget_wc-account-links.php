<?php
/**
** Widget WC Account Links
** To Do: Call this file in /functions/wp-widgets.php
** then add register_widget('Woo_Account_Links_Widget') to action widgets_init
**/
class Woo_Account_Links_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'baumchild_wc_account_links',
			__('Baum Links Mi Cuenta', 'baumchild'),
			array(
				'description' => __('Muestra los links a las ediciones de la cuenta.', 'baumchild')
			)
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', $instance['title']);

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if (empty($instance['exclude_soc_title']))
			echo $args['before_title'] . $title . $args['after_title'];

		?>
		<div class="cuenta-wrapper">
			<?php if (!(is_user_logged_in())) : ?>
				<ul class="menu-no-logged-in">
					<li class="menu-item no-logged-in">
						<a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" title="<?php esc_attr__('Login', 'baumchild'); ?>" class="user-icon">
							<span><?php esc_html_e('Ingresar / Registrarse', 'baumchild'); ?></span>
						</a>
					</li>

					<?php if(wc_get_endpoint_url('tinv_wishlist')) : ?>
						<li class="menu-item wishlist-page">
							<a href="<?= wc_get_endpoint_url('tinv_wishlist'); ?>">
								<span><?= __('Mis favoritos', 'baumchild') ?></span>
							</a>
						</li>
					<?php endif; ?>
				</ul>
				<?php else: ?>
					<?php get_template_part('/woocommerce/myaccount/navigation', ''); ?>
			<?php endif; ?>
		</div>
		<?php

		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		$title = $instance['title'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'baumchild'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

		return $instance;
	}
}