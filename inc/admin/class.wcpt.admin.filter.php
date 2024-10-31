<?php
/**
 * WCPT_Admin_Filter Class
 *
 * Handles the admin functionality.
 *
 * @package WordPress
 * @subpackage Plugin name
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WCPT_Admin_Filter' ) ) {

	/**
	 *  The WCPT_Admin_Filter Class
	 */
	class WCPT_Admin_Filter {

		function __construct() {
			// Allow 'reporter' User Role to  view the Dashboard
			add_filter( 'woocommerce_prevent_admin_access', array($this, 'wc_reporter_admin_access'), 20, 1 );
			add_filter('manage_posts_columns', array($this, 'wcpt_add_custom_columns_title_pt') , 10, 2 );
			add_filter( 'wp_editor_settings', array($this, 'wpse_199918_wp_editor_settings'), 10, 2 );
		}

		/*
		######## #### ##       ######## ######## ########   ######
		##        ##  ##          ##    ##       ##     ## ##    ##
		##        ##  ##          ##    ##       ##     ## ##
		######    ##  ##          ##    ######   ########   ######
		##        ##  ##          ##    ##       ##   ##         ##
		##        ##  ##          ##    ##       ##    ##  ##    ##
		##       #### ########    ##    ######## ##     ##  ######
		*/


		/*
		######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
		##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
		##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
		######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
		##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
		##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
		*/

		function wc_reporter_admin_access( $prevent_access ) {
			if( current_user_can('woo_product_ticket') )
				$prevent_access = false;
		
			return $prevent_access;
		} 

		function wcpt_add_custom_columns_title_pt( $columns, $post_type ) {
			switch ( $post_type ) {
				case 'product_ticket':
					$columns['ticketstatus'] = esc_html__('Status', "woo-product-ticket"); // you may use __() later on for translation support
					break;
				
				default:
					
					break;
			}
			
			return $columns;
		}

		function wpse_199918_wp_editor_settings( $settings, $editor_id ) {
			if ( $editor_id === 'content' && get_current_screen()->post_type === 'product_ticket' ) {
				$settings['tinymce']   = false;
				$settings['quicktags'] = false;
				$settings['media_buttons'] = false;
			}
		
			return $settings;
		}
		
	}

	add_action( 'plugins_loaded', function() {
		WCPT()->admin->filter = new WCPT_Admin_Filter;
	} );
}



