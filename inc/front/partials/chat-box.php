<?php
if (!defined('ABSPATH')) exit;
wp_verify_nonce('yslurl');
if (isset($_GET["ticketid"]) && !empty($_GET["ticketid"])) {
    $ticketid_clean = sanitize_text_field($_GET["ticketid"]);
    $ticketid = get_post($ticketid_clean)->ID;
    global $wpdb;
    $wcpt_chat = $wpdb->prefix . 'wcpt_chat';  // table name

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $getChat = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wcpt_chat}` WHERE post_id = %d", $ticketid));
    // phpcs:enable

    if ($getChat || ($status != "Resolved")) { ?>
        <div class="wcpt-chat">
            <h3><?php esc_html_e('Chat with us', 'woo-product-ticket'); ?></h3>
            <div class="wrapper chat-main">
                <section class="chat-area">
                    <div class="chat-inner chat-box" id="scroll-box">
                        <?php
                        if ($getChat) {
                            foreach ($getChat as $key => $value) {
                                $image = false;
                                $outgoing_display_name = get_userdata($value->outgoing_mgs)->data->display_name;
                                $incoming_display_name = get_userdata($value->incoming_msg)->data->display_name;

                                if ($value->img) {
                                    $image_attributes = wp_get_attachment_image_src($value->img, "full");
                                    if ($image_attributes) {
                                        $image = '<a href="' . esc_url($image_attributes[0]) . '" target="_blank" ><img src="' . esc_url($image_attributes[0]) . '" /></a>';
                                    }
                                }

                                // 1 = admin-side And 0 means customer-side
                                if (($value->in_out == 1)) {
                                    $agentName = $outgoing_display_name;
                                } else {
                                    $agentName = $incoming_display_name;
                                }
                                $msgDate = gmdate("D d M Y", strtotime($value->datetime));

                                if ($value->message || $image) {  ?>
                                    <div class="chat <?php echo ($value->in_out == 0) ? esc_html("outgoing") : esc_html("incoming"); ?> ">
                                        <div class="details">
                                            <?php echo ($value->message) ? "<p><span>" . esc_html($agentName) . "</span>" . esc_html($value->message) . "<span>" . esc_html($msgDate) . "</span></p>" : ""; ?>
                                            <?php echo ($image) ? wp_kses($image, array('a' => array('href' => true), 'img' => array('src' => true, 'alt' => true))) : ""; ?>
                                        </div>
                                    </div>
                        <?php }
                            }
                        } ?>
                    </div>
                    <?php if ($status != "Resolved") { ?>
                        <div class="typing-area">
                            <div class="type-inner-first">
                                <input type="text" name="chattext" id="chattext" class="input-field" placeholder="<?php esc_html_e('Type your message...', 'woo-product-ticket') ?>" autocomplete="off">
                            </div>
                            <div class="type-inner-second">
                                <div class="upload-btn-wrapper">
                                    <button class="btn prev-name-chat"><span><img src="<?php echo esc_url(WCPT_URL . 'assets/img/choose-image-click.svg'); ?>" alt="<?php esc_html_e('Upload Icon', 'woo-product-ticket') ?>"></span><span class="upload-img-btn-span"><?php esc_html_e('Choose Image', 'woo-product-ticket') ?></span></button>
                                    <input type="file" name="chat_attachment" id="chat_attachment" value="" accept="image/*">
                                </div>
                            </div>
                            <div class="type-inner-third">
                                <button name="chatsend" id="chatsend" type="button"><?php esc_html_e('Send', 'woo-product-ticket'); ?><img src="<?php echo esc_url(WCPT_URL . 'assets/img/send.png'); ?>" alt="<?php esc_html_e('Upload Icon', 'woo-product-ticket') ?>"></button>
                            </div>
                            <input type="hidden" class="pticket_id" name="pticket_id" value="<?php echo esc_attr($ticketid); ?>">
                            <input type="hidden" class="outgoing_mgs" name="outgoing_mgs" value="1">
                            <input type="hidden" class="incoming_msg" name="incoming_msg" value="<?php echo esc_attr(get_current_user_id()); ?>">
                            <input type="hidden" class="in_out" name="in_out" value="0">
                            <input type="hidden" class="agent-name" name="agent-name" value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>">
                        </div>
                    <?php } ?>
                </section>
            </div>
        </div>
<?php }
} ?>