<style>


.woocommerce li.product {
    width: 100%!important;
}

.product-wrapper {
    background-color: #fff!important;
    margin-bottom: 0px!important;
}

.product-image-container{
    float: left;
}

.content-product-item-details {
    float: left;
}

.content-product-item-details h3{
    text-align: center!important;
}

/*Remove the background color from the image*/
.woocommerce .product .product-image-container {

    background: none;
}


/*Remove the header
#site-header {
    display: none !important;
}*/
.toggle-text .svg-icon{
    display: none !important;
}

.header-navigation-wrapper{
    display: none !important;
}

.header-inner {
    align-items: center;
    display: flex;
    justify-content: center;
    padding: 2.8rem 0;
}

#site-footer{
    display: none !important;
}

/*Reomve the pagination*/
.woocommerce-pagination{
    display: none !important;
}
</style>

<li <?php post_class( $classes ); ?>>

<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

<a href="<?php the_permalink(); ?>" class="custom-product-list">


    <?php        
    /**         * woocommerce_before_shop_loop_item_title hook        
     *  *       * @hooked woocommerce_show_product_loop_sale_flash - 10        
     *  *       @hooked woocommerce_template_loop_product_thumbnail - 10       
    *   
    * */        
    
    do_action( 'woocommerce_before_shop_loop_item_title' );            
    
    ?>

    <div class="content-product-item-details">   
             <h3><?php the_title(); ?></h3>    
                 <?php the_excerpt(); ?>   
                    <?php          
                        /**             * woocommerce_after_shop_loop_item_title hook          
                         *    *             * @hooked woocommerce_template_loop_price - 10             */          
                          do_action( 'woocommerce_after_shop_loop_item_title' );     
                    ?>   
                </div>
</a>

<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>