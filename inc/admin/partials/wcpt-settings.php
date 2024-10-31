<?php
if (!defined('ABSPATH')) exit;

$fetchuser       = get_option('wcpt-users');
$ticket_no       = get_option('wcpt-ticketno');
$status          = get_option('wcpt-status');
$type            = get_option('wcpt-type');
$priority        = get_option('wcpt-priority');
// $order		 = get_option('wcpt-order');
$product         = get_option('wcpt-product');
$attachment      = get_option('wcpt-attachment');
$closedate       = get_option('wcpt-closedate');

$ticket_nofr     = get_option('wcpt-ticketno-fr');
$statusfr        = get_option('wcpt-status-fr');
$typefr          = get_option('wcpt-type-fr');
$priorityfr      = get_option('wcpt-priority-fr');
// $orderfr		 = get_option('wcpt-order-fr');
$productfr       = get_option('wcpt-product-fr');
$attachmentfr    = get_option('wcpt-attachment-fr');
$closedatefr     = get_option('wcpt-closedate-fr');

$custextfield    = get_option('wcpt-custextfield');
$metafield1      = get_option('wcpt-metafield1');
$metafield1fr    = get_option('wcpt-metafield1fr');

$statusoptions   = get_option('wcpt-statusoptions');

$typeoptions     = get_option('wcpt-typeoptions');
$priorityoption  = get_option('wcpt-priorityoption');

$adminsubject    = get_option('wcpt-adminsubject');
$admin_to        = htmlspecialchars_decode(get_option('wcpt-admin_to'));
$customersubject = get_option('wcpt-customersubject');
$customer_to     = htmlspecialchars_decode(get_option('wcpt-customer_to'));

$deletedata      = get_option('wcpt-deletedata');

?>
<script>
    let selectedUsers = [];
</script>
<?php if ($fetchuser) { ?> <script>
        selectedUsers = '<?php echo esc_js(wp_json_encode($fetchuser)); ?>';
    </script> <?php } ?>

