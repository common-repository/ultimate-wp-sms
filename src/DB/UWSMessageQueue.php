<?php
namespace UWS\LITE\SMS\DB;
/**
 * The core class, where logic is defined.
 */
class UWSMessageQueue extends UWSDB{
    const COLS = array(
        'messqid',
        'messqbatchid',
        'messqgrpid',
        'messqmemid',
        'messqcontent',
        'messqtype',
        'messqfromnumber',
        'messqstatus',
        'messqaudio',
        'messsenderid',
        'messqschedts',
        'messqts',
    );
    protected $table_name,$db;
    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = $this->db->prefix.'uws_messagequeue';
    }
    protected function create_table(){
        $structure = "CREATE TABLE IF NOT EXISTS ".$this->table_name." (
            messqid         INT(9) NOT NULL AUTO_INCREMENT,
            messqbatchid    VARCHAR(50) NOT NULL,
            messqgrpid      INT(9) NOT NULL,
            messqmemid      BIGINT(20) NOT NULL,
            messqcontent    VARCHAR(1600) NOT NULL,
            messqtype       CHAR(1) NOT NULL,
            messqfromnumber VARCHAR(40) NOT NULL,
            messqstatus     CHAR(1) NOT NULL,
            messqaudio      VARCHAR(2000) NOT NULL,
            messsenderid    VARCHAR(11) NOT NULL,
            messqschedts    TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
            messqts         TIMESTAMP NOT NULL,
            UNIQUE KEY messqid (messqid)
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
            if(!$data_count){
                $this->db->query("INSERT INTO ".$this->table_name." (".implode(",",self::COLS).")
                SELECT jot_".implode(",jot_",self::COLS)."
                FROM $old_table_name");
            }
        }
    }
}