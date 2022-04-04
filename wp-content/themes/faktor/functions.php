<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
require_once 'helpers/Request.php';
$theme = wp_get_theme('storefront');
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
    $content_width = 980; /* pixels */
}

$storefront = (object)array(
    'version' => $storefront_version,

    /**
     * Initialize all the things.
     */
    'main' => require 'inc/class-storefront.php',
    'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if (class_exists('Jetpack')) {
    $storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if (storefront_is_woocommerce_activated()) {
    $storefront->woocommerce = require 'inc/woocommerce/class-storefront-woocommerce.php';
    $storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

    require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

    require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
    require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
    require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if (is_admin()) {
    $storefront->admin = require 'inc/admin/class-storefront-admin.php';

    require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if (version_compare(get_bloginfo('version'), '4.7.3', '>=') && (is_admin() || is_customize_preview())) {
    require 'inc/nux/class-storefront-nux-admin.php';
    require 'inc/nux/class-storefront-nux-guided-tour.php';

    if (defined('WC_VERSION') && version_compare(WC_VERSION, '3.0.0', '>=')) {
        require 'inc/nux/class-storefront-nux-starter-content.php';
    }
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

function load_dashicons()
{
    wp_enqueue_style('dashicons');
}

add_action('wp_enqueue_scripts', 'load_dashicons');

// Add Custom roles
// Private Customer role
add_role('private_customer', 'Private Customer', [
    'read' => true,
    'edit_posts' => true,
]);

// Schools and Education role
add_role('schools_and_education', 'Schools and Education', [
    'read' => true,
    'edit_posts' => true,
]);

// custom discount
function filter_woocommerce_get_discounted_price($price, $values, $instance)
{
    //$price represents the current product price without discount
    //$values represents the product object
    //$instance represent the cart object
    global $wpdb;
    $user = wp_get_current_user();
    $discount = 0;
    $sql = 'select * from wp_bex_discount_details where orders_to >= ' . $values['quantity'] . ' && orders_from <= ' . $values['quantity'];
    $row = $wpdb->get_row($sql);
    if (in_array('schools_and_education', $user->roles) && $row) {
        $discount = (int)$row->discount;
    }
    $discount = ($price * $discount) / 100;
    return $price - $discount;
}

;
add_filter('woocommerce_get_discounted_price', 'filter_woocommerce_get_discounted_price', 10, 3);

//add_action('woocommerce_checkout_order_processed','custom_disount',10,1);
//function custom_disount($order_id){
//    $user = wp_get_current_user();
//    $userMeta = get_user_meta($user->ID);
//    $discountValue = (int)$userMeta['discount'][0];
//    $order = wc_get_order($order_id);
//    $order_items = $order->get_items();
//    foreach ($order_items as $order_item_key => $order_item) {
//        $product = new WC_Product((int) $order_item['product_id']);
//        die(var_dump($product->regular_price));
//        $quantity = (int) $order_item['qty'];
//        $discount=($product->regular_price*$discountValue)/100;  //disount.
//        wc_update_order_item_meta($order_item_key,'_line_total',($product->regular_price*$quantity)-($discount*$quantity));
//    }
//}

//add_action('woocommerce_before_add_to_cart_form', 'my_custom_cart_form');
//function my_custom_cart_form(){
//    global $product;
//    echo $product->get_description();
//    echo
//    '<select id="bookOrderType" class="input-text" name="order_type">
//        <option value="">Choose order type</option>
//        <option value="online">Online</option>
//        <option value="print">Print</option>
//        <option value="print_online">Print + Online</option>
//    </select>';
//}

add_filter('woocommerce_get_price_html', 'lw_hide_variation_price', 10, 2);
    function lw_hide_variation_price( $v_price, $v_product ) {
        $v_product_types = array( 'variable');
        
            if ( in_array ( $v_product->product_type, $v_product_types ) && !(is_shop()) ) {
             return '';
            }elseif(in_array ( $v_product->product_type, $v_product_types ) && (is_shop()) ){
                return '';
            }
          
            return $v_price;
}

add_action( 'woocommerce_after_shop_loop_item', 'bbloomer_loop_per_product' );
 
function bbloomer_loop_per_product() {
    global $product;
    if ( $product->is_type( 'variable' )) {
    ?> <span><a href="<?php the_permalink(); ?>" style="   
   
    font-weight:bolder;
    font-size: 24px;
    font-family: Roboto-Bold;
    color: black;
    cursor: pointer;">Select Options</a></span> <?php
    } 
}



function getProtectedValue($obj, $name)
{
    $array = (array)$obj;
    $prefix = chr(0) . '*' . chr(0);
    return $array[$prefix . $name];
}

// remove short description on product page
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

// remove related products on product page
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

// add custom 'Order Type' field
add_action('woocommerce_before_add_to_cart_button', 'wdm_add_custom_fields');
/**
 * Adds custom field for Product
 * @return [type] [description]
 */
function wdm_add_custom_fields()
{
    global $product;
    ob_start();
    // $terms = get_terms([
    //     'hide_empty' => false,
    //     'taxonomy' => 'pa_order-type'
    // ]);
    $terms = get_the_terms($product->get_id(), 'pa_order-type');
//    die(var_dump($terms));
    if (count($terms) <= 0) {
        return false;
    }
    echo '<div class="order-type-parent">';
    foreach ($terms as $term) {
        $checked = false;
        if (count($terms) === 1) {
            $checked = true;
        } else if ($term->slug === 'print') {
            $checked = true;
        }
        ?>
        <label>
            <input type="radio" name="order_type" id="term-<?php echo $term->slug ?>"
                <?php echo $checked ? 'checked' : '' ?>
                   value="<?php echo $term->name ?>">
            <span><?php echo $term->name ?></span>
        </label>
        <?php
    }
    echo '</div>';
    echo '<p class="product-amount-label"><strong>Menge</strong></p>';

    $content = ob_get_contents();
    ob_end_flush();

    return $content;
}

add_filter('woocommerce_add_cart_item_data', 'wdm_add_item_data', 10, 3);

/**
 * Add custom data to Cart
 * @param  [type] $cart_item_data [description]
 * @param  [type] $product_id     [description]
 * @param  [type] $variation_id   [description]
 * @return [type]                 [description]
 */
function wdm_add_item_data($cart_item_data, $product_id, $variation_id)
{
    if (isset($_REQUEST['order_type'])) {
        $cart_item_data['order_type'] = sanitize_text_field($_REQUEST['order_type']);
    } else if (isset($_POST['orderTypeCat'])) {
        // this data comes from products page
        $orderTypes = get_terms([
            'hide_empty' => false,
            'taxonomy' => 'pa_order-type'
        ]);
        $type = '';
        foreach ($orderTypes as $orderType) {
            if ($orderType->slug == $_POST['orderTypeCat']) {
                $type = $orderType->name;
            }
        }
        $cart_item_data['order_type'] = sanitize_text_field($type);
    } else {
        $cart_item_data['order_type'] = 'Print';
    }

    return $cart_item_data;
}


add_filter('woocommerce_get_item_data', 'wdm_add_item_meta', 10, 2);

/**
 * Display information as Meta on Cart page
 * @param  [type] $item_data [description]
 * @param  [type] $cart_item [description]
 * @return [type]            [description]
 */
function wdm_add_item_meta($item_data, $cart_item)
{

    if (array_key_exists('order_type', $cart_item)) {
        $custom_details = $cart_item['order_type'];

        $item_data[] = array(
            'key' => 'Format',
            'value' => $custom_details
        );
    }

    return $item_data;
}


add_action('woocommerce_checkout_create_order_line_item', 'wdm_add_custom_order_line_item_meta', 10, 4);

function wdm_add_custom_order_line_item_meta($item, $cart_item_key, $values, $order)
{
    if (!array_key_exists('Format', $values)) {
        $item->add_meta_data('Format', $values['order_type']);
    }
}

//Remove WooCommerce Tabs - this code removes all 3 tabs - to be more specific just remove actual unset lines
add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);

function woo_remove_product_tabs($tabs)
{

    unset($tabs['description']);        // Remove the description tab
    unset($tabs['reviews']);            // Remove the reviews tab
    unset($tabs['additional_information']);    // Remove the additional information tab

    return $tabs;

}

/**
 * Disable sidebar on product pages in Storefront.
 *
 * @param bool $is_active_sidebar
 * @param int|string $index
 *
 * @return bool
 */
function iconic_remove_sidebar($is_active_sidebar, $index)
{
    if ($index !== "sidebar-1") {
        return $is_active_sidebar;
    }

    if (!is_product()) {
        return $is_active_sidebar;
    }

    return false;
}

add_filter('is_active_sidebar', 'iconic_remove_sidebar', 10, 2);


// add description under product order button
add_action('woocommerce_after_add_to_cart_button', 'add_product_descriptions');
/**
 * Adds custom field for Product
 * @return [type] [description]
 */
function add_product_descriptions()
{
    global $product;
    $desc = $product->get_description();
    $shortDesc = $product->get_short_description();
    $artikelDetails = get_field('artikeldetails', $product->get_id());
    $leseprobe_pdf = get_field('leseprobe_pdf', $product->get_id());
    ?>
    <div class="product-accordion">
        <div class="acc-openers">
            <?php if (!empty(trim($desc))) : ?>
                <a href="javascript:void(0)" class="link acc-open" data-target="#productDesc">Beschreibung</a>
            <?php endif; ?>
            <?php if (!empty(trim($artikelDetails))) : ?>
                <a href="javascript:void(0)" class="acc-open" data-target="#artikelDetails">Artikeldetails</a>
            <?php endif; ?>
            <?php if ($leseprobe_pdf) : ?>
                <a href="javascript:void(0)" class="acc-open" data-target="#productLeseprobe">Leseprobe</a>
            <?php endif; ?>
            <?php if (!empty(trim($product->get_short_description()))) : ?>
                <a href="javascript:void(0)" class="acc-open" data-target="#productShortDesc">E-Book</a>
            <?php endif; ?>
        </div>
        <div class="acc-content">
            <?php if (!empty(trim($desc))) : ?>
                <div id="productDesc">
                    <div>
                        <?php foreach (explode("\n", $desc) as $line) : ?>
                            <p><?php echo $line; ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty(trim($artikelDetails))) : ?>
                <div id="artikelDetails">
                    <div>
                        <?php echo $artikelDetails; ?>

                        <a target="_blak"
                           href="<?php echo get_field('inhaltsverzeichnis', $product->get_id())['url'] ?>"
                           class="link">
                            <span class="dashicons dashicons-media-document"></span> Inhaltsverzeichnis
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($leseprobe_pdf) : ?>
                <div id="productLeseprobe">
                    <div class="lp-overlay-parent">
                        <img src="<?php echo get_field('leseprobe_image', $product->get_id())['url'] ?>" alt="">
                        <a class="lp-link-overlay"
                           href="<?php echo $leseprobe_pdf['url'] ?>" target="_blank">
                            <span class="dashicons dashicons-visibility"></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty(trim($shortDesc))) : ?>
                <div id="productShortDesc">
                    <div>
                        <?php echo $shortDesc ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * products list add to card button
 */
add_action('woocommerce_after_shop_loop_item_title', 'add_product_buttons');
function add_product_buttons()
{
    global $product;
    if (is_product_category() || is_shop()) {
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
    }
    ?>
    <!-- <div class="product-buttons">
        <a href="?add-to-cart=<?php echo $product->get_id() ?>" data-quantity="1"
           class="button product_type_simple add_to_cart_button ajax_add_to_cart added"
           data-product_id="<?php echo $product->get_id() ?>" data-product_sku=""
           aria-label="Add “<?php echo $product->get_title() ?>” to your cart" rel="nofollow">
            <span class="dashicons dashicons-cart"></span>
        </a>
        <a href="<?php echo get_permalink(); ?>">
            <span class="dashicons dashicons-visibility"></span>
        </a>
    </div> -->
    <?php
}

add_filter('woocommerce_product_tag_cloud_widget_args', 'nwb_woo_tag_cloud_filter');
function nwb_woo_tag_cloud_filter($args)
{
    $args = array(
        'hide_empty' => false,
        'smallest' => 14,
        'largest' => 14,
        'format' => 'list',
        'taxonomy' => 'product_tag',
        'unit' => 'px',
    );
    return $args;
}

// bz api after checkout
add_action('woocommerce_checkout_update_order_meta', 'new_order_request');
function new_order_request($order_id, $custom_order = false)
{
    $shippingPost = [
        'A-Post Schweiz' => 16,
        'B-Post Schweiz' => 10,
        'Sendung ins Ausland' => 28, // same as B-Post Ausland
//        'A-Post Ausland' => 12,
//        'B-Post Ausland' => 28,
    ];
    $order = wc_get_order($order_id);
    if ($custom_order) {
        $shippingMethod = $custom_order['shipping_post']['title'];
        $shippingTotal = (float)$custom_order['shipping_post']['price'];
    } else {
        $shippingMethod = $order->get_shipping_method();
        $shippingTotal = (float)$order->get_shipping_total();
    }

    $sendMail = false;
    $printOrKombi = false;

    foreach ($order->get_items() as $item) {
        if ($item->get_meta('Format') == 'Print' || $item->get_meta('Format') == 'Kombi') {
            $printOrKombi = true;
        }

        if ($item->get_meta('Format') !== 'Print') {
            $sendMail = true;
        }
    }
    if ($sendMail) {
        $emails = [$_POST['billing_email']];
        if (isset($_POST['shipping_email']) && !empty($_POST['shipping_email'])) {
            $emails[] = $_POST['shipping_email'];
        }

        $filePath = ABSPATH . 'wp-content/themes/faktor/woocommerce/emails/edubase-template.php';
        ob_start();
        if (file_exists($filePath)) {
            include($filePath);
        }
        $content = ob_get_clean();

        wp_mail($emails, 'Faktor Edubase', $content, array('Content-Type: text/html; charset=UTF-8'));
//        if(wp_mail($emails, 'Faktor Edubase', $content, array('Content-Type: text/html; charset=UTF-8'))){
//            die('sent');
//        }
//        die('error sending');
    }

    if ($printOrKombi) {
        bz_api_request($order, $shippingPost[$shippingMethod], $shippingTotal);
    }
}

// remove product short description
//function remove_short_description()
//{
//    remove_meta_box('postexcerpt', 'product', 'normal');
//}
//
//add_action('add_meta_boxes', 'remove_short_description', 999);

// remove product image zoom on hover
function remove_image_zoom_support()
{
    remove_theme_support('wc-product-gallery-zoom');
}

add_action('after_setup_theme', 'remove_image_zoom_support', 100);

// remove on sale product text
add_filter('woocommerce_sale_flash', 'lw_hide_sale_flash');
function lw_hide_sale_flash()
{
    return false;
}

// change country checkout label
add_filter('woocommerce_default_address_fields', 'change_country_checkout_label', 9999);
function change_country_checkout_label($fields)
{
    $fields['country']['label'] = 'Land';
    return $fields;
}

// change address2 checkout label
add_filter('woocommerce_default_address_fields', 'change_address2_checkout_label', 10, 1);
function change_address2_checkout_label($address_fields)
{
    $address_fields['address_2']['placeholder'] = __('Adresszusatz', 'woocommerce');
    $address_fields['address_1']['label'] = __('Strasse, Nr.', 'woocommerce');
    $address_fields['address_1']['placeholder'] = __('Strasse, Nr.', 'woocommerce');

    return $address_fields;
}

/**
 * Change a currency symbol
 */
add_filter('woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2);

function change_existing_currency_symbol($currency_symbol, $currency)
{
    switch ($currency) {
        case 'CHF':
            $currency_symbol = 'Fr.';
            break;
    }
    return $currency_symbol;
}

// remove billing street validation
add_filter('woocommerce_checkout_fields', 'remove_billing_street_validation');
function remove_billing_street_validation($fields)
{
    unset($fields['billing']['billing_address_1']['validate']);
    unset($fields['billing_address_1']['validate']);
    return $fields;

}

add_action('woocommerce_after_checkout_validation', 'misha_validate_fname_lname', 10, 2);

function misha_validate_fname_lname($fields, $errors)
{
    if (empty($fields['billing_address_1'])) {
        $errors->add('validation', '<strong>Strasse</strong> ist ein Pflichtfeld.');
    }
}

// add custom js to dashboard
add_action('admin_footer', 'add_admin_js');
function add_admin_js()
{
    echo '
        <style>
        #wp-content-editor-tools {
            padding-top: 10px !important;
        }
        
        #poststuff #post-body-content > label > b {
            font-weight: bold;
        }
</style>
        <script>
        jQuery(document).ready(function($){
           $("#postdivrich").before("<label><b>Beschreibung</b></label>"); 
        });
</script>
    ';
}

// create team post type
function team_post_type()
{
    register_post_type('team-member', [
        'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
        'publicly_queryable' => true,  // you should be able to query it
        'show_ui' => true,  // you should be able to edit it in wp-admin
        'exclude_from_search' => true,  // you should exclude it from search results
        'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
        'has_archive' => false,  // it shouldn't have archive page
        'rewrite' => false,  // it shouldn't have rewrite rules
        'label' => 'Team',
        'supports' => []
    ]);
}

add_action('init', 'team_post_type');

// insert active codes
function insert_activecodes($post_ID)
{
    if (get_post_type($post_ID) === 'product') {
        global $wpdb;
        require_once get_template_directory() . '/vendor/simplexlsx/src/SimpleXLSX.php';
        $file = get_field('codes_file', $post_ID);
        $path = parse_url($file['url'], PHP_URL_PATH);
        $filepath = get_home_path() . $path;
        if ($xlsx = SimpleXLSX::parse($filepath)) {
//        echo '<table><tbody>';
            $i = 0;
            foreach ($xlsx->rows() as $elt) {
                if ($i > 0) {
                    $wpdb->insert($wpdb->prefix . 'bex_activationcodes', ['product_id' => $post_ID, 'code' => $elt[2]]);
                    echo $elt[2] . '<br/>';
                }
                $i++;
            }
//        echo "</tbody></table>";
        } else {
            echo SimpleXLSX::parseError();
        }
    }
}

add_action('save_post', 'insert_activecodes');

//add_action( 'woocommerce_before_checkout_process', 'initiate_order' , 10, 1 );
//function initiate_order($order_id){
//    $order = wc_create_order($order_id);
//}

add_filter('woocommerce_add_to_cart_validation', 'so_validate_add_cart_item', 10, 5);
function so_validate_add_cart_item($passed, $product_id, $quantity, $variation_id = '', $variations = '')
{
    if ($_POST['order_type'] === 'Kombi' || $_POST['order_type'] === 'E-Book') {
        // do your validation, if not met switch $passed to false
        $passed = false;
        global $wpdb;
        $codesTable = $wpdb->prefix . 'bex_activationcodes';
        $free = $wpdb->get_row("select count(*) as nr from $codesTable where used = 0");
        if ($free->nr > 0) {
            $passed = true;
        }
        $check = $wpdb->get_row("select * from $codesTable where product_id = $product_id and user_id = " . get_current_user_id());
        if ($check) {
            $passed = true;
        }
        if (!$passed) {
            wc_add_notice(__('Leider sind keine Aktivierungscodes mehr verfügbar. Bitte kontaktiern Sie uns.', 'textdomain'), 'error');
        }
    } else {
        $passed = true;
    }

    if (!isset($_POST['order_type'])) {
        $passed = false;
        wc_add_notice(__('Bitte wählen Sie ein Format für Ihr Produkt.', 'textdomain'), 'error');
    }

    return $passed;
}

add_action('woocommerce_checkout_order_processed', 'new_order');
function new_order($order_id)
{
    global $wpdb;
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();
    $table = $wpdb->prefix . 'bex_activationcodes';
    $isAbo = false;
    // Get and Loop Over Order Items
    $products = [];
    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();
        $orderType = $item->get_meta('Format');

        if ($orderType === 'Print' || $wpdb->get_row("select * from $table where product_id = $product_id and user_id = $user_id")) {
            continue;
        }
        $row = $wpdb->get_row("select id, count(id) as free from $table where product_id = $product_id and used = 0");
        if (!$row) {
            echo 'not more activation codes';
        }
        $wpdb->update($table, ['user_id' => $user_id, 'used' => 1, 'date_used' => date('Y-m-d H:i:s')], ['id' => $row->id]);
        $total = $wpdb->get_row("select count(*) as total from $table where product_id = $product_id");
        $products[] = [
            'product' => $product_id,
            'free' => ((int)$row->free) - 1,
            'total' => $total->total
        ];

        if (((int)$row->free) - 1 <= 50) {
            wp_mail(['info@bexo.com', get_bloginfo('admin_email')], 'missing codes', 'missing codes for product: ' . $item->get_name(), array('Content-Type: text/html; charset=UTF-8'));
        }
    }
}

