<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Uber uns & Team
 *
 * @package faktor
 */

$team = get_posts(['post_type' => 'team-member']);

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

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
        </main><!-- #main -->
    </div><!-- #primary -->
    <div class="team home-products">
        <?php foreach ($team as $member) : ?>
            <div class="member home-product">
                <img src="<?php echo get_field('member_image', $member->ID)['url'] ?>" alt="">
                <h3>
                    <?php echo $member->post_title ?>
                </h3>
                <div class="member-description">
                    <?php echo $member->post_content ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php
get_footer();
