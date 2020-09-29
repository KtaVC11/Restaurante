
<style>

/* Product List */

.listCat {
	padding: 0 20px;
}
.listCat ul {
	list-style: none;
	margin: 0;
}

.listCat ul li {
	background-size: cover;
	margin: 0 0 10px 0;
}

.listCat ul li a {
	display: flex;
	padding: 20px;
	justify-content: center;
	color:#fff;
	background-color: rgba(0,0,0,0.6);
}

</style>
<?php
/**
 * The template for displaying pages
 */

get_header(); ?>

    <div class="listCat">
    <?php
        $taxonomy     = 'product_cat';
		//$orderby      = 'name';  
        $show_count   = 0;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no  
        $title        = '';  
        $empty        = 0;


        $args = array(
            'taxonomy'     => $taxonomy,
			// 'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
        );
        $all_categories = get_categories( $args );

        echo "<ul>";
        foreach ($all_categories as $cat) {
        if($cat->category_parent == 0) {
            
            $category_id = $cat->term_id;    
            $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
            $image = wp_get_attachment_url( $thumbnail_id );

				/* Start IF statement */
				if ( $image ) {
					echo "<li class='' style='background-image:url(". $image .")'>";
				} else {
					echo "<li>";
				}
				/* End If Statement */
			
				echo '<a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->name .'</a>';
				
            }   
            echo "</li>";
        } 
        echo "</ul>";
		?>
		

        
    </div>

<?php get_footer(); ?>

