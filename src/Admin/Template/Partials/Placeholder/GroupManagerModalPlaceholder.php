<?php 
namespace UWS\LITE\SMS\Admin\Template\Partials\Placeholder;
class GroupManagerModalPlaceholder{
    public function __construct() {
        $this->html();
    }
    function html(){ ?>
    <div class="sidebar_inner_modal member_list_modal">
        <div class="sidebar_header">
            <div class="top_ui">
               <span class="close close-modal"><img src="<?php echo UWS_URL; ?>images/gm/modal_close.svg" alt="" /></span>
               <h4><i class="icon"><span class="placeholder h-20 mb-10"></span></i><div class="placeholder h-20 mb-10"></div></h4>
            </div>
            <div class="desc">
                <div class="display-flex">
                    <div class="col-30">
                        <div class="placeholder h-20 mb-10"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar_body_content">
            <div class="group_modal_placeholder placeholder-glow col-100 mb-45">
                <div class="display-flex between_text">
                    <div class="col-30">
                        <div class="placeholder h-40 mb-10"></div>
                    </div>
                </div>
                <br>
                <hr class="mb-10">
                <div class="display-flex">
                    <div class="col-30">
                        <div class="placeholder h-20 mb-10"></div>
                    </div>
                </div>
                <div class="display-flex between_text">
                    <div class="col-90 ml-10">
                        <div class="placeholder h-40 mb-10"></div>
                        <div class="placeholder h-40 mb-10"></div>
                        <div class="placeholder h-40 mb-10"></div>
                        <div class="placeholder h-40 mb-10"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php }
}