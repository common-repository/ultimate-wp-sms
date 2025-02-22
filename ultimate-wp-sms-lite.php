<?php
/**
 *  * This code was generated by
 *                   _____
 * |    |   |    |   |   
 * |    |   |    | 	 |____
 * |    |   | /\ |       |
 * |____|   |/  \|   ____|
 * 
* Plugin Name: Ultimate Wp SMS Lite
* Plugin URI: https://ultimatewpsms.com/
* Description: SMS, MMS and text-to-voice messaging. Connect with your customers, subscribers, followers, members and friends.
* Version: 3.2.1
* Stable tag: 3.2.1
* Author: Phillip Dane
* Author URI: https://ultimatewptech.com/
* WP tested up to: 6.6.1
* Requires at least: 4.1
* Requires PHP: 7.2
**/

namespace UWS\LITE\SMS;
use \UWS\LITE\SMS\Core;

global $UWS;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

if ( class_exists( Core::class ) ) {

	/**
	 * Initialize plugin
	 */

    defined( 'UWS_PATH' ) || define( 'UWS_PATH', plugin_dir_path( __FILE__ ) );
    defined( 'UWS_FILE' ) || define( 'UWS_FILE', plugin_basename( __FILE__ ) );
    defined( 'UWS_URL' ) || define( 'UWS_URL', plugins_url( '/assets/', __FILE__ ) );
	define('UWS_VERSION', '3.2.1');
    /**
	 * add divi cutom form integration 
	*/

 	$plugin = new Core( 'ultimate-wp-sms', '3.2.1', __FILE__ );
 	$plugin->activation();
	add_action( 'init', [$plugin, 'init'] );
}