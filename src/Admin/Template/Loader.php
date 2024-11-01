<?php
namespace UWS\LITE\SMS\Admin\Template;
class Loader{
    
    public function __construct($args) {
        $this->html($args);
    }
    public function html($args){ ?>
        <div id="preloader">
            <div class="loader-inner-wrapper">
                <img class="loader-icon" src="<?php echo UWS_URL; ?>images/loader_icon.svg" alt=""/>
            </div>
        </div>
    <?php }
}