<div class="wrap admin-panel-setting">
    <div class="wcpt-table">
        <h1><?php esc_html_e('Product Ticket System For WooCommerce Settings', 'woo-product-ticket'); ?></h1>
        <?php echo wp_kses_post(settings_errors()); ?>
        <form name="wcpt-options" class="wcpt-options" method="post" action="">
            <?php wp_nonce_field('contact_form_submit', 'cform_generate_nonce'); ?>
            <h2><?php esc_html_e('Woo Product Ticket Users', 'woo-product-ticket'); ?></h2>
            <table class="form-table select-user-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Select Users', 'woo-product-ticket'); ?></th>
                    <td>
                        <?php $getUsers = get_users('role=woo_product_ticket'); ?>
                        <select id="users" name="users[]" multiple="multiple">
                            <?php
                            foreach ($getUsers as $key => $user) {
                                $userId = $user->ID;
                                $displayName = $user->display_name;
                                $selected = ($fetchuser[$key] == $user->ID) ? ' selected="selected"' : '';

                                echo '<option value="' . esc_attr($userId) . '"' . esc_attr($selected) . '>' . esc_html($displayName) . '</option>';
                            }

                            ?>
                        </select>
                    </td>
                </tr>
            </table>
            <h2><?php esc_html_e('Hide Show Fields For admin and Customer', 'woo-product-ticket'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"></th>
                    <th scope="row"><?php esc_html_e('Admin', 'woo-product-ticket'); ?></th>
                    <th scope="row"><?php esc_html_e('Customer', 'woo-product-ticket'); ?></th>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Ticket No', 'woo-product-ticket') ?></th>
                    <td>
                        <label for="ticketno">
                            <input type="checkbox" class="ticketno" name="ticketno" value="1" <?php echo ($ticket_no == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                    <td>
                        <label for="ticketnofr">
                            <input type="checkbox" class="ticketnofr" name="ticketnofr" value="1" <?php echo ($ticket_nofr == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Status', 'woo-product-ticket')  ?></th>
                    <td>
                        <label for="status">
                            <input type="checkbox" class="status" name="status" value="1" <?php echo ($status == 1) ? esc_html('checked="checked"') : '';    ?>>
                        </label>
                    </td>
                    <td>
                        <label for="statusfr">
                            <input type="checkbox" class="statusfr" name="statusfr" value="1" <?php echo ($statusfr == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Type', 'woo-product-ticket'); ?></th>
                    <td>
                        <label for="type">
                            <input type="checkbox" class="type" name="type" value="1" <?php echo ($type == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                    <td>
                        <label for="typefr">
                            <input type="checkbox" class="typefr" name="typefr" value="1" <?php echo ($typefr == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Priority', 'woo-product-ticket'); ?></th>
                    <td>
                        <label for="priority">
                            <input type="checkbox" class="priority" name="priority" value="1" <?php echo ($priority == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                    <td>
                        <label for="priorityfr">
                            <input type="checkbox" class="priorityfr" name="priorityfr" value="1" <?php echo ($priorityfr == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Product', 'woo-product-ticket'); ?></th>
                    <td>
                        <label for="product">
                            <input type="checkbox" class="product" name="product" value="1" <?php echo ($product == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                    <td>
                        <label for="productfr">
                            <input type="checkbox" class="productfr" name="productfr" value="1" <?php echo ($productfr == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Attachment', 'woo-product-ticket'); ?></th>
                    <td>
                        <label for="attachment">
                            <input type="checkbox" class="attachment" name="attachment" value="1" <?php echo ($attachment == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                    <td>
                        <label for="attachmentfr">
                            <input type="checkbox" class="attachmentfr" name="attachmentfr" value="1" <?php echo ($attachmentfr == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Close Date', 'woo-product-ticket'); ?></th>
                    <td>
                        <label for="closedate">
                            <input type="checkbox" class="closedate" name="closedate" value="1" <?php echo ($closedate == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                    <td>
                        <label for="closedatefr">
                            <input type="checkbox" class="closedatefr" name="closedatefr" value="1" <?php echo ($closedatefr == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                </tr>
            </table>
            <h2><?php esc_html_e('Custom Text field', 'woo-product-ticket'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Custom field name', 'woo-product-ticket'); ?></th>
                    <td> <input type="text" value="<?php echo esc_attr($custextfield); ?>" id="custextfield" name="custextfield"> </td>

                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Show/Hide', 'woo-product-ticket'); ?></th>
                    <td>
                        <label for="metafield1">
                            <input type="checkbox" class="metafield1" name="metafield1" value="1" <?php echo ($metafield1 == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                    <td>
                        <label for="metafield1fr">
                            <input type="checkbox" class="metafield1fr" name="metafield1fr" value="1" <?php echo ($metafield1fr == 1) ? esc_html('checked="checked"') : ''; ?>>
                        </label>
                    </td>
                </tr>
            </table>
            <h2><?php esc_html_e('Set Custom options (value) for below fields', 'woo-product-ticket'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="statusoptions"><?php esc_html_e('Status', 'woo-product-ticket'); ?></label></th>
                    <td>
                        <select class="form-control select2-tags" multiple="multiple" id="statusoptions" name="statusoptions[]">
                            <?php if ($statusoptions) {
                                foreach ($statusoptions as $key => $s_value) {
                                    echo '<option value="' . esc_attr($s_value) . '" selected>' . esc_html($s_value) . '</option>';
                                }
                            }; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="typeoptions"><?php esc_html_e('Issue Type', 'woo-product-ticket'); ?></label></th>
                    <td>
                        <select class="form-control select2-tags" multiple="multiple" id="typeoptions" name="typeoptions[]">
                            <?php if ($typeoptions) {
                                foreach ($typeoptions as $key => $t_value) {
                                    echo '<option value="' . esc_attr($t_value) . '" selected>' . esc_html($t_value) . '</option>';
                                }
                            }; ?>
                        </select>
                </tr>
                <tr>
                    <th><label for="priorityoption"><?php esc_html_e('Priority', 'woo-product-ticket'); ?></label></th>
                    <td>
                        <select class="form-control select2-tags" multiple="multiple" id="priorityoption" name="priorityoption[]">
                            <?php if ($priorityoption) {
                                foreach ($priorityoption as $key => $p_value) {
                                    echo '<option value="' . esc_attr($p_value) . '" selected>' . esc_html($p_value) . '</option>';
                                }
                            }; ?>
                        </select>
                </tr>
            </table>
            <h2><?php esc_html_e('Admin Email', 'woo-product-ticket'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="adminsubject"><?php esc_html_e('Email Subject', 'woo-product-ticket'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr($adminsubject); ?>" id="adminsubject" name="adminsubject"></td>
                </tr>
                <tr>
                    <th><label for="adminsubject"><?php esc_html_e('Email Template', 'woo-product-ticket'); ?></label></th>
                    <td>
                        <span><?php esc_html_e('Please use below variables for fields value', 'woo-product-ticket'); ?></span>
                        <code><?php esc_html_e("{pttitle}, {ptcontents},{status}, {type}, {priority}, {order}, {product}, {customfield1}", 'woo-product-ticket'); ?></code>
                        <textarea name="admin_to" id="admin_to" cols="100" rows="20"><?php echo isset($admin_to) ? esc_textarea($admin_to) : ''; ?></textarea>
                    </td>
                </tr>
            </table>
            <h2><?php esc_html_e('Customer Email', 'woo-product-ticket'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="customersubject"><?php esc_html_e('Email Subject', 'woo-product-ticket'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr($customersubject); ?>" id="customersubject" name="customersubject"></td>
                </tr>
                <tr>
                    <th><label for="customersubject"><?php esc_html_e('Email Template', 'woo-product-ticket'); ?></label></th>
                    <td>
                        <span><?php esc_html_e('Please use below variables for fields value', 'woo-product-ticket'); ?></span>
                        <code><?php esc_html_e("{pttitle}, {ptcontents},{status}, {type}, {priority}, {order}, {product}, {customfield1}", 'woo-product-ticket'); ?></code>
                        <textarea name="customer_to" id="customer_to" cols="100" rows="20"><?php echo isset($customer_to) ? esc_textarea($customer_to) : ''; ?></textarea>
                    </td>
                </tr>
            </table>

            <h2><?php esc_html_e('Delete Data on Uninstall', 'woo-product-ticket'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="customersubject"><?php esc_html_e('Please check to delete data on uninstall plugin', 'woo-product-ticket'); ?></label></th>
                    <td>
                        <input type="checkbox" class="deletedata" name="deletedata" value="1" <?php echo ($deletedata == 1) ? esc_html('checked="checked"') : ''; ?>>
                    </td>
                </tr>

            </table>

            <input type="hidden" name="wcpt-options-hidden" value="1">
            <input type="submit" class="button button-primary" value="<?php esc_html_e('Save', 'woo-product-ticket'); ?>">
        </form>
    </div>
</div>