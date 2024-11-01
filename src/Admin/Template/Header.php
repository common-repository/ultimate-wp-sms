<?php
namespace UWS\LITE\SMS\Admin\Template;
class Header{
    
    public function __construct() {
        $this->html();
    }
    public function html($args = []){ ?>
        <?php //new Loader($args); ?>
        <!-- loader html End -->
        <header class="cs_header">
            <a href="javascript:;" class="header-logo"><img class="w-full" src="<?php echo UWS_URL; ?>images/logo.svg" alt="" /></a>
            <div id="header_toggle" class="menu_toogle">
                <div class="one"></div>
                <div class="two"></div>
                <div class="three"></div>
            </div>
        </header>
<?php }
}
