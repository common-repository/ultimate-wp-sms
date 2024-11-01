<?php 
namespace UWS\LITE\SMS\Admin;
use UWS\LITE\SMS\Admin\Template\{
    SendSmsTemplate
};
class SendSms{
    private $error,
        $uws,
        $db,
        $message_queue_table,
        $duplicate_count,
        $invalid_count,
        $manual_reciptants;
    public $data,$reciptants;
    public function __construct(){
        global $wpdb;
        global $uws;
        $this->uws = $uws;
        $this->db = $wpdb;
        $this->message_queue_table = $this->db->prefix."uws_messagequeue";
    }
    public function render(){
        $args = array('page'=>'send_sms');
        new SendSmsTemplate($args);
    }
    public function assets(){
        wp_enqueue_script('uws-send-sms', UWS_URL . 'js/uws-send-sms.js', true, UWS_VERSION);
        wp_localize_script('uws-send-sms', 'uws_ss', array('ajax' => admin_url("admin-ajax.php"),'securty_check'=>wp_create_nonce('uws-ss-ajax-check')));
    }
    public function queue_message(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-ss-ajax-check' ) ) {
            wp_send_json(array('success'=>false,'msg'=>'unauthorize access'),401);
        }
        $this->sanitize_input($_POST);
        $this->get_reciptant_list();
        $this->validate_send_message();
        if($this->error){
            wp_send_json(array('success'=>false,'msg'=>implode(',',$this->error)),422);
        }
        
        $data = $this->setup_message_queue();
        
