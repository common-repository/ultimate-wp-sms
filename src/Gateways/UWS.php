<?php
namespace UWS\LITE\SMS\Gateways;
/**
* Joy_Of_Text Twilio. Class for Twilio API functions
*
*/
//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
abstract class UWS {
    abstract public function send_smsmessage($tonumber, $message, $senderid="", $alt_fromnumber = "");
    //abstract public function add_provider_fields( $settings_fields,$section );
}