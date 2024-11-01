<?php
namespace UWS\LITE\SMS\Admin\Template;
class HelpLeftTemplate{
    public function __construct($args) {
        $this->html($args);
    }
    private function html($args){?>
        <div class="tab_wrapper">
            <div class="left_sec">
                <div class="tab">
                    <?php $class = "active";
                    foreach($args['tabs'] as $tab => $tab_label): ?>
                        <button class="tablinks <?php echo esc_attr($class); ?>" data-tab="<?php _e($tab); ?>_help">
                            <?php $class = "";
                            _e($tab_label); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="right_sec">
                <?php foreach($args['tabs'] as $tab => $tab_label):
                    $class_name = "UWS\\LITE\\SMS\\Admin\\Template\\Partials\\Tabs\\".ucwords($tab).'Help';
                    if(class_exists($class_name)):
                        new $class_name([]);
                    else:
                        do_action('uws_setup_help_feild_'.$tab);
                    endif;
                endforeach; ?>
            </div>
        </div>
    <?php }
}