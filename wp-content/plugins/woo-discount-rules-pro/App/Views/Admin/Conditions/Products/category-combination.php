<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$combination = isset($options->combination) ? $options->combination : 'each';
$type = isset($options->type) ? $options->type : 'cart_quantity';
$operator = isset($options->operator) ? $options->operator : 'greater_than_or_equal';
$values = isset($options->category) ? $options->category   : false;
echo ($render_saved_condition == true) ? '' : '<div class="cart_item_category_combination">';
?>
<div class="category_combination_group wdr-condition-type-options">
    <div class="wdr-product-filter_qty wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][combination]" class="awdr-left-align">
            <option value="each" <?php echo ($combination == "each") ? "selected" : ""; ?>><?php _e('Each', 'woo-discount-rules-pro') ?></option>
            <option value="combine" <?php echo ($combination == "combine") ? "selected" : ""; ?>><?php _e('Combine', 'woo-discount-rules-pro') ?></option>
            <option value="any" <?php echo ($combination == "any") ? "selected" : ""; ?>><?php _e('Any', 'woo-discount-rules-pro') ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Combination type', 'woo-discount-rules-pro'); ?></span>
    </div>
    <div class="wdr-product_filter_method wdr-select-filed-hight wdr-search-box">
        <select multiple="" name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][category][]"
                class="awdr-cat-validation <?php echo ($render_saved_condition == true) ? 'edit-filters' : '' ?>"
                data-placeholder="<?php _e('Search Category', 'woo-discount-rules-pro');?>"
                data-list="product_category"
                data-field="autocomplete"
                style="width: 100%; max-width: 400px;  min-width: 180px;">
            <?php
            if ($values) {
                $item_name = '';
                foreach ($values as $value) {
                    $term_name = get_term_by('id', $value, 'product_cat');
                    if (!empty($term_name)) {
                        $parant_name = '';
                        if(isset($term_name->parent) && !empty($term_name->parent)){
                            if (function_exists('get_the_category_by_ID')) {
                                $parant_names = get_the_category_by_ID((int)$term_name->parent);
                                $parant_name = $parant_names . ' -> ';
                            }
                        }
                        $item_name = $parant_name.$term_name->name; ?>
                        <option value="<?php echo $value; ?>" selected><?php echo $item_name; ?></option><?php
                    }
                }
            } ?>
        </select>
        <span class="wdr_select2_desc_text"><?php _e('Select category', 'woo-discount-rules-pro'); ?></span>
    </div>
    <div class="wdr-input-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][type]" class="combination_operator awdr-left-align">
            <option value="cart_quantity" <?php echo ($type == "cart_quantity") ? "selected" : ""; ?>><?php _e('Quantity', 'woo-discount-rules-pro') ?></option>
            <option value="cart_subtotal" <?php echo ($type == "cart_subtotal") ? "selected" : ""; ?>><?php _e('Sub Total', 'woo-discount-rules-pro') ?></option>
            <option value="cart_line_item" <?php echo ($type == "cart_line_item") ? "selected" : ""; ?>><?php _e('Line Item Count', 'woo-discount-rules-pro') ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both"><?php _e('Select Value', 'woo-discount-rules-pro'); ?></span>
    </div>
    <div class="wdr-product-attributes-selector wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][operator]" class="cat_combination_operator awdr-left-align">
            <option value="less_than" <?php echo ($operator == "less_than") ? "selected" : ""; ?>><?php _e('Less than ( &lt; )', 'woo-discount-rules-pro') ?></option>
            <option value="less_than_or_equal" <?php echo ($operator == "less_than_or_equal") ? "selected" : ""; ?>><?php _e('Less than or equal ( &lt;= )', 'woo-discount-rules-pro') ?></option>
            <option value="greater_than_or_equal" <?php echo ($operator == "greater_than_or_equal") ? "selected" : ""; ?>><?php _e('Greater than or equal ( &gt;= )', 'woo-discount-rules-pro') ?></option>
            <option value="greater_than" <?php echo ($operator == "greater_than") ? "selected" : ""; ?>><?php _e('Greater than ( &gt; )', 'woo-discount-rules-pro') ?></option>
            <option value="equal_to" <?php echo ($operator == "equal_to") ? "selected" : ""; ?>><?php _e('Equal to ( = )', 'woo-discount-rules-pro') ?></option>
            <option value="not_equal_to" <?php echo ($operator == "not_equal_to") ? "selected" : ""; ?>><?php _e('Not equal to ( != )', 'woo-discount-rules-pro') ?></option>
            <option value="in_range" <?php echo ($operator == "in_range") ? "selected" : ""; ?>><?php _e('In range', 'woo-discount-rules-pro') ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Comparison should be', 'woo-discount-rules-pro'); ?></span>
    </div>
    <div class="cat_combination_from wdr-input-filed-hight">
        <input name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][from]"
               type="number" class="awdr-left-align cat_combination_from_placeholder cat_from_qty"
               value="<?php if(isset($options->from) && !empty($options->from)){ echo $options->from;}?>"
               placeholder="<?php _e('Value', 'woo-discount-rules-pro');?>" min="0" step="any">
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Value', 'woo-discount-rules-pro'); ?></span>
    </div>
    <div class="cat_combination_to wdr-input-filed-hight" style="<?php echo ($operator != "in_range") ? 'display: none;' : '';?>">
        <input name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][to]"
               type="number" class="awdr-left-align cat_to_qty"
               value="<?php if(isset($options->to) && !empty($options->to)){ echo $options->to;}?>"
               placeholder="<?php _e('To', 'woo-discount-rules-pro');?>" min="0" step="any">
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Value', 'woo-discount-rules-pro'); ?></span>
    </div>
</div>
<?php echo ($render_saved_condition == true) ? '' : '</div>'; ?>
