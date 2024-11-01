<?php
namespace UWS\LITE\SMS\Admin\Template\Modal;
class Wapper{
    public function __construct($args){
        $this->html($args);
    }
    private function html($args){ ?>
        <div id="uws_modal" class="modal_sidebar_wrapper">
            <?php if(isset($args['placeholder'])){
                $class = $args['placeholder'];
                new $class();
            } ?>
        </div>
        <div id="uws_modal_backdrop" class="sidebar_backdrop"></div>
    <?php }
}