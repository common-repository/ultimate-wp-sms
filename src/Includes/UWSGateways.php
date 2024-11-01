<?php
namespace UWS\LITE\SMS\Includes;
/**
 * NCS Payment Gateways
 *
 * Loads payment gateways via hooks for use in the store.
 *
 * @version 2.3.0
 * @package NCS\includes
 */
defined( 'ABSPATH' ) || exit;
class UWSGateways {
    /**
	 * Payment gateway classes.
	 *
	 * @var array
	 */
	public $uws_sms_gateways = array(),
	$load_gateways = array();
    /**
	 * Initialize payment gateways.
	 */
	public function __construct() {
		$this->init();
	}
    /**
	 * Load gateways and hook in functions.
	 */
	public function init() {
		$load_gateways = array(
			'twilio'=>'\UWS\LITE\SMS\Gateways\UWSTwilio',
			'telnyx'=>'\UWS\LITE\SMS\Gateways\UWSTelnyx',
		);
		$uws_sms_gateways = array(
			''=>'No Gateway Selected',
			'twilio'=>'Twilio',
			'telnyx'=>'Telnyx',
		);
		// Filter.
		$this->load_gateways = apply_filters( 'load_gateways', $load_gateways );
		$this->uws_sms_gateways = apply_filters( 'uws_sms_gateways_list', $uws_sms_gateways );
	}
	public function load_gateways($gateway_id){
		
		if(empty($gateway_id)){
			return null;
		}
		$gateway = null;
		// Load gateways in order.
		$gateway_class = $this->load_gateways[$gateway_id];
		if ( is_string( $gateway_class ) && class_exists( $gateway_class ) ) {
			$gateway = new $gateway_class();
		}
		// Gateways need to be valid and extend UWS.
		if (!$gateway || ! is_a( $gateway, 'UWS\LITE\SMS\Gateways\UWS' ) ) {
			return false;
		}
		
		return $gateway;
	}
}