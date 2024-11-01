<?php
namespace UWS\LITE\SMS\Gateways;
/**
* Ultimate WP SMS Twilio. Class for Twilio API functions
*
*/
//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Exception;
use UWS\LITE\SMS\Admin\{
    Settings
};
class UWSTelnyx extends UWS{
 
    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/
    public $id,
        $number_detail;
    private $settings,
        $settings_class,
        $uws,
        $telnyx,
        $number,
        $api_url,
        $error;
    /**
     * Initializes the plugin 
     */
    function __construct() {
        global $uws;
        $this->id = 'telnyx';
        $this->uws = $uws;
        $this->api_url = "https://api.telnyx.com/v2/";
        $this->init();
    } // end constructor
    
    public function init(){
        add_filter('uws_settings_tabs',array($this,'settings_tab'),10,1);
        add_filter( 'uws_settings_fields_telnyx', array($this,'settings_fields'),10,2 );
        $this->settings_class = new Settings();

        $this->settings = get_option('uws-gateway-'.$this->id);
        $this->setup_clinet();
        $this->getPhoneNumbers();
    }

    public function settings_tab($tabs){
        $tabs['gateway']['telnyx']='Telnyx';
        if(!empty($this->error)){
            $icon = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.64487 1.262L0.181868 16.708C0.0608026 16.931 0.000260357 17.1819 0.00626648 17.4356C0.0122726 17.6893 0.0846176 17.937 0.216101 18.154C0.347585 18.3711 0.533621 18.5499 0.755694 18.6727C0.977767 18.7955 1.22813 18.858 1.48187 18.854H18.4089C18.6623 18.8571 18.9121 18.7941 19.1337 18.671C19.3552 18.548 19.5408 18.3693 19.6721 18.1525C19.8034 17.9357 19.8759 17.6885 19.8823 17.4351C19.8888 17.1818 19.829 16.9311 19.7089 16.708L11.2349 1.262C11.106 1.03174 10.9181 0.840003 10.6905 0.706543C10.4628 0.573084 10.2037 0.502729 9.93987 0.502729C9.676 0.502729 9.41691 0.573084 9.18928 0.706543C8.96166 0.840003 8.77373 1.03174 8.64487 1.262ZM10.7809 7.671L10.6089 13.501H9.27087L9.09887 7.671H10.7809ZM9.93987 16.671C9.76733 16.6645 9.60051 16.6074 9.46018 16.5068C9.31985 16.4063 9.21221 16.2666 9.15067 16.1053C9.08913 15.944 9.0764 15.7681 9.11407 15.5996C9.15174 15.4311 9.23815 15.2774 9.36253 15.1577C9.48691 15.0379 9.64377 14.9574 9.81358 14.9262C9.98338 14.8949 10.1586 14.9143 10.3175 14.9819C10.4764 15.0495 10.6118 15.1624 10.707 15.3064C10.8022 15.4505 10.8529 15.6193 10.8529 15.792C10.8521 15.9102 10.8278 16.0271 10.7812 16.1358C10.7347 16.2445 10.6669 16.3429 10.5819 16.425C10.4968 16.5072 10.3962 16.5715 10.286 16.6143C10.1757 16.657 10.0581 16.6773 9.93987 16.674V16.671Z" fill="#FF9900"/>
                    </svg>';
            $tabs['gateway']['label'] = $tabs['gateway']['label'].$icon;
            $tabs['gateway']['telnyx'] = 'Telnyx'.$icon;
        }
        return $tabs;
    }
    public function settings_fields($fields,$values){
        $error_fields = array();
        if($this->error){
            $error_fields = array(
                'label'=>'Fix Folowing Error',
                'fields'=> array(
                    array(
                        'label'=>'',
                        'helper_text'=>'',
                        'type'=>'error',
                        'errors'=>$this->error
                    )
                )
            );
        }
        $fields = array(
            'error'=>$error_fields,
            'basic'=>array(
                'label'=>'Telnyx Basic Settings',
                'fields'=>array(
                    array(
                        'label'=>'API Key',
                        'helper_text'=>'Enter your Account API Key that you received from Telnyx.',
                        'type'=>'input',
                        'value'=>$values['uws-telnyx-api']??"",
                        'name'=>'uws-telnyx-api',
                        'id'=>'uws-telnyx-api',
                    ),
                    array(
                        'label'=>'Public Key',
                        'helper_text'=>'Enter your Public Key that you received from Telnyx.',
                        'type'=>'input',
                        'value'=>$values['uws-telnyx-public']??"",
                        'name'=>'uws-telnyx-public',
                        'id'=>'uws-telnyx-public',
                    ),
                    array(
                        'label'=>'Phone Numbers',
                        'helper_text'=>'Select the Telnyx number you wish to send your SMS messages from.',
                        'type'=>'select',
                        'value'=>$values['uws-telnyx-number']??"",
                        'name'=>'uws-telnyx-number',
                        'id'=>'uws-telnyx-number',
                        'options'=>$this->number
                    ),
                )
            ),
        );
       
        //$this->validate($fields);
        return $fields;
    }
    
