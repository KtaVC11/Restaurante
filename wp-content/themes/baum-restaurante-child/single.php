<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */
get_header();
?>

<section id="primary" class="content-area section-detalle-blog">
    <main id="main" class="site-main">

        <?php
        /* Start the Loop */
        while (have_posts()) :
            the_post();

            get_template_part('template-parts/content/content', 'single');

        endwhile; // End of the loop.
        ?>

    </main><!-- #main -->

    <?php
    $args = array(
		'posts_per_page' => 3, 
        'post_type' => 'post', 
        'post__not_in' => array(get_the_ID())
	);
    $the_query = new WP_Query($args);

    if ($the_query->have_posts()) :
        ?>
        <div class="sidebar">
            <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
                <?php
                get_template_part('template-parts/content/content', 'excerpt');
                ?>
            <?php endwhile; ?>
        </div>
        <?php
    endif;
    wp_reset_postdata();
    ?>

</section><!-- #primary -->

<?php
get_footer();
