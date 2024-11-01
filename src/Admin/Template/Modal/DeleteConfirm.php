<?php
namespace UWS\LITE\SMS\Admin\Template\Modal;
class DeleteConfirm{
    public function __construct($args){
        $this->html($args);
    }
    private function html($args){ ?>
        <div id="user_detail_modal" class="modal delete_modal active">
            <!-- Modal content -->
            <div class="modal-content mw-523">
                <div class="modal-header">
                <span class="close remove_modal"><img src="<?php echo esc_url(sprintf("%simages/gm/modal_close.svg",UWS_URL)); ?>" alt="" /></span>
                <h2>Confirm Action</h2>
                </div>
                <form id="confirmation_alert">
                    <?php wp_nonce_field( 'uws-confirm-delete','_security' ); ?>
                    <?php $class_name = apply_filters( 'uws_confirm_action','UWS\\LITE\\SMS\\Admin\\Template\\Partials\\Modal\\'.$args['next_action'],$args['next_action']);
                    new $class_name($args);  ?>
                </form>
            </div>
        </div>
    <?php }
}