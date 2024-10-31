<?php
if (!defined('ABSPATH')) exit;

function wcpt_sanitize_status_array($arr)
{
    if (is_array($arr)) {
        return array_map('sanitize_text_field', $arr);
    } else {
        return sanitize_text_field($arr);
    }
}

if (isset($_POST['wcpt-options-hidden']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['cform_generate_nonce'])), 'contact_form_submit')) {

    $users = isset($_POST['users']) ? $_POST['users'] : array();
    $fetchuser = array_map('wcpt_sanitize_status_array',$users);
    update_option('wcpt-users', $fetchuser);

    $ticket_no     = isset($_POST['ticketno']) ? sanitize_text_field($_POST['ticketno']) : 0;
    update_option('wcpt-ticketno', $ticket_no);
    $ticket_nofr     = isset($_POST['ticketnofr']) ? sanitize_text_field($_POST['ticketnofr']) : 0;
    update_option('wcpt-ticketno-fr', $ticket_nofr);

    $status     = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 0;
    update_option('wcpt-status', $status);
    $statusfr     = isset($_POST['statusfr']) ? sanitize_text_field($_POST['statusfr']) : 0;
    update_option('wcpt-status-fr', $statusfr);

    $type     = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 0;
    update_option('wcpt-type', $type);
    $typefr     = isset($_POST['typefr']) ? sanitize_text_field($_POST['typefr']) : 0;
    update_option('wcpt-type-fr', $typefr);

    $priority     = isset($_POST['priority']) ? sanitize_text_field($_POST['priority']) : 0;
    update_option('wcpt-priority', $priority);
    $priorityfr     = isset($_POST['priorityfr']) ? sanitize_text_field($_POST['priorityfr']) : 0;
    update_option('wcpt-priority-fr', $priorityfr);

    $product     = isset($_POST['product']) ? sanitize_text_field($_POST['product']) : 0;
    update_option('wcpt-product', $product);
    $productfr     = isset($_POST['productfr']) ? sanitize_text_field($_POST['productfr']) : 0;
    update_option('wcpt-product-fr', $productfr);

    $attachment     = isset($_POST['attachment']) ? sanitize_text_field($_POST['attachment']) : 0;
    update_option('wcpt-attachment', $attachment);
    $attachmentfr   = isset($_POST['attachmentfr']) ? sanitize_text_field($_POST['attachmentfr']) : 0;
    update_option('wcpt-attachment-fr', $attachmentfr);

    $closedate     = isset($_POST['closedate']) ? sanitize_text_field($_POST['closedate']) : 0;
    update_option('wcpt-closedate', $closedate);
    $closedatefr  = isset($_POST['closedatefr']) ? sanitize_text_field($_POST['closedatefr']) : 0;
    update_option('wcpt-closedate-fr', $closedatefr);

    $custextfield = isset($_POST['custextfield']) ? sanitize_text_field($_POST['custextfield']) : 0;
    update_option('wcpt-custextfield', $custextfield);
    $metafield1 = isset($_POST['metafield1']) ? sanitize_text_field($_POST['metafield1']) : 0;
    update_option('wcpt-metafield1', $metafield1);
    $metafield1fr = isset($_POST['metafield1fr']) ? sanitize_text_field($_POST['metafield1fr']) : 0;
    update_option('wcpt-metafield1fr', $metafield1fr);

    // Restriction to add same Status values.
    if (
        isset($_POST['statusoptions']) && !empty(isset($_POST['statusoptions']))
    ) {
        $statusOpArr = array("New", "Open", "Resolved");
        $reservedkes = [];
        foreach ($statusOpArr as $key => $value) {
            if (in_array($value, $_POST['statusoptions'])) {
                $reservedkes[] = $value;
            }
        }
        if (!empty($reservedkes)) {
            add_settings_error('status_theme_options', 'statusoptions', __('Reserved values like New, Open, Resolved are not allowed.', 'woo-product-ticket'), 'error');
        } else {
            $statusoption_check = array_map('wcpt_sanitize_status_array', $_POST['statusoptions']);
            $statusoption = wcpt_sanitize_status_array($statusoption_check);
            update_option('wcpt-statusoptions', $statusoption);
        }
    }

    $typeoptions_check = isset($_POST['typeoptions']) ? wcpt_sanitize_status_array($_POST['typeoptions']) : array();    
    update_option('wcpt-typeoptions', $typeoptions_check);

    $priorityoption_check = isset($_POST['priorityoption']) ? wcpt_sanitize_status_array($_POST['priorityoption']) : array();    
    update_option('wcpt-priorityoption', $priorityoption_check);

    $adminsubject = sanitize_text_field(isset($_POST['adminsubject']) ? $_POST['adminsubject'] : 0);
    update_option('wcpt-adminsubject', $adminsubject);

    $admin_to = sanitize_text_field(isset($_POST['admin_to']) ? $_POST['admin_to'] : 0);
    update_option('wcpt-admin_to', $admin_to);

    $customersubject = sanitize_text_field(isset($_POST['customersubject']) ? $_POST['customersubject'] : 0);
    update_option('wcpt-customersubject', $customersubject);

    $customer_to = sanitize_text_field(isset($_POST['customer_to']) ? $_POST['customer_to'] : 0);
    update_option('wcpt-customer_to', $customer_to);

    $deletedata = sanitize_text_field(isset($_POST['deletedata']) ? $_POST['deletedata'] : 0);
    update_option('wcpt-deletedata', $deletedata);
}
