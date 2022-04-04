<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Handles adding scripts functionality to the front pages
 * as well as the front pages.
 *
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */
class Wpens_Scripts {
	
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'wpens_public_scripts' ) );

	}
	
	/**
	 * Enqueue Scripts 
	 *
	 * Enqueue Scripts for public
	 *
	 * @package Easy Newsletter Signups
	 * @since 1.0.0
	 */
	
	public function wpens_public_scripts() {

		//Add style css
		wp_register_style( 'wpens-style', WPENS_PLUGIN_URL . '/css/wpens-style.css', array(), WPENS_VERSION );

		// Enqueue form style		
		wp_enqueue_style( 'wpens-style' );
		
		wp_register_script( 'wpens-public-js', WPENS_PLUGIN_URL.'/js/wpens-public.js', array('jquery'), WPENS_VERSION );

		// localization script
		wp_localize_script( 'wpens-public-js', 'WpEns', array(
															'ajaxurl' => admin_url('admin-ajax.php'),
															'fname_empty' => __( 'Please enter your firstname.', 'wpens' ),
															'lname_empty' => __( 'Please enter your lastname.', 'wpens' ),
															'email_empty' => __( 'Bitte E-Mail-Adresse eingeben.', 'wpens' ),
															'email_valid' => __( 'Bitte gültige E-Mail Adresse eingeben.', 'wpens' )
														) );
		
		

		// Enqueue form scripts
		wp_enqueue_script( 'wpens-public-js' );
	}
	
}

return new Wpens_Scripts();
