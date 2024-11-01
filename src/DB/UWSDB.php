<?php
namespace UWS\LITE\SMS\DB;
/**
 * The core class, where logic is defined.
 */
class UWSDB {
    protected $db,$updateclause,$uws;
    public $db_tables;
    public function __construct()
    {
        global $wpdb,$uws;
        $this->db = $wpdb;
        $this->uws = $uws;
        $this->updateclause = " ON UPDATE CURRENT_TIMESTAMP";
        $this->db_tables = array(
            'uws-group-members'=>new UWSGroupMembers(),
            'uws-groups'=>new UWSGroups(),
            'uws-members'=>new UWSMembers(),
            'uws-message-queue'=>new UWSMessageQueue(),
        );
    }
    public function setup_db(){
        foreach($this->db_tables as $table):
            $table->create_table();
        endforeach;
        $this->run_migration();
    }
    public function run_migration(){
        $old_table = $this->db->get_results("SHOW TABLES LIKE '%jot_%'",ARRAY_N);
        if(!empty($old_table) && !empty($old_table[0])){
            $old_table = array_column($old_table, '0');
        }
        $version = get_option('uws-version',true);
        foreach($this->db_tables as $table):
            if(!empty($old_table)):
                $table->migrate($old_table);
            endif;
            if(method_exists($table,'alter_table')):
                $table->alter_table($version);
            endif;
        endforeach;
        $setting_data = get_option('jot-plugin-smsprovider',true);
        $uws_fields = array(
                    'general'=>array(
                            'smscountrycode'=>'uws-country-code',
                            'enable-debugging'=>'uws-enable-debug',
                    ),
        );
        if(!empty($setting_data) && is_array($setting_data)){
            foreach($setting_data as $setting_key => $value){
                $key =str_replace('jot-','',$setting_key);
                foreach($uws_fields as $uws_key => $fields){
                    if(isset($fields[$key])){
                        if($value == 'true'){
                            $value = 'yes';
                        }
                        $data[$uws_key][$fields[$key]]=$value;
                    }
                }
            }
            foreach($data as $option_key => $option_value){
                update_option( 'uws-'.$option_key,$option_value);
            }
        }
    }
}