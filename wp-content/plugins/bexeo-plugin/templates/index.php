<?php
global $wp_roles, $wpdb;
if (isset($_POST['role']) && !empty($_POST['discount']) && !empty($_POST['role'])) {
    $wpdb->query("delete from wp_bex_discounts where role = '" . $_POST['role'] . "'");
    $wpdb->query("INSERT INTO wp_bex_discounts(`role`) VALUES('" . $_POST['role'] . "');");
    $foreignKey = $wpdb->insert_id;
    foreach($_POST['discount'] as $key=>$value){
        $wpdb->query("insert into wp_bex_discount_details(`orders_from`, `orders_to`, `discount`, `discount_id`) values('{$_POST['orders_from'][$key]}', '{$_POST['orders_to'][$key]}', '{$_POST['discount'][$key]}', '{$foreignKey}')");
    }
}
if (isset($_POST['role'])) {
    $discount = $wpdb->get_row("select * from wp_bex_discounts where role = '" . $_POST['role'] . "'");
    $discount_details = $wpdb->get_results('select orders_from, orders_to, discount from wp_bex_discount_details');
//    $discount = $discount ? $discount->discount . '%' : '0%';
    $attr = '';
} else {
    $attr = ' disabled';
}
if (!isset($wp_roles))
    $wp_roles = new WP_Roles();
?>
<h1>Discounts Manager</h1>
<?php
if (isset($_POST['role'])) {
    echo '<p>Selected role: <strong>' . $_POST['role'] . '</strong>';
} else {
    echo '<p>Choose role to view and change the discount.</p>';
}
?>
<form action="/wp-admin/admin.php?page=discounts_manager" method="POST" class="discount-form">
    <select name="role" id="roles" class="<?php if(isset($_POST['role'])){ echo 'dm-hidden'; } ?>">
        <option value=""></option>
        <?php
        foreach ($wp_roles->roles as $key => $role) {
            if (isset($_POST['role']) && $_POST['role'] === $key) {
                echo '<option value="' . $key . '" selected>' . $role['name'] . '</option>';
            } else {
                echo '<option value="' . $key . '">' . $role['name'] . '</option>';
            }
        }
        ?>
    </select>
    <?php if (isset($_POST['role'])) { ?>
        <div class="clonable">
            <?php foreach($discount_details as $detail){ ?>
                <div class="clone-content">
                    <input placeholder="Orders from" type="number" class="field-required" value="<?php echo $detail->orders_from ?>" name="orders_from[]">
                    <input placeholder="Orders to" type="number" class="field-required" value="<?php echo $detail->orders_to ?>" name="orders_to[]">
                    <input placeholder="discount" type="number" class="field-required" value="<?php echo $detail->discount ?>" name="discount[]">
                    <button class="clone-remove-row">X</button>
                </div>
            <?php } ?>
            <div class="clone-content">
                <input placeholder="Orders from" class="field-required" type="number" value="" name="orders_from[]">
                <input placeholder="Orders to" class="field-required" type="number" value="" name="orders_to[]">
                <input placeholder="discount" class="field-required" type="number" value="" name="discount[]">
                <button class="clone-remove-row">X</button>
            </div>
            <button class="clone-btn">+</button>
        </div>
    <?php } ?>
    <button class="go-back <?php if(!isset($_POST['role'])){echo 'dm-hidden';} ?>">BACK</button>
    <button type="submit"<?php echo $attr; ?> id="discountSubmit">
        <?php
        if (isset($_POST['role'])) {
            echo 'SAVE';
        } else {
            echo 'CHANGE';
        }
        ?>
    </button>
</form>
