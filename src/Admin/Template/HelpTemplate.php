<?php
namespace UWS\LITE\SMS\Admin\Template;
use UWS\LITE\SMS\Admin\Template\Modal\{
    Wapper
};
class HelpTemplate{
    public function __construct($args) {
        $this->html($args);
    }
    public function html($args = []){
        new Header(); ?>
        <div class="cs_app_main">
            <?php new Sidebar($args); ?>
            <div class="cs_app_main_outer ">
            <div class="group_manager_wrapper setting-page-wrap">
                <div class="left_wraper">
                    <div class="white_bg_wrapper">
                        <div class="top_gray_sec">
                        <h4>Help</h4>
                        </div>
                        <div class="body_content_sec">
                            <?php new HelpLeftTemplate($args); ?>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <?php new Wapper(['placeholder'=>'UWS\LITE\SMS\Admin\Template\Partials\Placeholder\GroupManagerModalPlaceholder']); ?>
    <?php }
}