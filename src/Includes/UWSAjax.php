<?php
namespace UWS\LITE\SMS\Includes;
use UWS\LITE\SMS\Admin\{
    Settings,
    MessageHistory,
    GroupManager,
    SendSms,
    UWSHelp
};
/**
 * The core class, where logic is defined.
 */
class UWSAjax{
    private $settings,$groupManager,$sendSms,$help;
    public $name,
        $version,
        $file_path;
        
    public function __construct(string $name, string $version, string $file_path ) {
		$this->name      = $name;
		$this->version   = $version;
		$this->file_path = $file_path;
        if(is_admin()){
            $this->settings  = new Settings();
            $this->groupManager = new GroupManager();
            $this->sendSms = new SendSms();
            $this->help = new UWSHelp();
            add_action( 'wp_ajax_show_modal', array($this,'uws_show_modal') );
            add_action( 'wp_ajax_show_alert_form', array($this,'show_alert_form') );
            
            //Group Manager
            add_action( 'wp_ajax_get_group_cards',array($this->groupManager,'get_group_cards_ajax'));
            add_action( 'wp_ajax_get_group_members',array($this->groupManager,'get_group_members_ajax'));
            add_action( 'wp_ajax_add_edit_group',array($this->groupManager,'add_edit_group_ajax'));
            add_action( 'wp_ajax_add_edit_group_member',array($this->groupManager,'add_edit_group_member_ajax'));
            add_action( 'wp_ajax_get_member_edit_form',array($this->groupManager,'get_member_edit_form'));
            add_action( 'wp_ajax_RemoveMemberGroup' ,array($this->groupManager,'remove_member_group_ajax'));
            add_action( 'wp_ajax_get_members_json', array($this->groupManager,'get_members_json'));
            add_action( 'admin_post_process_downloadgroup', array( $this->groupManager, 'process_download_group' ) );
            
            //Send SMS
            add_action( 'wp_ajax_uws_queue_message', array($this->sendSms,'queue_message') );
            //Settings
            add_action( 'wp_ajax_uws_save_setting' ,array($this->settings,'save_setting'));
            add_action( 'wp_ajax_uws_after_save_setting' ,array($this->settings,'get_setting_left_html'));
            //Help
            add_action('wp_ajax_get_log_file',array($this->help,'get_log_file'));
            add_action('wp_ajax_clear_log_file',array($this->help,'clear_log_file'));            
        }
    }
    public function uws_show_modal(){
        
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-modal-ajax-check' ) ) {
            wp_send_json(array('error'=>'unauthorize access'),401);
        }
        if(!isset($_POST['type'])){
            wp_send_json(array('error'=>'unauthorize access'),422);
        }
        $type = sanitize_text_field($_POST['type']);
        $class_name = apply_filters('uws_ajax_filter','UWS\\LITE\\SMS\\Admin\\Template\\Partials\\Modal\\'.$type,$type);
        if(class_exists($class_name)){
            ob_start();
            new $class_name($_POST);
            $html = ob_get_clean();
            ob_end_clean();
            wp_send_json(array('html'=>$html));
        } else {
            wp_send_json(array('error'=>'Unknown Request'),422);
        }
    }
    public function show_alert_form(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-modal-ajax-check' ) ) {
            wp_send_json(array('success'=>false,'msg'=>'Unauthorize Access'),401);
        }
        ob_start();
        new \UWS\LITE\SMS\Admin\Template\Modal\DeleteConfirm($_POST);
        $html = ob_get_clean();
        ob_end_clean();
        wp_send_json(array('success'=>true,'html'=>$html));
    }
}
