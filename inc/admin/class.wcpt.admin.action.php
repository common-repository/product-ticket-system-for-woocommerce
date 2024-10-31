<?php

/**
 * WCPT_Admin_Action Class
 *
 * Handles the admin functionality.
 *
 * @package WordPress
 * @package Plugin name
 * @since 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!class_exists('WCPT_Admin_Action')) {

	/**
	 *  The WCPT_Admin_Action Class
	 */
	class WCPT_Admin_Action
	{
		function __construct()
		{
			add_action('admin_menu', array($this, 'register_wcpt_admin'));
			add_action('admin_init', array($this, 'admin_init_callback'));

			add_action('add_meta_boxes_product_ticket', array($this, 'product_ticket_add_meta_box'));
			add_action('save_post_product_ticket', array($this, 'product_ticket_save_meta_box_data'));

			add_action('wp_loaded', array($this, 'register_all_scripts'));

			add_action('manage_posts_custom_column', array($this, 'wcpt_add_custom_column_data_pt'), 10, 2);
			add_action('bulk_edit_custom_box', array($this, 'wcpt_quick_edit_fields'), 10, 2);
			add_action('save_post', array($this, 'wcpt_bulk_edit_save'));
		}

		/*
		   ###     ######  ######## ####  #######  ##    ##  ######
		  ## ##   ##    ##    ##     ##  ##     ## ###   ## ##    ##
		 ##   ##  ##          ##     ##  ##     ## ####  ## ##
		##     ## ##          ##     ##  ##     ## ## ## ##  ######
		######### ##          ##     ##  ##     ## ##  ####       ##
		##     ## ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##     ##  ######     ##    ####  #######  ##    ##  ######
		*/

		/**
		 * Action: admin_init
		 *
		 * - Register admin min js and admin min css
		 *
		 */

		function register_all_scripts()
		{
			wp_enqueue_style(WCPT_PREFIX . '_select2_css', WCPT_URL . 'assets/css/select2.min.css', array(), WCPT_VERSION);
			wp_enqueue_script(WCPT_PREFIX . '_select2_js', WCPT_URL . 'assets/js/select2.min.js', array('jquery'), WCPT_VERSION, array());
		}

		/*
		######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
		##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
		##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
		######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
		##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
		##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
		##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######
		*/


		public function register_wcpt_admin()
		{
			add_submenu_page('edit.php?post_type=product_ticket', esc_html__('Settings', 'ysl-product-ticket'), esc_html__('Settings', 'ysl-product-ticket'), 'manage_options', 'wcpt-settings', array($this, 'wcpt_settings'));

			global $submenu;
			unset($submenu['edit.php?post_type=product_ticket'][10]);
			// // Hide link on listing page
			// if (isset($_GET['post_type']) && $_GET['post_type'] == 'product_ticket') {
			// 	echo '<style type="text/css">
			// 	.page-title-action { display:none !important; }
			// 	</style>';
			// }
		}

		// Plugin Settings.
		public function wcpt_settings()
		{
			require_once 'partials/wcpt-settings.php';
		}

		public function admin_init_callback()
		{

			// Admin role capability.
			$roles = array('ysl_product_ticket', "administrator");
			foreach ($roles as $the_role) {
				$role = get_role($the_role);
				$role->add_cap('read');
				$role->add_cap('read_product_ticket');
				$role->add_cap('read_private_product_ticket');
				$role->add_cap('edit_product_ticket');
				$role->add_cap('edit_product_ticket');
				$role->add_cap('edit_others_product_ticket');
				$role->add_cap('edit_published_product_ticket');
				$role->add_cap('publish_product_ticket');
				$role->add_cap('delete_others_product_ticket');
				$role->add_cap('delete_private_product_ticket');
				$role->add_cap('delete_published_product_ticket');
			}
			// Save Plugin Settings.
			require_once 'partials/wcpt-save-settings.php';
		}

		// Custom fields for Woo products tickets
		function product_ticket_add_meta_box($post)
		{
			add_meta_box('product_ticket_meta_box', esc_html__('Support Ticket', 'ysl-product-ticket'), array($this, 'product_ticket_build_meta_box'));
			add_meta_box('product_ticket_meta_box_chat',  esc_html__('Chat', 'ysl-product-ticket'), array($this, 'product_ticket_build_meta_box_chat'));
		}

		function product_ticket_build_meta_box($post)
		{
			// Meta fields Product post type.
			require_once 'partials/meta-support-ticket.php';
		}

		function product_ticket_build_meta_box_chat($post)
		{
			// Meta fields Product post type.
			require_once 'partials/meta-support-ticket-chat.php';
		}

		function product_ticket_save_meta_box_data($post_id)
		{
			$product_ticket_meta_box_nonce = sanitize_text_field(wp_unslash($_POST['product_ticket_meta_box_nonce']));

			if (!isset($_POST['product_ticket_meta_box_nonce']) || !wp_verify_nonce($product_ticket_meta_box_nonce, 'update_action_status')) {
				return;
			}

			if (isset($_REQUEST['status'])) {
				update_post_meta($post_id, '_status', sanitize_text_field($_POST['status']));

				if (sanitize_text_field($_POST['status']) == "Resolved") {
					update_post_meta($post_id, '_closedate', current_datetime()->format('Y-m-d H:i:s'));
				} else {
					update_post_meta($post_id, '_closedate', "");
				}
			}
		}


		// add custom column data with custom meta value for custom post types

		function wcpt_add_custom_column_data_pt($column_name, $post_id)
		{
			switch ($column_name) {
				case 'ticketstatus': // specified for this column assigned in the column title
					echo esc_html(get_post_meta($post_id, '_status', true));
					break;

				default:
					break;
			}
		}


		function wcpt_quick_edit_fields($column_name, $post_type)
		{
			switch ($column_name) {
				case 'ticketstatus': {

						$statusOpArr = array("New", "Open", "Resolved");
						$statusoptions = get_option("wcpt-statusoptions");
						$newarr = [];
						if ($statusoptions) {
							$newarr = array_merge($statusOpArr, $statusoptions);
						} else {
							$newarr = $statusOpArr;
						}
?>
						<fieldset class="inline-edit-col-left">
							<div class="inline-edit-col">
								<?php wp_nonce_field('_wpnonce', 'bulk-posts');	?>
								<label><span class="title"><?php esc_html_e('Ticket Status', 'ysl-product-ticket'); ?></span></label>
								<select name="status" id="">
									<?php
									if (is_array($newarr)) {
										foreach ($newarr as $key => $value) {
											echo '<option value="' . esc_attr($value) . '">' . esc_html($value) . '</option>';
										}
									}
									?>
								</select>
							</div>
						</fieldset>
<?php
						break;
					}
			}
		}

		function wcpt_bulk_edit_save($post_id)
		{
			if (isset($_REQUEST['_wpnonce'])) {
				// check bulk edit nonce

				$wpnonce = sanitize_text_field(wp_unslash($_REQUEST['_wpnonce']));

				if (!wp_verify_nonce($wpnonce, 'bulk-posts')) {
					return;
				}

				$status = !empty($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 0;
				update_post_meta($post_id, '_status', $status);

				if ($_REQUEST['status'] == "Resolved") {
					update_post_meta($post_id, '_closedate', current_datetime()->format('Y-m-d H:i:s'));
				} else {
					update_post_meta($post_id, '_closedate', "");
				}
			}
		}
	}

	add_action('plugins_loaded', function () {
		WCPT()->admin->action = new WCPT_Admin_Action;
	});
}
