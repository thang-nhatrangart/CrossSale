<?php
defined('ABSPATH') || exit;

if ( !class_exists('NjtReview') ) {
    class NjtReview
    {
        public $pluginPrefix = '';
        public $pluginName = '';
        public $textDomain = '';
        public $pluginDirURL = '';

        public function __construct($pluginPrefix, $pluginName, $textDomain)
        {
            $this->pluginPrefix = $pluginPrefix;
            $this->pluginName = $pluginName;
            $this->textDomain = $textDomain;
            $this->doHooks();
        }

        public function doHooks() {
            $option = get_option("{$this->pluginPrefix}_review");
            if (time() >= (int)$option && $option !== '0'){
                add_action('admin_notices', array($this, 'add_notification'));
                add_action("wp_ajax_{$this->pluginPrefix}_save_review", array($this, 'save_review'));
            }
        }

        public function save_review(){
            check_ajax_referer("{$this->pluginPrefix}_review_nonce", 'nonce', true);
            
            $field = sanitize_text_field($_POST['field']);

            if ($field == 'later'){
                update_option("{$this->pluginPrefix}_review", time() + 3*60*60*24); //After 3 days show
            } else if ($field == 'alreadyDid'){
                update_option("{$this->pluginPrefix}_review", 0);
            }
            wp_send_json_success();
        }

        public function add_notification() {
            if (function_exists('get_current_screen')) {
                if (get_current_screen()->id == 'upload' || get_current_screen()->id == 'plugins') {
                    $selector = esc_attr($this->pluginPrefix) . '-review';
                    ?>
                    <div class="notice notice-success is-dismissible" id="<?php echo $selector ?>">
                        <h3><?php _e("Give {$this->pluginName} a review", $this->textDomain)?></h3>
                        <p>
                            <?php _e("Thank you for choosing {$this->pluginName}. We hope you love it. Could you take a couple of seconds posting a nice review to share your happy experience?", $this->textDomain)?>
                        </p>
                        <p>
                            <?php _e('We will be forever grateful. Thank you in advance ;)', $this->textDomain)?>
                        </p>
                        <p>
                            <a href="javascript:;" data="rateNow" class="button button-primary" style="margin-right: 5px"><?php _e('Rate now', $this->textDomain)?></a>
                            <a href="javascript:;" data="later" class="button" style="margin-right: 5px"><?php _e('Later', $this->textDomain)?></a>
                            <a href="javascript:;" data="alreadyDid" class="button"><?php _e('Already did', $this->textDomain)?></a>
                        </p>
                    </div>
                    <script>
                    jQuery(document).ready(function () {
                        jQuery("#<?php echo $selector?> a").on("click", function () {
                            var thisElement = this;
                            var fieldValue = jQuery(thisElement).attr("data");
                            var proLink = "https://codecanyon.net/item/media-folders-manager-for-wordpress/reviews/21715379?utf8=%E2%9C%93&reviews_controls%5Bsort%5D=ratings_descending";
                            var freeLink = "https://wordpress.org/support/plugin/filebird/reviews/#new-post";
                            var hidePopup = false;
                            if (fieldValue == "rateNow") {
                                window.open(freeLink, "_blank");
                            } else {
                                hidePopup = true;
                            }

                            jQuery.ajax({
                                url: window.ajaxurl,
                                type: "post",
                                data: {
                                    action: '<?php echo $this->pluginPrefix . '_save_review' ?>',
                                    field: fieldValue,
                                    nonce: '<?php echo wp_create_nonce($this->pluginPrefix . '_review_nonce') ?>',
                                },
                                }).done(function (result) {
                                    if (result.success) {
                                        if (hidePopup == true) {
                                            jQuery('#<?php echo $selector ?>').hide("slow");
                                        }
                                    } else {
                                        console.log("Error", result.message);
                                        if (hidePopup == true) {
                                            jQuery('#<?php echo $selector ?>').hide("slow");
                                        }
                                    }
                                }).fail(function (res) {
                                console.log(res.responseText);

                                if (hidePopup == true) {
                                    jQuery('#<?php echo $selector ?>').hide("slow");
                                    }
                                });
                        })
                    })
                    </script>
                    <?php
                }
            }
        }
    }
}

if ( !class_exists('NJTWhatsAppReviewCross') ) {
    class NJTWhatsAppReviewCross extends NjtReview {
    }
    new NJTWhatsAppReviewCross('njt_wa', 'WhatsApp Plugin', 'ninjateam-whatsapp');
}



