<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package storefront
 */
$orderby = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'menu_order';

$args = array(
    'post_type'      => 'product',
//    'posts_per_page' => 10,
    'product_cat'    => 'books',
    'orderby' => $orderby,
    'order' => 'desc'
);

$loop = new WP_Query( $args );
get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <form action="#" method="POST">
                <select name="" id="" class="input-text">
                    <option value="">Filter criteria 1</option>
                </select>

                <select name="" id="" class="input-text">
                    <option value="">Filter criteria 2</option>
                </select>

                <select name="" id="" class="input-text">
                    <option value="">Filter criteria 3</option>
                </select>
                <button type="submit">Filter</button>
            </form>
            <select name="orderby" id="booksOrderBy" class="orderby input-text">
                <option value="menu_order">Default sorting</option>
                <option value="popularity">Sort by popularity</option>
                <option value="rating">Sort by average rating</option>
                <option value="date">Sort by latest</option>
                <option value="price">Sort by price: low to high</option>
                <option value="price-desc">Sort by price: high to low</option>
            </select>
            <?php
//            while ( have_posts() ) :
//                the_post();
//
//                do_action( 'storefront_page_before' );
//
//                get_template_part( 'content', 'page' );
//
//                /**
//                 * Functions hooked in to storefront_page_after action
//                 *
//                 * @hooked storefront_display_comments - 10
//                 */
//                do_action( 'storefront_page_after' );
//
//            endwhile; // End of the loop.
?>
            <div class="products columns-3">
                <?php
                    while ( $loop->have_posts() ) : $loop->the_post();
                        global $product;
                        $data = getProtectedValue($product, 'data');
                        echo '<div class="product type-product">';
                        echo '<br /><a href="'.get_permalink().'">' . woocommerce_get_product_thumbnail().' '.get_the_title().'</a>';
                        echo '<p>'.$product->get_short_description().'</p>';
                        echo '<p><a href="?add-to-cart='.$product->get_id().'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product->get_id().'" data-product_sku="" rel="nofollow">Add to cart</a></p>';
                        echo '</div>';
                    endwhile;

                    wp_reset_query();
                ?>
            </div>
        </main><!-- #main -->
    </div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();
?>
<script>
    jQuery('#booksOrderBy').on('change', function(){
        if(jQuery(this).val() !== ''){
            window.location.href = '?orderBy=' + jQuery(this).val()
        }
    });
</script>
