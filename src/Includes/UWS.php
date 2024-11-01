<?php
namespace UWS\LITE\SMS\Includes;
/**
 * UWS
 *
 * Loads global functions to plublic access
 *
 * @version 2.3.0
 * @package UWS\LITE\SMS\includes
 */
defined( 'ABSPATH' ) || exit;
use UWS\LITE\SMS\Admin\{
    GroupManager,
    Settings,    
};
class UWS {
    private $settings,
    $json_path,
    $group_manager;
    public $name,
        $filter_uws,
        $version,
        $file_path,
        $smsGateway,
        $smsGatewayList,
        $smsGatewayId,
        $setting,
        $debug,
        $groups,
        $notification;
    public function __construct(string $name, string $version, string $file_path)
    {
        $this->name      = $name;
		$this->version   = $version;
		$this->file_path = dirname($file_path);
        $this->settings = new Settings();
        $this->debug = $this->settings->settings['general']['uws-enable-debug']??false;
        $this->smsGatewayId = $this->settings->settings['gateway']['configuration']['uws-gateway']??"";//'uws_twillio';
        $this->group_manager = new GroupManager;
        $this->json_path = $this->file_path."/assets/json/";
        $uws_cron = new UWSCron($this->name,$this->version,$this->file_path);
        add_filter('cron_schedules', array($uws_cron,'uws_interval'));
        add_action('uws_queue_sweeper', array($uws_cron,'uws_run_queue_sweeper') );
        if(isset($this->settings->settings['gateway']) && isset($this->settings->settings['gateway']['configuration']) && isset($this->settings->settings['gateway']['configuration']['uws-top-bar-balance']) && $this->settings->settings['gateway']['configuration']['uws-top-bar-balance']=='yes'){
            add_action('admin_bar_menu', array($this,'uws_balance_top_bar'), 100,1);
        }
    }
    function uws_balance_top_bar($admin_bar) {
        $balance = $this->settings->settings['gateway']['configuration']['uws-gateway-credit'];
        $admin_bar->add_menu(array(
          'id'    => 'uws-sms-gateway-balance',
          'title' => 'UWS Credits: '.$balance
        ));
    }
    
    public function init(){
        
        $instance = new UWSGateways();
        new UWSShortcode();
        $this->smsGatewayList = $instance->uws_sms_gateways;
        if(!empty($this->smsGatewayId)){
            $this->smsGateway = $instance->load_gateways($this->smsGatewayId);
        }
    }

