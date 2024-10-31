<?php
if (!defined('ABSPATH')) exit;

wp_nonce_field('update_action_status', 'product_ticket_meta_box_nonce');

$status     = get_post_meta($post->ID, '_status', true);
$type       = get_post_meta($post->ID, '_type', true);
$priority   = get_post_meta($post->ID, '_priority', true);
$order      = get_post_meta($post->ID, '_order', true);
$product    = get_post_meta($post->ID, '_product', true);
$closedate  = get_post_meta($post->ID, '_closedate', true);

?>
<div class='inside admin-support-ticket'>
    <?php
    $wcpt_ticket_no   = get_option('wcpt-ticketno');
    $wcpt_status      = get_option('wcpt-status');
    $wcpt_type        = get_option('wcpt-type');
    $wcpt_priority    = get_option('wcpt-priority');
    $wcpt_product     = get_option('wcpt-product');
    $wcpt_attachment  = get_option('wcpt-attachment');
    $wcpt_closedate   = get_option('wcpt-closedate');

    $metafield1       = get_option('wcpt-metafield1');
    if ($metafield1) {
        $custextfield       = get_option('wcpt-custextfield');
        $customfield1   = get_post_meta($post->ID, '_custextfieldval', true);
    }

    if (!$wcpt_ticket_no && !$wcpt_status && !$wcpt_type && !$wcpt_priority && !$wcpt_product && !$wcpt_attachment && !$wcpt_closedate && !$metafield1) {
        echo "<P>" . esc_html__("Please enable fields from settings.", "woo-product-ticket") . "</P>";
    } else { ?>
        <div class="ticket-dropdown">
            <?php
            if ($wcpt_ticket_no) { ?>
                <div class="ticket-dropdown-inner">
                    <h3><?php esc_html_e('Ticket No', 'woo-product-ticket'); ?></h3>
                    <input type="text" name="ticket_no" value="<?php echo esc_attr('#wcpt_' . get_the_ID()); ?>" disabled />
                </div>
            <?php }

            if ($wcpt_status) {
                $statusOpArr = array("New", "Open", "Resolved");
                $statusoptions    = get_option('wcpt-statusoptions');
                $newarr = [];
                if ($statusoptions) {
                    $newarr = array_merge($statusOpArr, $statusoptions);
                } else {
                    $newarr = $statusOpArr;
                }
            ?>
                <div class="ticket-dropdown-inner">
                    <h3><?php esc_html_e('Status', 'woo-product-ticket'); ?></h3>
                    <select name="status" id="">
                        <?php
                        if (is_array($newarr)) {
                            foreach ($newarr as $key => $value) {
                                $statusSelected = ($status == $value) ? "selected" : "";
                                echo '<option value="' . esc_attr($value) . '" ' . esc_attr($statusSelected) . '>' . esc_html($value) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
            }

            if ($wcpt_type) {
                if ($type) { ?>
                    <div class="ticket-dropdown-inner">
                        <h3><?php esc_html_e('Type', 'woo-product-ticket'); ?></h3>
                        <?php
                        echo '<input type="text" name="status" value="' . esc_attr($type) . '" disabled>';
                        ?>
                    </div>
                <?php
                }
            }

            if ($wcpt_priority) {
                if ($priority) {   ?>
                    <div class="ticket-dropdown-inner">
                        <h3><?php esc_html_e('Priority', 'woo-product-ticket'); ?></h3>
                        <?php echo '<input type="text" name="status" value="' . esc_attr($priority) . '" disabled>'; ?>
                    </div>
                <?php
                }
            }

            if ($order) { ?>
                <div class="ticket-dropdown-inner">
                    <h3><?php esc_html_e('Order', 'woo-product-ticket'); ?></h3>
                    <?php $get_order = wc_get_order($order); ?>
                    <input type="text" name="order" value="<?php echo '#' . esc_attr($order) . ' ' . esc_attr($get_order->get_billing_first_name()) . ' ' . esc_attr($get_order->get_billing_last_name()); ?>" disabled>

                </div>
                <?php
                if (!$wcpt_product) { ?>
                    <?php if ($product) { ?>
                        <div class="ticket-dropdown-inner">
                            <h3><?php esc_html_e('Product', 'woo-product-ticket'); ?></h3>
                            <?php $getProduct = get_post($product);
                            echo '<input type="text" name="status" value="' . esc_attr($getProduct->post_title) . '" disabled>';
                            ?>
                        </div>
                    <?php }
                }

                if ($metafield1 && $custextfield) { ?>
                    <div class="ticket-dropdown-inner">
                        <h3><?php echo esc_html($custextfield); ?></h3>
                        <input type="text" name="customfield1" id="customfield1" value="<?php echo esc_attr($customfield1); ?>" disabled>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <?php
        if ($wcpt_attachment) {
            $image_size = 'full';
            if ($image_attributes = wp_get_attachment_image_src(get_post_meta($post->ID, '_second_featured_img', true), $image_size)) { ?>
                <div class="attach-img">
                    <h3><?php esc_html_e('Attachments', 'woo-product-ticket'); ?></h3>
                    <img src="<?php echo esc_url($image_attributes[0]); ?>" />
                </div>
            <?php
            }
        }

        if ($wcpt_closedate && $closedate) {  ?>
            <h3><?php esc_html_e('Close Date', 'woo-product-ticket'); ?></h3>
            <input type="text" name="closedate" value="<?php echo esc_attr(gmdate('d-m-Y', strtotime($closedate))); ?>" disabled />
    <?php }
    } ?>
</div>