<?php 
namespace UWS\LITE\SMS\Admin;
use UWS\LITE\SMS\Admin\Template\{
    GroupManagerTemplate,
};
use UWS\LITE\SMS\Admin\Template\Partials\Cards\{
    GroupManagerCard,
    MemberListRow
};
use UWS\LITE\SMS\Admin\Template\Partials\{
    ScrollPaginate
};
class GroupManager{
    public $members_table,$group_member_ref_table;
    private $uws,
        $db,
        $group_table,
        $limit = null,
        $offset,
        $where,
        $error_code,
        $errors,
        $wp_settings;
    public function __construct(){
        global $wpdb;
        global $uws;
        $this->uws = $uws;
        $this->db = $wpdb;
        $this->group_table = $this->db->prefix."uws_groups"; //a
        $this->members_table = $this->db->prefix."uws_groupmembers"; //b
        $this->group_member_ref_table =  $this->db->prefix."uws_groupmemxref"; //c
        $this->errors = $this->error_code();
        $this->wp_settings = get_option('uws-integration-wp');
    }
    
    private function error_code(){
        return array(
            'member'=>array(
                '0'=>__( 'Member %s %s successfully.', 'ultimate-wp-sms-pro' ),
                '1'=>__("Missing Required Fields", "ultimate-wp-sms-pro"),
                '2'=> __("%s - The phone number is not numeric.", "ultimate-wp-sms-pro"),
                '3'=>__("%s - Phone number already exists in this group", "ultimate-wp-sms-pro"),
                '4'=>__("Unauthorized Access", "ultimate-wp-sms-pro"),
                '5'=>__("%s - number is not valid. Try again by adding your area code/country code. Ensure your Twilio credentials are configured.", "ultimate-wp-sms-pro"),
                '6'=>__("Could not save. A database error occurred.", "ultimate-wp-sms-pro"),
            )
        );
    }
    public function render(){
        $args = array('page'=>'group_manger','settings'=>$this->wp_settings);
        new GroupManagerTemplate($args);
    }
    public function assets(){
        wp_enqueue_media();
        wp_enqueue_script('uws-group-manager', UWS_URL . 'js/uws-group-manager.js', true, UWS_VERSION);
        wp_localize_script('uws-group-manager', 'uws_gm', array('ajax' => admin_url("admin-ajax.php"),'securty_check'=>wp_create_nonce('uws-gm-ajax-check')));
    }
    public function get_group_cards_ajax(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-gm-ajax-check' ) ) {
            wp_send_json(array('error'=>'unauthorize access'),401);
        }
        
