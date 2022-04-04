<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package storefront
 */
if (!is_active_sidebar('sidebar-1')) {
    return;
}
$orderTypes = get_terms([
    'hide_empty' => false,
    'taxonomy' => 'pa_order-type'
]);

?>
<?php if (!is_home() && !is_front_page() && !is_page('impressum') && !is_page('agb') && !is_page('datenschutzerklaerung') && !is_page('kontakt')): ?>
    <div id="secondary" class="widget-area" role="complementary">
<!--        --><?php //dynamic_sidebar('sidebar-1'); ?>
        <div id="woocommerce_layered_nav-2"
             class="widget woocommerce widget_layered_nav woocommerce-widget-layered-nav">
            <span class="gamma widget-title">Filtern nach</span>
            <?php
            $args = array(
                'hide_empty' => false,
                'taxonomy' => 'product_tag', // Taxonomy to return. Valid values are 'category', 'post_tag' or any registered taxonomy.
                'show_option_none' => 'Fachgebiet',
                'show_count' => 0,
                'orderby' => 'name',
                'value_field' => 'slug',
                'echo' => 0,
                'name' => 'product_tag',
                'id' => 'productTagFilter'
            );
            $select = wp_dropdown_categories($args);
            echo $select;
            ?>
            <ul class="woocommerce-widget-layered-nav-list products-filter-list">
                <?php foreach ($orderTypes as $orderType) : ?>
                    <?php
                    $allowedCategories = ['Print', 'E-Book', 'Kombi'];
                    $chosen = '';
                    $url = explode('?', $_SERVER['REQUEST_URI']);
                    $queryString = $url[0] . '?filter_order-type=' . $orderType->slug;
                    if ($_GET['filter_order-type'] === $orderType->slug) {
                        $chosen = 'chosen';
                        $queryString = $url[0];
                    }
                    ?>
                    <?php if (in_array($orderType->name, $allowedCategories)) : ?>
                        <li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term <?php echo $chosen ?>">
                            <a rel="nofollow"
                               href="<?php echo $queryString ?>">
                                <?php echo $orderType->name ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="products-sort">
            <p>Sortiert nach</p>
<!--            <form class="woocommerce-ordering" method="get">-->
<!--                <select name="orderby" class="orderby" aria-label="Shop-Bestellung">-->
<!--                    <option value="menu_order" selected="selected">Standardsortierung</option>-->
<!--                    <option value="popularity">Nach Beliebtheit sortiert</option>-->
<!--                    <option value="date">Sortieren nach neuesten</option>-->
<!--                    <option value="price">Nach Preis sortiert: niedrig zu hoch</option>-->
<!--                    <option value="price-desc">Nach Preis sortiert: hoch zu niedrig</option>-->
<!--                </select>-->
<!--                <input type="hidden" name="paged" value="1">-->
<!--            </form>-->
            <select name="orderby" class="orderby" aria-label="Shop-Bestellung">
                <option value="menu_order" selected="selected">Standardsortierung</option>
                <option value="popularity">Nach Beliebtheit sortiert</option>
                <option value="date">Sortieren nach neuesten</option>
                <option value="price">Nach Preis sortiert: niedrig zu hoch</option>
                <option value="price-desc">Nach Preis sortiert: hoch zu niedrig</option>
            </select>
            <input type="hidden" name="paged" value="1">
        </div>
    </div><!-- #secondary -->
<?php endif; ?>
