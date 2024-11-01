<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Modal;
use UWS\LITE\SMS\Admin\GroupManager;
use UWS\LITE\SMS\Admin\Template\Partials\Placeholder\GroupManagerTablePlaceholder;
class MemberList{
   public $html;
   public function __construct($args){
      $this->html($args);
   }
    private function html($args){ 
      $group_manager = new GroupManager();
      $is_virtual = false;
      $group_data = $group_manager->get_group_data($args['id']);
      ?>
      <div class="sidebar_inner_modal member_list_modal">
         <div class="sidebar_header">
            <div class="top_ui">
               <span class="close close-modal" data-type="member-list"><img src="<?php echo esc_url(sprintf("%simages/gm/modal_close.svg",UWS_URL)); ?>" alt="" /></span>
               <h4><i class="icon"><img src="<?php echo esc_url(sprintf("%simages/gm/icon_new_group.svg",UWS_URL)); ?>" alt="" /></i><?php echo esc_html($group_data->groupname) ?> (Member List)</h4>
            </div>
            <div class="desc">
               <p>(<?php esc_html_e( "Group ID","ultimate-wp-sms"); ?>: <?php echo esc_html($args['id']); ?>)</p>
            </div>
            
         </div>
         <div class="sidebar_body_content">
            <label class="heading4 <?php echo $is_virtual?'uws_hide':''; ?>"><?php esc_html_e( "New Member","ultimate-wp-sms"); ?></label>
            <form id="add-new-member" class="<?php echo $is_virtual?'uws_hide':''; ?>">
               <div class="form_field_wrap">
                  <div class="field_ui">
                     <div class="form-group">
                        <input type="hidden" name="group_id" value="<?php echo esc_attr($args['id']); ?>">
                        <div class="input_wrap">
                           <input type="text" name="member_name" placeholder="Enter Name" value="" />
                        </div>
                     </div>
                     <div class="form-group">
                        <div class="input_wrap">
                           <input type="text" name="member_number" placeholder="Enter Phone Number" value="" />
                        </div>
                     </div>
                  </div>
                  <button class="btn add_btn"><i class="icon"><img src="<?php echo esc_url(sprintf("%simages/gm/icon_add_member.svg",UWS_URL)); ?>" alt=""></i><span id="add_new"><?php esc_html_e( "Add New","ultimate-wp-sms"); ?></span></button>
               </div>
            </form>
            <div class="members_table">
               <div class="table_header">
                  <h4><?php esc_html_e( "Member(s)","ultimate-wp-sms"); ?></h4>
                  <div class="right_wrapper">
                     <form id="uws-gm-member-filter">
                        <input type="hidden" name="id" value="<?php echo esc_attr($args['id']); ?>">
                        <div class="search_wrapper mr-4">
                           <div class="search_field_member_list">
                           <input type="text" name="search" placeholder="<?php esc_attr_e( "Search Groups","ultimate-wp-sms"); ?>" />
                           <i class="search_icon"></i>
                           </div>
                        </div>
                        <div class="page_filter dropdown_ui">
                              <label><?php esc_html_e( "Display","ultimate-wp-sms"); ?>:</label>
                              <div class="ctsm-selctpicker">
                              <select id="uws-per-page-member" name="per_page">
                                 <option>10</option>
                                 <option>20</option>
                                 <option>25</option>
                                 <option>50</option>
                                 <option>100</option>
                              </select>
                           </div>
                        </div>
                        <div class="dropdown_ui">
                           <div class="custom_dropdown bulk_action" id="dropdown4">
                              <button class="action_btn"><?php esc_html_e( "Bulk Action","ultimate-wp-sms"); ?><i class="icon">
                                 <svg class="ss-arrow" viewBox="0 0 100 100"><path d="M10,30 L50,70 L90,30"></path></svg>
                              </i></button>
                              <div class="dropdown_btn_wrapper">
                                 <ul>
                                    <li class="bulk_member_action" data-next_action="RemoveMemberGroup" data-member_ids="" data-group_id="<?php echo esc_attr($args['id']); ?>"><a href="javascript:;">
                                       <p><?php esc_html_e( "Remove","ultimate-wp-sms"); ?></p> 
                                       <i class="icon">
                                          <svg width="23" height="17" viewBox="0 0 23 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                             <path fill-rule="evenodd" clip-rule="evenodd" d="M6.61546 0.341459C6.80534 0.124466 7.07965 0 7.368 0H19.193C19.9406 0 20.6577 0.297001 21.1863 0.825666C21.715 1.35433 22.012 2.07136 22.012 2.819V13.735C22.012 14.4826 21.715 15.1997 21.1863 15.7283C20.6577 16.257 19.9406 16.554 19.193 16.554H7.368C7.07965 16.554 6.80534 16.4295 6.61546 16.2125L0.247455 8.93554C-0.0824851 8.5585 -0.0824851 7.9955 0.247455 7.61846L6.61546 0.341459ZM7.82174 2L2.32882 8.277L7.82174 14.554H19.193C19.4102 14.554 19.6185 14.4677 19.7721 14.3141C19.9257 14.1605 20.012 13.9522 20.012 13.735V2.819C20.012 2.60179 19.9257 2.39347 19.7721 2.23988C19.6185 2.08629 19.4102 2 19.193 2H7.82174ZM10.2989 4.84089C10.6894 4.45037 11.3226 4.45037 11.7131 4.84089L13.735 6.86279L15.7569 4.84089C16.1474 4.45037 16.7806 4.45037 17.1711 4.84089C17.5616 5.23142 17.5616 5.86458 17.1711 6.25511L15.1492 8.277L17.1711 10.2989C17.5616 10.6894 17.5616 11.3226 17.1711 11.7131C16.7806 12.1036 16.1474 12.1036 15.7569 11.7131L13.735 9.69121L11.7131 11.7131C11.3226 12.1036 10.6894 12.1036 10.2989 11.7131C9.90837 11.3226 9.90837 10.6894 10.2989 10.2989L12.3208 8.277L10.2989 6.25511C9.90837 5.86458 9.90837 5.23142 10.2989 4.84089Z" fill="#787879"/>
                                             </svg>                                             
                                       </i></a>
                                    </li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
               <table>
                  <thead>
                     <tr>
                        <th>
                           <div class="checkbox_btn_ui">
                              <input type="checkbox" name="" id="select_all_member">
                              <span class="checkbox_icon"></span>
                           </div>
                        </th>
                        <th></th>
                        <th><?php esc_html_e( "Name","ultimate-wp-sms"); ?><div class="sorting_wrap"> <span class="icon icon_top"></span> <span class="icon icon_bottom"></span></div></th>
                        <th><?php esc_html_e( "Phone Number","ultimate-wp-sms"); ?><div class="sorting_wrap"> <span class="icon icon_top"></span> <span class="icon icon_bottom"></span></div></th>
                        <th><?php esc_html_e( "Actions","ultimate-wp-sms"); ?></th>
                     </tr>
                  </thead>
                  <tbody id="uws-member-list">
                     <?php new GroupManagerTablePlaceholder(); ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   <?php }
}