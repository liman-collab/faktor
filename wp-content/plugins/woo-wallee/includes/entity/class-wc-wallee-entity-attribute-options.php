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
 * This entity holds data about a the product attribute options.
 * 
 * @method int get_id()
 * @method int get_attribute_id()
 * @method void set_attribute_id(int $id)
 * @method boolean get_send()
 * @method void set_send(boolean $send)
 *  
 */
class WC_Wallee_Entity_Attribute_Options extends WC_Wallee_Entity_Abstract {

	
	protected static function get_field_definition(){
	    return array(
	        'attribute_id' => WC_Wallee_Entity_Resource_Type::INTEGER,
	        'send' => WC_Wallee_Entity_Resource_Type::BOOLEAN
	    );
	}
	
	protected static function get_base_fields(){
	    return array(
	        'id' => WC_Wallee_Entity_Resource_Type::INTEGER
	    );
	}

	protected static function get_table_name(){
		return 'wc_wallee_attribute_options';
	}

	protected function prepare_base_fields_for_storage(&$data_array, &$type_array){

	}
	
	public static function load_by_attribute_id($attribute_id){
		global $wpdb;
		$result = $wpdb->get_row(
		    $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . self::get_table_name() . " WHERE attribute_id = %d", $attribute_id), ARRAY_A);
		if ($result !== null) {
			return new self($result);
		}
		return new self();
	}
	
}