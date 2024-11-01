<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Cards;
class GroupManagerCard{
    public function __construct($args){
        $this->html($args);
    }
    private function html($args){ ?>
        <div class="admin_group_card">
            <div class="card_top_ui">
                <div class="radio_checkbox_ui">
                    
                    <label><?php echo esc_html($args->groupname) ?></label>
                </div>
                <div class="icons_list">
                    <button class="btn icon_btn modal-show" data-type="MemberList" title="Add Group Member" data-id="<?php echo esc_attr($args->groupid) ?>">
                        <svg width="26" height="21" viewBox="0 0 26 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M25.053 8.351H22.483V5.782C22.4825 5.6119 22.4147 5.44891 22.2944 5.32862C22.1741 5.20834 22.0111 5.14053 21.841 5.14H20.557C20.3869 5.14053 20.2239 5.20834 20.1036 5.32862C19.9833 5.44891 19.9155 5.6119 19.915 5.782V8.352H17.345C17.1749 8.35253 17.0119 8.42034 16.8916 8.54062C16.7713 8.66091 16.7035 8.8239 16.703 8.994V10.279C16.7035 10.4491 16.7713 10.6121 16.8916 10.7324C17.0119 10.8527 17.1749 10.9205 17.345 10.921H19.915V13.491C19.9155 13.6611 19.9833 13.8241 20.1036 13.9444C20.2239 14.0647 20.3869 14.1325 20.557 14.133H21.842C22.0121 14.1325 22.1751 14.0647 22.2954 13.9444C22.4157 13.8241 22.4835 13.6611 22.484 13.491V10.921H25.054C25.2241 10.9205 25.3871 10.8527 25.5074 10.7324C25.6277 10.6121 25.6955 10.4491 25.696 10.279V8.994C25.6957 8.82355 25.6279 8.66015 25.5074 8.53962C25.3868 8.4191 25.2235 8.35127 25.053 8.351ZM8.994 10.278C10.0104 10.2778 11.0038 9.97624 11.8488 9.41144C12.6938 8.84664 13.3523 8.04397 13.7411 7.10492C14.1299 6.16587 14.2315 5.13262 14.0331 4.13582C13.8347 3.13902 13.3452 2.22343 12.6265 1.50483C11.9077 0.786225 10.992 0.296882 9.9952 0.0986736C8.99836 -0.0995348 7.96513 0.00229293 7.02616 0.391281C6.08719 0.780269 5.28464 1.43895 4.72001 2.28403C4.15537 3.12911 3.854 4.12265 3.854 5.139C3.854 5.81395 3.98696 6.48229 4.24528 7.10584C4.5036 7.7294 4.88223 8.29596 5.35953 8.77318C5.83684 9.25039 6.40348 9.62891 7.02708 9.88711C7.65069 10.1453 8.31905 10.2781 8.994 10.278ZM12.594 11.563H11.92C11.0023 11.9863 10.0036 12.2054 8.993 12.2054C7.98236 12.2054 6.98374 11.9863 6.066 11.563H5.4C3.96783 11.563 2.59432 12.1319 1.58162 13.1446C0.568927 14.1573 0 15.5308 0 16.963V18.633C0.000264968 19.144 0.203373 19.634 0.564698 19.9953C0.926023 20.3566 1.41601 20.5597 1.927 20.56H16.06C16.571 20.5597 17.061 20.3566 17.4223 19.9953C17.7836 19.634 17.9867 19.144 17.987 18.633V16.963C17.987 15.5315 17.4186 14.1586 16.4068 13.146C15.395 12.1335 14.0225 11.5641 12.591 11.563H12.594Z" fill="#1E2327"/>
                        </svg>                                                      
                    </button>
                    <a href="<?php echo esc_url( sprintf("%sadmin-post.php?action=process_downloadgroup&grpid=%s&_security=%s",admin_url(''),$args->groupid,wp_create_nonce('uws-gm-download-check'))); ?>" class="btn icon_btn" title="Download Group Member" >
                        <svg width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.996 6.99805H10.996V0.998047H4.99597V6.99805H0.995972L7.99597 13.998L14.996 6.99805ZM0.995972 15.998V17.998H14.996V15.998H0.995972Z" fill="#1E2327"/>
                        </svg>                                                      
                    </a>
                <?php do_action('uws_after_group_manager_top_action',$args); ?>
                </div>
            </div>
            <div class="card_middle_ui">
                <label><?php esc_html_e( "Description","ultimate-wp-sms"); ?></label>
                <p><?php echo esc_attr($args->groupdesc) ?></p>
            </div>
            <div class="card_bottom_ui">
                <button class="btn primary_btn green-800 modal-show" data-type="AddGroup" data-id="<?php echo esc_attr($args->groupid) ?>"><?php esc_html_e( "View/Edit Group","ultimate-wp-sms"); ?> <i class="icon"><img src="<?php echo esc_url(sprintf("%simages/gm/icon_view_edit.svg",UWS_URL)); ?>" alt="" /></i></button>
                <button class="btn primary_btn modal-show" data-type="MemberList"  data-id="<?php echo esc_attr($args->groupid) ?>"><p><?php esc_html_e( "View Members","ultimate-wp-sms"); ?> <span class="count">(<?php echo esc_html( $args->total_member) ?>)</span> </p><i class="icon"><img src="<?php echo esc_url(sprintf("%simages/gm/icon_view_members.svg",UWS_URL)); ?>" alt="" /></i></button>
            </div>
        </div>
    <?php }
}