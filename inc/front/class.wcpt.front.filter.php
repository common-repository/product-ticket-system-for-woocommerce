<?php

/**
 * WCPT_Front_Filter Class
 *
 * Handles the Frontend Filters.
 *
 * @package WordPress
 * @subpackage Plugin name
 * @since 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!class_exists('WCPT_Front_Filter')) {

	/**
	 *  The WCPT_Front_Filter Class
	 */
	class WCPT_Front_Filter
	{

		function __construct()
		{
			add_filter('woocommerce_my_account_my_orders_actions', array($this, 'add_my_account_my_orders_custom_action'), 10, 2);
			add_filter('the_title',  array($this, 'endpoint_title'));
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


		function add_my_account_my_orders_custom_action($actions, $order)
		{
			$action_slug = 'viewticket';
			$actions[$action_slug] = array(
				'url'  => esc_url(home_url('/my-account/viewticket/?orderid=' . $order->get_id())),
				'name' => esc_html__('Raise Ticket', 'ysl-product-ticket')
			);
			return $actions;
		}

		function endpoint_title($title)
		{
			global $wp_query;

			$is_endpoint = isset($wp_query->query_vars['mytickets']);

			if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
				$title = esc_html__('My Tickets', 'ysl-product-ticket');
				remove_filter('the_title', array($this, 'endpoint_title'));
			} elseif (isset($wp_query->query_vars['viewticket']) && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
				if (isset($_GET['ticketid'])) {
					$url_nonce = isset($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : '';
					if (wp_verify_nonce($url_nonce, 'view_ticket_action')) {
						// Nonce is valid
						$title = esc_html__('View Ticket', 'ysl-product-ticket');
					} else {
						// Nonce is invalid
						$title = esc_html__('Invalid Request', 'ysl-product-ticket');
					}
				} else {
					$title = esc_html__('New Ticket', 'ysl-product-ticket');
				}
				remove_filter('the_title', array($this, 'endpoint_title'));
			}

			return $title;
		}
	}

	add_action('plugins_loaded', function () {
		WCPT()->front->filter = new WCPT_Front_Filter;
	});
}
