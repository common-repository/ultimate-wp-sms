<?php
namespace UWS\LITE\SMS\DB;
/**
 * The core class, where logic is defined.
 */
class UWSGroups extends UWSDB{
    const COLS = array(
        'groupid',
        'groupname',
        'groupdesc',
        'groupoptout',
        'groupopttxt',
        'groupallowdups',
        'groupautosub',
        'ts',
    );
    protected $table_name,$db;
    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = $this->db->prefix.'uws_groups';
    }
    protected function create_table(){
        $structure = "CREATE TABLE IF NOT EXISTS ".$this->table_name." (
            groupid        INT(9) NOT NULL AUTO_INCREMENT,
            groupname      VARCHAR(40) NOT NULL,
            groupdesc      VARCHAR(60) NOT NULL,
            groupoptout    VARCHAR(20) NOT NULL,
            groupopttxt    VARCHAR(160) NOT NULL,
            groupallowdups BOOLEAN DEFAULT 0,
            groupautosub   BOOLEAN DEFAULT 0,
            ts             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY groupid (groupid)
        )
            CHARACTER SET utf8 
            COLLATE utf8_unicode_ci
        ;";
        \dbDelta($structure);
    }
    protected function migrate($old_table){
        $old_table_name = str_replace('uws','jot',$this->table_name);
        if(in_array($old_table_name,$old_table)){
            $data_count = $this->db->get_var("SELECT count(*) from ".$this->table_name. " LIMIT 1");
            $old_data_count = $this->db->get_var("SELECT count(*) from ".$old_table_name. " LIMIT 1");
            if(!$data_count){
                if($old_data_count){
                    $this->db->query("INSERT INTO ".$this->table_name." (".implode(",",self::COLS).")
                    SELECT jot_".implode(",jot_",self::COLS)."
                    FROM $old_table_name");
                } else {
                    $this->db->query("INSERT INTO ".$this->table_name." (".implode(",",self::COLS).") VALUES (1,'Default Group','Default Group','','',0,0,'')");
                }
            }
        } else {
            $data_count = $this->db->get_var("SELECT count(*) from ".$this->table_name. " LIMIT 1");
            if(!$data_count){
                $this->db->query("INSERT INTO ".$this->table_name." (".implode(",",self::COLS).") VALUES (1,'Default Group','Default Group','','',0,0,'')");
            }
        }
    }
    protected function alter_table($vesion){
        $data_count = $this->db->get_var("SELECT count(*) from ".$this->table_name. " LIMIT 1");
        if(!$data_count){
            $this->db->query("INSERT INTO ".$this->table_name." (".implode(",",self::COLS).") VALUES (1,'Default Group','Default Group','','',0,0,'')");
        }
    }
}