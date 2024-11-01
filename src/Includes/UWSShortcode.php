<?php
namespace UWS\LITE\SMS\Includes;
/**
 * UWS
 *
 * Loads global functions to plublic access
 *
 * @version 2.3.0
 * @package UWS\SMS\includes
 */
defined( 'ABSPATH' ) || exit;
use UWS\LITE\SMS\Admin\{
    GroupManager,
};
class UWSShortcode {
    private $db,$uws,$group_manager,$settings;
    public function __construct(){
        global $uws,$wpdb;
        $this->uws = $uws;
        $this->db = $wpdb;
        $this->settings = get_option( 'uws-general');
        $this->group_manager = new GroupManager();
        $this->setup_shortcode();
        $this->setup_ajax_hooks();
        $this->assets();
    }
    private function setup_shortcode(){
        add_shortcode( 'uws_form', array($this,'uws_form_init') );
    }
    private function setup_ajax_hooks(){
        add_action( 'wp_ajax_uws_process_shortcode', array($this,'uws_process_shortcode') );
        add_action( 'wp_ajax_nopriv_uws_process_shortcode', array($this,'uws_process_shortcode') );
    }
    private function assets(){
        if(!is_admin()):
            wp_enqueue_style('uws-notie', UWS_URL . 'css/uws-notie.min.css', false, UWS_VERSION);
            wp_enqueue_style('uws-slimselect', UWS_URL . 'css/uws-slimselect.css', true, UWS_VERSION);
            wp_enqueue_style( 'uws-shortcode', UWS_URL . 'css/uws-shortcode.css', true, UWS_VERSION);
            //js
            wp_enqueue_script('uws-notie', UWS_URL . 'js/uws-notie.min.js', array( 'jquery' ), UWS_VERSION);
            wp_enqueue_script('uws-slimselect', UWS_URL . 'js/uws-slimselect.min.js', array( 'jquery' ), UWS_VERSION);
            wp_enqueue_script('uws-shortcode', UWS_URL . 'js/uws-shortcode.js', true, UWS_VERSION);
            wp_localize_script('uws-shortcode', 'uws_sc', array('ajax' => admin_url("admin-ajax.php"),'securty_check'=>wp_create_nonce('uws-sc-ajax-check')));
        endif;
    }
    public function uws_process_shortcode(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-sc-ajax-check' ) ) {
            wp_send_json(array('success'=>false,'msg'=>__( "Unauthorized Access.","ultimate-wp-sms")));
        }
        if(!isset($_POST['uws-form-special']) || !empty($_POST['uws-form-special'])){
            wp_send_json(array('success'=>false,'msg'=>__( "Spam Bot detected.","ultimate-wp-sms")));
        }
        $uws_type = sanitize_text_field($_POST['uws_form_id']);
        $data = [];
        $uws_type = $uws_type.'_form_process';
        $data = $this->sanitize_input($_POST);
        if(method_exists($this,$uws_type)):
            $this->$uws_type($data);
        endif; 
    }
    private function sanitize_input($data){
        foreach($data as $key => $value){
            if(is_array($value)){
                $data[$key] = $this->sanitize_input($value);
            } else {
                $data[$key] = sanitize_text_field($value);
            }
        }
        return $data;
    }
    private function invite_form_process($post_data){
        $error = $this->validate_invite_form($post_data);
        if($error['error']){
            $response = array('success'=>false,'msg'=>$error['message']);
        } else {
            $post_data['number'] = $error['number'];
            if(!isset($post_data['uws-confirm-groupid'])){
                $response = $this->add_subscriber($post_data);
            } else {
                $response = $this->send_confirmation($post_data);
            }
        }
        wp_send_json( $response  );
    }
    private function confirmation_form_process($post_data){
        if(empty($post_data['uws-verified-number']) || empty($post_data['uws-confirm-groupid']) || empty($post_data['uws-confirm-code'])){
            $response = array('success'=>false,'msg'=>__( "Missing Requeried fields.","ultimate-wp-sms"));
        } else {
            $error = $this->validate_invite_form($post_data);
            if($error['error']){
                $response = array('success'=>false,'msg'=>$error['message']);
            } else {
                $post_data['number'] = $error['number'];
                $response = $this->verify_code($post_data);
            }
        }
        wp_send_json( $response  );
    }
    private function contact_form_process($post_data){
        if(empty($post_data['uws-name']) || empty($post_data['uws-number']) || empty($post_data['uws-message'])){
            wp_send_json( array('success'=>false,'msg'=>__( "Missing Requeried fields.","ultimate-wp-sms")));
        }
        $number = $this->group_manager->validate_number($post_data['uws-number']);
        if(empty($number)){
            wp_send_json( array('success'=>false,"msg"=>__( "Please enter a valid number.", "ultimate-wp-sms" )));
        }
        $post_data['number'] = $number;
        if(isset($post_data['uws-group-id'])){
            $sanitizeed_input["grpmemname"]=$post_data['uws-sub-name']??$number;
            $sanitizeed_input["grpmemnum"] = $number;
            $sanitizeed_input['grpmemstatus'] = 1;
            // Insert into members table
            $this->db->insert( $this->group_manager->members_table , $sanitizeed_input );
            $member_id = $this->db->insert_id;
            do_action('uws_add_member',$member_id);
            $this->group_manager->add_new_member_to_group($post_data['uws-group-id'],$member_id,$number,'contact-form');
        }
        $admin_number = $this->settings['uws-admin-number']??"";
        if(!empty($admin_number)){
            $default_inbound_message = __("You have received a message from ","ultimate-wp-sms") . $number . ". " . __("The message was","ultimate-wp-sms") . " '" . $post_data['uws-message'];
            $this->uws->smsGateway->send_smsmessage($admin_number,$default_inbound_message);
            wp_send_json( array('success'=>true,"msg"=>__( "Thank you for your message. We will respond to you shortly.", "ultimate-wp-sms" ))  );
        } else {
            wp_send_json( array('success'=>false,"msg"=>__( "Missing Admin Configration.", "ultimate-wp-sms" ))  );
        }
    }
    private function opt_out_form_process($post_data){
        if(empty($post_data['uws-optout-number'])){
            wp_send_json( array('success'=>false,'msg'=>__( "Missing Requeried fields.","ultimate-wp-sms")));
        }
        $number = $this->group_manager->validate_number($post_data['uws-optout-number']);
        if(empty($number)){
            wp_send_json( array('success'=>false,"msg"=>__( "Please enter a valid number.", "ultimate-wp-sms" )));
        }
        $groups = $this->group_manager->get_groups_by_number($number);
        if(empty($groups)){
            wp_send_json( array('success'=>false,"msg"=>__( "You are not subscribed.", "ultimate-wp-sms" )));
        }
        $args = array(
            'number'=>$number,
            'uws-group-id'=>0,
            'uws-key-prefix'=>'uws_opt_out_'
        );
        $this->send_confirmation($args);
        wp_send_json( array(
            'success'=>true,
            "uws_verified_number"=>$number,
            "uws_group_id"=>0,
            "html"=>"",
            "btn_text"=>$post_data['uws-opt-out-next'],
            "msg"=>__( "Select the groups and enter confimation code sent on your mobile to opt out.", "ultimate-wp-sms" )
        ));
    }
    private function opt_out_confirmation_form_process($post_data){
        if(empty($post_data['uws-verified-number']) || empty($post_data['uws-confirm-code'])){
            wp_send_json( array('success'=>false,'msg'=>__( "Missing Requeried fields.","ultimate-wp-sms")));
        }
        $number = $this->group_manager->validate_number($post_data['uws-optout-number']);
        if(empty($number)){
            wp_send_json( array('success'=>true,"msg"=>__( "Please enter a valid number.", "ultimate-wp-sms" )));
        }
        if($post_data['uws-verified-number'] != $number){
            wp_send_json( array('success'=>false,'msg'=>__( "Number Mismatch.","ultimate-wp-sms")));
        }
        $key = "uws_opt_out_".$post_data['uws-verified-number'];
        $confirm_code_data = get_option($key);
        $confirm_code_data = (array)json_decode($confirm_code_data);
        if(!empty($confirm_code_data) && isset($confirm_code_data['cc']) && $confirm_code_data['cc'] == $post_data['uws-confirm-code']){
            delete_option($key);
            $member_id = $this->group_manager->member_exsist($number);
            $this->group_manager->remove_member($member_id);
        } else {
            wp_send_json( array('success'=>false,'msg'=>__( "Not able to verify confirmation code.","ultimate-wp-sms")));
        }
        wp_send_json( array('success'=>true,'msg'=>__( "You are optout from the selected groups.","ultimate-wp-sms")));
    }
    
    private function verify_code($post_data){
        if($post_data['uws-verified-number'] != $post_data['number']){
            $response = array('success'=>false,'msg'=>__( "Number Mismatch.","ultimate-wp-sms"));
        } else{
            $key = "uws_grp_conf_code_".$post_data['uws-verified-number'];
            $confirm_code_data = get_option($key);
            $confirm_code_data = (array)json_decode($confirm_code_data);
            if(!empty($confirm_code_data) && isset($confirm_code_data['cc']) && $confirm_code_data['cc'] == $post_data['uws-confirm-code']){
                delete_option($key);
                $response = $this->add_subscriber($post_data);
            } else {
                $response = array('success'=>false,'msg'=>__( "Not able to verify confirmation code.","ultimate-wp-sms"));
            }
        }
        return $response;   
    }
    private function add_subscriber($post_data){
        $member_number = $post_data['number'];
        $member_id = $this->group_manager->member_exsist($member_number);
        if(!$member_id){
            $sanitizeed_input["grpmemname"]=$post_data['uws-sub-name']??$member_number;
            $sanitizeed_input["grpmemnum"] = $member_number;
            $sanitizeed_input['grpmemstatus'] = 1;
            // Insert into members table
            $this->db->insert( $this->group_manager->members_table , $sanitizeed_input );
            $member_id = $this->db->insert_id;
            do_action('uws_add_member',$member_id);
        } else if(!empty($post_data['uws-sub-name'])){
            $sanitizeed_input["grpmemname"]=$post_data['uws-sub-name'];
            $this->db->update( $this->group_manager->members_table , $sanitizeed_input, array("grpmemid"=>$member_id) );
        }

        $sql = "SELECT groupid FROM ".$this->db->prefix."uws_groups ORDER BY groupid DESC LIMIT 1";
        $group_id = $this->db->get_var( $sql );
        $this->group_manager->add_new_member_to_group($group_id,$member_id,$member_number,'invite-form');
        return array('success'=>true,'redirect'=>true,'msg'=>__( "You are subscribed successfully.","ultimate-wp-sms"));
    }
    private function send_confirmation($post_data){
        $verified_number = $post_data['number'];
        $sql = "SELECT groupid FROM ".$this->db->prefix."uws_groups ORDER BY groupid DESC LIMIT 1";
        $group_id = $this->db->get_var( $sql );
        $confirm_code = rand ( 1000 , 9999 );
        $confirm_meta = array( 'cc' => $confirm_code,
                                'ts' => current_time('mysql', 0)
                            );
        $confirm_meta_json = json_encode($confirm_meta);
        $prifix = $post_data['uws-key-prefix']??"uws_grp_conf_code_";
        update_option( $prifix.$verified_number, $confirm_meta_json );
        $confirm_msg = sprintf(__("Your confirmation code is : %s","uws_pro"),$confirm_code);
        $this->uws->smsGateway->send_smsmessage($verified_number,$confirm_msg);
        return array('success'=>true,'uws_verified_number'=>$verified_number,'uws_group_id'=>$group_id,'btn_text'=>__( "Verify Code", "ultimate-wp-sms" ),'msg'=>__( "Confirmation code sent. Please enter code.","ultimate-wp-sms"));
    }
    private function validate_invite_form($post_data){
        if(!empty($post_data['uws-form-special'])){
            return array('error'=>true,"message"=>__( "Spam Bot detected.", "ultimate-wp-sms" ));
        }
        if (!isset($post_data['uws-sub-name']) || empty(trim($post_data['uws-sub-name']))) {
            return array('error'=>true,"message"=>__( "Please enter a valid name.", "ultimate-wp-sms" ));
        }
        $number = $this->group_manager->validate_number($post_data['uws-sub-number']);
        if(empty($number)){
            return array('error'=>true,"message"=>__( "Please enter a valid number.", "ultimate-wp-sms" ));
        }
        $sql = "SELECT groupid FROM ".$this->db->prefix."uws_groups ORDER BY groupid DESC LIMIT 1";
        $group_id = $this->db->get_var( $sql );
        if($this->group_manager->is_group_member($number,$group_id)){
            return array('error'=>true,"message"=>__( sprintf("Phone Number %s already subscribed to group.",$number), "ultimate-wp-sms" ));
        }
        return array('error'=> false,"number"=>$number);
    }
    public function uws_form_init($atts){
        
        $function = $atts['type'].'_form';
        foreach($atts as $atts_key => $att){
            $atts[$atts_key] = sanitize_text_field( $att );
        }
        if(method_exists($this,$function)):
            $form_html = $this->$function($atts);
        else:
            $form_html = sprintf("<div><p>%s</p></div>",__( "UWS Shortcode Error : Invalid Form Type.", "ultimate-wp-sms" ));
        endif;
        return apply_filters( 'uws_form_'.$atts['type'], $form_html);
    }
    private function subscribe_form($atts){
        $atts = shortcode_atts(
            array(
                'name'        => 'yes',
                'grpinvdesc'=>__( "Please subscribe for SMS updates", "ultimate-wp-sms" ),
                'grpinvnametxt'=> __( "Enter Your Name", "ultimate-wp-sms" ),
                'grpinvnumtxt'=> __( "Enter Your Number", "ultimate-wp-sms" ),
                'grpinvconfirm'=> '1',
                'grpgdprchk'=> 'yes',
                'grpgdprtxt'=> __( "This form collects phone number so that we can send you SMS notifications about our services.", "ultimate-wp-sms" ),
            ), $atts, 'uws_form' );
        ob_start();
            $this->uws->get_uws_template_part('invite-form','',$atts);
        return ob_get_clean();
    }
    private function contact_us_form($atts){
        $admin_number = $this->settings['uws-admin-number']??"";
        if(!empty($admin_number)){
            $atts = shortcode_atts(
                array(	
                    'open_button_text'   => __('Text Us',"ultimate-wp-sms"),
                    'title_text'         => __('Contact Us',"ultimate-wp-sms"),
                    'name_text'          => __('Your Name',"ultimate-wp-sms"),
                    'number_text'        => __('Your Number',"ultimate-wp-sms"),
                    'message_text'       => __('Message',"ultimate-wp-sms"),
                    'send_button_text'   => __('Send',"ultimate-wp-sms"),
                    'popup'              => 'no'
                ), $atts, 'uws_form' );
            ob_start();
                $this->uws->get_uws_template_part('contact-form','',$atts);
            return ob_get_clean();
        } else {
            return __('Missing Admin Configration!!',"ultimate-wp-sms");   
        }
    }
    
    private function opt_out_form($atts){
        $atts = shortcode_atts(
            array(	
                'title_text'   => __('Opt Out Form',"ultimate-wp-sms"),
                'enter_number_text'   => __('Enter your number',"ultimate-wp-sms"),
                'enter_number_button' => __("Unsubscribe","ultimate-wp-sms"),
                'current_groups_text' => __("Select the groups you want to unsubscribe from.","ultimate-wp-sms"),
                'unsubscribe_button'  => __("Unsubscribe","ultimate-wp-sms"),
            ), $atts, 'uws_form' );
        ob_start();
            $this->uws->get_uws_template_part('opt-out','',$atts);
        return ob_get_clean();
    }
}