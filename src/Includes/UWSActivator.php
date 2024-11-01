<?php
namespace UWS\LITE\SMS\Includes;
use UWS\LITE\SMS\DB\UWSDB;
/**
 * The core class, where logic is defined.
 */
class UWSActivator {
    public static function activate(){
        self::add_cap();
        self::setup_db();
    }
    /**
        * Adding new capability in the plugin
    */
    public static function add_cap()
    {
        // Get administrator role
        $role = get_role('administrator');
        $role->add_cap('uws_sendsms');
        $role->add_cap('uws_group');
        $role->add_cap('uws_setting');
    }
    public static function setup_db(){
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $UWSDB = new UWSDB();
        $UWSDB->setup_db();
    }  
}
