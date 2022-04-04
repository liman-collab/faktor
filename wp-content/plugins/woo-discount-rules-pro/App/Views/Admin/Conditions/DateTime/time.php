<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
echo ($render_saved_condition == true) ? '' : '<div class="order_time">';
?>
    <div class="wdr_time_group wdr-condition-type-options">
        <div class="wdr-time-from wdr-input-filed-hight">
            <input type="text"
                   name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][from]" placeholder="<?php _e('From', 'woo-discount-rules-pro');?>"
                   class="wdr_time_picker awdr-left-align wdr-from-time"
                   value="<?php echo isset($options->from) ? $options->from : ''; ?>">
            <span class="wdr_desc_text awdr-clear-both "><?php _e('Time From', 'woo-discount-rules-pro'); ?></span>
        </div>
        <div class="wdr-time-to wdr-input-filed-hight">
            <input type="text"
                   name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][to]" placeholder="<?php _e('To', 'woo-discount-rules-pro');?>"
                   class="wdr_time_picker awdr-left-align wdr-to-time"
                   value="<?php echo isset($options->to) ? $options->to : ''; ?>">
            <span class="wdr_desc_text awdr-clear-both "><?php _e('Time To', 'woo-discount-rules-pro'); ?></span>
        </div>
    </div>
<?php echo ($render_saved_condition == true) ? '' : '</div>'; ?>