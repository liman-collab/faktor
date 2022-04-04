<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Html for newsletter form
 *
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */
class Wpens_Shortcodes {
	
	public function __construct(){
		
		// Shortcode to print newletter form 
		// Shortcode : [wpens_easy_newsletter lastname="yes" firstname="yes" button_text="Send"]
		add_shortcode( 'wpens_easy_newsletter', array($this, 'wpens_newsletter_form_shortcode') );

		//Use shortcode in widget
		add_filter('widget_text','do_shortcode');

	}
	
	/**
	 * Adding Html
	 *
	 * Adding html for front end side.
	 *
	 * @package Easy Newsletter Signups
	 * @since 1.0.0
	 */
	function wpens_newsletter_form_shortcode( $atts, $content ) {
		
		// Getting attributes of shortcode
		extract( shortcode_atts( array(
			'button_text'	=> __( 'Submit', 'wpens' ),
			'firstname'	=> 'yes',
			'lastname'	=> 'yes',
		), $atts ) );
		
		ob_start(); ?>

		<div class="easy-newsletter">
			<form  id="easy-newsletter-form" >
				<?php
				if( $firstname == 'yes' ) { ?>
					<div class="input-field">
						<label><?php echo __( 'First Name', 'wpens' ); ?></label>
						<input type="text" name="first_name" class="wpens_firstname" />
					</div>
				<?php } ?>

				<?php
				if( $lastname == 'yes' ) { ?>
					<div class="input-field">
						<label><?php echo __( 'Last Name', 'wpens' ) ?></label>
						<input type="text" name="last_name" class="wpens_lastname">
					</div>
				<?php } ?>

				<div class="input-field">
					<label><?php echo __( 'Ihre E-Mail-Adresse', 'wpens' ); ?></label>
					<input type="text" name="email" class="wpens_email">
				</div>

				<div class="input-field input-submit">
					<button type="button" id="easy-newsletter-submit" ><?php echo $button_text; ?> </button>
					
					<div class="wpens_ajax_loader" style="display: none;"><img src="<?php echo WPENS_PLUGIN_URL; ?>/images/ajax_loader.gif"></div>
				</div>
				<div class="wpens-message-container"></div>
			</form>
		</div>

		<?php
		$content .= ob_get_clean();
		return $content;
	}
}

return new Wpens_Shortcodes();
