<?php
namespace UWS\LITE\SMS\Admin\Template;
use UWS\LITE\SMS\Admin\Template\Modal\{
    Wapper,
    DeleteConfirm
};
use UWS\LITE\SMS\Admin\Template\Partials\Placeholder\GroupManagerPlaceholder;
class GroupManagerTemplate{
    public function __construct($args) {
        $this->html($args);
    }
    private function html($args){
        new Header(); ?>
        <div class="cs_app_main">
            <?php new Sidebar($args); ?>
            <div class="cs_app_main_outer ">
            <div class="group_manager_wrapper">
                <div class="left_wraper">
                    <div class="heading_wrapper mb-30">
                        <h4 class="heading5">Group Manager</h4>
                    </div>
                    
                    <div class="white_bg_wrapper">
                        <div class="top_gray_sec">
                            
                        </div>
                        <div class="body_content_sec" id="uws-gm-content">
                            <div id="main_group" class="active">
                                <?php new GroupManagerPlaceholder(); ?>
                            </div>
                            <div style="
                                    padding: 40px;
                                    text-align: center;
                                ">
                                <h3 style="
                                    color: #0051c2;
                                ">Group Management with Advanced Options</h3>
                                <p style="
                                    color: #0051c2;
                                    font-size: 14px;
                                    padding: 20px 0px;
                                    font-weight: 500;
                                ">In the Pro version, you have the ability to create multiple groups, each with its own unique settings. You can assign inbound keywords, set specific shortcodes, customize welcome messages, and create invite forms tailored to each group. This feature offers flexibility and control, allowing you to engage with different segments of your audience in a personalized and effective way.</p>
                                <a class="btn primary_btn ml-12" href="https://ultimatewpsms.com/features/" target="_blanck">Check Pro Fetures</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <?php new Wapper(['placeholder'=>'UWS\LITE\SMS\Admin\Template\Partials\Placeholder\GroupManagerModalPlaceholder']); ?>
    <?php }
}