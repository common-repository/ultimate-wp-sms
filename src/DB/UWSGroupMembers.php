<?php
namespace UWS\LITE\SMS\DB;
/**
 * The core class, where logic is defined.
 */
class UWSGroupMembers extends UWSDB{
    const COLS = array(
        'grpid',
        'grpmemid',
        'grpxrefts',
    );
    protected $table_name,$db;
    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = $this->db->prefix.'uws_groupmemxref';
    }
    protected function create_table(){
        
        $structure = "CREATE TABLE IF NOT EXISTS ".$this->table_name." (
            grpid         INT(9) NOT NULL,
            grpmemid      BIGINT(20) NOT NULL,
            grpxrefts     TIMESTAMP DEFAULT CURRENT_TIMESTAMP".$this->updateclause.",
            UNIQUE KEY grpmemxref (grpid, grpmemid )
        )
            CHARACTER SET utf8 
            COLLATE utf8_unicode_ci;";
        \dbDelta($structure);
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