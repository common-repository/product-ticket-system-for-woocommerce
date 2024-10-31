<?php if (!defined('ABSPATH')) exit; ?>
<div class="wcpt-chat admin-chat">
    <?php global $wpdb;
    $wcpt_chat = $wpdb->prefix . 'wcpt_chat'; // table name

    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $getChat = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wcpt_chat}` WHERE post_id = %d", $post->ID));
    // phpcs:enable 

    $_status = get_post_meta($post->ID, '_status', true);

    if ($getChat || ($_status != "Resolved")) { ?>
        <div class="wrapper chat-main">
            <section class="chat-area">
                <div class="chat-box chat-inner" id="scroll-box">
                    <?php
                    if ($getChat) {
                        foreach ($getChat as $key => $value) {

                            $image = false;
                            $outgoing_display_name = get_userdata($value->outgoing_mgs)->data->display_name;
                            $incoming_display_name = get_userdata($value->incoming_msg)->data->display_name;

                            if ($value->img) {
                                $image_attributes = wp_get_attachment_image_src($value->img, 'full');
                                if ($image_attributes) {
                                    $image = '<a href="' . esc_url($image_attributes[0]) . '" target="_blank" ><img src="' . esc_url($image_attributes[0])  . '" /></a>';
                                }
                            }

                            // 1 = admin-side And 0 means customer-side
                            if (($value->in_out == 1)) {
                                $agentName = $outgoing_display_name;
                            } else {
                                $agentName = $incoming_display_name;
                            }

                            $msgDate = gmdate("D d M Y", strtotime($value->datetime));

                            if ($value->message || $image) { ?>
                                <div class="chat <?php echo ($value->in_out == 1) ? esc_html("outgoing") : esc_html("incoming"); ?> ">
                                    <div class="details">
                                        <?php
                                        echo ($value->message) ? "<p><span>" . esc_html($agentName) . "</span>" . esc_html($value->message) . "<span>" . esc_html($msgDate) . "</span></p>" : "";
                                        echo ($image) ? wp_kses($image, array('a' => array('href' => true), 'img' => array('src' => true, 'alt' => true))) : "";
                                        ?>
                                    </div>
                                </div>
                    <?php
                            }
                        }
                    }
                    ?>
                </div>

                <?php
                $_status = get_post_meta($post->ID, '_status', true);

                if ($_status != "Resolved") { ?>
                    <div class="typing-area">
                        <div class="type-inner-first">
                            <input type="text" name="chattext" id="chattext" class="input-field" placeholder="<?php esc_html_e('Type your message...', 'ysl-product-ticket') ?>" autocomplete="off">
                        </div>
                        <?php // $meta_key = 'chat_attachment'; // echo wcpt_image_uploader_field($meta_key, get_post_meta($post->ID, $meta_key, true)); 
                        ?>
                        <div class="type-inner-second">
                            <div class="upload-btn-wrapper">
                                <button type="button" class=" wcpt_upload_image_button btn prev-name-chat">
                                    <span><img src="<?php echo esc_url(WCPT_URL . 'assets/img/choose-image-click.svg'); ?>" alt="upload icon"></span>
                                    <span class="upload-img-btn-span"><?php esc_html_e('Choose Image', 'ysl-product-ticket') ?></span>
                                </button>
                                <input type="hidden" name="chat_attachment" id="chat_attachment" value="" />
                            </div>
                        </div>
                        <div class="type-inner-third">
                            <button name="chatsend" id="chatsend" type="button"><?php esc_html_e('Send', 'ysl-product-ticket') ?> <img src="<?php echo esc_url(WCPT_URL . 'assets/img/send.png'); ?>" alt="upload icon"></button>
                        </div>
                        <input type="hidden" class="pticket_id" name="pticket_id" value="<?php echo esc_attr($post->ID); ?>">
                        <input type="hidden" class="outgoing_mgs" name="outgoing_mgs" value="<?php echo esc_attr(get_current_user_id()); ?>">
                        <input type="hidden" class="incoming_msg" name="incoming_msg" value="<?php echo esc_attr($post->post_author); ?>">
                        <input type="hidden" class="in_out" name="in_out" value="1">
                        <input type="hidden" class="agent-name" name="agent-name" value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>">
                    </div>
                <?php
                }
                ?>
            </section>
        </div>
    <?php } else { ?>
        <div class="wrapper">
            <p><?php esc_html_e('No Chat History.', 'ysl-product-ticket') ?></p>
        </div>
    <?php } ?>
</div>