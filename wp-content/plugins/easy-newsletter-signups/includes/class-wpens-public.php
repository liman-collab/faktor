<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Html for newslterr form
 *
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */
class Wpens_Public {
	
	public function __construct(){
		
		add_action( 'wp_ajax_wpens_add_newsletter'	,array( $this, 'wpens_add_newsletter' ) );
		add_action( 'wp_ajax_nopriv_wpens_add_newsletter',array( $this,'wpens_add_newsletter' ) ) ;
	}

	/**
	 * Validating and insert
	 *
	 * Validate whole form and insert data into database
	 *
	 * @package Easy Newsletter Signups
	 * @since 1.0.0
	 */
	public function wpens_add_newsletter() {
		
		global $wpdb;

		$firstname  = isset( $_POST['firstname'] ) ? sanitize_text_field( $_POST['firstname'] ) : '';
		$lastname   = isset( $_POST['lastname'] ) ? sanitize_text_field( $_POST['lastname'] ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$ip 		= isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
        
		$response = array();

		// Get email id if registerd
		$table_name = $wpdb->prefix . 'ens_subscribers';
		$myrows = $wpdb->get_results( "SELECT email FROM ".$table_name." WHERE email = '".$email."'" );
		
		// Check firstname validation
		if( isset($_POST['firstname']) && empty($firstname) ) {
			$response['status'] = '0';
			$response['errmsg'] = __( 'Please enter firstname', 'wpens' );
		} 

		// Check email validation
		if( empty($email) ) {
			$response['status'] = '0';
			$response['errmsg'] = __( 'Please enter email address', 'wpens' );
		} else if( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			$response['status'] = '0';
			$response['errmsg'] = __( 'Bitte geben Sie eine gültige E-Mail Adresse ein.', 'wpens' );
		} else if( count($myrows) > 0 ) {
			$response['status'] = '0';
			$response['errmsg'] = __( 'Sie haben sich bereits angemeldet.', 'wpens' );
		}

		// if error print and exit
		if( $response['status'] == '0' ) {
			echo json_encode($response);
			exit;
		}

		// insert newslertter data 
		$wpdb->insert( 
			    $table_name, 
				    array( 
				        'first_name'	=> $firstname,
				        'last_name'		=> $lastname,
				        'email'			=> $email,
				        'user_ip'		=> $ip,
				        'date'			=> current_time( 'mysql' )
				    ),
				    array( 
						'%s', 
						'%s',
						'%s'
					)
				);
		
		// Check if data inserted
		if( !empty($wpdb) && !is_wp_error($wpdb) ) {

			$response['status'] = '1';
			$response['errmsg'] = __( 'Sie haben sich erfolgreich angemeldet!', 'wpens' );
			
		}

		echo json_encode($response);
		exit;
	}
}

return new Wpens_Public();