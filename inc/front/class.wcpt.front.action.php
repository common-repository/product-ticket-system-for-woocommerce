<?php

/**
 * WCPT_Front_Action Class
 *
 * Handles the Frontend Actions.
 *
 * @package WordPress
 * @subpackage Plugin name
 * @since 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!class_exists('WCPT_Front_Action')) {

	/**
	 *  The WCPT_Front_Action Class
	 */
	class WCPT_Front_Action
	{
		function __construct()
		{
			add_action('wp_enqueue_scripts', array($this, 'action__wp_enqueue_scripts'), 45);
			add_action('woocommerce_account_viewticket_endpoint', array($this, 'wootickets_content_view'));
			add_action('woocommerce_account_mytickets_endpoint', array($this, 'mytickets_content'));
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
		 * Action: wp_enqueue_scripts
		 *
		 * - enqueue script in front side
		 *
		 */
		function action__wp_enqueue_scripts()
		{
			wp_enqueue_style(WCPT_PREFIX . '_front_css', WCPT_URL . 'assets/css/front.min.css', array(), WCPT_VERSION);
			wp_enqueue_script(WCPT_PREFIX . '_front_js', WCPT_URL . 'assets/js/front.min.js', array('jquery'), WCPT_VERSION, array());
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
		// my-account/mytickets/ page ticket listing.
		function mytickets_content()
		{	?>
			<div class="wrap my-tickets-list">
				<div class="add-new-ticket">
					<a href="<?php echo esc_url(home_url('/my-account/viewticket/')); ?>" class="btn button"><?php echo esc_html__('Add New Ticket', 'ysl-product-ticket'); ?></a>
				</div>
				<?php

				if (empty(get_query_var('mytickets'))) :
					$paged = 1;
				else :
					$paged_array = explode('/', get_query_var('mytickets'));
					$paged = $paged_array[1];
				endif;

				$ticket_nofr    = get_option('wcpt-ticketno-fr');
				$statusfr       = get_option('wcpt-status-fr');
				$typefr         = get_option('wcpt-type-fr');
				$priorityfr     = get_option('wcpt-priority-fr');
				$productfr      = get_option('wcpt-product-fr');

				$args =  array(
					'paged'          	=> $paged,
					'post_type'         => 'product_ticket',
					'post_status'       => 'publish',
					'posts_per_page'    => 10,
					'orderby'           => 'date',
					'order'             => 'DESC',
					'author' 			=> get_current_user_id()
				);

				$the_query = new WP_Query($args);
				if ($the_query->have_posts()) { ?>
					<div class="product-ticket-table-wrapper">
						<table>
							<tr>
								<?php if ($ticket_nofr) {
									echo '<th>' . esc_html__('Ticket No', 'ysl-product-ticket') . '</th>';
								} ?>
								<th><?php echo esc_html__('Title', 'ysl-product-ticket'); ?></th>
								<?php if ($statusfr) {
									echo '<th>' . esc_html__('Status', 'ysl-product-ticket') . '</th>';
								} ?>
								<?php if ($typefr) {
									echo '<th>' . esc_html__('Type', 'ysl-product-ticket') . '</th>';
								} ?>
								<?php if ($priorityfr) {
									echo '<th>' . esc_html__('Priority', 'ysl-product-ticket') . '</th>';
								} ?>
								<th><?php esc_html_e('Order', 'ysl-product-ticket'); ?></th>
								<?php if ($productfr) {
									echo '<th>' . esc_html__('Product', 'ysl-product-ticket') . '</th>';
								} ?>
								<th><?php esc_html_e('Action', 'ysl-product-ticket'); ?></th>
							</tr>
							<?php
							while ($the_query->have_posts()) {
								$the_query->the_post();
								$ticket_no 			= get_post_meta(get_the_ID(), '_ticket_no', true);
								$status 			= get_post_meta(get_the_ID(), '_status', true);
								$type 				= get_post_meta(get_the_ID(), '_type', true);
								$priority 			= get_post_meta(get_the_ID(), '_priority', true);
								$order 				= get_post_meta(get_the_ID(), '_order', true);
								$product 			= get_post_meta(get_the_ID(), '_product', true);
							?>
								<tr>
									<?php if ($ticket_nofr) {
										echo '<td>' . esc_html($ticket_no) . '</td>';
									} ?>
									<td> <?php echo esc_html(get_the_title()); ?> </td>
									<?php if ($statusfr) {
										echo '<td>' . esc_html($status) . '</td>';
									} ?>
									<?php if ($typefr) {
										echo '<td>' . esc_html($type) . '</td>';
									} ?>
									<?php if ($priorityfr) {
										echo '<td>' . esc_html($priority) . '</td>';
									} ?>
									<td> <a href="<?php echo esc_url(home_url('my-account/view-order/')) . esc_attr($order); ?>"><?php echo esc_html("#" . $order); ?></a></td>
									<?php if ($productfr) {
										echo '<td>';
										if ($product) {
											echo esc_html(get_the_title($product));
										}
										echo '</td>';
									} ?>
									<?php $url_nonce = wp_create_nonce('view_ticket_action'); ?>
									<td><a href="<?php echo esc_url(home_url('my-account/viewticket')) . "/?ticketid=" . esc_attr(get_the_ID() . "&nonce=" . esc_attr($url_nonce)); ?>"><?php esc_html_e('View/Chat', 'ysl-product-ticket'); ?></a></td>
								</tr>
							<?php }
							wp_reset_postdata();
							flush_rewrite_rules(); ?>
						</table>
					</div>
				<?php
					$max_page = $the_query->max_num_pages;
					$big = 999999999; // need an unlikely integer
					// if( ! $paged ) {
					// 	$paged = get_query_var('paged');
					// }

					if (!$max_page) {
						global $wp_query;
						$max_page = isset($wp_query->max_num_pages) ? $wp_query->max_num_pages : 1;
					}

					echo wp_kses_post(paginate_links(array(
						'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
						'format' => '?paged=%#%',
						'current' => max(1, $paged),
						'total' => $max_page,
						'mid_size' => 1,
						'prev_text' => esc_html__('«', 'ysl-product-ticket'),
						'next_text' => esc_html__('»', 'ysl-product-ticket'),
						'type' => 'list'
					)));
				} else {
					echo "<p>" . esc_html__("No Tickets Yet", "ysl-product-ticket") . "</p>";
				}
				echo '</div>';
			}

			function wootickets_content_view()
			{
				global $wpdb;
				$chkticket = $ticketid = $closedate = $post_title = $post_content = $type = $priority = $product = $order = $second_featured_img = $status = $inputdisabled = "";

				//Change
				$get_ticket_id = isset($_GET['ticketid']) ? sanitize_text_field($_GET['ticketid']) : "";

				// Get the nonce from the URL parameter
				$url_nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : "";

				$sanitize_text_field_ticketid = sanitize_text_field($get_ticket_id);

				if ((isset($sanitize_text_field_ticketid) && !empty($sanitize_text_field_ticketid)) && (!empty($url_nonce) && wp_verify_nonce($url_nonce, 'view_ticket_action'))) {
					$post_exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE id LIKE %d", $sanitize_text_field_ticketid), 'ARRAY_A');

					if ($post_exists) {
						$ticketid = $sanitize_text_field_ticketid;
						$chkticket = "viewticket";
						$inputdisabled = "disabled";

						$ticketTitle = "<h2>" . esc_html__('View/Chat Ticket', 'ysl-product-ticket') . "</h2>";
						$post_title         = get_post_field('post_title', $ticketid);
						$post_content       = get_post_field('post_content', $ticketid);
						$status             = get_post_meta($ticketid, '_status', true);
						$type               = get_post_meta($ticketid, '_type', true);
						$priority           = get_post_meta($ticketid, '_priority', true);
						$product            = get_post_meta($ticketid, '_product', true);
						$orderid            = get_post_meta($ticketid, '_order', true);
						$order              = wc_get_order($orderid);
						$items              = $order->get_items();
						$second_featured_img = get_post_meta($ticketid, '_second_featured_img', true);
						$closedate 			= get_post_meta($ticketid, '_closedate', true);
					} else {
						echo "<p>" . esc_html__("Wrong Ticket ID", "ysl-product-ticket") . "</p>";
						return;
					}
				} else {
					$chkticket = "new";
					$ticketTitle = "<h2>" . esc_html__('Raise Ticket', 'ysl-product-ticket') . "</h2>";

					$get_order_id = isset($_GET['orderid']) ? sanitize_text_field($_GET['orderid']) : "";

					$sanitize_text_field_orderid = sanitize_text_field($get_order_id);
					$orderid = isset($sanitize_text_field_orderid) ? $sanitize_text_field_orderid : "";

					if ($orderid) {
						$order_exists = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT * FROM $wpdb->posts WHERE (post_type = 'shop_order' OR post_type = 'shop_order_placehold') AND ID = %d",
								$orderid
							),
							ARRAY_A
						);

						if ($order_exists) {
							$order = wc_get_order($orderid);
							$items = $order->get_items();
						} else {
							echo '<p>' . esc_html__('Wrong Order ID', 'ysl-product-ticket') . '</p>';
							return;
						}
					}
				}

				// Customer settings for hide/show fields.
				$ticket_nofr    = get_option('wcpt-ticketno-fr');
				$statusfr       = get_option('wcpt-status-fr');
				$typefr         = get_option('wcpt-type-fr');
				$priorityfr     = get_option('wcpt-priority-fr');
				$productfr      = get_option('wcpt-product-fr');
				$attachmentfr   = get_option('wcpt-attachment-fr');
				$closedatefr    = get_option('wcpt-closedate-fr');
				$metafield1fr   = get_option('wcpt-metafield1fr');
				if ($metafield1fr) {
					$custextfield   	= get_option('wcpt-custextfield');
					$customfield1   = get_post_meta($ticketid, '_custextfieldval', true);
				} ?>

				<div class="new-ticket">
					<?php
					// Order
					$args = array(
						'customer_id' => get_current_user_id(),
						'limit' => -1, // to retrieve _all_ orders by this user
					);
					$orders = wc_get_orders($args);
					if ($orders) {
						echo wp_kses_post($ticketTitle); ?>
						<form id="ptform" name="ptform" action="" method="post" enctype="multipart/form-data">
							<?php wp_nonce_field('wpt_ptform_nonce_action'); ?>
							<div id="pt-text" style="background-color:#E6E6FA" class="ticket-inner">

								<div class="ticket-header">
									<?php if ($ticket_nofr && $ticketid) { ?>
										<div class="ticket-id">
											<p><label> <?php esc_html_e('Ticket No:', 'ysl-product-ticket'); ?> </label><?php echo esc_html(" #wcpt_" . $ticketid); ?></p>
										</div>
									<?php }

									if ($closedate && $closedatefr) {  ?>
										<div class="ticket-date">
											<p><label for="closedate" class="form-label"> <?php esc_html_e('Closed:', 'ysl-product-ticket'); ?> </label><?php echo esc_html(" " . gmdate('d-m-Y', strtotime($closedate))); ?></p>
										</div>
									<?php } ?>
								</div>

								<div class="ticket-form">
									<label for="pttitle" class="form-label"> <?php esc_html_e('Title', 'ysl-product-ticket'); ?> </label>
									<input type="text" id="pttitle" name="pttitle" value="<?php echo esc_attr($post_title); ?>" <?php echo esc_html($inputdisabled); ?> />
									<span class="error pttitle"></span>
								</div>
								<div class="ticket-form">
									<label for="ptcontents" class="form-label"> <?php esc_html_e('Message', 'ysl-product-ticket'); ?> </label>
									<textarea id="ptcontents" name="ptcontents" rows="5" cols="1" <?php echo esc_attr($inputdisabled); ?>><?php echo $post_content ? esc_html($post_content) : ""; ?></textarea>
									<span class="error ptcontents"></span>
								</div>

								<div class="ticket-dropdown">
									<?php
									// Status
									if ($statusfr) {
										echo '<div class="ticket-dropdown-inner"><label for="status" class="form-label"> ' . esc_html__('Status', 'ysl-product-ticket') . ' </label>';
										if ($chkticket == "viewticket") {
											$statusOpArr = array("New", "Open", "Resolved");
											$statusoptions = get_option("wcpt-statusoptions");

											$newarr = [];
											if ($statusoptions) {
												$newarr = array_merge($statusOpArr, $statusoptions);
											} else {
												$newarr = $statusOpArr;
											}

											if (is_array($newarr)) {
												foreach ($newarr as $key => $value) {
													if (($status == $value)) {
														echo '<input type="text" name="status" value="' . esc_attr($value) . '" disabled>';
													}
												}
											}
										} else { ?>
											<input type="text" name="status" value="New" disabled>
										<?php 	}
										echo '</div>';
									}

									// Type
									if ($typefr) {
										$typeoptions    = get_option('wcpt-typeoptions');
										if ($typeoptions) {
											echo '<div class="ticket-dropdown-inner"><label for="type" class="form-label">' . esc_html__('Type', 'ysl-product-ticket') . '</label>';
											if ($chkticket != "viewticket") {
												echo '<select name="type" id="type">';
												foreach ($typeoptions as $key => $value) {
													echo '<option value="' . esc_attr($value) . '">' . esc_html($value) . '</option>';
												}
												echo "</select>";
											} else {
												echo '<input type="text" name="type" value="' . esc_attr($type) . '" disabled>';
											}
											echo '</div>';
										}
									}

									if ($orders) {  ?>
										<div class="ticket-dropdown-inner">
											<label for="order" class="form-label"> <?php esc_html_e('Order', 'ysl-product-ticket'); ?> </label>
											<?php if ($chkticket == "viewticket") {	?>
												<input type="text" name="order" value="<?php echo esc_attr('#' . $orderid . ' ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?>" disabled>
											<?php } else {
												echo '<select name="order" id="order">';
												echo '<option value="" > ' . esc_html__('Select Order', 'ysl-product-ticket') . ' </option>';
												foreach ($orders as $key => $value) {
													$order_id  = $value->get_id(); // Get the order ID
													$selectedOrder = "";
													if (isset($orderid)) {
														$selectedOrder = ($order_id == $orderid) ? "selected" : "";
													}
													echo '<option value="' . esc_attr($order_id) . '" ' . esc_attr($selectedOrder) . '> #' . esc_html($order_id) . " " . esc_html($value->get_billing_first_name()) . " " . esc_html($value->get_billing_last_name()) . ' </option>';
												}
												echo '</select><span class="error order"></span>';
											} ?>
										</div>
										<?php }

									// Product
									if ($productfr) {
										if ($chkticket == "viewticket") {
											if ($product) { ?>
												<div class="ticket-dropdown-inner">
													<label for="product" class="form-label"> <?php echo esc_html__('Product', 'ysl-product-ticket'); ?> </label>
													<input type="text" name="order" value="<?php echo esc_attr('#' . $product . ' ' . get_the_title($product)); ?>" disabled>
												</div>
										<?php }
										} else {
											echo '<div class="ticket-dropdown-inner">';
											echo '<label for="product" class="form-label"> ' . esc_html__('Product', 'ysl-product-ticket') . ' </label>';
											echo '<select name="product" id="wcpt-products">
													<option value="">' . esc_html__('Select Product', 'ysl-product-ticket') . '</option>';
											if (isset($items)) {
												$firstSelected = 1;
												foreach ($items as $ke => $item) {
													$product_id = $item->get_product_id();
													$selected = ($firstSelected == 1) ? "selected=selected" : "";
													echo '<option value="' . esc_attr($product_id) . '" ' . esc_attr($selected) . '>' . esc_html(get_the_title($product_id)) . '</option>';
													$firstSelected++;
												}
											}
											echo '</select>';
											echo "</div>";
										}
									}

									// Custom field 
									if ($metafield1fr && $custextfield) { ?>
										<div class="ticket-dropdown-inner">
											<label for="custextfield" class="form-label custextfield"><?php echo esc_html($custextfield); ?></label>
											<input type="text" name="customfield1" id="customfield1" value="<?php echo esc_attr($customfield1); ?>" <?php echo ($chkticket === "viewticket") ? 'disabled' : ''; ?>>
										</div>
									<?php }

									//Priority
									if ($priorityfr) {
										if ($chkticket == "viewticket") {
											if ($priority) {
												echo '<div class="ticket-radio ticket-dropdown-inner">';
												echo '<label for="priority" class="form-label priority"> ' . esc_html__('Priority', 'ysl-product-ticket') . ' </label>';
												echo '<input type="text" name="status" value="' . esc_attr($priority) . '" disabled>';
												echo '</div>';
											}
										} else {
											$priorityoption    = get_option('wcpt-priorityoption');
											if ($priorityoption) {
												echo '<div class="ticket-radio ticket-dropdown-inner">';
												echo '<label for="priority" class="form-label priority"> ' . esc_html__('Priority', 'ysl-product-ticket') . ' </label>';
												foreach ($priorityoption as $key => $value) {
													$firstchecked = ($key == 0) ? "checked" : "";
													echo '<div class="priority-detail"><input type="radio" name="priority" value="' . esc_attr($value) . '" id="' . esc_attr($value) . '" ' . checked($firstchecked, true, false) . '/><label for="' . esc_attr($value) . '">' . esc_html($value) . '</label></div>';
												}
												echo '</div>';
											}
										}
									} ?>
								</div>

								<?php
								if ($attachmentfr) { ?>
									<div class="upload-main">
										<?php if ($chkticket == "viewticket") {
											if ($second_featured_img) { ?>
												<div class="upload-inner">
													<label for=""><?php echo esc_html__('Image', 'ysl-product-ticket'); ?></label>
													<div class="s-feature-img">
														<?php echo wp_kses(wp_get_attachment_image($second_featured_img), array('img' => array('src' => true, 'alt' => true, 'class' => true))); ?>
													</div>
												</div>
											<?php }
										} else { ?>
											<div class="upload-inner">
												<div class="upload-btn-wrapper new-ticket-upload-btn">
													<button class="btn prev-name"><span><img src="<?php echo esc_url(WCPT_URL . 'assets/img/upload-icon.png'); ?>" alt="upload icon"></span><?php esc_html_e('Upload a new image', 'ysl-product-ticket'); ?></button>
													<input type="file" name="second_featured_img" id="second_featured_img" value="" accept="image/*">
													<span class="error attacherror"></span>
												</div>
											</div>
										<?php }	?>
									</div>
								<?php }

								if ($chkticket == "new") {
									$btnText = esc_html__('Create', 'ysl-product-ticket');
								} else {
									if ($status != "Resolved") {
										$btnText = esc_html__('Close Ticket', 'ysl-product-ticket');
										echo '<input type="hidden" value="closeticket" name="closeticket">';
									}
								}

								if ($status != "Resolved") { ?>
									<div class="final-btn">
										<button type="submit">
											<?php echo esc_html($btnText); ?>
											<div class="loader">
												<div class="loading"></div>
											</div>
										</button>
										<span class="success"></span>
									</div>
								<?php } ?>

								<input type="hidden" name="submitted" id="submitted" value="true" />
								<input type="hidden" value="<?php echo esc_attr($ticketid); ?>" name="ticketid">
							</div>
						</form>

					<?php
						// Chat Box
						require_once 'partials/chat-box.php';
					} else {
						echo "<p>" . esc_html__("No Orders Yet", "ysl-product-ticket") . "</p>";
					}
					?>
				</div>
	<?php
			}
		}

		add_action('plugins_loaded', function () {
			WCPT()->front->action = new WCPT_Front_Action;
		});
	}
