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
//require_once 'helpers/Request.php';
//die(get_request());
$args  = array( 'post_type' => 'product', 'category' => 34, 'posts_per_page' => -1 );
$teaserProduct = wc_get_products($args);
$teaser = get_posts(['category_name' => 'teaser', 'posts_per_page' => 1]);
$banners = get_posts(['post_type' => 'banners', 'posts_per_page' => 4]);
$bannersSorted = [];
/**
 * sort banners by field 'position'
 */
foreach($banners as $banner){
    $fields = get_field('banner', $banner->ID);
    $position = $fields['position_1-4'];

    $bannersSorted[$position] = $banner;
}

/**
 * sort array by keys
 */
ksort($bannersSorted);

$args = array(
    'post_type' => 'product',
    'posts_per_page' => 4,
    'order' => 'desc'
);

$loop = new WP_Query($args);
get_header(); ?>
<style>
    .entry-header {
        display: none;
    }
</style>
<div class="home-info">
    <?php
    while (have_posts()) :
        the_post();

        do_action('storefront_page_before');

        get_template_part('content', 'page');

        /**
         * Functions hooked in to storefront_page_after action
         *
         * @hooked storefront_display_comments - 10
         */
        do_action('storefront_page_after');

    endwhile; // End of the loop.
    ?>
</div>
<?php if(count($teaser) > 0) : ?>
<?php
    $teaser = $teaser[0];
    $teaserImage = wp_get_attachment_url(get_post_thumbnail_id($teaser->ID), 'thumbnail');
?>
<h2 class="title">Aktuell</h2>
<div class="home-aktuelle links home-section">
    <div class="aktuelle-columns">
        <div class="aktuelle-column img-column">
            <a href="<?php echo $teaser->guid ?>">
                <figure class="wp-block-image size-large">
                    <img src="<?php echo $teaserImage ?>" alt="" class="wp-image-58">
                </figure>
            </a>
        </div>
        <div class="aktuelle-column content-column">
            <p class="teaser-title" data-href="<?php echo $teaser->guid ?>">
                <strong><?php echo $teaser->post_title ?></strong>
            </p>
<!--            --><?php //if(strlen(strip_tags($teaser->post_content)) > 600) : ?>
<!--            --><?php //echo substr($teaser->post_content, 0, 599) ?><!--...-->
            <p><?php echo get_field('teaser', $teaser->ID) ?></p>
           
            <div class="aktuelle-more">

                <a href="<?php  the_permalink(480); ?>" class="teaser-more link">Mehr</a>
              
            </div>

<!--            --><?php //else : ?>
<!--                --><?php //echo $teaser->post_content; ?>
<!--            --><?php //endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<h2 class="title">Neuerscheinungen</h2>
<div class="home-products home-section">
    <?php
    while ($loop->have_posts()) : $loop->the_post();
        global $product;
        ?>
        <div class="home-product">
            <div class="product-image-wrapper">
                <a href="<?php echo get_permalink() ?>"> <?php echo woocommerce_get_product_thumbnail() ?></a>
            </div>
            <span class="title"><?php echo get_the_title() ?></span>
            <?php if($product->get_price_html()){ ?>
            <p class="home-product-price"><?php echo $product->get_price_html() ?>.-</p>
                   
            <?php }else{?>
               

                <p class="home-product-price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">Select Options</bdi></span> <small class="woocommerce-price-suffix"></small></p>


                <?php }?>
            <!-- <div class="product-buttons">
                <a href="?add-to-cart='.$product->get_id().'" data-quantity="1"
                   class="button product_type_simple add_to_cart_button ajax_add_to_cart"
                   data-product_id="<?php echo $product->get_id() ?>" data-product_sku="" rel="nofollow">
                    <span class="dashicons dashicons-cart"></span>
                </a>
                <a href="<?php echo get_permalink() ?>">
                    <span class="dashicons dashicons-visibility"></span>
                </a>
            </div> -->
        </div>
    <?php
    endwhile;

    wp_reset_query();
    ?>
</div>
<h2 class="title">Unsere Partner</h2>
<div class="home-partners home-section">
    <?php foreach ($bannersSorted as $partner) : ?>
        <?php $fields = get_field('banner', $partner->ID); ?>
        <div class="partner">
            <a href="<?php echo $fields['url'] ?>" target="_blank">
                <img src="<?php echo $fields['logo']['url'] ?>" alt="">
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?php
do_action('storefront_sidebar');
get_footer();
?>
