<?php
namespace UWS\LITE\SMS\Gateways;
/**
* Joy_Of_Text Twilio. Class for Twilio API functions
*
*/
//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Exception;
use Twilio\{
    Rest\Client,
    Exceptions\TwilioException,
};
use UWS\LITE\SMS\Admin\{
    Settings,
};
class UWSTwilio extends UWS{
 
    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/
    public $id,
        $number_detail;
    private $settings,
        $settings_class,
        $uws,
        $twilio,
        $number,
        $webhook_url,
        $error;
    /**
     * Initializes the plugin 
     */
    function __construct() {
        global $uws;
        $this->id = 'twilio';
        $this->uws = $uws;
        
        $this->webhook_url = get_site_url(null,'/uws-webhook/twilio/', 'https');
        $this->init();
    } // end constructor
    
    public function init(){
            add_filter('uws_settings_tabs',array($this,'twilio_settings_tab'),10,1);
            add_filter( 'uws_settings_fields_twilio', array($this,'twilio_settings_fields'),10,2 );
            $this->settings_class = new Settings();

            $this->settings = get_option('uws-gateway-'.$this->id);
            $this->setup_clinet();
            $this->getPhoneNumbers();
    }

    public function twilio_settings_tab($tabs){
        $tabs['gateway']['twilio']='Twilio';
        if(!empty($this->error)){
            $icon = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.64487 1.262L0.181868 16.708C0.0608026 16.931 0.000260357 17.1819 0.00626648 17.4356C0.0122726 17.6893 0.0846176 17.937 0.216101 18.154C0.347585 18.3711 0.533621 18.5499 0.755694 18.6727C0.977767 18.7955 1.22813 18.858 1.48187 18.854H18.4089C18.6623 18.8571 18.9121 18.7941 19.1337 18.671C19.3552 18.548 19.5408 18.3693 19.6721 18.1525C19.8034 17.9357 19.8759 17.6885 19.8823 17.4351C19.8888 17.1818 19.829 16.9311 19.7089 16.708L11.2349 1.262C11.106 1.03174 10.9181 0.840003 10.6905 0.706543C10.4628 0.573084 10.2037 0.502729 9.93987 0.502729C9.676 0.502729 9.41691 0.573084 9.18928 0.706543C8.96166 0.840003 8.77373 1.03174 8.64487 1.262ZM10.7809 7.671L10.6089 13.501H9.27087L9.09887 7.671H10.7809ZM9.93987 16.671C9.76733 16.6645 9.60051 16.6074 9.46018 16.5068C9.31985 16.4063 9.21221 16.2666 9.15067 16.1053C9.08913 15.944 9.0764 15.7681 9.11407 15.5996C9.15174 15.4311 9.23815 15.2774 9.36253 15.1577C9.48691 15.0379 9.64377 14.9574 9.81358 14.9262C9.98338 14.8949 10.1586 14.9143 10.3175 14.9819C10.4764 15.0495 10.6118 15.1624 10.707 15.3064C10.8022 15.4505 10.8529 15.6193 10.8529 15.792C10.8521 15.9102 10.8278 16.0271 10.7812 16.1358C10.7347 16.2445 10.6669 16.3429 10.5819 16.425C10.4968 16.5072 10.3962 16.5715 10.286 16.6143C10.1757 16.657 10.0581 16.6773 9.93987 16.674V16.671Z" fill="#FF9900"/>
                    </svg>';
            $tabs['gateway']['label'] = $tabs['gateway']['label'].$icon;
            $tabs['gateway']['twilio'] = 'Twilio'.$icon;
        }
        return $tabs;
    }
    public function twilio_settings_fields($fields,$values){
        
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
                'label'=>'Twilio Basic Settings',
                'fields'=>array(
                    array(
                        'label'=>'Twilio Account SID',
                        'helper_text'=>'Enter your Account SID number that you received from Twilio.',
                        'type'=>'input',
                        'value'=>$values['uws-twilio-sid']??"",
                        'name'=>'uws-twilio-sid',
                        'id'=>'uws-twilio-sid',
                    ),
                    array(
                        'label'=>'Twilio Auth Token',
                        'helper_text'=>'Enter your Auth token that you received from Twilio.',
                        'type'=>'input',
                        'value'=>$values['uws-twilio-auth']??"",
                        'name'=>'uws-twilio-auth',
                        'id'=>'uws-twilio-auth',
                    ),
                    array(
                        'label'=>'Phone Numbers',
                        'helper_text'=>'Select the Twilio number you wish to send your SMS messages from.',
                        'type'=>'select',
                        'value'=>$values['uws-twilio-number']??"",
                        'name'=>'uws-twilio-number',
                        'id'=>'uws-twilio-number',
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
	    $number = $this->uws->parse_phone_number($number); 
        if ( $countrycode == "") {
            $countrycode = $this->settings_class->settings['general']['uws-country-code']??"US";
        }     
        try{
            $phone_number = $this->twilio->lookups->v1->phoneNumbers($number)->fetch(["CountryCode" => $countrycode]);
            return $phone_number->phoneNumber;
        } catch (\Exception $e){
            return "";
        }      
    }
    
    private function setup_clinet(){
        $this->twilio = null;
        if(!empty($this->settings) && !empty($this->settings['uws-twilio-sid']) && !empty($this->settings['uws-twilio-auth'])){
            try{
                $this->twilio = new Client($this->settings['uws-twilio-sid'], $this->settings['uws-twilio-auth']);
                $this->get_balance();
            } catch ( \Exception $e) {
                add_action( 'admin_notices', function(){
                    $class = 'notice notice-error';
                    $message = __( ' Unable to connect with Twillio. Please check you account details and status.', 'ultimate-wp-sms' );
                
                    printf( '<div class="%1$s"><strong>Ultimate Wp SMS</strong><p>%2$s</p><a href="admin.php?page=uws-settings">Check UWS Settings</a></div>', esc_attr( $class ), esc_html( $message ) ); 
                } );
                $this->error[] = "Twillio Connection error".$e->getMessage();
                $this->twilio = null;
            }
        } else {
            //$this->error[] = "Missing Twillio Values";
        }
    }
    private function getPhoneNumbers() {
        $this->number = array(""=> __("Select a number","ultimate-wp-sms-pro"));
        if($this->twilio){
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
        try {
            $incomingPhoneNumbers = $this->twilio->incomingPhoneNumbers->read([], 20);
        } catch ( \Exception $e){
            //Exception goes here
            $incomingPhoneNumbers = [];
        }
        if(!empty($incomingPhoneNumbers) && is_array($incomingPhoneNumbers)):
            foreach($incomingPhoneNumbers as $incomingPhoneNumber):
                $this->number[$incomingPhoneNumber->phoneNumber] = $incomingPhoneNumber->friendlyName;
                $this->number_detail[] = $incomingPhoneNumber;
            endforeach;
        endif;
    }
    /*
    *
    * Get array of short codes associated with this account.
    *
    */
    private function getShortCodeNumbers() {
        
        try {
            $shortCodes = $this->twilio->shortCodes->read([], 20);
        } catch ( \Exception $e){
            //Exception goes here
            $shortCodes = [];
        }
        if(!empty($shortCodes) && is_array($shortCodes)):
            foreach($shortCodes as $shortCode):
                $this->number[$shortCode->shortCode] = $shortCode->shortCode;
            endforeach;
        endif;
    }
    
    public function send_smsmessage($tonumber, $message, $senderid="", $alt_fromnumber = "") {
        $param = array("body"=>$this->uws->remove_unresolved_tags($message));
        if($this->settings['uws-enable-messaging-service']=="yes" && !empty($this->settings['uws-messaging-service'])){
            $param['MessagingServiceSid'] = $this->settings['uws-messaging-service'];
            $senderid = "Messaging Services";
            $success_message = __('SMS message sent to Twilio successfully. Check Message History for delivery status.','ultimate-wp-sms-pro');
        } else {
            $senderid = !empty($senderid)?$senderid:$alt_fromnumber;
            if(!empty($senderid)){
                $param['from'] = $senderid;
            } else {
                $param['from'] = $this->settings['uws-twilio-number'];
                $senderid = $this->settings['uws-twilio-number'];
                $success_message = __('SMS message sent to Twilio successfully.','ultimate-wp-sms-pro');
            }
        }
        $param['statusCallback'] = $this->webhook_url;
        $param = apply_filters('uws_before_sending_sms', $param);
        if($this->twilio){
            try{
                $message = $this->twilio->messages->create($tonumber, $param);
                do_action('uws_after_sms_send_success',$tonumber, $message, $senderid, $alt_fromnumber, $param, $message);
                return array('send_message_type'=>'SMS','send_message_from_number'=>$senderid,'send_message_number'=>$tonumber,'send_message_content' => stripcslashes($param['body']),'send_message_media'=>'','send_message_errorcode'=>"", 'send_message_msg'=> $success_message,'send_details'=>$message);
            } catch (TwilioException $e){
                do_action('uws_after_sms_send_failure',$tonumber, $message, $senderid, $alt_fromnumber, $param, $e);
                return array('send_message_type'=>'SMS','send_message_from_number'=>$senderid,'send_message_number'=>$tonumber,'send_message_content' => stripcslashes($param['body']),'send_message_media'=>'','send_message_errorcode'=>$e->getCode(), 'send_message_msg'=> $e->getMessage(),'send_details'=>"");
            }
        } else {
            return array('send_message_type'=>'SMS','send_message_from_number'=>$senderid,'send_message_number'=>$tonumber,'send_message_content' => stripcslashes($param['body']),'send_message_media'=>'','send_message_errorcode'=>20003, 'send_message_msg'=> 'Permission Denied','send_details'=>"");
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
    * Get account balance from Twilio
    */
    public function get_balance() {
            $sid = $this->settings['uws-twilio-sid'];                  
            try {
                $url = "https://api.twilio.com/2010-04-01/Accounts/$sid/Balance.json";
                $response = wp_remote_get($url );
                $response = json_decode(wp_remote_retrieve_body( $response ));
                if(isset($response->balance)){
                    $balance = $response->currency.' '.$response->balance;
                } else {
                    $balance = '0.00';
                }
                
            }  catch (Exception $e) {
                $balance = '0.00';
            }
            $settings = get_option('uws-gateway-configuration');
            $settings['uws-gateway-credit'] = $balance;
            update_option('uws-gateway-configuration',$settings);
            return $balance;
    }
    
    
} // end class