    public function emoji_to_unicode($emoji) {
        $emoji = mb_convert_encoding($emoji, 'UTF-32', 'UTF-8');
        $unicode = strtoupper(preg_replace("/^[0]+/","U+",bin2hex($emoji)));
        return $unicode;
    }
    public function get_message_type_description($message_type) {
	    
	    switch ( $message_type ) {
                    case 'c'; // A call
                        $typestr = __('Call','uws');
                    break;
                    case 'S'; // An SMS
                        $typestr = __('SMS','uws');
                    break;
                    case 'M'; // An MMS
                        $typestr = __('MMS','uws');
                    break;
                    default:
                        $typestr = __('Not found','uws');
                    break;
                }
		return $typestr;
	}
    public function get_uws_template($template_name, $args = array(), $template_path = 'ultimate-wp-sms', $default_path = '')
    {
        if ($args && is_array($args)) {
            extract($args);
        }
        include($this->locate_uws_template($template_name, $template_path, $default_path));
    }
    /**
     * Get template part (for templates in loops).
     *
     * @param string $slug
     * @param string $name (default: '')
     * @param string $template_path (default: 'ultimate-wp-sms')
     * @param string|bool $default_path (default: '') False to not load a default
     */
    function get_uws_template_part($slug, $name = '', $args=[], $template_path = 'ultimate-wp-sms', $default_path = '')
    {
        $template = '';
        if ($name) {
            $template = $this->locate_uws_template("{$slug}-{$name}.php", $template_path, $default_path);
        }
        // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/ultimate-wp-sms/slug.php
        if (!$template) {
            $template = $this->locate_uws_template("{$slug}.php", $template_path, $default_path);
        }
        if ($template) {
            load_template($template, false,$args);
        }
    }
    private function locate_uws_template($template_name, $template_path = 'ultimate-wp-sms', $default_path = '')
    {
        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
                $template_name
            )
        );
        // Get default template
        if (!$template && $default_path !== false) {
            $default_path = $default_path ? $default_path : UWS_PATH . 'src/Templates/';
            if (file_exists(trailingslashit($default_path) . $template_name)) {
                $template = trailingslashit($default_path) . $template_name;
            }
        }
        // Return what we found
        return apply_filters('uws_locate_template', $template, $template_name, $template_path);
    }
    
    public function unsubscribe_notifications($number, $group_id){
        $args = array(
            'number' =>$number,
            'group_id' => $group_id
        );
        $this->notification->admin_notification('unsubscribe',$args);
        $this->notification->member_notification('unsubscribe',$args);
    }
    
    public function remove_unresolved_tags($message) {	     
	    
	    preg_match_all('/%[a-zA-Z_]*\%/s', $message, $matches);
	   	    
	    foreach($matches[0] as $match) {
		$message = str_ireplace($match, "", $message);		
	    }
	    
	    return $message;	     
    }

    public function replace_member_tag($args,$text){
        $from = $args['From'];
        $body = $args['Body'];
        $to = $args['To'];
        $member = false;
        if(!empty($from)){
            $member = $this->group_manager->get_member_details_by_number($from);
        }
        if($member){
            $name = $member->grpmemname;
            $number = $member->grpmemnum;
            $text = $this->get_replace_tags($text,$member);
        } else {
            $name = $number = $from;
        }
        $text = str_ireplace('%name%',$name,$text);
        $text = str_ireplace('%number%',$number,$text);
        $text = str_ireplace('%message%',$body,$text);
        return $text;
    }
    public function get_replace_tags($message,$member) {
	                                   
        // Merge tags for names
        if (isset($member->grpmemname)) {
            // Remove (WP) or other membership plugin suffix
            $fullname = preg_replace( '~\(.*\)~' , "", $member->grpmemname);
            $message = str_ireplace('%name%',$fullname,$message);
            $message = str_ireplace('%fullname%',$fullname,$message);
            $nameparts = $this->split_name($fullname);
            $message = str_ireplace('%firstname%',$nameparts['firstname'], $message);
            $message = str_ireplace('%lastname%',$nameparts['lastname'], $message);
        }
        
        // Merge tags for phone number
        if (isset($member->grpmemname)) {
            $message = str_ireplace('%number%',$member->grpmemnum, $message);
        }
        
        // Merge tags for last WP post
        //$message = str_ireplace('%lastpost%',$this->get_last_post(), $message);
            
        // Merge tags for extended member info
        if (isset($member->grpmememail)) $message = str_ireplace('%email%',$member->grpmememail, $message);
        if (isset($member->grpmememail)) $message = str_ireplace('%emailaddress%',$member->grpmememail, $message);
        if (isset($member->grpmemaddress)) $message = str_ireplace('%address%',$member->grpmemaddress, $message);
        if (isset($member->grpmemcity)) $message = str_ireplace('%city%',$member->grpmemcity, $message);
        if (isset($member->grpmemstate)) $message = str_ireplace('%state%',$member->grpmemstate, $message);
        if (isset($member->grpmemzip)) $message = str_ireplace('%zip%',$member->grpmemzip, $message);
        
        // Date tags
        $message = str_ireplace('%day%',date("l"), $message);
        
        // Strip non-UTF tags
        $message = $this->strip_non_utf8($message);
                      
        // Replace subscription manager merge tag
        // $subcommand = Joy_Of_Text_Plugin()->settings->get_smsprovider_settings('jot-inbmanagesubs');
        // $message = str_ireplace('%submgr%',$subcommand, $message);
        // $message = str_ireplace('%jot_submgr%',$subcommand, $message);
            
        // // If groupid is set then replace group details
        // if ($groupid != null) {
        //              // Merge tags for group info.
        //              $groupdetails = Joy_Of_Text_Plugin()->settings->get_group_details($groupid);
                     
        //              //if (Joy_Of_Text_Plugin()->debug) Joy_Of_Text_Plugin()->messenger->log_to_file(__METHOD__,"Group details" . print_r($groupdetails,true) );
                     
        //              if ($groupdetails) {
        //                 $message = str_ireplace('%jot_groupid%',$groupid, $message);
        //                 $message = str_ireplace('%jot_groupname%',$groupdetails->jot_groupname, $message);
        //                 $message = str_ireplace('%jot_groupdesc%',$groupdetails->jot_groupdesc, $message);
        //              }
                     
        //              // Replace optout
        //              $groupinvite = Joy_Of_Text_Plugin()->options->get_groupinvite($groupid);
        //              $jot_groupoptout = isset($groupinvite->jot_groupoptout) ? $groupinvite->jot_groupoptout : "";
        //              $message = str_ireplace('%optout%',$jot_groupoptout, $message);
        //              $message = str_ireplace('%opt_out%',$jot_groupoptout, $message);
        //              $message = str_ireplace('%jot_optout%',$jot_groupoptout, $message);             
        // }
        if (strlen($message) > 640) {
            $message = mb_substr($message, 0, 640);
        }
        $message = str_ireplace("%da", "=da", $message); // see https://github.com/Yoast/wordpress-seo/issues/9790
        $message = implode(
            "\n",
            array_map(
                "sanitize_text_field",
                explode("\n", $message)
            )
        );
        $message = str_ireplace("=da", "%da", $message);
        return apply_filters('uws_get_replace_tags',$message);    
    }

    private function strip_non_utf8($message)
    {
        //Remove non-UTF8 characters
        //reject overly long 2 byte sequences, as well as characters above U+10000 and replace with no-string
        $message = preg_replace(
            '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
                '|[\x00-\x7F][\x80-\xBF]+' .
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            "",
            $message
        );
        //reject overly long 3 byte sequences and UTF-16 surrogates and replace with no-string
        $message = preg_replace(
            '/\xE0[\x80-\x9F][\x80-\xBF]' . '|\xED[\xA0-\xBF][\x80-\xBF]/S',
            "",
            $message
        );
        return $message;
    }

    private function split_name($name)  {
        $name = trim($name);
        if (strpos($name, ' ') === false) {
            return array('firstname' => $name, 'lastname' => '');                
        } else {
            $parts     = explode(" ", $name);
            $firstname = reset($parts);                 
            array_shift($parts);
            $lastname = implode(" ", $parts);
            return array('firstname' => $firstname, 'lastname' => $lastname);
        }    
    }

    public function add_member_to_group($number,$grp_id,$name=null){
        $name = $name??$number;
        return $this->group_manager->add_member_number($number,$name, $grp_id);
    }
    public function remove_member($member_id,$group_id=""){
        return $this->group_manager->remove_member($member_id,$group_id);
    }

    public function get_member_details_by_number($number){
        return $this->group_manager->get_member_details_by_number($number);
    }
    public function get_country_codes() {
        $country_codes = json_decode(file_get_contents($this->json_path."country_codes.json"),true);
        return apply_filters('uws_country_codes',$country_codes);
    }
    private function get_country_phone_codes($country_code="") {
        $country_phone_codes = json_decode(file_get_contents($this->json_path."country_phone_codes.json"),true);
        if(!empty($country_code)){
            return apply_filters('uws_country_phone_code',$country_phone_codes[$country_code],$country_code);
        }
        return apply_filters('uws_country_phone_codes',$country_phone_codes);
    }
    private function get_country_currency($currency_code) {
        $country_currency = json_decode(file_get_contents($this->json_path."country_currency.json"),true);
        if(!empty($currency_code)){
            return apply_filters('uws_country_currency',$country_currency[$currency_code],$currency_code);
        }
        return apply_filters('uws_country_currencies',$country_currency);
    }
    public function parse_phone_number($number,$country_code=null) {
        $number = str_replace(' ', '', $number);
        $number = str_replace('(', '', $number);
        $number = str_replace(')', '', $number);
        $number = str_replace('-', '', $number);
        $number = str_replace('.', '', $number);
        $number = str_replace('<', '', $number);
        $number = str_replace('>', '', $number);
        $number = str_replace('&', '', $number);
        $number = str_replace('"', '', $number);
        $number = str_replace("'", '', $number);
        $country_code = $country_code??$this->settings->settings['general']['uws-country-code'];
        if(!empty($country_code)){
            $country_phone_code = $this->get_country_phone_codes($country_code);
            if(isset($country_phone_code['dial_code'])){
                if (!preg_match('/^\+/', $number)) {
                    $number = $country_phone_code['dial_code'].substr($number,'-'.$country_phone_code['length']);
                }
            }
            if(strlen($number)<$country_phone_code['length']){
                return false;
            }
        }
        $sanitized_number = sanitize_text_field($number);
        return apply_filters('uws_parse_phone_number',$sanitized_number);
    }
    public function log_to_file($method, $text = "") {
        
        $selected_provider = $this->smsGatewayId;
        $date = date('Y-m-d');
        $file = $this->file_path. "/logs/UWS-$selected_provider-$date.log";
       
        // log message info to a file
        if(!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);            
        }       
        file_put_contents($file, "==" . date('h:i:s a', time()) . "||" . $method . "||" . $text . "\r\n"  ,FILE_APPEND);
    }
}