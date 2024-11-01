<?php
namespace UWS\LITE\SMS\DB;
/**
 * The core class, where logic is defined.
 */
class UWSMembers extends UWSDB{
    const COLS = array(
        'grpmemid',
        'grpmemname',
        'grpmemnum',
        'grpmemstatus',
        'grpmememail',
        'grpmemaddress',
        'grpmemcity',
        'grpmemstate',
        'grpmemzip',
        'grpmemts',
    );
    protected $table_name,$db;
    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = $this->db->prefix.'uws_groupmembers';
    }
    
    protected function create_table(){
        global $uws;
        $structure = "CREATE TABLE IF NOT EXISTS ".$this->table_name." (
            grpmemid      BIGINT(20) NOT NULL AUTO_INCREMENT,			    
            grpmemname    VARCHAR(40) NOT NULL,
            grpmemnum     VARCHAR(40) NOT NULL,
            grpmemstatus  INT(2) NOT NULL,
            grpmememail   VARCHAR(90) NOT NULL,
            grpmemaddress VARCHAR(240) NOT NULL,
            grpmemcity    VARCHAR(40) NOT NULL,
            grpmemstate   VARCHAR(40) NOT NULL,
            grpmemzip     VARCHAR(20) NOT NULL,                           
            grpmemts      TIMESTAMP DEFAULT CURRENT_TIMESTAMP".$this->updateclause.",
            UNIQUE KEY grpmemid (grpmemid)
        )
            CHARACTER SET utf8 
            COLLATE utf8_unicode_ci
        ;";
        \dbDelta($structure);
        $index_grpmemnum =  $this->table_name . "_indx_grpmemnum";
        $index_val = $this->db->get_var("SHOW INDEX FROM " . $this->table_name . " WHERE KEY_NAME = '" . $index_grpmemnum .  "'");
        if ( $index_val != $this->table_name) {
            $structure = "CREATE INDEX " . $index_grpmemnum  . " ON $this->table_name (grpmemnum);";
            $return = $this->db->query($structure);
            $uws->log_to_file(__METHOD__,"Run query for " . $index_grpmemnum . " Index. Return : " . $return );                           
        }
    }
    protected function migrate($old_table){
        $old_table_name = str_replace('uws','jot',$this->table_name);
        if(in_array($old_table_name,$old_table)){
            $data_count = $this->db->get_var("SELECT count(*) from ".$this->table_name. " LIMIT 1");
            if(!$data_count){
                $this->db->query("INSERT INTO ".$this->table_name." (".implode(",",self::COLS).")
                SELECT jot_".implode(",jot_",self::COLS)."
                FROM $old_table_name");
            }
        }
    }
}