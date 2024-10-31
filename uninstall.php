<?php

if (!defined('ABSPATH')) exit;

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$deletedata = get_option('wcpt-deletedata');
if ($deletedata) {
    global $wpdb;
    
    // Delete options settings.
    delete_option('wcpt-users');
    delete_option('wcpt-ticketno');
    delete_option('wcpt-ticketno-fr');
    delete_option('wcpt-status');
    delete_option('wcpt-status-fr');
    delete_option('wcpt-type');
    delete_option('wcpt-type-fr');
    delete_option('wcpt-priority');
    delete_option('wcpt-priority-fr');
    delete_option('wcpt-product');
    delete_option('wcpt-product-fr');
    delete_option('wcpt-attachment');
    delete_option('wcpt-attachment-fr');
    delete_option('wcpt-closedate');
    delete_option('wcpt-closedate-fr');
    delete_option('wcpt-custextfield');
    delete_option('wcpt-metafield1');
    delete_option('wcpt-metafield1fr');
    delete_option('wcpt-statusoptions');
    delete_option('wcpt-typeoptions');
    delete_option('wcpt-priorityoption');
    delete_option('wcpt-adminsubject');
    delete_option('wcpt-admin_to');
    delete_option('wcpt-customersubject');
    delete_option('wcpt-customer_to');
    delete_option('wcpt-deletedata');

    // Delete Custom post type.
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}posts WHERE post_type = %s", "product_ticket"));

    $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE post_id NOT IN (SELECT id FROM {$wpdb->prefix}posts)");

    $wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships WHERE object_id NOT IN (SELECT id FROM {$wpdb->prefix}posts)");

    // drop a custom database table
     $table_name = $wpdb->prefix . 'wcpt_chat';
if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    $sql = "DROP TABLE $table_name";
    $wpdb->query($sql);
}
}
