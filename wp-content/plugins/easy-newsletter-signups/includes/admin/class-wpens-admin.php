<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Pages Class
 *
 * Handles generic Admin functionailties
 *
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */
class Wpens_Admin {

	
	public function __construct()	{		

		add_action( 'admin_menu', array( $this, 'wpens_ltable_add_menu_page' ) );
			
		add_action('admin_init', array($this, 'wpesn_ltable_process_bulk_action'));
	}

	/**
	 * Creat menu page
	 *
	 * Adding required menu pages and submenu pages
	 * to manage the plugin functionality
	 * 
	 * @package Easy Newsletter Signups
	 * @since 1.0.0
	 */
	public function wpens_ltable_add_menu_page() {
		 
		$wpens_setting = add_menu_page( __( 'Easy Newsletter Signups', 'wpens' ), __( 'Easy Newsletter Signups', 'wpens' ), 'manage_options','wpens-list', array($this, 'wpens_ltable_newsletter_list') , WPENS_PLUGIN_URL.'/images/icon.jpg' );
		
		// add_action( "admin_head-$wpens_setting", array() );
	}

		
	
	/**
	 * Includes Newsletter List
	 * 
	 * Including File for Newsletter listing
	 *
	 * @package Easy Newsletter Signups
	 * @since 1.0.0
	 */
	public function wpens_ltable_newsletter_list() {
		
		include_once( WPENS_ADMIN_DIR . '/forms/wpens-newsletter-list.php' );
		
	}

	
	/**
	 * Bulk Action
	 *
	 * @package Easy Newsletter Signups
	 * @since 1.0.0
	 */
	public function wpesn_ltable_process_bulk_action() {

		// check if action is not blank and if page is newsletter listing page
		if(((isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) || (isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' )) && isset($_GET['page']) && $_GET['page'] == 'wpens-list' ) { //check action and page

		
			global $wpdb;
			
			// get redirect url
			$redirect_url = add_query_arg( array( 'page' => 'wpens-list' ), admin_url( 'admin.php' ) );
			
			$nsl_id = array();
			if( isset($_GET['nsl_id']) ) { 
				$nsl_id = $_GET['nsl_id'];
			}
				
			if( count( $nsl_id ) > 0 ) { //check if any checkbox is selected

				$table_name = $wpdb->prefix . 'ens_subscribers';

				// Delete bulk subsribers
				foreach ($nsl_id as $key => $value) {
					$wpdb->delete( $table_name , array( 'ID' => $value ) );
				}
				
				$redirect_url = add_query_arg( array( 'message' => '3' ), $redirect_url );
				
				wp_redirect( $redirect_url ); 
				exit;
				
			} else {
				
				wp_redirect( $redirect_url ); 
				exit;
			}
				
		}
		
		if((isset( $_GET['export'] ) && $_GET['export'] == 'Export CSV' ) && isset($_GET['page']) && $_GET['page'] == 'wpens-list' ) { 

				global $wpdb;
				$table_name = $wpdb->prefix . 'ens_subscribers';
		        $query = "SELECT * FROM $table_name";

				if( isset($_GET['nsl_id']) ) { 
					$query .= " WHERE ";
					foreach($_GET['nsl_id'] as $id){
						$query  .= " id = $id || ";
					}
					$query = rtrim($query, '|| ');
				}

				$data = $wpdb->get_results( $query , 'ARRAY_A' );				

				$csv_output = "First Name,Last Name,Email,User IP,Created Date \n";
				foreach($data as $dt){
					extract($dt);
					//$date = date("F d Y h:i A",strtotime($date));
					$csv_output .= "$first_name,$last_name,$email,$user_ip,$date\n";
				}
				
				$cdt = date("d-M-Y");
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Content-Description: File Transfer');
				header('Content-type: text/csv');
				header('Content-Disposition: attachment; filename= wpens-subscribers-'.$cdt.'.csv');
				header('Expires: 0');
				header('Pragma: public');
				echo $csv_output;
				exit;
		}

	}

}

return new Wpens_Admin();