<?php
namespace UWS\LITE\SMS\Admin\Template\Partials;
class ScrollPaginate{
    public function __construct($args){
        $this->html($args);
    }
    private function html($args){ ?>
      <input type="hidden" value="<?php echo $args['page']; ?>" name="page">
   <?php }
}