        $message = apply_filters("uws_queue_response_message",$data['message'],$this->data,$this->reciptants);
        wp_send_json(array('success'=>true,'batch_id'=>$data['batchid'],'remaining_messages'=>0,'msg'=>$message));
    }
    public function setup_message_queue(){
        $message_type = "S";
        $media_id = "";

        $this->data['uws-sender'] = "";
        if ($this->uws->debug) $this->uws->log_to_file(__METHOD__, "reciptants : " . print_r($this->reciptants,true));
        $batchid = uniqid(rand(), false);
        $mess_status = count($this->reciptants)>40?'D':'Q'; //Q = Quick send, D= Dripped chunk of messages
        update_option('uws_last_text',$this->data['uws-message']);
        $this->data['uws-message'] = $this->data['uws-message'];
        $mess_status = apply_filters('uws_message_status',$mess_status,$this->data);
        $mess_sched_ts = apply_filters('uws_message_sent_time','2000-01-01 00:00:01',$this->data);
        do_action('uws_before_queue_loop');
        foreach($this->reciptants as $reciptants){
            $message = $this->uws->get_replace_tags($this->data['uws-message'],$reciptants['extra_data']);
            $batch = array(
                'messqbatchid'    => $batchid,
                'messqgrpid'      => (int) isset($reciptants['extra_data']->grpid)?$reciptants['extra_data']->grpid:"",
                'messqmemid'      => (int) isset($reciptants['extra_data']->grpmemid)?$reciptants['extra_data']->grpmemid:"",
                'messqcontent'    => $message,
                'messqtype'       => $message_type,
                'messqfromnumber' => $this->data['uws-sender'],
                'messqaudio'      => $media_id,
                'messqstatus'     => $mess_status,
                'messsenderid'    => "",
                'messqschedts'    => $mess_sched_ts,
                'messqts'         => current_time('mysql', 0)                
            );
            
            apply_filters('before_insert_message_queue',$batch);
            $this->db->insert( $this->message_queue_table, $batch );
            if ($this->db->last_error) {
                // Log the last query error
                print_r("MySQL Error: " . $this->db->last_error);
            }
        }
        do_action('uws_after_queue_loop',$batchid,$this->data,$this->reciptants);
        if($mess_status != 'S' && count($this->reciptants) <= 40){
            $process_message = array('uws-batchid'=>$batchid,'uws-process-maxbatchsize'=>40);
            $this->process_queue($process_message);
        }
        $message = "Message sent to all the reciptants. Total Message sent ".count($this->reciptants).". Duplicate reciptants removed:".count($this->duplicate_count);
        if($mess_status == 'D'){
            //$this->uws->save_batchauditmeta($batchid,'uws_sent_by_wpuser',json_encode(wp_get_current_user()->data));
            $message = "Messages are added in queue, You can check the status from the Batch History. Total Message queued ".count($this->reciptants).". Duplicate reciptants removed:".count($this->duplicate_count);
        }
        return array('message'=>$message,'batchid'=>$batchid);
    }
    public function process_queue($args = array()){
        global $uws;
        $this->uws = $uws;
        $batchid = $args['uws-batchid']??"";
        $process_type  = $args['uws-process-type']?? 'Q';
        $maxbatchsize  = (int)($args['uws-process-maxbatchsize']??5);
        $where = "";
        if(!empty($batchid)){
            $where = " AND messqbatchid = '$batchid'";
        }
        $where = apply_filters( 'uws_process_queue_where', $where, $args );
        $query = " SELECT  messqid, messqbatchid, messqgrpid, messqmemid, messqcontent,messqtype,messqstatus,messqaudio,messsenderid, messqfromnumber FROM $this->message_queue_table WHERE messqstatus = %s $where ORDER BY messqts, messqid  LIMIT  %d";
        $sql = $this->db->prepare($query, $process_type, $maxbatchsize);
        if ($this->uws->debug) $this->uws->log_to_file(__METHOD__, "sql : " . $sql);
        $batchlist = $this->db->get_results( $sql ); 
        if (count($batchlist) > 0) {
            $group_manager = new GroupManager();
            foreach ($batchlist as $queueitem) {      
                if(empty($batchid)){
                    $batchid = $queueitem->messqbatchid;
                }
                do_action('uws_message_loop_start',$queueitem);
                
                $this->update_queue_status($queueitem->messqid,'X');
                $remaining_messages = $this->count_queue_batch($queueitem->messqbatchid, "X");
                
                $member = $group_manager->get_member_details($queueitem->messqmemid);
                if ($this->uws->debug) $this->uws->log_to_file(__METHOD__, "member : " . print_r($member,true));
                if (!empty($member)) {
                    $senderid = $queueitem->messsenderid??"";
                    $message = stripslashes_deep($queueitem->messqcontent??"");

                    $message_error = $this->uws->smsGateway->send_smsmessage($member->grpmemnum,$message,$senderid,$queueitem->messqfromnumber);
                    
                    do_action('uws_after_message_send_queue',$queueitem,$message_error);
                    if ($this->uws->debug) $this->uws->log_to_file(__METHOD__, "message_response : " . print_r($message_error,true));
                }
                if($remaining_messages ==0){
                    do_action('uws_after_batch_complete',$batchid);
                }
            }
        }
    }
    private function update_queue_status($mess_id,$status){
        $data = array(
            'messqstatus'   => $status                             
        );
        $this->db->update( $this->message_queue_table, $data, array( 'messqid' =>  $mess_id ) );
    }
    public function count_queue_batch($batchid, $status) {
        $query = " SELECT count(*) as messcount FROM $this->message_queue_table WHERE messqstatus <> %s  AND messqbatchid = %s";
        
        $sql = $this->db->prepare($query, $status, $batchid);
        
        $batchcount = $this->db->get_var( $sql );  
                
        $remaining_count = $batchcount ?? -1;
                     
        return apply_filters('uws_count_queue_batch',$remaining_count,$batchid,$status);  
    }
    private function sanitize_input($data){
        
        foreach($data as $name => $input){
            if(is_string($input)){
                $input = str_ireplace("%da", "=da", $input); // %da being removed by sanitize_text_field, see https://github.com/Yoast/wordpress-seo/issues/9790
                $input = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $input ) ) );
                $this->data[$name] = str_ireplace("=da", "%da", $input);
            } else {
                $this->data[$name] = $input;
            }
        }
    }
    private function validate_send_message(){
        if(empty($this->reciptants)){
            $this->error[] = 'No Recipient added/selected';
        }
        if(empty($this->data['uws-message']) && $this->data['uws-message-type'] == 'uws-sms'){
            $this->error[] = 'Cannot send blank message';
        }
    }
    private function get_reciptant_list(){
        $group_manager = new GroupManager();
        $this->reciptants = [];
        $this->duplicate_count = [];
        $this->manual_reciptants = [];
        $this->invalid_count = 0;
        if(!empty($this->data['uws-reciptant-custom-number'])){
            if (strpos($this->data['uws-reciptant-custom-number'], ',') !== false) {
                $uws_reciptant_custom_numbers = explode(',',$this->data['uws-reciptant-custom-number']);
            } else {
                $uws_reciptant_custom_numbers = explode("\n",$this->data['uws-reciptant-custom-number']);
            }
            if(!empty($uws_reciptant_custom_numbers) && is_array($uws_reciptant_custom_numbers)){
                foreach($uws_reciptant_custom_numbers as $uws_reciptant_number){
                    $uws_reciptant_number = $group_manager->validate_number($uws_reciptant_number);
                    if($uws_reciptant_number){
                        $member_details = $group_manager->get_member_details_by_number($uws_reciptant_number);
                        if(!$member_details){
                            // Insert into members table
                            $member_details =array('grpmemname'=>$uws_reciptant_number,'grpmemnum'=>$uws_reciptant_number);
                            $this->db->insert( $group_manager->members_table ,  $member_details);
                            $member_details = $group_manager->get_member_details_by_number($uws_reciptant_number);
                        } 
                        if($this->check_dups($uws_reciptant_number)){
                            $this->reciptants[] = array('name'=>$member_details->grpmemname??"guest",'number'=>$uws_reciptant_number,'extra_data'=>$member_details);
                            $this->manual_reciptants[] = $uws_reciptant_number;
                        } else {
                            $this->duplicate_count[] = $member_details->grpmemnum;
                        }
                    } else {
                        $this->invalid_count++;
                    }
                }
            }
        }
        if(!empty($this->data['uws-reciptant-number']) && is_array($this->data['uws-reciptant-number'])){
            $uws_reciptant_numbers = $this->data['uws-reciptant-number'];
            foreach($uws_reciptant_numbers as $uws_reciptant_number){
                $uws_reciptant_number = $group_manager->validate_number($uws_reciptant_number);
                if($uws_reciptant_number){
                    $member_details = $group_manager->get_member_details_by_number($uws_reciptant_number);
                    if(!$member_details){
                        // Insert into members table
                        $member_details =array('grpmemname'=>$uws_reciptant_number,'grpmemnum'=>$uws_reciptant_number);
                        $this->db->insert( $group_manager->members_table ,  $member_details);
                        $member_details = $group_manager->get_member_details_by_number($uws_reciptant_number);
                    } 
                    if($this->check_dups($uws_reciptant_number)){
                        $this->reciptants[] = array('name'=>$member_details->grpmemname??"guest",'number'=>$uws_reciptant_number,'extra_data'=>$member_details);
                        $this->manual_reciptants[] = $uws_reciptant_number;
                    } else {
                        $this->duplicate_count[] = $member_details->grpmemnum;
                    }
                } else {
                    $this->invalid_count++;
                }
            }
        }
    }
    public function get_member_by_number(){
        $group_manager = new GroupManager();
        $name = $group_manager->get_member_name_by_number($_POST['number_to_find']);
        wp_send_json(array('success'=>true,'name'=>$name));
    }
    public function check_dups($number){
        
        if(!array_search($number, array_column($this->reciptants, 'number'))){
            return true;
        }
        return false;
    }
}