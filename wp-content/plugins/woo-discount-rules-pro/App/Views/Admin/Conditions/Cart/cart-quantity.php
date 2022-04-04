<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$operator = isset($options->operator) ? $options->operator : 'greater_than_or_equal';
$calculate_from = isset($options->calculate_from) ? $options->calculate_from : 'from_cart';
echo ($render_saved_condition == true) ? '' : '<div class="cart_items_quantity">';
?>
<div class="wdr_cart_item_quantity_group wdr-condition-type-options">
    <div class="wdr-quantity-subtotal wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][operator]" class="awdr-left-align wdr_quantity_operator">
            <option value="less_than" <?php echo ($operator == "less_than") ? "selected" : ""; ?>><?php _e('Less than ( &lt; )', 'woo-discount-rules-pro') ?></option>
            <option value="less_than_or_equal" <?php echo ($operator == "less_than_or_equal") ? "selected" : ""; ?>><?php _e('Less than or equal ( &lt;= )', 'woo-discount-rules-pro') ?></option>
            <option value="greater_than_or_equal" <?php echo ($operator == "greater_than_or_equal") ? "selected" : ""; ?>><?php _e('Greater than or equal ( &gt;= )', 'woo-discount-rules-pro') ?></option>
            <option value="greater_than" <?php echo ($operator == "greater_than") ? "selected" : ""; ?>><?php _e('Greater than ( &gt; )', 'woo-discount-rules-pro') ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Cart item quantity should be', 'woo-discount-rules-pro'); ?></span>
    </div>

    <div class="cart-quantity-value wdr-input-filed-hight">
        <input name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][value]"
               value="<?php echo (isset($options->value)) ? $options->value : '' ?>" type="text" class="float_only_field awdr-left-align"
               placeholder="<?php _e('1', 'woo-discount-rules-pro');?>">
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Cart item quantity', 'woo-discount-rules-pro'); ?></span>
    </div>
    <div class="wdr-quantity-subtotal wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][calculate_from]" class="calculate_cart_from awdr-left-align">
            <option value="from_cart" <?php echo ($calculate_from == "from_cart") ? "selected" : ""; ?>><?php _e('Count all items in cart', 'woo-discount-rules-pro') ?></option>
            <option value="from_filter" <?php echo ($calculate_from == "from_filter") ? "selected" : ""; ?>><?php _e('Only count items chosen in the filters set for this rule', 'woo-discount-rules-pro') ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both "><?php _e('How to calculate the item quantity', 'woo-discount-rules-pro'); ?></span>
    </div>
</div>
<?php echo ($render_saved_condition == true) ? '' : '</div>'; ?>
