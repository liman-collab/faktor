<?php
$products = Helper::getWcProducts();

$shippingMethods = [];

$zones = WC_Shipping_Zones::get_zones();
$zones = array_map(function ($zone) {
    return $zone['shipping_methods'];
}, $zones);
foreach ($zones as $methods) {
    foreach ($methods as $method) {
//                        if($method->get_title() === 'B-Post Schweiz'){
//                            die(var_dump($method->instance_settings));
//                        }
        $countConditions = count($method->instance_settings['method_rules']);
        $shippingMethods[] = [
            'title' => $method->get_title(),
            'cost_per_order' => $method->instance_settings['method_rules'][$countConditions - 1]['cost_per_order']
        ];
    }
}
?>

<div class="custom-order-wrapper">
    <h1>Custom Order</h1>
    <br>
    <div class="row">
        <div class="col-md-6">
            <form id="addProductForm">
                <div class="row">
                    <div class="col-12">
                        <select id="customOrderProduct" name="product_id" class="co-field form-control" required>
                            <option value="">Produkt*</option>
                            <?php foreach ($products as $product) : ?>
                                <option value="<?php echo $product->get_id() ?>"
                                        data-categories="<?php echo $product->categories_as_string ?>"
                                        data-price="<?php echo $product->get_price() ?>"
                                        data-variation="<?php echo $product->is_type('variable') ?>">
                                    <?php echo $product->get_title() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <div id="productVariations"></div>
                    </div>
                    <div class="col-12 col-sm-5">
                        <input type="number" name="price" class="co-field form-control" id="priceEl"
                               placeholder="Preis in CHF*" required>
                    </div>
                    <div class="col-12 col-sm-5">
                        <input type="number" name="quantity" min="1" class="co-field form-control" id="qtyEl"
                               placeholder="Anzahl*" required>
                    </div>
                    <div class="col-12 col-sm-2">
                        <button class="primary button-primary" type="submit" id="addProductBtn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <div class="shipping-methods"></div>
        </div>
    </div>
    <br>
    <table id="orderProductsTable" class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Produkte</th>
            <th>Variation</th>
            <th>Preis</th>
            <th>Anzahl</th>
            <th>Entfernen</th>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th id="opTableTotal"></th>
            <th></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
    <br>
    <form id="customOrderForm">
        <div class="co-addresses row">
            <div class="co-billing-address col-md-6">
                <div class="page-row">
                    <h4>Rechnungsadresse</h4>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_company" placeholder="Firma">
                    </div>
                    <div class="col-12 col-sm-6"></div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_first_name"
                               placeholder="Vorname*" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_last_name" placeholder="Name*"
                               required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="email" class="co-field form-control" name="billing_email" placeholder="E-Mail">
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="tel" class="co-field form-control" name="billing_phone" placeholder="Telefon">
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_address_1" placeholder="Adresse*"
                               required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_address_2"
                               placeholder="Adresszusatz">
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_postcode" placeholder="PLZ*"
                               required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_country" placeholder="Land*"
                               value="Schweiz" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_city" placeholder="Ort*"
                               required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="co-field form-control" name="billing_state" placeholder="Kanton">
                    </div>
                </div>
            </div>
            <div class="col-md-6"></div>
            <div class="co-shipping-address col-md-6"></div>
            <div class="col-md-6"></div>
        </div>
        <div class="page-row">
            <label for="useDiffShippingAdd">
                <input type="checkbox" id="useDiffShippingAdd" class="co-field form-control" name="use_diff_shipping">
                Abweichende Lieferadresse
            </label>
        </div>
        <div class="page-row">
            <button class="primary button-primary" type="submit">Bestellen</button>
        </div>


    </form>
</div>
<div id="shippingMethods" class="d-none">
    <div class="shipping-methods-wrapper">
        <div class="row">
            <div class="col-12">
                <h4>Versandarten</h4>
            </div>
            <div class="col-12 col-sm-5">
                <select name="shipping_method_title" class="form-control" id="shippingMethodTitle">
                    <option value="">Wählen</option>
                    <?php foreach ($shippingMethods as $key => $shippingMethod) : ?>
                        <option value="<?php echo $shippingMethod['title'] ?>">
                            <?php echo $shippingMethod['title'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-sm-5">
                <input type="number" name="shipping_method_price" class="form-control" id="shippingMethodPrice"
                       placeholder="Preis">
            </div>
            <div class="col-6 col-sm-2">
                <button id="saveShippingMethod" class="button">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<div id="shippingTemplate" class="d-none">
    <div class="page-row">
        <h4>Lieferadresse</h4>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_company" placeholder="Firma">
        </div>
        <div class="col-12 col-sm-6"></div>
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_first_name" placeholder="Vorname*" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_last_name" placeholder="Name*" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="email" class="co-field form-control" name="shipping_email" placeholder="E-Mail">
        </div>
        <div class="col-12 col-sm-6">
            <input type="tel" class="co-field form-control" name="shipping_phone" placeholder="Telefon">
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_address_1" placeholder="Adresse*" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_address_2" placeholder="Adresszusatz">
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_postcode" placeholder="PLZ*" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_country" placeholder="Land*" value="Schweiz"
                   required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_city" placeholder="Ort*" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="co-field form-control" name="shipping_state" placeholder="Kanton">
        </div>
       
    </div>
</div>