// display codes and users to product owner
// Add custom Meta box to admin products pages
add_action('add_meta_boxes', 'create_product_technical_specs_meta_box');
function create_product_technical_specs_meta_box()
{
    add_meta_box(
        'product_codes_used',
        __('Product Codes used', 'cmb'),
        'add_custom_content_meta_box',
        'product',
        'normal',
        'default'
    );
}

// Custom metabox content in admin product pages
function add_custom_content_meta_box($post)
{
    global $wpdb;
    $codesTable = $wpdb->prefix . 'bex_activationcodes';
    $product = wc_get_product($post->ID);
    $results = $wpdb->get_results(
        "select codes.user_id, codes.code,
        MAX(CASE WHEN usermeta.meta_key = 'first_name' THEN meta_value END) AS first_name,
        MAX(CASE WHEN usermeta.meta_key = 'last_name' THEN meta_value END) AS last_name 
        from $codesTable codes, {$wpdb->prefix}usermeta usermeta 
        where usermeta.user_id = codes.user_id
        and product_id = $post->ID and used = 1"
    );
    echo '<div class="product_codes_used">';
    foreach ($results as $result) {
        echo $result->code . ' - ' . $result->first_name . ' ' . $result->last_name;
    }
    echo '</div>';
}

// wrap product images to div
add_action('woocommerce_before_shop_loop_item_title', function () {
    echo '<div class="product-image-wrapper">';
}, 9);
add_action('woocommerce_before_shop_loop_item_title', function () {
    echo '</div>';
}, 11);


