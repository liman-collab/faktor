<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$values = isset($options->value) ? $options->value : false;
$operator = isset($options->operator) ? $options->operator : 'user_email_tld';
echo ($render_saved_condition == true) ? '' : '<div class="user_email">';
?>
<div class="wdr_user_email_group wdr-condition-type-options">
    <div class="wdr_operator wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][operator]" class="awdr-left-align awdr-email-condition-eg-text">
            <option value="user_email_tld" <?php echo ($operator == "user_email_tld") ? "selected" : ""; ?>><?php _e('TLD (Eg:edu)', 'woo-discount-rules-pro') ?></option>
            <option value="user_email_domain" <?php echo ($operator == "user_email_domain") ? "selected" : ""; ?>><?php _e('Domain (Eg:gmail.com)', 'woo-discount-rules-pro') ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both "><?php _e('Email should be', 'woo-discount-rules-pro'); ?></span>
    </div>

    <div class="wdr_value wdr-input-filed-hight">
        <input type="text" style="min-width: 250px;" name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][value]"
               class="awdr-left-align awdr-validation"
               value="<?php echo ($values) ? $values : ''; ?>" placeholder="<?php _e('Enter values ', 'woo-discount-rules-pro');?>">
        <span class="wdr_desc_text awdr-clear-both awdr_user_email_tld" ><?php _e('Example : edu, org', 'woo-discount-rules-pro'); ?></span>
        <span class="wdr_desc_text awdr-clear-both awdr_user_email_domain"><?php _e('Example : gmail.com, yahoo.com', 'woo-discount-rules-pro'); ?></span>
    </div>
</div>
<?php echo ($render_saved_condition == true) ? '' : '</div>'; ?>
