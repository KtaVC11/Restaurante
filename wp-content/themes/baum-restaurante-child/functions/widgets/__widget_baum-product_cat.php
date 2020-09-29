<?php
/**
** Widget Baum Product Categories
** To Do: Call this file in /functions/wp-widgets.php
** then add register_widget('WC_Baum_Product_Categories') to action widgets_init
** Pex Edition
**/
class WC_Baum_Product_Categories extends WC_Widget {
	/**
	** Constructor.
	**/
	public function __construct() {
		$this->widget_cssclass    = 'wc_baum_widget widget_product_categories';
		$this->widget_description = __('Muestra categorías de productos según un padre específico.', 'baumchild');
		$this->widget_id          = 'wc_baum_product_cat';
		$this->widget_name        = __( 'Baum Categoria de Productos', 'baumchild' );

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		global $wp_query, $post;

		$cur_parent_cat = isset( $instance['parent_cat'] ) ? $instance['parent_cat'] : -1;
		$count = isset( $instance['count'] ) ? $instance['count'] : '';
		$hierarchical = isset( $instance['hierarchical'] ) ? $instance['hierarchical'] : '';
		$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : 0;
		$orderby = isset( $instance['orderby'] ) ? $instance['orderby'] : '';
		$max_depth = isset( $instance['max_depth'] ) ? $instance['max_depth'] : 0;
		$parent_tabs = isset( $instance['parent_tabs'] ) ? $instance['parent_tabs'] : 0;
		
		$list_args = array(
			'show_count'   => $count,
			'hierarchical' => $hierarchical,
			'taxonomy'     => 'product_cat',
			'hide_empty'   => $hide_empty,
			'parent_tabs'   => $parent_tabs,
		);
		$list_args['menu_order'] = false;
		$list_args['depth']      = $max_depth;

		if ( 'name' === $orderby ) {
			$list_args['orderby'] = 'title';
		} else {
			$list_args['menu_order'] = str_replace('order_', '', $orderby);
		}

		if($cur_parent_cat != -1) {
			$list_args['child_of'] = $cur_parent_cat;
		}

		$this->widget_start( $args, $instance );
		ob_start();

		include_once WC()->plugin_path() . '/includes/walkers/class-wc-product-cat-list-walker.php';

		$list_args['walker']                     = new WC_Product_Cat_List_Walker();
		$list_args['title_li']                   = '';
		$list_args['pad_counts']                 = 1;
		$list_args['show_option_none']           = __( 'No existen categorías de producto.', 'baumchild' );
		$list_args['max_depth']                  = $max_depth;
		?>

		<ul class="wc-product-categories menu" data-depth="<?= $max_depth ?>">
			<?php
				wp_list_categories( apply_filters( 'woocommerce_product_categories_widget_args', $list_args ) );
			?>
		</ul>

		<?php
		echo apply_filters('wc_cat_list_categories', ob_get_clean(), $list_args);

		$this->widget_end( $args );
	}

	public function update( $new_instance, $old_instance ) {
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['parent_cat'] = sanitize_text_field( $new_instance['parent_cat'] );
		$instance['hide_empty'] = sanitize_text_field( $new_instance['hide_empty'] );
		$instance['hierarchical'] = sanitize_text_field( $new_instance['hierarchical'] );
		$instance['count'] = sanitize_text_field( $new_instance['count'] );
		$instance['orderby'] = sanitize_text_field( $new_instance['orderby'] );
		$instance['max_depth'] = sanitize_text_field( $new_instance['max_depth'] );
		$instance['parent_tabs'] = sanitize_text_field( $new_instance['parent_tabs'] );

		return $instance;
	}

