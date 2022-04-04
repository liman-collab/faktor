<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$operator = isset($options->operator) ? $options->operator : 'greater_than_or_equal';
$values = isset($options->time) ? $options->time : 'all_time';
$order_status = isset($options->status) ? $options->status : false;
echo ($render_saved_condition == true) ? '' : '<div class="purchase_spent">';
?>
<div class="wdr_purchase_spent_group wdr-condition-type-options">
    <div class="wdr-spent-method wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][time]" class="awdr-left-align">
            <optgroup label="All time">
                <option value="all_time" <?php echo ($values == "all_time") ? "selected" : ""; ?>><?php _e('all time', 'woo-discount-rules-pro') ?></option>
            </optgroup>
            <optgroup label="<?php _e('Current', 'woo-discount-rules-pro') ?>">
                <option value="now" <?php echo ($values == "now") ? "selected" : ""; ?>><?php _e('current day', 'woo-discount-rules-pro') ?></option>
                <option value="this_week" <?php echo ($values == "this_week") ? "selected" : ""; ?>><?php _e('current week', 'woo-discount-rules-pro') ?></option>
                <option value="first_day_of_this_month" <?php echo ($values == "first_day_of_this_month") ? "selected" : ""; ?>><?php _e('current month', 'woo-discount-rules-pro') ?></option>
                <option value="first_day_of_january_this_year" <?php echo ($values == "first_day_of_january_this_year") ? "selected" : ""; ?>><?php _e('current year', 'woo-discount-rules-pro') ?></option>
            </optgroup>
            <optgroup label="<?php _e('Days', 'woo-discount-rules-pro') ?>">
                <option value="-1_day" <?php echo ($values == "-1_day") ? "selected" : ""; ?>>
                    1 <?php _e('day', 'woo-discount-rules-pro') ?></option>
                <option value="-2_days" <?php echo ($values == "-2_days") ? "selected" : ""; ?>>
                    2 <?php _e('days', 'woo-discount-rules-pro') ?></option>
                <option value="-3_days" <?php echo ($values == "-3_days") ? "selected" : ""; ?>>
                    3 <?php _e('days', 'woo-discount-rules-pro') ?></option>
                <option value="-4_days" <?php echo ($values == "-4_days") ? "selected" : ""; ?>>
                    4 <?php _e('days', 'woo-discount-rules-pro') ?></option>
                <option value="-5_days" <?php echo ($values == "-5_days") ? "selected" : ""; ?>>
                    5 <?php _e('days', 'woo-discount-rules-pro') ?></option>
                <option value="-6_days" <?php echo ($values == "-6_days") ? "selected" : ""; ?>>
                    6 <?php _e('days', 'woo-discount-rules-pro') ?></option>
            </optgroup>
            <optgroup label="<?php _e('Weeks', 'woo-discount-rules-pro') ?>">
                <option value="-1_week" <?php echo ($values == "-1_week") ? "selected" : ""; ?>>
                    1 <?php _e('week', 'woo-discount-rules-pro') ?></option>
                <option value="-2_weeks" <?php echo ($values == "-2_weeks") ? "selected" : ""; ?>>
                    2 <?php _e('weeks', 'woo-discount-rules-pro') ?></option>
                <option value="-3_weeks" <?php echo ($values == "-3_weeks") ? "selected" : ""; ?>>
                    3 <?php _e('weeks', 'woo-discount-rules-pro') ?></option>
                <option value="-4_weeks" <?php echo ($values == "-4_weeks") ? "selected" : ""; ?>>
                    4 <?php _e('weeks', 'woo-discount-rules-pro') ?></option>
            </optgroup>
            <optgroup label="<?php _e('Months', 'woo-discount-rules-pro') ?>">
                <option value="-1_month" <?php echo ($values == "-1_month") ? "selected" : ""; ?>>
                    1 <?php _e('month', 'woo-discount-rules-pro') ?></option>
                <option value="-2_months" <?php echo ($values == "-2_months") ? "selected" : ""; ?>>
                    2 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-3_months" <?php echo ($values == "-3_months") ? "selected" : ""; ?>>
                    3 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-4_months" <?php echo ($values == "-4_months") ? "selected" : ""; ?>>
                    4 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-5_months" <?php echo ($values == "-5_months") ? "selected" : ""; ?>>
                    5 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-6_months" <?php echo ($values == "-6_months") ? "selected" : ""; ?>>
                    6 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-7_months" <?php echo ($values == "-7_months") ? "selected" : ""; ?>>
                    7 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-8_months" <?php echo ($values == "-8_months") ? "selected" : ""; ?>>
                    8 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-9_months" <?php echo ($values == "-9_months") ? "selected" : ""; ?>>
                    9 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-10_months" <?php echo ($values == "-10_months") ? "selected" : ""; ?>>
                    10 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-11_months" <?php echo ($values == "-11_months") ? "selected" : ""; ?>>
                    11 <?php _e('months', 'woo-discount-rules-pro') ?></option>
                <option value="-12_months" <?php echo ($values == "-12_months") ? "selected" : ""; ?>>
                    12 <?php _e('months', 'woo-discount-rules-pro') ?></option>
            </optgroup>
            <optgroup label="<?php _e('Years', 'woo-discount-rules-pro') ?>">
                <option value="-2_years" <?php echo ($values == "-2_years") ? "selected" : ""; ?>>
                    2 <?php _e('years', 'woo-discount-rules-pro') ?></option>
                <option value="-3_years" <?php echo ($values == "-3_years") ? "selected" : ""; ?>>
                    3 <?php _e('years', 'woo-discount-rules-pro') ?></option>
                <option value="-4_years" <?php echo ($values == "-4_years") ? "selected" : ""; ?>>
                    4 <?php _e('years', 'woo-discount-rules-pro') ?></option>
                <option value="-5_years" <?php echo ($values == "-5_years") ? "selected" : ""; ?>>
                    5 <?php _e('years', 'woo-discount-rules-pro') ?></option>
                <option value="-6_years" <?php echo ($values == "-6_years") ? "selected" : ""; ?>>
                    6 <?php _e('years', 'woo-discount-rules-pro') ?></option>
                <option value="-7_years" <?php echo ($values == "-7_years") ? "selected" : ""; ?>>
                    7 <?php _e('years', 'woo-discount-rules-pro') ?></option>
                <option value="-8_years" <?php echo ($values == "-8_years") ? "selected" : ""; ?>>
                    8 <?php _e('years', 'woo-discount-rules-pro') ?></option>
                <option value="-9_years" <?php echo ($values == "-9_years") ? "selected" : ""; ?>>
                    9 <?php _e('years', 'woo-discount-rules-pro') ?></option>
                <option value="-10_years" <?php echo ($values == "-10_years") ? "selected" : ""; ?>>
                    10 <?php _e('years', 'woo-discount-rules-pro') ?></option>
            </optgroup>
        </select>
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Purchase history', 'woo-discount-rules-pro'); ?></span>
    </div>

    <div class="wdr-spent-comparision wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][operator]" class="awdr-left-align">
            <option value="less_than" <?php echo ($operator == "less_than") ? "selected" : ""; ?>><?php _e('Less than ( &lt; )', 'woo-discount-rules-pro') ?></option>
            <option value="less_than_or_equal" <?php echo ($operator == "less_than_or_equal") ? "selected" : ""; ?>><?php _e('Less than or equal ( &lt;= )', 'woo-discount-rules-pro') ?></option>
            <option value="greater_than_or_equal" <?php echo ($operator == "greater_than_or_equal") ? "selected" : ""; ?>><?php _e('Greater than or equal ( &gt;= )', 'woo-discount-rules-pro') ?></option>
            <option value="greater_than" <?php echo ($operator == "greater_than") ? "selected" : ""; ?>><?php _e('Greater than ( &gt; )', 'woo-discount-rules-pro') ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Purchased amount should be', 'woo-discount-rules-pro'); ?></span>
    </div>

    <div class="wdr-spent-amount wdr-input-filed-hight">
        <input name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][amount]"
               value="<?php echo (isset($options->amount)) ? $options->amount : '' ?>" type="text"
               class="float_only_field awdr-left-align" placeholder="<?php _e('0.00', 'woo-discount-rules-pro');?>"
               min="0">
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Amount', 'woo-discount-rules-pro'); ?></span>
    </div>

    <div class="wdr-spent-order-status wdr-search-box">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][status][]" multiple
                class="wdr-wc-order-status <?php echo ($render_saved_condition == true) ? 'edit-all-loaded-values' : '' ?>"
                data-placeholder="<?php _e('Search Order Status', 'woo-discount-rules-pro');?>"
                data-list="order_status"
                data-field="autoloaded"
                style="width: 100%; max-width: 400px;  min-width: 180px;"><?php
            if ($order_status) {
                $settings_config = new \Wdr\App\Controllers\Admin\Settings();
                $woo_order_status = $settings_config->getWoocommerceOrderStatus();
                foreach ($order_status as $status) {
                    foreach ($woo_order_status as $woo_status) {
                        if ($woo_status['id'] == $status) {
                            $order_status_label = $woo_status['text'];
                        }
                    }
                    if ($order_status_label != '') { ?>
                        <option value="<?php echo $status; ?>" selected><?php echo $order_status_label; ?></option><?php
                    }
                }
            }
            ?>
        </select>
        <span class="wdr_select2_desc_text"><?php _e('order status', 'woo-discount-rules-pro'); ?></span>
    </div>
</div>
<?php echo ($render_saved_condition == true) ? '' : '</div>'; ?>
