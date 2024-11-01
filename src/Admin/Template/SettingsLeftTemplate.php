<?php
namespace UWS\LITE\SMS\Admin\Template;
class SettingsLeftTemplate{
    public function __construct($args) {
        $this->html($args);
    }
    private function html($args){?>
        <div class="tab_wrapper">
            <div class="left_sec">
                <div class="tab">
                    <?php foreach($args['tabs'] as $tab => $tab_label): ?>
                        <?php if(is_array($tab_label)): ?>
                            <div class="custom_dropdown settings <?php _e($tab); ?>_dropdown">
                                <button class="tablinks <?php _e($tab); ?>_btn">
                                    <?php _e($tab_label['label']); ?>
                                    <i class="arrow_icon">
                                        <svg class="ss-arrow" viewBox="0 0 100 100">
                                            <path d="M10,30 L50,70 L90,30"></path>
                                        </svg>
                                    </i>
                                </button>
                                <div class="dropdown_btn_wrapper" >
                                    <ul>
                                        <?php foreach($tab_label as $t => $t_label): ?>
                                            <?php if($t=='label')
                                                        continue; ?>
                                            <li>
                                                <button class="tablinks"  data-tab="<?php _e($t) ?>_<?php _e($tab); ?>_settings">
                                                    <?php _e($t_label) ?>
                                                </button>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <button class="tablinks" data-tab="<?php _e($tab); ?>_settings">
                                <?php _e($tab_label); ?>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="right_sec">
                <?php foreach($args['tabs'] as $tab => $tab_label):
                    if(is_array($tab_label)):
                        foreach($tab_label as $t => $t_label):
                            if($t=='label'):
                                continue;
                            endif;
                            $class_args = array('fields'=>$args['fields'][$tab][$t],'hidden_type'=>$tab.'-'.$t);
                            $class_name = "UWS\\LITE\\SMS\\Admin\\Template\\Partials\\Tabs\\".ucwords($t).ucwords($tab).'Settings';
                            if(class_exists($class_name)):
                                new $class_name($class_args);
                            else:
                                do_action('uws_setup_setting_feild_'.$t,$class_args);
                            endif;
                        endforeach; 
                    else:
                        $class_args = array('fields'=>$args['fields'][$tab],'hidden_type'=>$tab);
                        $class_name = "UWS\\LITE\\SMS\\Admin\\Template\\Partials\\Tabs\\".ucwords($tab).'Settings';
                        if(class_exists($class_name)):
                            new $class_name($class_args);
                        else:
                            do_action('uws_setup_setting_feild_'.$tab,$class_args);
                        endif;
                    endif;
                endforeach; ?>
                
            </div>
        </div>
    <?php }
}