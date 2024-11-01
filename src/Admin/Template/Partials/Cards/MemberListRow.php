<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Cards;
class MemberListRow{
    public function __construct($args){
        $this->html($args);
    }
    private function html($args){ ?>
        <tr>
            <td>
                <div class="checkbox_btn_ui">
                    <input type="checkbox" name="member_id[]" value="<?php echo esc_attr($args->grpmemid) ?>">
                    <span class="checkbox_icon"></span>
                </div>
            </td>
            <td>
                <span class="icon member_extra_details">
                    <i class="plus_icon"> <img src="<?php echo esc_url(sprintf("%simages/gm/plus_icon.svg",UWS_URL)); ?>" alt=""></i>
                    <i class="remove_icon"> <img src="<?php echo esc_url(sprintf("%simages/gm/ic_remove_list.svg",UWS_URL)); ?>" alt=""></i>
                </span>
            </td>
            <td><strong><?php echo esc_html($args->grpmemname) ?></strong></td>
            <td><?php echo esc_html($args->grpmemnum) ?></td>
            <td>
                <div class="custom_dropdown action_dropdown" id="dropdown1">
                    <button class="action_btn"><?php esc_html_e( "Action","ultimate-wp-sms"); ?><i class="icon">
                        <svg class="ss-arrow" viewBox="0 0 100 100"><path d="M10,30 L50,70 L90,30"></path></svg>
                    </i></button>
                    <div class="dropdown_btn_wrapper">
                        <ul>
                            <li class="edit_member" data-id="<?php echo esc_attr($args->grpmemid) ?>" data-grp-id="<?php echo esc_attr($args->grpid); ?>"><a href="javascript:;" >
                                <p><?php esc_html_e( "Edit","ultimate-wp-sms"); ?></p> 
                                <i class="icon">
                                    <svg width="27" height="22" viewBox="0 0 27 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.313 10.643C10.3653 10.6428 11.3939 10.3306 12.2688 9.74581C13.1437 9.16106 13.8255 8.33002 14.2282 7.35778C14.6308 6.38553 14.7361 5.31574 14.5307 4.28366C14.3254 3.25158 13.8186 2.30356 13.0745 1.55946C12.3304 0.815369 11.3824 0.308613 10.3503 0.103269C9.31824 -0.102075 8.24845 0.00321469 7.2762 0.405825C6.30396 0.808435 5.47292 1.49029 4.88817 2.36517C4.30341 3.24005 3.9912 4.26867 3.991 5.32098C3.99087 6.01991 4.12844 6.71202 4.39585 7.35778C4.66325 8.00353 5.05527 8.59028 5.54948 9.08449C6.0437 9.57871 6.63045 9.97072 7.2762 10.2381C7.92196 10.5055 8.61407 10.6431 9.313 10.643ZM13.038 11.973H12.344C11.3938 12.4111 10.3599 12.6381 9.3135 12.6381C8.26713 12.6381 7.23321 12.4111 6.283 11.973H5.588C4.10605 11.9732 2.68488 12.5621 1.63698 13.61C0.589084 14.6579 0.000265131 16.079 0 17.561V19.29C0 19.8204 0.210714 20.3291 0.585786 20.7042C0.960859 21.0793 1.46957 21.29 2 21.29H13.424C13.3217 21.0066 13.2848 20.7037 13.316 20.404L13.599 17.872L13.649 17.411L17.191 13.869C16.6732 13.2763 16.0349 12.801 15.3187 12.4747C14.6026 12.1484 13.825 11.9787 13.038 11.977V11.973ZM14.921 18.014L14.638 20.55C14.6269 20.6488 14.6381 20.7489 14.671 20.8428C14.7038 20.9367 14.7574 21.0219 14.8277 21.0923C14.8981 21.1626 14.9833 21.2162 15.0772 21.249C15.1711 21.2819 15.2712 21.2931 15.37 21.282L17.9 21L23.633 15.267L20.652 12.286L14.919 18.015L14.921 18.014ZM26.321 11.179L24.741 9.59998C24.5538 9.41525 24.3015 9.31166 24.0385 9.31166C23.7755 9.31166 23.5231 9.41525 23.336 9.59998L21.595 11.341L24.58 14.322L26.318 12.584C26.5039 12.3967 26.6083 12.1434 26.6083 11.8795C26.6083 11.6155 26.5039 11.3623 26.318 11.175L26.321 11.179Z" fill="#787879"/>
                                        </svg>                                                
                                </i></a>
                            </li>
                            <li class="member_action" data-member_id="<?php echo esc_attr($args->grpmemid) ?>" data-group_id="<?php echo esc_attr($args->grpid); ?>" data-next_action="RemoveMemberGroup"><a href="javascript:;">
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
            </td>
            </tr>
            
            <tr class="collpased_row">
            <td colspan="5">
                <div class="table_data">
                    <ul>
                        <li>
                        <label><?php esc_html_e( "Email","ultimate-wp-sms"); ?>:</label>
                        <p><?php echo esc_html($args->grpmememail) ?></p>
                        </li>
                        <li>
                        <label><?php esc_html_e( "Address","ultimate-wp-sms"); ?>:</label>
                        <p><?php echo esc_html($args->grpmemaddress) ?></p>
                        </li>
                        <li>
                        <label><?php esc_html_e( "City","ultimate-wp-sms"); ?>:</label>
                        <p><?php echo esc_html($args->grpmemcity) ?></p>
                        </li>
                        <li>
                        <label><?php esc_html_e( "State","ultimate-wp-sms"); ?>:</label>
                        <p><?php echo esc_html($args->grpmemstate) ?></p>
                        </li>
                        <li>
                        <label><?php esc_html_e( "Zipcode","ultimate-wp-sms"); ?>:</label>
                        <p><?php echo esc_html($args->grpmemzip) ?></p>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    <?php }
}