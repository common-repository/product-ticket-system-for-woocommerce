<?php

/**
 * Plugin Name: Product Ticket System For WooCommerce
 * Plugin URL: https://www.yudiz.com/wordpress-development/
 * Description: Customer can Generate Ticket against his/her Order/product and take updates for the same.
 * Version: 1.0
 * Contributors: yudiz
 * Author: Yudiz Solutions Ltd.
 * Author URI: https://www.yudiz.com/
 * Text Domain: ysl-product-ticket
 * Copyright: © 2009-2024 Yudiz Solutions Ltd.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Constants Variables
 *
 * @package Product Ticket System For WooCommerce
 * @since 1.0
 */

if (!defined('WCPT_VERSION')) {
    define('WCPT_VERSION', '1.0'); // Version of plugin
}

if (!defined('WCPT_FILE')) {
    define('WCPT_FILE', __FILE__); // Plugin File
}

if (!defined('WCPT_DIR')) {
    define('WCPT_DIR', dirname(__FILE__)); // Plugin dir
}

if (!defined('WCPT_URL')) {
    define('WCPT_URL', plugin_dir_url(__FILE__)); // Plugin url
}

if (!defined('WCPT_PLUGIN_BASENAME')) {
    define('WCPT_PLUGIN_BASENAME', plugin_basename(__FILE__)); // Plugin base name
}

if (!defined('WCPT_META_PREFIX')) {
    define('WCPT_META_PREFIX', 'wcpt_'); // Plugin metabox prefix
}

if (!defined('WCPT_PREFIX')) {
    define('WCPT_PREFIX', 'wcpt'); // Plugin prefix
}


# Register plugin activation hook
register_activation_hook(__FILE__,  'wcpt_ptsfw_action__plugin_activation');
//* Register deactivation hook to remove Blog Manager role
register_deactivation_hook(__FILE__,  'wcpt_deactivation');

/**
 * register_activation_hook
 *
 * - When active plugin
 *
 */
function wcpt_ptsfw_action__plugin_activation()
{
    global $wpdb;
    $wcpt_chat = $wpdb->prefix . 'wcpt_chat';  // table name
    $charset_collate = $wpdb->get_charset_collate();

    //Check to see if the table exists already, if not, then create it
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        post_id INT(11) NOT NULL,
        outgoing_mgs INT(11) NOT NULL,
        incoming_msg INT(11) NOT NULL,
        img INT(11) NOT NULL,
        in_out ENUM('0', '1'),
        message VARCHAR(1000) NOT NULL,
        datetime DATETIME DEFAULT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

    // Custom user role for custom post type.
    // Register activation hook to add Blog Manager role
    $caps = [
        'read'         => true,
        'edit_posts'   => true,
        'upload_files' => true,
    ];

    add_role('ysl_product_ticket', 'YSL Product Ticket', $caps);
}

function wcpt_deactivation()
{
    remove_role('ysl_product_ticket');
}

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_init', 'wcpt_plugin_active_deactivate_handler');
    return;
}

function wcpt_plugin_active_deactivate_handler()
{
    // Deactivating 
    deactivate_plugins(plugin_basename(__FILE__));

    // Admin Notice
    unset($_GET['activate']);
    add_action('admin_notices', 'wcpt_woocommerce_inactive_notice');
}

function wcpt_woocommerce_inactive_notice()
{
?>
    <div class="alert alert-danger notice is-dismissible">
        <p><?php esc_html(esc_html_e('Sorry, but this plugin requires WooCommerce in order to work. So please ensure that WooCommerce is both installed and activated.', 'ysl-product-ticket')); ?></p>
    </div>
    <div class="notice notice-warning is-dismissible">
        <p><?php esc_html_e('Plugin deactivated.', 'ysl-product-ticket'); ?></p>
    </div>
<?php }

/**	
 * Initialize the main class
 */
if (!function_exists('WCPT')) {

    if (is_admin()) {
        require_once(WCPT_DIR . '/inc/admin/class.' . WCPT_PREFIX . '.admin.php');
        require_once(WCPT_DIR . '/inc/admin/class.' . WCPT_PREFIX . '.admin.action.php');
        require_once(WCPT_DIR . '/inc/admin/class.' . WCPT_PREFIX . '.admin.filter.php');
    } else {
        require_once(WCPT_DIR . '/inc/front/class.' . WCPT_PREFIX . '.front.php');
        require_once(WCPT_DIR . '/inc/front/class.' . WCPT_PREFIX . '.front.action.php');
        require_once(WCPT_DIR . '/inc/front/class.' . WCPT_PREFIX . '.front.filter.php');
    }

    // require_once( WCPT_DIR . '/inc/lib/class.' . WCPT_PREFIX . '.lib.php' );	

    //Initialize all the things.
    require_once(WCPT_DIR . '/inc/class.' . WCPT_PREFIX . '.php');
}