function customize_wc_errors($error)
{
    if (strpos($error, 'Billing') !== false) {
        $error = str_replace('Billing ', '', $error);
    }
    if (strpos($error, 'Shipping') !== false) {
        $error = str_replace('Shipping ', '', $error);
    }
    return $error;
}

add_filter('woocommerce_add_error', 'customize_wc_errors');

// Callback function to insert 'styleselect' into the $buttons array
function my_mce_buttons_2($buttons)
{
    array_unshift($buttons, 'styleselect');
    return $buttons;
}

// Register our callback to the appropriate filter
add_filter('mce_buttons_2', 'my_mce_buttons_2');

// Callback function to filter the MCE settings
function my_mce_before_init_insert_formats($init_array)
{
    // Define the style_formats array
    $style_formats = array(
        // Each array child is a format with it's own settings
        array(
            'title' => '20px font-size',
            'block' => 'span',
            'classes' => 'twenty-px',
            'wrapper' => true,
        ),
        array(
            'title' => 'Red UL List',
            'selector' => 'ul',
            'classes' => 'custom-bullet-ul',
            'wrapper' => true,
        ),
        array(
            'title' => 'Red Links',
            'selector' => 'div',
            'classes' => 'links',
            'wrapper' => false,
        ),
    );
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = wp_json_encode($style_formats);

    return $init_array;

}