        $groups_list = $this->get_groups();
        ob_start();
        if(count($groups_list)>0){
            foreach($groups_list as $group):
                new GroupManagerCard($group);
            endforeach;
        }
        new ScrollPaginate(array('page'=>"done"));
        $html = ob_get_clean();
        ob_end_clean();
        wp_send_json(array('html'=>$html ));
    }
    public function get_group_members_ajax(){
       
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-gm-ajax-check' ) ) {
            wp_send_json(array('error'=>'unauthorize access'),401);
        }
        
        $this->limit = 100;
        
        if(isset($_POST['per_page'])){
            $this->limit = $_POST['per_page'];
        }
        $page = $_POST['page']??1;
        $this->offset = $this->limit*($_POST['page']-1);
        if(strpos($_POST['id'],'virtual') === false){
            $group_members_list = $this->get_group_members($_POST['id'],$_POST['search']??"");
        } else {
            $group_members_list = $this->get_virtual_member($_POST['id'],$_POST['search']??"");
        }
        ob_start();
        if(count($group_members_list)>0){
            foreach($group_members_list as $member):
                new MemberListRow($member);
            endforeach;
            new ScrollPaginate(array('page'=>++$page));
        } else {
            new ScrollPaginate(array('page'=>"done"));
        }
        $html = ob_get_clean();
        ob_end_clean();
        wp_send_json(array('html'=>$html ));
    }
    public function add_edit_group_ajax(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-gm-ajax-check' ) ) {
            wp_send_json(array('error'=>'unauthorize access'),401);
        }
        $reset = false;
        $error = "Unable to update group info";
        $success = 'Successfully updated the group info!!';
        if(isset($_POST['id'])){
            $this->update_group_details($_POST,$_POST['id']);
        } 
        if($this->db->last_error !== '') :
            $this->db->print_error();
            wp_send_json(array('success'=>false,'msg'=>$error,'error_report'=>$this->db->last_error));
        endif;
        wp_send_json(array('success'=>true,'msg'=>$success ,'reset'=>$reset ));
    }
    public function add_edit_group_member_ajax(){
        $this->error_code = 0;
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-gm-ajax-check' ) ) {
            wp_send_json(array('error'=>'unauthorize access'),401);
        }
        $group_id = sanitize_text_field($_POST['group_id']);
        if(empty(trim($group_id))){
            $this->error_code = 1;
        }
        $old_member_id = (int)sanitize_text_field(trim($_POST['member_id']??0));
        unset($_POST['group_id'],$_POST['member_id']);
        $sanitizeed_input = $this->sanitize_member_feilds($_POST);
        if(!$this->error_code){
            $member_number = $this->validate_number($sanitizeed_input['grpmemnum'],$group_id);
            if(!$this->error_code){
                $sanitizeed_input['grpmemnum'] = $member_number;
                if(!$old_member_id){
                    $number_exsits = $this->is_group_member($member_number, $group_id);
                    if ($number_exsits) {
                        $this->error_code = 3;
                    }
                }
                if(!$this->error_code){
                    $member_id = $this->member_exsist($member_number);
                    if(!$member_id){
                        $sanitizeed_input['grpmemstatus'] = 1;
                        // Insert into members table
                        $this->db->insert( $this->members_table , $sanitizeed_input );
                        $member_id = $this->db->insert_id;
                        do_action('uws_add_member',$member_id);
                    }
                    if($old_member_id){
                        $where = array('grpmemid'=>$old_member_id);
                        $this->db->update( $this->members_table , $sanitizeed_input,$where );
                    } else {
                        $this->add_new_member_to_group($group_id,$member_id,$member_number);
                    }
                }
            }
        }
        if($this->error_code){
            wp_send_json(array('success'=>false,'msg'=>sprintf($this->errors['member'][$this->error_code],$sanitizeed_input['grpmemnum'])));
        } else {
            $action = !$old_member_id?"Added":"Updated";
            wp_send_json(array('success'=>true,'msg'=>sprintf($this->errors['member'][$this->error_code],$sanitizeed_input['grpmemname'],$action)));
        }
    }
    public function add_new_member_to_group($group_id,$member_id,$member_number,$added_by='admin'){
        $number_exsits = $this->is_group_member($member_number, $group_id);
        if(!$number_exsits):
            $data = array(
                'grpid'       => $group_id,
                'grpmemid'    => $member_id
            );
            $this->db->insert( $this->group_member_ref_table, $data );
            do_action('uws_add_member_group',$group_id,$member_id);
        endif;
        return $number_exsits;
    }
    
    public function process_download_group(){
        $grpid = (int) sanitize_text_field( $_GET['grpid']);
        if ( ! wp_verify_nonce( $_GET['_security'], 'uws-gm-download-check' ) ) {
            wp_send_json(array('success'=>false,'msg'=>'unauthorize access'),401);
        }
        if (!empty($grpid)) {
            $group_name = $this->get_group_col_data($grpid,'groupname','groupid');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="uws-'.$group_name.'-member.csv"');
            $fp = fopen('php://output', 'wb');
            fputcsv($fp, array('Name', 'Number', 'Email','Address','City','State','Zip'), ',');
            $members = $this->get_groups_members([$grpid]);
            foreach ($members as $member) {
                $data = array(
                   $member->grpmemname,
                   $member->grpmemnum,
                   $member->grpmememail,
                   $member->grpmemaddress,
                   $member->grpmemcity,
                   $member->grpmemstate,
                   $member->grpmemzip,
                );
                fputcsv($fp, $data, ',');
            }
            fclose($fp);
        }
        exit();
    }
    
    public function add_member_number($fromnumber, $name ="",  $group_id=""){
        $member_id = $this->member_exsist($fromnumber);
        $new_member_added = false;
        if(!$member_id){
            $new_member_added = true;
            $name = !empty($name)?$name:$fromnumber;
            $sanitizeed_input = array('grpmemstatus'=>1,'grpmemname'=>$name,'grpmemnum'=>$fromnumber);
            $sanitizeed_input['grpmemstatus'] = 1;
            // Insert into members table
            $this->db->insert( $this->members_table , $sanitizeed_input );
            $member_id = $this->db->insert_id;
            do_action('uws_add_member',$member_id);
        }
        if(!empty($group_id)){
            $number_exsits = $this->add_new_member_to_group($group_id,$member_id,$fromnumber,"keyword-subscribe");
        }
        return array('new_member_added'=>$new_member_added,'number_exsits'=>$number_exsits,'member_id'=>$member_id);
    }
    
    public function get_member_edit_form(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-gm-ajax-check' ) ) {
            wp_send_json(array('error'=>'unauthorize access'),401);
        }
        $member_id = sanitize_text_field($_POST['member_id']);
        if(empty(trim($member_id))){
            wp_send_json(array('success'=>false,'msg'=>'No Member found'));
        } else {
            $data = $this->get_member_details($member_id);
            $data->group_id = $_POST['group_id'];
            ob_start();
            new \UWS\LITE\SMS\Admin\Template\Modal\EditMember($data);
            $html = ob_get_clean();
            ob_end_clean();
            wp_send_json(array('success'=>true,'html'=>$html));
        }
    }

    public function get_all_members(){
        $query     = "SELECT members_table.grpmemid,members_table.grpmemname,members_table.grpmemnum FROM $this->members_table as members_table JOIN $this->group_member_ref_table as group_member_ref on group_member_ref.grpmemid = members_table.grpmemid WHERE group_member_ref.grpid = (SELECT max(groupid) FROM ".$this->group_table." ) ORDER BY members_table.`grpmemid` DESC";
        return $this->db->get_results( $query );
    }

    
    public function get_members_json(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-gm-ajax-check' ) ) {
            wp_send_json(array());
        }
        $search = sanitize_text_field($_POST['search']);
        if(empty($search) || strlen($search)<4){
            wp_send_json(array());
        }
        $query     = "SELECT members_table.grpmemid,members_table.grpmemname,members_table.grpmemnum FROM $this->members_table as members_table JOIN $this->group_member_ref_table as group_member_ref on group_member_ref.grpmemid = members_table.grpmemid WHERE 1=1 AND members_table.grpmemname like %s OR members_table.grpmemnum like %s GROUP BY members_table.grpmemid ORDER BY members_table.`grpmemid` DESC LIMIT 10";
        $sql = $this->db->prepare($query,'%'.$search.'%','%'.$search.'%');
        $member_data = $this->db->get_results( $sql );
        wp_send_json($member_data);
    }
    public function get_group_data($id){
        $query = " SELECT groupid,groupname,groupdesc,groupoptout,groupopttxt,groupautosub FROM ".$this->group_table." WHERE groupid=%d";
        $sql = $this->db->prepare($query,$id);
        return $this->db->get_row( $sql );
    }
    public function get_group_col_data($id,$col,$filter){
        $query = "SELECT $col FROM $this->group_table as group_table WHERE $filter=%d ";
        $sql = $this->db->prepare($query,$id);
        //die;
        return $this->db->get_var( $sql );
    }
    public function get_group_member_meta($id,$col,$filter){
        $query = "SELECT $col FROM $this->members_table as members_table JOIN $this->group_member_ref_table as group_member_ref on group_member_ref.grpmemid = members_table.grpmemid JOIN $this->group_table as group_table on group_table.groupid = group_member_ref.grpid WHERE $filter=%d ";
        $sql = $this->db->prepare($query,$id);
        //die;
        return $this->db->get_var( $sql );
    }
    public function get_member_details_by_number($phn_num){
        $query = "SELECT members_table.* FROM $this->members_table as members_table WHERE members_table.grpmemnum like %s LIMIT 1";
        $sql = $this->db->prepare($query,'%'.$phn_num.'%');
        return $this->db->get_row( $sql );
    }
    public function is_group_member($phn_num,$group_id){
        $query = "SELECT group_member_ref.grpmemid FROM $this->members_table as members_table JOIN $this->group_member_ref_table as group_member_ref on group_member_ref.grpmemid = members_table.grpmemid JOIN $this->group_table as group_table on group_table.groupid = group_member_ref.grpid WHERE members_table.grpmemnum like %s AND group_member_ref.grpid = %d GROUP By group_member_ref.grpmemid";
        $sql = $this->db->prepare($query,'%'.$phn_num.'%',$group_id);
        return $this->db->get_var( $sql )??false;
    }
    public function member_exsist($number){                
        $query = "SELECT grpmemid FROM $this->members_table WHERE grpmemnum = %s";                  
        $sql = $this->db->prepare( $query, $number);
        
        return $this->db->get_var( $sql )??false;
    }

    public function get_groups_members($ids = array()){
        $query = "SELECT members_table.*,group_member_ref.grpid FROM $this->members_table as members_table JOIN $this->group_member_ref_table as group_member_ref on group_member_ref.grpmemid = members_table.grpmemid WHERE group_member_ref.grpid IN (".implode(',',$ids).") ORDER BY members_table.`grpmemid` DESC";
        return $this->db->get_results( $query );
    }
    public function get_members_detail($ids = array()){
        $query = "SELECT members_table.*,GROUP_CONCAT(group_member_ref.grpid) as group_ids FROM $this->members_table as members_table JOIN $this->group_member_ref_table as group_member_ref on group_member_ref.grpmemid = members_table.grpmemid WHERE group_member_ref.grpmemid IN (".implode(',',$ids).") group by group_member_ref.grpid ORDER BY members_table.`grpmemid` DESC";
        return $this->db->get_results( $query );
    }
    public function get_groups_by_number($num){
        $query = "SELECT group_table.* FROM $this->group_table as group_table JOIN $this->group_member_ref_table as group_member_ref on group_member_ref.grpid = group_table.groupid JOIN $this->members_table as members_table on group_member_ref.grpmemid = members_table.grpmemid WHERE members_table.grpmemnum = %s ORDER BY members_table.`grpmemid` DESC";
        $sql = $this->db->prepare($query,$num);
        return $this->db->get_results( $sql );
    }
    
    public function remove_member_group_ajax(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-confirm-delete' ) ) {
            wp_send_json(array('success'=>false,'msg'=>'unauthorize access'),401);
        }
        if(isset($_POST['group_id'])){
            $group_id = sanitize_text_field($_POST['group_id']);
        }
        if(isset($_POST['member_id'])){
            $member_id = sanitize_text_field($_POST['member_id']);
        }
        if(isset($_POST['member_ids'])){
            $member_ids = sanitize_text_field($_POST['member_ids']);
            $member_ids = explode(',',$member_ids);
        }
        if(!empty($group_id) && is_numeric($group_id)){
            if(!empty($member_id) && is_numeric($member_id)){
                $this->remove_member($member_id,$group_id);
                wp_send_json(array('success'=>true,'msg'=>'Member Removed from group successfully'));
            }
            if(!empty($member_ids) && is_array($member_ids)){
                foreach($member_ids as $member_id):
                    $this->remove_member($member_id,$group_id);
                endforeach;
                wp_send_json(array('success'=>true,'msg'=>'Selected Members Removed from group successfully'));
            }
        }
        wp_send_json(array('success'=>false,'msg'=>'Invalid/Missing Data'),422);
    }
    
    public function bulk_member_remove_ajax(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-confirm-delete' ) ) {
            wp_send_json(array('success'=>false,'msg'=>'unauthorize access'),401);
        }
        if(isset($_POST['member_ids'])){
            $member_ids = sanitize_text_field($_POST['member_ids']);
        }
        if(!empty($member_ids)){ 
            $member_ids = explode(',',$member_ids);
            foreach($member_ids as $member_id):
                $this->remove_member($member_id);
                //$this->remove_member_history($member_id);
            endforeach;
            
            wp_send_json(array('success'=>true,'msg'=>'Member removed from all groups and message history deleted successfully!!'));
        }
        wp_send_json(array('success'=>false,'msg'=>'Invalid/Missing Data'),422);
    }
    
    public function remove_member($member_id,$group_id=""){
        if(empty($group_id)){
            $this->db->delete( $this->group_member_ref_table, array( 'grpmemid'=>$member_id ) );
            do_action('uws_remove_member_group_all',$member_id);
        } else {
            $this->db->delete( $this->group_member_ref_table, array( 'grpid' => $group_id,'grpmemid'=>$member_id ) );
            do_action('uws_remove_member_group',$member_id,$group_id);
        }
    }
    public function get_group_members($id,$filter=""){
        $this->where = " 1=1 ";
        if(!empty($filter)){
            $this->sql_member_where($filter);
        } 
        $sql     = "SELECT members_table.*,group_member_ref.grpid FROM $this->members_table as members_table JOIN $this->group_member_ref_table as group_member_ref on group_member_ref.grpmemid = members_table.grpmemid WHERE ".$this->where." AND group_member_ref.grpid={$id} ORDER BY members_table.`grpmemid` DESC";
        if($this->limit){
            $sql .= " LIMIT ".$this->offset.",".$this->limit;
        }
        //$sql = $this->db->prepare($query,$id);
        return $this->db->get_results( $sql );
    }
    
    public function get_member_details($id){
        $query = "SELECT * FROM $this->members_table WHERE grpmemid=%d";
        $sql = $this->db->prepare($query,$id);
        return $this->db->get_row( $sql );
    }
    
    private function get_groups(){
        // Exclude Wordpress users from the member count - they will be added if applicable in the Wordpress class.
	    $sql = " SELECT groupid,groupname,groupdesc, (select count(*) from $this->group_member_ref_table where grpid=groupid) as total_member FROM ".$this->group_table." ORDER BY groupid DESC LIMIT 1";
        //die('dfd');
        return $this->db->get_results( $sql );
    }

    private function sql_member_where($filter){
        
        if(!empty($filter)){
            $this->where .= " AND (LOWER(grpmemname) LIKE '%" . strtolower($filter) . "%' ";
            $this->where .= " OR LOWER(grpmemnum) LIKE '%" . strtolower($filter) . "%'  ";
            $this->where .= " OR LOWER(grpmememail) LIKE '%" . strtolower($filter) . "%'  ";
            $this->where .= " OR LOWER(grpmemaddress) LIKE '%" . strtolower($filter) . "%'  ";
            $this->where .= " OR LOWER(grpmemcity) LIKE '%" . strtolower($filter) . "%'  ";
            $this->where .= " OR LOWER(grpmemstate) LIKE '%" . strtolower($filter) . "%'  ";
            $this->where .= " OR LOWER(grpmemzip) LIKE '%" . strtolower($filter) . "%' ) ";
        }
    }

    private function update_group_details($data,$id){
        $new_input = $this->get_group_sanitized_data($data);
        $where = array( 'groupid' => $id );
        $this->db->update( $this->group_table , $new_input,$where);
    }

    private function get_group_sanitized_data($post_data){
        $columns = array(
            "grp_title"=>"groupname",
            "grp_desc"=>"groupdesc",
            "grp_auto_sub"=>"groupautosub",
            "grp_opt_out_keyword"=>"groupoptout",
            "grp_opt_out_text"=>"groupopttxt",
        );
        $data = [];
        foreach($columns as $input_key => $column){
            if(!empty($post_data[$input_key])){
                $data[$column] = sanitize_text_field($post_data[$input_key]);
            }
        }
        return $data;
    }
    private function sanitize_member_feilds($member_data){
        $columns = array(
            "member_name"=>array("db_col"=>"grpmemname","require"=>true),
            "member_number"=>array("db_col"=>"grpmemnum","require"=>true),
            "member_email"=>array("db_col"=>"grpmememail","require"=>false),
            "member_address"=>array("db_col"=>"grpmemaddress","require"=>false),
            "member_state"=>array("db_col"=>"grpmemcity","require"=>false),
            "member_city"=>array("db_col"=>"grpmemstate","require"=>false),
            "member_zip"=>array("db_col"=>"grpmemzip","require"=>false),
        );
        $data = [];
        foreach($columns as $input_key => $column){
            if(empty($member_data[$input_key]) && $column['require']){
                $this->error_code = 1;
            }
            if(!empty($member_data[$input_key])){
                $data[$column['db_col']] = sanitize_text_field(trim($member_data[$input_key]));
            }
        }
        return $data;
    }
    public function validate_number($member_number,$group_id=""){
        $member_number = $this->make_number($member_number);
        if(!$member_number){
            $this->error_code = 2;
        }
        if (!$this->error_code) {
            if(!empty($group_id)){
                
            }
            if($this->uws->smsGateway){
                $member_number = $this->uws->smsGateway->verify_number($member_number, "");           
                if ( $member_number == "") {
                    $this->error_code = 5;
                }
            }
        }
        
        return $member_number;
    }
    public function make_number($member_number){
        $removed_plus = false;
        $member_number = $this->uws->parse_phone_number( $member_number );
        // Does phone number start with a plus
        if (preg_match('/^\+/', $member_number)) {
            $member_number = substr($member_number,1);
            $removed_plus = true;
        } 
        if (!is_numeric($member_number)) {
            return false;
        }
        
        if ($removed_plus) {
            $member_number = "+" . $member_number;
        }
        return $member_number;
    }
}