	public function form( $instance ) {
		$cur_parent_cat = isset( $instance['parent_cat'] ) ? esc_attr( $instance['parent_cat'] ) : -1;
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$hide_empty = isset( $instance['hide_empty'] ) ? esc_attr( $instance['hide_empty'] ) : 0;
		$hierarchical = isset( $instance['hierarchical'] ) ? esc_attr( $instance['hierarchical'] ) : 0;
		$count = isset( $instance['count'] ) ? esc_attr( $instance['count'] ) : 0;
		$orderby = isset( $instance['orderby'] ) ? esc_attr( $instance['orderby'] ) : '';
		$max_depth = isset( $instance['max_depth'] ) ? esc_attr( $instance['max_depth'] ) : '';
		$parent_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
		$parent_tabs = isset( $instance['parent_tabs'] ) ? esc_attr( $instance['parent_tabs'] ) : 0;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __('Título', 'baumchild'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" type="checkbox" value="1" <?php checked($hide_empty, '1'); ?>/>
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>"><?php echo __('Ocultar categorías vacías', 'baumchild'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="checkbox" value="1" <?php checked($count, '1'); ?>/>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php echo __('Mostrar contador de productos', 'baumchild'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" type="checkbox" value="1" <?php checked($hierarchical, '1'); ?>/>
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php echo __('Mostar jerarquía', 'baumchild'); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php echo __('Ordenar por', 'baumchild') ?>
				<select class="widefat" name="<?php echo $this->get_field_name( 'orderby' ); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
					<option value="order_asc" <?php selected($orderby, 'order_asc') ?>><?php echo __('Orden ascendente', 'baumchild') ?></option>
					<option value="order_desc" <?php selected($orderby, 'order_desc') ?>><?php echo __('Orden descendente', 'baumchild') ?></option>
					<option value="name" <?php selected($orderby, 'name') ?>><?php echo __('Nombre', 'baumchild') ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('parent_cat'); ?>"><?php echo __('Mostrar hijas de', 'baumchild') ?>
				<select class="widefat" name="<?php echo $this->get_field_name( 'parent_cat' ); ?>" id="<?php echo $this->get_field_id('parent_cat'); ?>">
					<option value="-1"><?php echo __('Show all', 'baumchild') ?></option>
					<?php foreach ($parent_cats as $parent_cat) : ?>
						<option value="<?= $parent_cat->term_id ?>" <?php selected($parent_cat->term_id, $cur_parent_cat) ?>><?= $parent_cat->name ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'max_depth' ); ?>"><?php echo __('Profundidad máxima', 'baumchild'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'max_depth' ); ?>" name="<?php echo $this->get_field_name( 'max_depth' ); ?>" type="text" value="<?php echo $max_depth; ?>" />
		</p>

		<p>
			<input class="widefat" id="<?php echo $this->get_field_id( 'parent_tabs' ); ?>" name="<?php echo $this->get_field_name( 'parent_tabs' ); ?>" type="checkbox" value="1" <?php checked($parent_tabs, '1'); ?>/>
			<label for="<?php echo $this->get_field_id( 'parent_tabs' ); ?>"><?php echo __('Mostar padres como tabs', 'baumchild'); ?></label>
		</p>
		<?php
		
	}
}

/**
**  Get custom categories
**/
function baumchild_wp_list_categories($output, $args) {
	if( $args['parent_tabs'] == 0 )
		return $output;

	$current_cat = get_queried_object();

	$new_list_args = $args;
	$terms_id = array();
	$new_list_args['parent'] = $args['child_of'];
	$categories = get_categories($new_list_args);

	if( $categories ) :
		ob_start();
		?>
		<div class="categories-tab-top">
			<?php
			foreach ($categories as $key => $parent_cat) {
				$terms_id[] = $parent_cat->term_id;
				$classes = array('cat');
				$thumbnail_id = get_woocommerce_term_meta( $parent_cat->term_id, 'thumbnail_id', true ); 
				$image = wp_get_attachment_url( $thumbnail_id );
				$enable_on_desktop = get_woocommerce_term_meta( $parent_cat->term_id, 'custom_permalink_on_desktop', true ); 
				$custom_permalink = get_woocommerce_term_meta( $parent_cat->term_id, 'cat_custom_permalink', true ); 
				$custom_attr = ' data-hash="cat_' . $parent_cat->term_id . '"';
				
				if (!empty($custom_permalink)) {
					$the_permalink = $custom_permalink['url'];
					$custom_attr .= ' target="' . $custom_permalink['target'] . '"';
				} else {
					$the_permalink = get_category_link($parent_cat->term_id);
				}

				if ($current_cat->term_id == $parent_cat->term_id)
					$classes[] = 'current_cat';

				if ($enable_on_desktop)
					$classes[] = 'link_on_desktop';
				?>
				<a href="<?= esc_url($the_permalink) ?>" <?= $custom_attr ?> class="<?= implode(' ', $classes); ?>">
					<?php if (!empty($thumbnail_id)) : ?>
						<img src="<?= $image ?>" class="category-image" alt="<?= $parent_cat->name ?>">
					<?php endif; ?>
					<span class="category-name"><?= $parent_cat->name ?></span>
				</a>
				<?php
			}
			?>
		</div>
		<?php
	endif;

	if( $terms_id ) :
		?>
		<div class="categories-tab-content">
			<?php
			foreach ($terms_id as $key => $_term_id) {
				$args['child_of'] = $_term_id;

				include_once WC()->plugin_path() . '/includes/walkers/class-wc-product-cat-list-walker.php';

				$args['walker']                     = new WC_Product_Cat_List_Walker();
				$args['title_li']                   = '';
				$args['pad_counts']                 = 1;
				$args['show_option_none']           = '';
				$args['max_depth']                  = $args['max_depth'];
				?>
				<div id="cat_<?= $_term_id ?>" class="wc-product-categories-wrapper">
					<ul class="wc-product-categories menu">
						
						<?php wp_list_categories( apply_filters( 'woocommerce_product_categories_widget_args', $args ) ); ?> 
						
					</ul>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	endif;

	return ob_get_clean();
}
add_filter('wc_cat_list_categories', 'baumchild_wp_list_categories', 10, 2);