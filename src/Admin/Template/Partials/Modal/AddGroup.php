<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Modal;
use UWS\LITE\SMS\Admin\GroupManager;
class AddGroup{
   public $html;
   public function __construct($args){
      $group_manager = new GroupManager();
      $group_data = (object) [];
      if($args['id']):
         $group_data = $group_manager->get_group_data($args['id']);
      endif;
      $this->html($group_data);
   }
    private function html($args){ ?>
      <div class="sidebar_inner_modal">
         <div class="sidebar_header">
            <div class="top_ui">
               <span class="close close-modal"><img src="<?php echo esc_url(sprintf("%simages/gm/modal_close.svg",UWS_URL)); ?>" alt="" /></span>
               <h4><i class="icon"><img src="<?php echo esc_url(sprintf("%simages/gm/icon_new_group.svg",UWS_URL)); ?>" alt="" /></i>New Group</h4>
            </div>
            <div class="desc">
               <p>( <?php esc_html_e(sprintf("Group ID:%s",$args->groupid??""),"ultimate-wp-sms"); ?>)</p>
            </div>
            <div class="btn_wrapper">
               <button class="btn primary_btn" id="addnewgroup">
                  <i class="icon">
                     <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.413 3.47902L12.433 0.499024C12.2749 0.340322 12.0869 0.214447 11.8799 0.128639C11.673 0.0428311 11.4511 -0.00121801 11.227 -0.000975567H1.70001C1.47626 -0.000976961 1.25471 0.0431928 1.04806 0.129001C0.841414 0.214809 0.653741 0.340567 0.495803 0.499062C0.337865 0.657558 0.212769 0.845673 0.127689 1.05262C0.0426099 1.25957 -0.000779127 1.48127 1.05892e-05 1.70502V14.205C1.05892e-05 14.6559 0.179117 15.0883 0.497929 15.4071C0.816741 15.7259 1.24914 15.905 1.70001 15.905H14.2C14.6509 15.905 15.0833 15.7259 15.4021 15.4071C15.7209 15.0883 15.9 14.6559 15.9 14.205V4.68402C15.9003 4.45997 15.8562 4.23808 15.7704 4.03111C15.6846 3.82414 15.5587 3.63618 15.4 3.47802L15.413 3.47902ZM7.95601 13.639C7.50645 13.639 7.06699 13.5057 6.6932 13.256C6.31941 13.0062 6.02807 12.6512 5.85603 12.2359C5.68399 11.8205 5.63898 11.3635 5.72669 10.9226C5.81439 10.4817 6.03087 10.0767 6.34876 9.75877C6.66664 9.44089 7.07165 9.2244 7.51257 9.1367C7.95349 9.049 8.41051 9.09401 8.82585 9.26605C9.24119 9.43808 9.59618 9.72942 9.84594 10.1032C10.0957 10.477 10.229 10.9165 10.229 11.366C10.229 11.6645 10.1702 11.9601 10.056 12.2359C9.94176 12.5116 9.77433 12.7622 9.56326 12.9733C9.3522 13.1843 9.10162 13.3518 8.82585 13.466C8.55008 13.5802 8.2545 13.639 7.95601 13.639ZM11.366 2.82302V6.39302C11.366 6.50601 11.3211 6.61436 11.2412 6.69425C11.1613 6.77414 11.053 6.81902 10.94 6.81902H2.70001C2.58703 6.81902 2.47867 6.77414 2.39878 6.69425C2.31889 6.61436 2.27401 6.50601 2.27401 6.39302V2.69902C2.27401 2.58604 2.31889 2.47769 2.39878 2.3978C2.47867 2.31791 2.58703 2.27302 2.70001 2.27302H10.817C10.9296 2.27341 11.0375 2.31835 11.117 2.39802L11.241 2.52202C11.3207 2.60158 11.3656 2.71043 11.366 2.82302Z" fill="white"/>
                        </svg>                                                                                 
                     </i>
                     <span id="save_group"><?php esc_html_e( "Save Group","ultimate-wp-sms"); ?></span>
               </button>
               <button class="btn gray_outline_btn close-modal">
                  <i class="icon">
                     <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 4.00839e-06C6.41775 4.00839e-06 4.87103 0.469196 3.55544 1.34825C2.23985 2.2273 1.21447 3.47673 0.608967 4.93854C0.00346625 6.40035 -0.15496 8.00888 0.153721 9.56073C0.462403 11.1126 1.22433 12.538 2.34315 13.6569C3.46197 14.7757 4.88743 15.5376 6.43928 15.8463C7.99113 16.155 9.59966 15.9965 11.0615 15.391C12.5233 14.7855 13.7727 13.7602 14.6518 12.4446C15.5308 11.129 16 9.58225 16 8C16.0011 6.94913 15.7948 5.90837 15.3932 4.93729C14.9915 3.96621 14.4023 3.08388 13.6592 2.3408C12.9161 1.59772 12.0338 1.00849 11.0627 0.606824C10.0916 0.205159 9.05087 -0.00104844 8 4.00839e-06ZM12 10.871L10.871 12L8 9.127L5.127 12L4 10.871L6.871 8L4 5.127L5.127 4L8 6.871L10.871 4L12 5.127L9.127 8L12 10.871Z" fill="#787879"/>
                        </svg>                                                                                                                     
                     </i>
                     <?php esc_html_e( "Cancel","ultimate-wp-sms"); ?>
               </button>
            </div>
         </div>
         <div class="sidebar_body_content">
            <form id="group_manager">
               <h5 class="form-title mb-16"><?php esc_html_e( "Group Details","ultimate-wp-sms"); ?></h5>
               <?php if(isset($args->groupid)): ?>
                  <input type="hidden" name="id" value="<?php echo esc_attr($args->groupid); ?>">
               <?php endif; ?>
               <div class="form-group">
                  <label class="form-label"><?php esc_html_e( "Group Name","ultimate-wp-sms"); ?></label>
                  <p class="info-text"><?php esc_html_e( "Enter your group name","ultimate-wp-sms"); ?> </p>
                  <div class="input_wrap">
                     <input type="text" name="grp_title" value="<?php echo esc_attr($args->groupname??""); ?>" />
                  </div>
               </div>
               <div class="form-group">
                  <label class="form-label"><?php esc_html_e( "Group Description","ultimate-wp-sms"); ?></label>
                  <p class="info-text"><?php esc_html_e( "Enter your group description","ultimate-wp-sms"); ?> </p>
                  <div class="input_wrap">
                     <input type="text" name="grp_desc" value="<?php echo esc_attr($args->groupdesc??""); ?>" />
                  </div>
               </div>
               
            </form>
         </div>
      </div>
   <?php }
}