// Attach callback to 'tiny_mce_before_init' 
add_filter('tiny_mce_before_init', 'my_mce_before_init_insert_formats');

function my_theme_add_editor_styles()
{
    add_editor_style('mce-custom-style.css');
}

add_action('init', 'my_theme_add_editor_styles');

/**
 * Force WooCommerce terms and conditions link to open in a new page when clicked on the checkout page
 *
 * @author   Golden Oak Web Design <info@goldenoakwebdesign.com>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPLv2+
 */
function golden_oak_web_design_woocommerce_checkout_terms_and_conditions()
{
    remove_action('woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30);
}

add_action('wp', 'golden_oak_web_design_woocommerce_checkout_terms_and_conditions');


add_filter('woocommerce_variable_sale_price_html', 'wpglorify_variation_price_format', 10, 2);
add_filter('woocommerce_variable_price_html', 'wpglorify_variation_price_format', 10, 2);

function wpglorify_variation_price_format($price, $product)
{

// Main Price
    $prices = array($product->get_variation_price('min', true), $product->get_variation_price('max', true));
    $price = $prices[0] !== $prices[1] ? sprintf(__('Ab %1$s', 'woocommerce'), wc_price($prices[0])) : wc_price($prices[0]);

// Sale Price
    $prices = array($product->get_variation_regular_price('min', true), $product->get_variation_regular_price('max', true));
    sort($prices);
    $saleprice = $prices[0] !== $prices[1] ? sprintf(__('Ab %1$s', 'woocommerce'), wc_price($prices[0])) : wc_price($prices[0]);

    if ($price !== $saleprice) {
        $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
    }
    return $price;
}

add_filter( 'gettext', 'change_fields_label', 10, 2 );
function change_fields_label( $translation, $original )
{
    if ( 'Product short description' == $original ) {
        return 'E-Book';
    }
    return $translation;
}

// add_filter( 'wp_mail_from_name', 'sender_name' );
// function sender_name( $original_email_from ) {
// return 'Test';
// }

add_filter( 'wc_product_sku_enabled', '__return_false' );

add_filter( 'woocommerce_shipping_calculator_enable_address', '__return_true' );