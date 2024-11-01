<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Modal;
use UWS\LITE\SMS\Admin\GroupManager;
class RemoveMemberGroup{
   public function __construct($args){
      $this->html($args);
   }
    private function html($args){ 
      $group_manager = new GroupManager();
      $group_name = $group_manager->get_group_col_data($args['group_id'],'group_table.groupname','group_table.groupid');
      if(isset($args['member_id'])):
         $member_name = $group_manager->get_group_member_meta($args['member_id'],'members_table.grpmemname','members_table.grpmemid');?>
         <div class="modal-body">
            <p><?php _e( sprintf("Are you sure you want to Remove <strong>%s</strong> From <strong>%s</strong>",$member_name,$group_name),"ultimate-wp-sms"); ?>?</p>
         </div>
         <input type="hidden" name="member_id" value="<?php echo esc_attr($args['member_id']); ?>">
      <?php endif; ?>
      <?php if(isset($args['member_ids'])): ?>
         <div class="modal-body">
            <p><?php _e( sprintf("Are you sure you want to Remove Selected Members from <strong>%s</strong>",$group_name),"ultimate-wp-sms"); ?><?php echo esc_html($group_name); ?> </strong>?</p>
         </div>
         <input type="hidden" name="member_ids" value="<?php echo esc_attr($args['member_ids']); ?>">
      <?php endif; ?>
      <div class="modal-footer">
         <button class="btn outline_btn mr-10 remove_modal" type="button">Cancel</button>
         <input type="hidden" name="action" value="<?php echo esc_attr($args['next_action']); ?>">
         <input type="hidden" name="group_id" value="<?php echo esc_attr($args['group_id']); ?>">
         <button class="btn primary_btn red-700"><i class="icon"><img src="<?php echo esc_url(sprintf("%simages/gm/icon_delete_group.svg",UWS_URL)); ?>" alt="" /></i><?php esc_html_e( "Yes Proceed","ultimate-wp-sms"); ?></button>
      </div>
   <?php }
}