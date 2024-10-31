<?php

/**
 * WCPT Class
 *
 * Handles the plugin functionality.
 *
 * @package WordPress
 * @package Plugin name
 * @since 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


if (!class_exists('WCPT')) {

	/**
	 * The main WCPT class
	 */
	class WCPT
	{

		private static $_instance = null;
		protected $min_wc = '5.0.0';

		var $admin = null,
			$front = null,
			$lib   = null;

		public static function instance()
		{

			if (is_null(self::$_instance))
				self::$_instance = new self();

			return self::$_instance;
		}

		function __construct()
		{
			add_action('init', array($this, 'init_actions'), 1);
			# Action to load custom post type
			add_action('init', array($this, 'action__init'));

			add_action('plugins_loaded', array($this, 'action__plugins_loaded'), 1);

			add_action('wp_enqueue_scripts', array($this, 'globalcss'), 50);
			add_action('wp_enqueue_scripts', array($this, 'globaljs'));
			add_action('admin_enqueue_scripts', array($this, 'globaljscss'));

			add_filter('query_vars', array($this, 'mytickets_query_vars'));
			add_filter('woocommerce_account_menu_items',  array($this, 'mytickets_item_tab'));

			add_action("wp_ajax_orders_products", array($this, "orders_products"));
			add_action("wp_ajax_ysl_product_ticket", array($this, "ysl_product_ticket"));
			add_action("wp_ajax_chatajax", array($this, "chatajax_call"));
			add_action("current_screen", array($this, "current_screen_call"));
		}


		/**
		 * Action: init
		 *
		 * -
		 *
		 * @return [type] [description]
		 */

		function current_screen_call()
		{
			global $pagenow;

			$custom_post_type = 'product_ticket';

			$screen = get_current_screen();

			if (
				!in_array($pagenow, array('post-new.php'), true)
				&& 'post' === $screen->base
				&& $custom_post_type === $screen->post_type
			) {

				add_action('admin_print_scripts', array($this, 'hide_batch_update_buttons'));
			}
		}

		function hide_batch_update_buttons()
		{
?>
			<script type="text/javascript">
				(function($) {
					'use strict';
					// Remove the update buttons so batches can't be edited.
					$('#submitdiv .edit-post-status').remove();
					$('#submitdiv .edit-visibility').remove();
					$('#submitdiv .edit-timestamp').remove();
					$('#minor-publishing-actions').remove();
					$('#major-publishing-actions').remove();

					// Add the "Add New" button in the right-hand column
					$('.wrap .page-title-action').clone().appendTo('#side-sortables');
				})(jQuery);
			</script>
<?php
		}

		function init_actions()
		{
			// Custom endpoint for My-tickets and View-tickets
			flush_rewrite_rules();
			add_rewrite_endpoint('mytickets', EP_ROOT | EP_PAGES);
			add_rewrite_endpoint('viewticket', EP_ROOT | EP_PAGES);
		}

		/**
		 * Action: plugins_loaded
		 *
		 * -
		 *
		 * @return [type] [description]
		 */
		function action__plugins_loaded()
		{

			global $wp_version;

			# Set filter for plugin's languages directory
			$WCPT_lang_dir = dirname(WCPT_PLUGIN_BASENAME) . '/languages/';
			$WCPT_lang_dir = apply_filters('WCPT_languages_directory', $WCPT_lang_dir);

			# Traditional WordPress plugin locale filter.
			$get_locale = get_locale();

			if ($wp_version >= 4.7) {
				$get_locale = get_user_locale();
			}

			# Traditional WordPress plugin locale filter
			$locale = apply_filters('plugin_locale',  $get_locale, 'ysl-product-ticket');
			$mofile = sprintf('%1$s-%2$s.mo', 'ysl-product-ticket', $locale);

			# Setup paths to current locale file
			$mofile_global = WP_LANG_DIR . '/plugins/' . basename(WCPT_DIR) . '/' . $mofile;

			if (file_exists($mofile_global)) {
				# Look in global /wp-content/languages/plugin-name folder
				load_textdomain('ysl-product-ticket', $mofile_global);
			} else {
				# Load the default language files
				load_plugin_textdomain('ysl-product-ticket', false, $WCPT_lang_dir);
			}
		}

		/**
		 * Action: init
		 */
		function action__init()
		{

			register_post_type('product_ticket', array(
				'labels'                    => array(
					'name'                  => esc_html__('Support Tickets', 'ysl-product-ticket'),
					'singular_name'         => esc_html__('Support Tickets', 'ysl-product-ticket'),
					'all_items'             => esc_html__('All Support Tickets', 'ysl-product-ticket'),
					'archives'              => esc_html__('Support Tickets Archives', 'ysl-product-ticket'),
					'attributes'            => esc_html__('Support Tickets Attributes', 'ysl-product-ticket'),
					'insert_into_item'      => esc_html__('Insert into Support Tickets', 'ysl-product-ticket'),
					'uploaded_to_this_item' => esc_html__('Uploaded to this Support Tickets', 'ysl-product-ticket'),
					'featured_image'        => esc_html__('Featured Image', 'ysl-product-ticket'),
					'set_featured_image'    => esc_html__('Set featured image', 'ysl-product-ticket'),
					'remove_featured_image' => esc_html__('Remove featured image', 'ysl-product-ticket'),
					'use_featured_image'    => esc_html__('Use as featured image', 'ysl-product-ticket'),
					'filter_items_list'     => esc_html__('Filter Support Tickets list', 'ysl-product-ticket'),
					'items_list_navigation' => esc_html__('Support Tickets list navigation', 'ysl-product-ticket'),
					'items_list'            => esc_html__('Support Tickets list', 'ysl-product-ticket'),
					'new_item'              => esc_html__('New Support Tickets', 'ysl-product-ticket'),
					'add_new'               => esc_html__('Add New', 'ysl-product-ticket'),
					'add_new_item'          => esc_html__('Add New Support Tickets', 'ysl-product-ticket'),
					'edit_item'             => esc_html__('Edit Support Tickets', 'ysl-product-ticket'),
					'view_item'             => esc_html__('View Support Tickets', 'ysl-product-ticket'),
					'view_items'            => esc_html__('View Support Tickets', 'ysl-product-ticket'),
					'search_items'          => esc_html__('Search Support Tickets', 'ysl-product-ticket'),
					'not_found'             => esc_html__('No Support Tickets found', 'ysl-product-ticket'),
					'not_found_in_trash'    => esc_html__('No Support Tickets found in trash', 'ysl-product-ticket'),
					'parent_item_colon'     => esc_html__('Parent Support Tickets:', 'ysl-product-ticket'),
					'menu_name'             => esc_html__('Support Tickets', 'ysl-product-ticket'),
				),
				'supports'              => array('title', 'editor', 'thumbnail', 'author'),
				'public'                => false,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_nav_menus'     => true,
				'has_archive'           => true,
				'rewrite'               => array('slug' => "product_ticket"),
				'query_var'             => true,
				'menu_position'         => 4,
				'exclude_from_search'   => false,
				'show_in_rest'          => false,
				'publicly_queryable'    => false,
				'capability_type'         => array('ysl_product_ticket', 'product_ticket'),
				'capabilities'          => array('create_posts' => false),
				'map_meta_cap'             => true,
				'menu_icon'             => 'dashicons-tickets-alt',
				'create_posts'          => 'do_not_allow',
				'taxonomies' => array('product_ticket_categories', 'product_ticket_tag')
			));
			// Custom post type Product Ticket
			// require_once 'public/post-type.php';

			flush_rewrite_rules();
		}

		function globalcss()
		{
			wp_enqueue_style(WCPT_PREFIX . '_global_css', WCPT_URL . 'assets/css/global.css', array(), WCPT_VERSION);
		}

		function globaljs()
		{
			wp_enqueue_script(WCPT_PREFIX . '_jstz_js', WCPT_URL . 'assets/js/jstz.min.js', array('jquery'), WCPT_VERSION, true);
			wp_enqueue_script(WCPT_PREFIX . '_global_js', WCPT_URL . 'assets/js/global.js', array('jquery'), WCPT_VERSION, true);

			wp_localize_script(WCPT_PREFIX . '_global_js', 'wcptvar', array(
				'wcptadminurl' => admin_url('admin-ajax.php'),
				'mytickets' => home_url("my-account/mytickets"),
				'ajaxnonce' => wp_create_nonce('chatajaxnonce_generate_nonce'),
			));
		}

		/**
		 * Action: Global js css.
		 */
		function globaljscss($hook_suffix)
		{
			$allowed_pages = array('post.php', 'edit.php', 'product_ticket_page_wcpt-settings');

			if (in_array($hook_suffix, $allowed_pages)) {

				wp_enqueue_style(WCPT_PREFIX . '_global_css', WCPT_URL . 'assets/css/global.css', array(), WCPT_VERSION);

				wp_enqueue_script(WCPT_PREFIX . '_jstz_js', WCPT_URL . 'assets/js/jstz.min.js', array('jquery'), WCPT_VERSION, true);
				wp_enqueue_script(WCPT_PREFIX . '_global_js', WCPT_URL . 'assets/js/global.js', array('jquery'), WCPT_VERSION, true);

				wp_localize_script(WCPT_PREFIX . '_global_js', 'wcptvar', array(
					'wcptadminurl' => admin_url('admin-ajax.php'),
					'mytickets' => home_url("my-account/mytickets"),
					'ajaxnonce' => wp_create_nonce('chatajaxnonce_generate_nonce'),
				));


				wp_register_script(WCPT_PREFIX . '_admin_js', WCPT_URL . 'assets/js/admin.js', array('jquery'), WCPT_VERSION, true);
				wp_enqueue_script(WCPT_PREFIX . '_admin_js');
				wp_register_style(WCPT_PREFIX . '_admin_css', WCPT_URL . 'assets/css/admin.min.css', array(), WCPT_VERSION);
				wp_enqueue_style(WCPT_PREFIX . '_admin_css');
			}
		}


		// Chat ajax 
		public function chatajax_call()
		{
			if (!check_ajax_referer('chatajaxnonce_generate_nonce', 'ajaxnonce')) {
				return;
			}

			$usertimezone = sanitize_text_field($_POST['timezone']); //Asia/Kolkata
			$user_timezone = new DateTimeZone($usertimezone);
			$userAgentDT = new DateTime(null, $user_timezone);
			// Convert to UTC
			$userAgentDT->setTimezone(new DateTimeZone("UTC"));
			$outputDate = $userAgentDT->format('Y-m-d H:i:s');
			// $outputDate = $userAgentDT->date;

			$admin_email = get_option('admin_email');
			$wcptUsers = get_option('wcpt-users');
			$args = [
				'include' => $wcptUsers, // ID's of users you want to get
				'fields'  => ['user_email'],
			];

			$users = get_users($args);
			$emailids = [$admin_email];
			if ($users) {
				foreach ($users as $key => $value) {
					$emailids[] = $value->user_email;
				}
			}

			$sanitize_text_field_action = sanitize_text_field($_POST['action']);
			if (isset($sanitize_text_field_action)  && ($sanitize_text_field_action == "chatajax")) {
				global $wpdb;
				$wcpt_chat = $wpdb->prefix . 'wcpt_chat';
				$attachment_id = "";
				$attachmenterror = [];

				// Attachment function. {insert in wp media attachment}

				$sanitize_text_field = sanitize_text_field($_POST['img']);

				if (isset($_FILES['img']) && !empty($_FILES["img"]["name"])) {
					$allowed = array("jpg", "jpeg", "png");
					$filename = sanitize_file_name($_FILES['img']['name']);
					$ext = pathinfo($filename, PATHINFO_EXTENSION);

					// Allowed file size -> 2MB
					$allowed_file_size = 2000000;

					// Check file size
					if ($_FILES['img']['size'] > $allowed_file_size) {
						$attachmenterror["attacherror"] = esc_html__('The maximum file size supported is 2MB', 'ysl-product-ticket');
					}
					if (!in_array($ext, $allowed)) {
						// translators: 1: Opening <span> tag, 2: Closing </span> tag.
						$attachmenterror["attacherror"] = sprintf(esc_html__('%1$s Only files with the following extensions are allowed: jpg, jpeg and png %2$s', 'ysl-product-ticket'), '<span>', '</span>');
					}

					if (empty($attachmenterror)) {
						$upload = wp_handle_upload(
							$_FILES['img'],
							array('test_form' => false)
						);

						if (!empty($upload['error'])) {
							$attachmenterror["attacherror"] = $upload['error'];
						} else {
							// it is time to add our uploaded image into WordPress media library
							$attachment_id = wp_insert_attachment(
								array(
									'guid'           => esc_url($upload['url']),
									'post_mime_type' => $upload['type'],
									'post_title'     => basename($upload['file']),
									'post_content'   => '',
									'post_status'    => 'inherit',
								),
								$upload['file']
							);

							if (is_wp_error($attachment_id) || !$attachment_id) {
								$attachmenterror["attacherror"] = $upload['error'];
							} else {
								// update medatata, regenerate image sizes
								require_once(ABSPATH . 'wp-admin/includes/image.php');
								wp_update_attachment_metadata(
									$attachment_id,
									wp_generate_attachment_metadata($attachment_id, $upload['file'])
								);
							}
						}
					}
				} else if (isset($sanitize_text_field)) {
					$attachment_id = $sanitize_text_field;
				}

				$insertres = $wpdb->insert(
					$wcpt_chat,
					array(
						'post_id' => sanitize_text_field($_POST['post_id']),
						'incoming_msg' => sanitize_text_field($_POST['incoming_msg']),
						'outgoing_mgs' => sanitize_text_field($_POST['outgoing_mgs']),
						'img' => $attachment_id,
						'in_out' => sanitize_text_field($_POST['in_out']),
						'message' => sanitize_text_field($_POST['message']),
						'datetime' => $outputDate,
					),
					array('%s', '%s', '%s', '%s', '%s')
				);

				$jsonData = [];
				if ($insertres) {
					$incoming_msg = sanitize_text_field($_POST['incoming_msg']);

					$headers  = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
					$headers .= "From: " . $display_name . " <" . $user_email . ">" . "\r\n";

					$message = "";
					if (empty(sanitize_text_field($_POST['message']))) {
						$message = "<p>New Attachment.</p>";
					} else {
						$ysl_view_ticket_url = wp_nonce_url(home_url('my-account/viewticket') . '/?ticketid=' . sanitize_text_field($_POST['post_id']), 'yslurl');
						$message .= '<ul>
										<li><strong>Ticket :</strong><a href="' . $ysl_view_ticket_url . '">#wcpt_' . sanitize_text_field($_POST['post_id']) . '</li>
										<li><strong>Message: </strong>' . sanitize_text_field($_POST['message']) . '</li>
									</ul>';
					}

					// 1 = admin-side And 0 means customer-side
					if (sanitize_text_field($_POST['in_out']) == 0) {
						$chatMail = wp_mail($emailids, esc_html__("Product Ticket Message", "ysl-product-ticket"), $message, $headers);
					} else {
						$author_obj = get_user_by('id', $incoming_msg);
						$chatMail = wp_mail($author_obj->user_email, esc_html__("Product Ticket Message", "ysl-product-ticket"), $message, $headers);
					}

					$jsonData = array("status" => "true", 'textstatus' => $insertres, "attastatus" =>  $attachmenterror);
				} else {
					$jsonData = array("status" => "false", 'textstatus' => $insertres, "attastatus" =>  $attachmenterror);
				}

				echo wp_json_encode($jsonData);
				exit;
			}
		}

		function mytickets_query_vars($vars)
		{
			$vars[] = 'mytickets';
			$vars[] = 'viewticket';
			return $vars;
		}

		function mytickets_item_tab($items)
		{
			return array(
				'dashboard'          => esc_html__('Dashboard', 'ysl-product-ticket'),
				'orders'             => esc_html__('Orders', 'ysl-product-ticket'),
				'downloads'          => esc_html__('Downloads', 'ysl-product-ticket'),
				'edit-address'       => esc_html__('Addresses', 'ysl-product-ticket'),
				'edit-account'       => esc_html__('Edit Account', 'ysl-product-ticket'),
				'mytickets'          => esc_html__('My Tickets', 'ysl-product-ticket'),
				'customer-logout'    => esc_html__('Logout', 'ysl-product-ticket'),
			);
		}

		// Insert product ticket or update.
		public function ysl_product_ticket()
		{
			if (
				isset($_POST['submitted']) &&
				(isset($_REQUEST['_wpnonce'])
					|| check_ajax_referer('wpt_ptform_nonce_action', $_REQUEST['_wpnonce']))
			) {
				//Close Ticket.
				if (isset($_POST['closeticket']) && (sanitize_text_field($_POST['closeticket']) == "closeticket")) {
					$_statusUpdate = update_post_meta(sanitize_text_field($_POST['ticketid']), '_closedate', current_datetime()->format('Y-m-d H:i:s'));
					update_post_meta(sanitize_text_field($_POST['ticketid']), '_status', "Resolved");
					$jsonData = array("status" => "true", 'statusUpdate' => $_statusUpdate);
					echo wp_json_encode($jsonData);
					exit;
				}

				// Or its new so insert new post.
				$pttitle 				= sanitize_text_field($_POST['pttitle']);
				$ptcontents 			= sanitize_textarea_field($_POST['ptcontents']);
				$type 					= sanitize_text_field($_POST['type']);
				$priority 				= sanitize_text_field($_POST['priority']);
				$product 				= sanitize_text_field($_POST['product']);
				$nonce 					= sanitize_text_field(wp_unslash($_REQUEST['_wpnonce']));
				$order 					= sanitize_text_field($_POST['order']);
				$customfield1 		    = sanitize_text_field($_POST['customfield1']);

				$hasError = [];
				if (!wp_verify_nonce($nonce, 'wpt_ptform_nonce_action')) {
					$hasError["nonce"] = esc_html__("Security check error.", "ysl-product-ticket");
				}

				if (trim($pttitle) === '') {
					$hasError["pttitle"] = esc_html__("Please Enter Title", "ysl-product-ticket");
				}

				if (trim($ptcontents) === '') {
					$hasError["ptcontents"] = esc_html__("Please Enter Description", "ysl-product-ticket");
				}

				if ($order == '') {
					$hasError["order"] = esc_html__("Please Select Order", "ysl-product-ticket");
				}



				$second_featured_img = $_FILES["second_featured_img"];

				if (isset($_FILES["second_featured_img"])) {

					// Attachment Validation.
					$allowed 	= array("jpg", "jpeg", "png");
					$filename 	= sanitize_file_name($second_featured_img['name']);
					$ext 		= pathinfo($filename, PATHINFO_EXTENSION);

					// Allowed file size -> 2MB
					$allowed_file_size = 2000000;

					// Check file size
					if ($second_featured_img['second_featured_img']['size'] > $allowed_file_size) {
						$hasError["attacherror"] = esc_html__('The maximum file size supported is 2MB', 'ysl-product-ticket');
					}

					// Check file type
					if (!in_array($ext, $allowed)) {
						$hasError["attacherror"] = sprintf(esc_html__('Only files with the following extensions are allowed: jpg, jpeg and png', 'ysl-product-ticket'), '<span>', '</span>');
					}
				}

				if (empty($hasError)) {
					// Insert product ticket post type.

					// Insert new post
					$new_post = array(
						'post_title'    => $pttitle,
						'post_content'  => $ptcontents,
						'post_author'   => get_current_user_id(),
						'post_status'   => 'publish',
						'post_type' 	=> 'product_ticket'
					);
					$pid = wp_insert_post($new_post);

					if ($pid) {

						// Image Upload to media start.
						$attachment_id = $uploadErr = "";

						if (isset($second_featured_img)) {
							$upload = wp_handle_upload(
								$second_featured_img,
								array('test_form' => false)
							);

							if (!empty($upload['error'])) {
								$uploadErr =  $upload['error'];
							}

							$attachment_id = wp_insert_attachment(
								array(
									'guid'           => esc_url($upload['url']),
									'post_mime_type' => $upload['type'],
									'post_title'     => basename($upload['file']),
									'post_content'   => '',
									'post_status'    => 'inherit',
								),
								$upload['file']
							);


							if (is_wp_error($attachment_id) || !$attachment_id) {
								$uploadErr =  esc_html__('Upload error.', 'ysl-product-ticket');
							}

							if (!$uploadErr) {
								// update medatata, regenerate image sizes
								require_once(ABSPATH . 'wp-admin/includes/image.php');
								wp_update_attachment_metadata(
									$attachment_id,
									wp_generate_attachment_metadata($attachment_id, sanitize_file_name($upload['file']))
								);
							}
						}
						// Image Upload to media End.

						// Insert post meta.
						$prefix_ticket_no = '#wcpt_' . $pid;
						update_post_meta($pid, '_ticket_no', $prefix_ticket_no);
						update_post_meta($pid, '_status', "New");
						update_post_meta($pid, '_type', sanitize_text_field($type));
						update_post_meta($pid, '_priority', sanitize_text_field($priority));
						update_post_meta($pid, '_custextfieldval', sanitize_text_field($customfield1));
						update_post_meta($pid, '_order', sanitize_text_field($order));
						update_post_meta($pid, '_product', sanitize_text_field($product));
						update_post_meta($pid, '_second_featured_img', sanitize_text_field($attachment_id));

						// For Email attachment.
						if ($attachment_id) {
							$dd_url = wp_get_attachment_image_src($attachment_id, "full");
							if ($dd_url) {
								$dd_path = wp_parse_url($dd_url[0]);
								$array = explode("/woo/wp-content", $dd_path['path']);
								unset($array[0]);
								$text = implode("/", $array);
								$attachments = WP_CONTENT_DIR . $text;
							}
						}

						// Send Email
						$emailStatus = $this->newticketEmail($attachments, $_POST);
						$jsonData =  array(
							'status' => 'true',
							'message' => 'OK',
							'new_post_ID' => $pid,
							'emailStatus' => $emailStatus,
							'uploadErr' => $uploadErr,
						);
					} else {
						$jsonData = array("status" => "false", 'error' => $pid);
					}
				} else {
					$jsonData = array("status" => "false", 'error' => $hasError);
				}
				echo wp_json_encode($jsonData);
			}
			exit;
		}

		// New Ticket Email.
		public function newticketEmail($attachment, $postdata)
		{
			$getUser = wp_get_current_user();
			$display_name = $getUser->data->display_name;
			$user_email = $getUser->data->user_email;

			$adminsubject	= get_option('wcpt-adminsubject');
			if (!$adminsubject) {
				$adminsubject = "New Product Ticket";
			}

			$customersubject = get_option('wcpt-customersubject');
			if (!$customersubject) {
				$customersubject = "New Product Ticket";
			}

			$admin_email = get_option('admin_email');

			$headers  = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
			$headers .= "From: " . $display_name . " <" . $user_email . ">" . "\r\n";

			// Default Email template and Subject.
			$emailTemplate = '<html> <body> <table rules="all" style="border-color: #666;" cellpadding="10"> <tr style="background: #eee;"> <td><strong>Ticket Title:</strong> </td> <td>{pttitle}</td> </tr> <tr> <td><strong>Ticket Message:</strong> </td> <td>{ptcontents}</td> </tr> <tr> <td><strong>Status:</strong> </td> <td>New</td> </tr> <tr> <td><strong>Type:</strong> </td> <td>{type}</td> </tr> <tr> <td><strong>Priority:</strong> </td> <td>{priority}</td> </tr> <tr> <td><strong>Order:</strong> </td> <td>{order}</td> </tr> <tr> <td><strong>Product:</strong> </td> <td>{product}</td> </tr> </table> </body> </html>';

			// Email template
			$adminToMsg = html_entity_decode(stripslashes(get_option('wcpt-admin_to')));
			if (!$adminToMsg) {
				$adminToMsg = $emailTemplate;
			}

			$customerToMsg = html_entity_decode(stripslashes(get_option('wcpt-customer_to')));
			if (!$customerToMsg) {
				$customerToMsg = $emailTemplate;
			}

			$fieldsArr = [
				'pttitle',
				'ptcontents',
				'status',
				'type',
				'priority',
				'order',
				'product',
				'customfield1',
			];

			foreach ($fieldsArr as $key => $value) {
				$findstring = '{' . $value . '}';
				$_POSTVal = sanitize_text_field($postdata[$value]);

				if ($value == "product") {
					if ($_POSTVal) {
						$replaceString = get_the_title($_POSTVal);
					} else {
						$replaceString = "--";
					}
				} else {
					$replaceString = $_POSTVal;
				}
				if (!$replaceString) {
					$replaceString = "--";
				}

				$adminToMsg 	= str_replace($findstring, $replaceString, $adminToMsg);
				$customerToMsg 	= str_replace($findstring, $replaceString, $customerToMsg);
			}

			// Send new email for new Ticket.
			$emailRes_admin = wp_mail($admin_email, $adminsubject, $adminToMsg, $headers, array($attachment));
			$emailRes_user  = wp_mail($user_email, $customersubject, $customerToMsg, $headers, array($attachment));

			if ($emailRes_admin ||  $emailRes_user) {
				$returnRes = ["status" => true, "Message" => esc_html__("Thanks for contacting us, expect a response soon.", "ysl-product-ticket"), "emailRes_admin" => $emailRes_admin, "emailRes_user" => $emailRes_user];
			} else {
				$returnRes = ["status" => false, "emailRes_admin" => $emailRes_admin, "emailRes_user" => $emailRes_user];
			}
			return $returnRes;
		}

		// On change order get its products
		public function orders_products()
		{
			$nonce_verified = check_ajax_referer('wpt_ptform_nonce_action', 'nonce', false);
			if ($nonce_verified) {
				$order 	= wc_get_order(sanitize_text_field($_POST['valueSelected']));
				$items 	= $order->get_items();
				$optionHtml = "";
				$optionHtml .= "<option value=''> " . esc_html__('Select Product', 'ysl-product-ticket') . " </option>";
				foreach ($items as $item) {
					$product_id = $item->get_product_id();
					$optionHtml .= '<option value="' . esc_attr($product_id) . '">' . esc_html(get_the_title($product_id)) . '</option>';
				}

				$jsonData =  array(
					'status' => 'true',
					'message' => 'OK',
					'optionHtml' => $optionHtml
				);

				echo wp_json_encode($jsonData);
				exit;
			}
		}
	}
}

function WCPT()
{
	return WCPT::instance();
}

WCPT();
