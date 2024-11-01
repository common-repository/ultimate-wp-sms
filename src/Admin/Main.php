<?php 
namespace UWS\LITE\SMS\Admin;

use UWS\LITE\SMS\Admin\Template\{
    SubscribersTemplate,
    ConversationTemplate
};
class Main{

 
    private $settings,$groupManager,$sendSms,$help,$messageHistory,$UWSSubscribers;
    public $name,
        $version,
        $file_path;
    public function __construct(string $name, string $version, string $file_path ) {
		$this->name      = $name;
		$this->version   = $version;
		$this->file_path = $file_path;
        add_action( 'admin_menu', array($this,'admin_menu'), 99 );
        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
    }
    /**
     * Administrator admin_menu
     */
    public function admin_menu()
    {
        $this->settings  = new Settings();
        $this->groupManager = new GroupManager();
        $this->sendSms = new SendSms();
        $this->help = new UWSHelp();
        $hook_suffix = array();
        add_menu_page(__('Ultimate SMS', 'ultimate-wp-sms'), __('Ultimate SMS', 'ultimate-wp-sms'), 'uws_sendsms', 'uws', array($this->sendSms, 'render'), UWS_URL.'images/plugin-icon.svg','31');
        
        $hook_suffix['sendSms'] = add_submenu_page('uws', __('Send Message', 'ultimate-wp-sms'), __('Send Message', 'ultimate-wp-sms'), 'uws_sendsms', 'uws', array($this->sendSms, 'render'));
        $hook_suffix['groupManager'] = add_submenu_page('uws', __('Group Manager', 'ultimate-wp-sms'), __('Group Manager', 'ultimate-wp-sms'), 'uws_group', 'uws-group', array($this->groupManager, 'render'),'32');
        add_submenu_page('uws', __('Conversations', 'ultimate-wp-sms'), __('Conversations', 'ultimate-wp-sms'), 'uws_group', 'uws-message-history', array($this, 'render_history'),'32');
        add_submenu_page('uws', __('Subscribers', 'ultimate-wp-sms'), __('Subscribers', 'ultimate-wp-sms'), 'uws_group', 'uws-subscribers', array($this, 'render_subscriber'),'32');
        $hook_suffix['settings'] = add_submenu_page('uws', __('Settings', 'ultimate-wp-sms'), __('Settings', 'ultimate-wp-sms'), 'uws_setting', 'uws-settings', array($this->settings, 'render'));
        $hook_suffix['help'] = add_submenu_page('uws', __('Go Pro & Help', 'ultimate-wp-sms'),sprintf(__('%s Go Pro & Help %s', 'ultimate-wp-sms'), '<span style="color:#FF7600">', '</span>'), 'manage_options', 'uws-help', array($this->help, 'render'));
        do_action('uws_admin_menu');
        // Add styles to menu pages
        foreach ($hook_suffix as $menu => $hook) {
            // Backward compatibility
            if (method_exists($this->$menu, 'assets')) {
                add_action("load-{$hook}", array($this->$menu, 'assets'));
            }
        }
    }
    /**
     * Load assets
     */
    public function admin_assets()
    {
        
        wp_enqueue_style('uws-notie', UWS_URL . 'css/uws-notie.min.css', false, UWS_VERSION);
        wp_enqueue_style('uws-slimselect', UWS_URL . 'css/uws-slimselect.css', true, UWS_VERSION);
        wp_enqueue_style('uws-style', UWS_URL . 'css/uws-style.css', true, UWS_VERSION);
        
        wp_enqueue_script('uws-notie', UWS_URL . 'js/uws-notie.min.js', array( 'jquery' ), UWS_VERSION);
        wp_enqueue_script('uws-slimselect', UWS_URL . 'js/uws-slimselect.min.js', array( 'jquery' ), UWS_VERSION);
        wp_enqueue_script('uws-script', UWS_URL . 'js/uws-script.js', array( 'jquery', 'wp-hooks' ), UWS_VERSION);
        wp_localize_script('uws-script', 'uws_script', array('ajax' => admin_url("admin-ajax.php"),'securty_check'=>wp_create_nonce('uws-modal-ajax-check')));
    }

    public function render_history(){
        $args = array('page'=>'history');
        new ConversationTemplate($args);
    }


    public function render_subscriber(){
        $args = array('page'=>'subscriber');
        new SubscribersTemplate($args);
    }
}
?>