<?php
if (!defined('ABSPATH')) {
	exit();
}
/**
 * wallee WooCommerce
 *
 * This WooCommerce plugin enables to process payments with wallee (https://www.wallee.com).
 *
 * @author wallee AG (http://www.wallee.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache Software License (ASL 2.0)
 */
/**
 * This entity holds data about a token on the gateway.
 * 
 * @method int get_id()
 * @method int get_token_id()
 * @method void set_token_id(int $id)
 * @method string get_state()
 * @method void set_state(string $state)
 * @method int get_space_id()
 * @method void set_space_id(int $id)
 * @method string get_name()
 * @method void set_name(string $name)
 * @method int get_customer_id()
 * @method void set_customer_id(int $id)
 * @method int get_payment_method_id()
 * @method void set_payment_method_id(int $id)
 * @method int get_connector_id()
 * @method void set_connector_id(int $id)
 * 
 */
class WC_Wallee_Entity_Token_Info extends WC_Wallee_Entity_Abstract {

	protected static function get_field_definition(){
		return array(
		    'token_id' => WC_Wallee_Entity_Resource_Type::INTEGER,
		    'state' => WC_Wallee_Entity_Resource_Type::STRING,
		    'space_id' => WC_Wallee_Entity_Resource_Type::INTEGER,
		    'name' => WC_Wallee_Entity_Resource_Type::STRING,
		    'customer_id' => WC_Wallee_Entity_Resource_Type::INTEGER,
		    'payment_method_id' => WC_Wallee_Entity_Resource_Type::INTEGER,
		    'connector_id' => WC_Wallee_Entity_Resource_Type::INTEGER 
		);
	}

	protected static function get_table_name(){
		return 'wc_wallee_token_info';
	}

	public static function load_by_token($space_id, $token_id){
		global $wpdb;
		$result = $wpdb->get_row(
				$wpdb->prepare("SELECT * FROM " . $wpdb->prefix . self::get_table_name() . " WHERE space_id = %d AND token_id = %d", $space_id, 
						$token_id), ARRAY_A);
		if ($result !== null) {
			return new self($result);
		}
		return new self();
	}
}