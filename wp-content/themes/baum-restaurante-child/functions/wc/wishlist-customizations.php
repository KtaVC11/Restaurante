<?php

/**
** Wishlist Add to Cart button text
**/

add_filter('tinvwl_wishlist_item_add_to_cart', function() {
	return __('Comprar', 'baumchild');
});
