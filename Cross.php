<?php
defined('ABSPATH') || exit;

if ( !class_exists('NjtCross') ) {
    class NjtCross
    {
        public $pluginPrefix = '';
        public $pluginInstallSearching = '';
        public $pluginDirURL = '';

        public function __construct($pluginPrefix, $pluginInstallSearching, $pluginDirURL)
        {
            $this->pluginPrefix = $pluginPrefix;
            $this->pluginInstallSearching = $pluginInstallSearching;
            $this->pluginDirURL = $pluginDirURL;
            $this->doHooks();
        }

        public function is_plugin_exist() { return false; }
    
        public function doHooks() {
            add_action('init', function(){
                if ($this->is_plugin_exist()) {
                    $notificationOption = get_option("njt_noti_{$this->pluginPrefix}_cross"); //Save the next time notification will appear
                    $popupOption        = get_option("njt_popup_{$this->pluginPrefix}_cross"); //Save the next time notification will appear
        
                    add_action('wp_dashboard_setup', array($this, 'add_dashboard'));
                    
                    if ($notificationOption === false || time() >= $notificationOption) {
                        add_action('admin_notices', array($this, 'add_notification'));
                        add_action("wp_ajax_njt_{$this->pluginPrefix}_cross_notification", array($this, 'ajax_set_notification'));
                    }

                    if ($popupOption === false || time() >= $popupOption) {
                        add_action('admin_enqueue_scripts', array($this, 'add_popup'));
                        add_action("wp_ajax_njt_{$this->pluginPrefix}_cross_popup", array($this, 'ajax_set_notification'));
                    }
                }
            });
        }

        public function add_notification() {
            if ( function_exists('get_current_screen') ) {
                $screen = get_current_screen();
                if ( 'plugins' != $screen->id ) return;
            } else return;
        
            if ( function_exists('current_user_can') && current_user_can('install_plugins') ) {
                $nonce = wp_create_nonce('install-plugin_' . $this->pluginPrefix);
                $url   = self_admin_url('update.php?action=install-plugin&plugin=' . $this->pluginPrefix . '&_wpnonce=' . $nonce);
            } else {
                $url = admin_url("plugin-install.php?s={$this->pluginInstallSearching}&tab=search&type=term");
            }
            ?>
            <div class="notice notice-info is-dismissible" id="njt-wa-ads-wrapper">
                <div>
                    <h4><?php _e('Recommend', 'ninjateam-whatsapp') ?></h4>
                    <p><?php _e('To easily manage your files in WordPress media library with folders, please try FileBird plugin.', 'ninjateam-whatsapp') ?></p>
                    <div>
                        <a class="button button-primary" target="_blank" href="<?php echo esc_url($url) ?>">
                            <strong><?php _e('I\'m feeling lucky', 'ninjateam-whatsapp') ?></strong>
                        </a>
                        <a class="button button-secondary" target="_blank" href="<?php echo esc_url('https://1.envato.market/FileBird3') ?>">
                            <strong><?php _e('Go FileBird Pro', 'ninjateam-whatsapp') ?></strong>
                        </a>
                        <a class="button" href="javascript:;" id="njt-wa-ads">
                            <?php _e('No, thanks', 'ninjateam-whatsapp') ?>
                        </a>
                    </div>
                </div>
                <img src="<?php echo esc_url($this->pluginDirURL . 'assets/img/filebird-recommended.png') ?>" alt="filebird">
            </div>
            <style>
                #njt-wa-ads-wrapper{
                    text-align: right;
                }
                #njt-wa-ads-wrapper > div{
                    float: left;
                    text-align: left;
                }
                #njt-wa-ads-wrapper img{
                    width: 200px;
                }
                #njt-wa-ads-wrapper #njt-wa-ads{
                    background: none;
                    border: none;
                    color: #7e868f;
                }
            </style>
            <script>
            jQuery(document).ready(function() {
                jQuery('#njt-wa-ads').click(function() {
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            'action': '<?php echo "njt_{$this->pluginPrefix}_cross_notification" ?>',
                            'nonce': '<?php echo wp_create_nonce("njt_{$this->pluginPrefix}_cross_notification_nonce") ?>'
                        }
                    }).done(function(result) {
                        if (result.success) {
                            jQuery('#njt-wa-ads-wrapper').hide('slow')
                        } else {
                            console.log("Error", result.data.status)
                        }
                    });
                })
            });
            </script>
            <?php
        }

        public function add_dashboard(){
            wp_add_dashboard_widget( 'dashboard_widget', 'Recommended', array($this, 'add_dashboard_widget') );
        }

        public function add_dashboard_widget(){
            ?>
            <div>
                <div>
                    <h3>Your WordPress media library is messy?</h3>
                    <span>Start using FileBird to organize your files into folders by drag and drop.</span>
                </div>
                <div class="fbv-ads-window-img-wrap">
                    <img src="https://ps.w.org/filebird/assets/screenshot-2.gif" alt="screenshot_demo">
                </div>
                <div class="fbv-ads-window-btn">
                    <div><a class="button button-primary fbv-ads-install" href="#"><i class="dashicons dashicons-wordpress-alt"></i>Install for free</a></div>
                    <div><a class="fbv-ads-link" href="javascript:void(0)">Don't display again</a></div>
                </div>
            </div>
            <style>
                .fbv-ads-link {
                    color: #a1a1a1;
                    text-decoration: none;
                }
                .fbv-ads-link:hover, .fbv-ads-link:focus, .fbv-ads-link:active {
                    box-shadow: none;
                    color: #a1a1a1;
                    opacity: 0.8;
                    outline: none;
                }
                .fbv-ads-window {
                    background-color: #fff;
                    border-radius: 3px;
                    bottom: 100%;
                    box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.05);
                    margin-bottom: 10px;
                    opacity: 0;
                    pointer-events: none;
                    position: absolute;
                    right: -5px;
                    transform: translateY(10px);
                    transition: all 0.3s;
                    visibility: hidden;
                    width: 360px;
                }
                .fbv-ads-window-mess {
                    background-color: #0085ba;
                    border-radius: 3px 3px 0 0;
                    color: #fff;
                    padding: 15px 20px;
                }
                .fbv-ads-window-mess h3 {
                    color: #fff;
                    margin: 0 0 10px;
                }
                .fbv-ads-window-mess span {
                    line-height: 1.5;
                    opacity: 0.9;
                }
                .fbv-ads-window-img-wrap {
                    padding: 20px;
                }
                .fbv-ads-window-img-wrap img {
                    max-width: 100%;
                }
                .fbv-ads-window-btn {
                    padding: 5px 20px 25px;
                    text-align: center;
                }
                .fbv-ads-window-btn .button-primary {
                    align-items: center;
                    display: inline-flex;
                    font-weight: 500;
                    height: 42px;
                    justify-content: center;
                    margin-bottom: 10px;
                    max-width: 100%;
                    padding: 0;
                    width: 162px;
                }
                .fbv-ads-window-btn .button-primary, .fbv-ads-window-btn .button-primary:hover, .fbv-ads-window-btn .button-primary:focus, .fbv-ads-window-btn .button-primary:active {
                    box-shadow: none;
                    outline: none;
                }
                .fbv-ads-window-btn .button-primary i {
                    margin-right: 8px;
                }
                .fbv-ads-window-btn .button-primary.fbv_installing, .fbv-ads-window-btn .button-primary.fbv_installing:hover, .fbv-ads-window-btn .button-primary.fbv_installing:focus, .fbv-ads-window-btn .button-primary.fbv_installing:active {
                    background-color: #e4f7ff;
                    border-color: #e4f7ff;
                    color: #0085ba;
                    cursor: not-allowed;
                }
                .fbv-ads-window-btn .button-primary.fbv_installing i {
                    animation: rotate360 1s linear infinite both;
                }
            </style>
            <?php
        }

        public function ajax_set_notification(){
            check_ajax_referer("njt_{$this->pluginPrefix}_cross_notification_nonce", 'nonce', true);
            //Save after 30 days
            update_option("njt_noti_{$this->pluginPrefix}_cross", time() + (30 * 60 * 60 * 24));
            wp_send_json_success();
        }

        public function ajax_set_popup(){
            check_ajax_referer("njt_{$this->pluginPrefix}_cross_popup_nonce", 'nonce', true);
            //Save after 30 days
            update_option("njt_popup_{$this->pluginPrefix}_cross", time() + (30 * 60 * 60 * 24));
            wp_send_json_success();
        }

        public function add_popup(){
            if ( function_exists('get_current_screen') ) {
                $screen = get_current_screen();
                if ( 'upload' != $screen->id ) return;
            } else return;

            wp_enqueue_script("njt-popup-{$this->pluginPrefix}-cross", $this->pluginDirURL . 'assets/js/fbv-ads.js', ['jquery'], '1.0', true);
            ?>
            <script>
             jQuery(document).ready(function() {
                jQuery('#njt-wa-ads').click(function() {
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            'action': '<?php echo "njt_{$this->pluginPrefix}_cross_popup" ?>',
                            'nonce': '<?php echo wp_create_nonce("njt_{$this->pluginPrefix}_cross_popup_nonce") ?>'
                        }
                    }).done(function(result) {
                        if (result.success) {
                            jQuery('#njt-wa-ads-wrapper').hide('slow')
                        } else {
                            console.log("Error", result.data.status)
                        }
                    });
                })
            });
            </script>
            <?php
        }
    }
}

if ( !class_exists('FileBirdCross') ) {
    class FileBirdCross extends NjtCross {
        public function is_plugin_exist()
        {
            return ( !defined('NJT_FILEBIRD_VERSION') && !defined('NJFB_VERSION') );
        }
    }

    new FileBirdCross('filebird', 'filebird+ninjateam', NTA_WHATSAPP_PLUGIN_URL);
}