    public function verify_number($number, $countrycode = "") {
      
        if (empty($number)) {
            return "";
        }
       
        // Strip out formatting characters like brackets
        if ( $countrycode == "") {
            $countrycode = $this->settings_class->settings['general']['uws-country-code']??"US";
        }    
        if ($this->uws->debug) $this->uws->log_to_file(__METHOD__,"parse phone : ".$number ." countrycode >". $countrycode);
	    $number = $this->uws->parse_phone_number($number,$countrycode); 
        
        if($this->telnyx){
            $query = array(
                "type" => "caller-name"
              );
            if ($this->uws->debug) $this->uws->log_to_file(__METHOD__,"verify number : ".$number ." query >". print_r($query,true). " type >".print_r($this->telnyx,true) );
            $response = wp_remote_get( $this->api_url."number_lookup/".$number."?". build_query($query),$this->telnyx );
            $body     = json_decode(wp_remote_retrieve_body( $response ),true);
            if(!empty($body['data'])){
                return $body['data']['phone_number'];
            }
        }
        return "";     
    }
    
    private function setup_clinet(){
        $this->telnyx = null;
        //print_r($this->settings);
        if(!empty($this->settings) && !empty($this->settings['uws-telnyx-api'])){
            //die('here');
            try{
                $this->telnyx = array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $this->settings['uws-telnyx-api']
                    )
                );
                $this->get_balance();
            } catch ( \Exception $e) {
                add_action( 'admin_notices', function(){
                    $class = 'notice notice-error';
                    $message = __( ' Unable to connect with Telnyx. Please check you account details and status.', 'ultimate-wp-sms' );
                
                    printf( '<div class="%1$s"><strong>Ultimate Wp SMS</strong><p>%2$s</p><a href="admin.php?page=uws-settings">Check UWS Settings</a></div>', esc_attr( $class ), esc_html( $message ) ); 
                } );
                $this->error[] = "Telnyx Connection error".$e->getMessage();
                $this->telnyx = null;
            }
        } else {
            //$this->error[] = "Missing Telnyx Keys";
        }
    }
    private function getPhoneNumbers() {
        $this->number = array(""=> __("Select a number","ultimate-wp-sms-pro"));
        if($this->telnyx){
            $this->getLongCodeNumbers();               
            $this->getShortCodeNumbers();
        } else {
            $this->number = array(''=>"--No Connection--");
        }
    }
    /*
    *
    * Get long code numbers and Twilio connection status
    *
    */
    private function getLongCodeNumbers() {
        if($this->telnyx){
            $query = array(
                "page[number]" => "1",
                "page[size]" => "20",
            );
            
            $response = wp_remote_get( $this->api_url."phone_numbers/messaging?" . build_query($query),$this->telnyx );
            $body     = json_decode(wp_remote_retrieve_body( $response ),true);
            if(!empty($body['data'])){
                foreach($body['data'] as $data){
                    $this->number[$data['phone_number']] = $data['phone_number'];
                    $this->number_detail[] = (object)array('phoneNumber'=>$data['phone_number'],'friendlyName'=>$data['phone_number']);
                }
            }
        }
    }
    /*
    *
    * Get array of short codes associated with this account.
    *
    */
    private function getShortCodeNumbers() {
        if($this->telnyx){
            $query = array(
                "page[number]" => "1",
                "page[size]" => "20",
            );
            $response = wp_remote_get( $this->api_url."short_codes?" . build_query($query),$this->telnyx );
            $body     = json_decode(wp_remote_retrieve_body( $response ),true);
            if(!empty($body['data'])){
                foreach($body['data'] as $data){
                    $this->number[$data['short_code']] = $data['short_code'];
                }
            }
        }
    }

    public function send_smsmessage($tonumber, $message, $senderid="", $alt_fromnumber = "") {
        $param = array(
            "text"=>$this->uws->remove_unresolved_tags($message),
            "to" => $tonumber,
            "type" => "SMS",
        );
        $success_message = "";

        $senderid = !empty($senderid)?$senderid:$alt_fromnumber;
        if(!empty($senderid)){
            $param['from'] = $senderid;
        } else {
            $param['from'] = $this->settings['uws-telnyx-number'];
            $senderid = $this->settings['uws-telnyx-number'];
        }
        $success_message = __('SMS message sent to Telnyx successfully.','ultimate-wp-sms-pro');
        $param = apply_filters('uws_before_sending_sms', $param);
        if($this->telnyx){
            if ($this->uws->debug) $this->uws->log_to_file(__METHOD__, "sms payload : " . print_r($param,true));
            $this->telnyx['headers']['Content-Type'] = 'application/json'; 
            $this->telnyx['body'] = wp_json_encode($param); 
            //print_r($this->telnyx);
            $response = wp_remote_post( $this->api_url."messages",$this->telnyx );
            $message_response     = json_decode(wp_remote_retrieve_body( $response ),true);
            //print_r($message_response);
            unset($this->telnyx['body']);
            if ($this->uws->debug) $this->uws->log_to_file(__METHOD__, "sms response : " . print_r($message_response,true));
            if(!empty($message_response['data'])){
                do_action('uws_after_sms_send_success',$tonumber, $message, $senderid, $alt_fromnumber, $param, $message_response['data']);
                return array('send_message_type'=>'SMS','send_message_from_number'=>$senderid,'send_message_number'=>$tonumber,'send_message_content' => stripcslashes($param['text']),'send_message_media'=>'','send_message_errorcode'=>"", 'send_message_msg'=> $success_message,'send_details'=>$message_response['data']);
            } else {
                do_action('uws_after_sms_send_failure',$tonumber, $message, $senderid, $alt_fromnumber, $param, $message_response['errors']);
                return array('send_message_type'=>'SMS','send_message_from_number'=>$senderid,'send_message_number'=>$tonumber,'send_message_content' => stripcslashes($param['text']),'send_message_media'=>'','send_message_errorcode'=>$message_response['errors'][0]['code'], 'send_message_msg'=> $message_response['errors'][0]['title'],'send_details'=>"");
            }
        } else {
            return array('send_message_type'=>'SMS','send_message_from_number'=>$senderid,'send_message_number'=>$tonumber,'send_message_content' => stripcslashes($param['text']),'send_message_media'=>'','send_message_errorcode'=>20003, 'send_message_msg'=> 'Permission Denied','send_details'=>"");
        }
    }
    
    
    public function xmlEscape($string) {
        return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
    }
    
    public function is_to_our_numbers($to) {       
        
        $numbers = wp_list_pluck($this->number_detail,'phoneNumber');
      
        if (!empty($numbers) && is_array($numbers) && in_array($to,$numbers)) {               
                return true;
        }            
        return false;
        
    }
    
    /**
    * Get account details from Twilio
    */
    public function getAccountDetails() {
        if($this->telnyx){
            $query = array(
                "type" => "caller-name"
              );
            $response = wp_remote_get( $this->api_url."number_lookup/".$number."?". build_query($query),$this->telnyx );
            $body     = json_decode(wp_remote_retrieve_body( $response ),true);
            if(!empty($body['data'])){
                return $body['data']['phone_number'];
            }
        }
    }
    
    /**
    * Get account balance from Telnyx
    */
    public function get_balance() {
        $balance =  "0.00";
        if($this->telnyx){
            $response = wp_remote_get( $this->api_url."balance",$this->telnyx);
            $body     = json_decode(wp_remote_retrieve_body( $response ),true);
            if(!empty($body['data'])){
                $balance = $body['data']['currency'].' '.$body['data']['balance'];
            }
        } 
        $settings = get_option('uws-gateway-configuration');
        $settings['uws-gateway-credit'] = $balance;
        update_option('uws-gateway-configuration',$settings);
        return $balance;
    }
    
} // end class