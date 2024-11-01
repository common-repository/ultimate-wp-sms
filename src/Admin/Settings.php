<?php
namespace UWS\LITE\SMS\Admin;
use UWS\LITE\SMS\Admin\Template\Partials\Icons\{
    ErrorIcon,
    SuccessIcon
};
use UWS\LITE\SMS\Admin\Template\{
    SettingsTemplate,
    SettingsLeftTemplate
};
if (!defined('ABSPATH')) {
    exit;
} // No direct access allowed ;)
class Settings{
    public $settings,$setting_fields,$uws;
    private $data,$tabs;
    public function __construct(){
        global $uws;
        $this->uws = $uws;
        $this->get_tabs();
        $this->get_all_settings();
    }
    public function render(){
        $this->get_settings_fields();
        $args = array('page'=>'uws_setting','fields'=>$this->setting_fields,'tabs'=>$this->tabs);
        new SettingsTemplate($args);
    }
    public function assets(){
        wp_enqueue_script('uws-settings', UWS_URL . 'js/uws-settings.js', true, UWS_VERSION);
        wp_localize_script('uws-settings', 'uws_settings', array('ajax' => admin_url("admin-ajax.php"),'securty_check'=>wp_create_nonce('uws-sm-ajax-check')));
    }
    public function render_row($fields){
         ?>
        <?php if(!empty($fields['label'])): ?>
            <label class="form-title"><?php echo esc_html($fields['label']); ?></label>
        <?php endif; ?>
        <div class="form_fields">
            <?php if(!empty($fields['fields']) && is_array($fields['fields'])): 
                foreach($fields['fields'] as $field):?>
                    <div class="form-group">
                        <label class="form-label"><?php echo esc_html( $field['label']??"") ?></label>
                        <p class="helper_text"><?php echo wp_kses( $field['helper_text']??"",array(
                                'div' => array(
                                    'class' => array()
                                ),
                                'br' => array(
                                    'class' => array()
                                ),
                            )) ?></p>
                        <?php $function_name = $field['type'].'_field';
                        if(method_exists($this,$function_name)):
                            $this->$function_name($field);
                        endif; ?>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    <?php }
    public function save_setting(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-sm-ajax-check' ) ) {
            wp_send_json(array('success'=>'false','msg'=>'unauthorize access'),401);
        }
        if(!isset($_POST['type'])){
            wp_send_json(array('success'=>'false','msg'=>'missing param'),422);
        }
        $type = sanitize_text_field($_POST['type']);
        unset($_POST['type']);
        $this->sanitize_input($_POST);
        update_option( 'uws-'.$type,$this->data);
        wp_send_json(array('success'=>true,'msg'=>'Setting Saved for currect tab'));
    }
    public function get_setting_left_html(){
        if ( ! wp_verify_nonce( $_POST['_security'], 'uws-sm-ajax-check' ) ) {
            wp_send_json(array('success'=>'false','msg'=>'unauthorize access'),401);
        }
        ob_start();
            $this->get_tabs();
            $this->get_all_settings();
            $this->get_settings_fields();
            $args = array('page'=>'uws_setting','fields'=>$this->setting_fields,'tabs'=>$this->tabs);
            new SettingsLeftTemplate($args);
            $html = ob_get_clean();
        ob_end_clean();
        wp_send_json(array('success'=>true,'msg'=>'Setting Saved for currect tab','html'=>$html));
    }
    private function sanitize_input($data){
        foreach($data as $name => $input){
            if(is_string($input)){
                $this->data[$name] = sanitize_text_field($input);
            } else {
                $this->data[$name] = $input;
            }
        }
    }
    
    private function select_field($field){ ?>
        <div class="ctsm-selctpicker">
            <select id="<?php echo esc_attr( $field['id']) ?>" name="<?php echo esc_attr( $field['name']) ?>" <?php echo esc_html( $field['multiple']??"") ?>>
                <?php foreach($field['options'] as $option_value => $option_label): ?>
                    <?php if(is_array($option_label)): ?>
                        <optgroup label="<?php echo esc_attr( $option_value) ?>">
                        <?php foreach($option_label as $opt_Val => $opt_labele): ?>
                            <?php if(is_array($field['value'])){
                                $selected = in_array($opt_Val,$field['value'])?"selected":""; 
                            } else {
                                $selected = $opt_Val==$field['value']?"selected":"";
                            }?>
                            <option value="<?php echo esc_attr( $opt_Val) ?>" <?php echo esc_html( $selected); ?>><?php echo esc_html( $opt_labele) ?></option>
                        <?php endforeach; ?>
                        </optgroup>
                    <?php else: ?>
                        <?php if(is_array($field['value'])){
                            $selected = in_array($option_value,$field['value'])?"selected":""; 
                        } else {
                            $selected = $option_value==$field['value']?"selected":"";
                        }?>
                        <option value="<?php echo esc_attr( $option_value) ?>" <?php echo esc_html( $selected); ?>><?php echo esc_html( $option_label) ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    <?php }
    private function button_field($field){ ?>
        <button class="btn primary_btn mr-10" type="button" id="<?php echo esc_attr( $field['id']) ?>">
            <?php echo esc_html( $field['value']) ?>
        </button>
    <?php }
    private function input_field($field){ ?>
        <div class="input_wrap">
            <input type="text" placeholder="" name="<?php echo esc_attr( $field['name']) ?>" value="<?php echo esc_attr( $field['value']) ?>" 
            <?php if(!empty($field['args'])){
            foreach($field['args'] as $args_key => $args):
                echo esc_html($args_key.'="'.$args.'"');
            endforeach; 
            } ?>
            />
        </div>
    <?php }
    private function radio_field($field){ ?>
        <div class="radio_listing">
            <ul>
                <?php foreach($field['options'] as $option_value => $option_label): ?>
                    <?php $checked = $option_value==$field['value']?"checked":""; ?>
                    <li>
                        <input type="radio" name="<?php echo esc_attr( $field['name']) ?>" value="<?php echo esc_attr( $option_value) ?>" <?php echo esc_html( $checked); ?>/>
                        <span class="radio_icon"></span>
                        <label><?php echo esc_html( $option_label) ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php }
    private function checkbox_field($field){ ?>
        
        <div class="radio_listing">
            <ul>
                <?php foreach($field['options'] as $option_value => $option_label): ?>
                    <?php $checked = $option_value==$field['value']?"checked":""; ?>
                    <li>
                        <input type="checkbox" name="<?php echo esc_attr( $field['name']); ?>" value="<?php echo esc_attr( $option_value); ?>" <?php echo esc_html( $checked); ?> />
                        <span class="radio_icon"></span>
                        <label><?php echo wp_kses($option_label,array(
                                'div' => array(
                                    'class' => array()
                                ),
                                'i' => array(
                                    'class' => array()
                                ),
                                'img' => array(
                                    'src' => array()
                                )
                            )); ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php }
    private function hidden_field($field){ ?>
        
        <div class="ctsm-selctpicker">
            <input type="hidden" placeholder="" name="<?php echo esc_attr( $field['name']) ?>" value="<?php echo esc_attr( $field['value']) ?>" 
            <?php if(!empty($field['args'])){
            foreach($field['args'] as $args_key => $args):
                echo esc_html($args_key.'="'.$args.'"');
            endforeach; 
            } ?> />
        </div>
    <?php }
    private function send_as_field($field){ ?>
        <div class="mesaage-types">
            <div class="radio-card">
            <input type="radio" name="<?php echo esc_attr( $field['name']) ?>" value="uws-sms" checked="" />
            <label>
                <i class="icon">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8 0H1.2C0.882781 0.00158611 0.579095 0.128714 0.355348 0.353586C0.1316 0.578458 0.00599608 0.882777 0.00600004 1.2L0 12L2.4 9.6H10.8C11.1183 9.6 11.4235 9.47357 11.6485 9.24853C11.8736 9.02348 12 8.71826 12 8.4V1.2C12 0.88174 11.8736 0.576516 11.6485 0.351472C11.4235 0.126428 11.1183 0 10.8 0ZM4.2 5.4H3V4.2H4.2V5.4ZM6.6 5.4H5.4V4.2H6.6V5.4ZM9 5.4H7.8V4.2H9V5.4Z" fill="#3481C4"/>
                    </svg>
                </i>
                <span>SMS</span>
            </label>
            </div>
            <div class="radio-card">
            <input type="radio" name="<?php echo esc_attr( $field['name']) ?>" value="uws-call" />
            <label>
                <i class="icon">
                    <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.889648 12.013V10.75C0.889648 9.071 4.51765 8.224 6.33465 8.224C8.15165 8.224 11.7796 9.071 11.7796 10.75V12.014L0.889648 12.013ZM12.3826 7.813C12.8307 7.36913 13.1864 6.84087 13.4291 6.25875C13.6718 5.67664 13.7968 5.05219 13.7968 4.4215C13.7968 3.79081 13.6718 3.16636 13.4291 2.58425C13.1864 2.00213 12.8307 1.47387 12.3826 1.03L13.4926 0C14.1193 0.553639 14.6211 1.23415 14.9648 1.99643C15.3085 2.7587 15.4862 3.58533 15.4862 4.4215C15.4862 5.25767 15.3085 6.0843 14.9648 6.84657C14.6211 7.60885 14.1193 8.28936 13.4926 8.843L12.3826 7.813ZM4.01965 4.437C4.01965 3.97202 4.15753 3.51747 4.41586 3.13085C4.67419 2.74423 5.04137 2.4429 5.47096 2.26496C5.90055 2.08702 6.37326 2.04046 6.82931 2.13117C7.28536 2.22189 7.70426 2.4458 8.03306 2.77459C8.36185 3.10339 8.58576 3.52229 8.67647 3.97834C8.76719 4.43439 8.72063 4.9071 8.54269 5.33669C8.36475 5.76628 8.06341 6.13345 7.67679 6.39179C7.29017 6.65012 6.83563 6.788 6.37065 6.788C5.74712 6.788 5.14914 6.54031 4.70824 6.09941C4.26734 5.65851 4.01965 5.06052 4.01965 4.437ZM10.0996 5.647C10.3777 5.29789 10.5291 4.86479 10.5291 4.4185C10.5291 3.97221 10.3777 3.53911 10.0996 3.19L11.2396 2.122C11.5639 2.41021 11.8234 2.76383 12.0011 3.15956C12.1788 3.5553 12.2707 3.98419 12.2707 4.418C12.2707 4.85181 12.1788 5.2807 12.0011 5.67643C11.8234 6.07217 11.5639 6.42579 11.2396 6.714L10.0996 5.647Z" fill="#3481C4"/>
                    </svg>
                </i>
                <span>A text-to-voice call/audio file</span>
            </label>
            </div>
        </div>
    <?php }
    private function text_field($field){?>
        <div class="input_wrap">
            <input type="text" placeholder="" name="<?php echo esc_attr( $field['name']) ?>" readonly value="<?php echo esc_attr( $field['value']) ?>" 
            <?php if(!empty($field['args'])){
            foreach($field['args'] as $args_key => $args):
                echo esc_html($args_key.'="'.$args.'"');
            endforeach; 
            } ?> />
        </div>
    <?php }
    private function allow_text_field($field){ ?>
        <label>
            <i class="icon">
                <?php if($field['value'] == 'yes'){
                    new SuccessIcon([]);
                } else {
                    new ErrorIcon([]);
                } ?>
            </i>
        </label>
    <?php }
    private function group_field($field){ ?>
        <div class="radio_group">
            <?php if(!empty($field['fields']) && is_array($field['fields'])): 
                foreach($field['fields'] as $inner_fields):?>
                        <label class="form-label"><?php echo esc_html( $inner_fields['label']??"") ?></label>
                        <p class="helper_text"><?php echo esc_html( $inner_fields['helper_text']??"") ?></p>
                        <?php 
                            $function_name = $inner_fields['type'].'_field';
                            if(method_exists($this,$function_name)):
                                $this->$function_name($inner_fields);
                            endif; 
                        ?>
                <?php endforeach;
            endif; ?>
        </div>
    <?php }
    private function error_field($field){ ?>
        <?php if(!empty($field['errors']) && is_array($field['errors'])): ?>
            <?php foreach($field['errors'] as $error): ?>
                <div>
                    <p class="helper_text text-red-700"><?php echo esc_html($error); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php }
    private function get_all_settings(){
        $this->settings = [];
        foreach($this->tabs  as $key => $tab){
            if(is_array($tab)){
                foreach($tab as $t=>$t_label){
                    if($t=='label')
                        continue;
                    $this->settings[$key][$t] = get_option( 'uws-'.$key.'-'.$t);
                    $this->settings[$key][$t] = apply_filters('uws_settings_data_'.$t,$this->settings[$key][$t]);
                }
            } else {
                $this->settings[$key] = get_option( 'uws-'.$key);
            }
        }
        $this->settings = \apply_filters('uws_settings_data',$this->settings);
    }
    private function get_settings_fields(){
        $this->setting_fields = [];
        foreach($this->tabs  as $key => $tab){
            if(is_array($tab)){
                foreach($tab as $t => $t_label){
                    if($t=='label')
                        continue;
                    $method_name = $t.'_fields';
                    $fields = array();
                    if(method_exists($this,$method_name)){
                        $fields = $this->$method_name($this->settings[$key][$t]);
                    }
                    $this->setting_fields[$key][$t] = \apply_filters('uws_settings_fields_'.$t,$fields,$this->settings[$key][$t]);
                }
            } else {
                $method_name = $key.'_fields';
                $fields = array();
                if(method_exists($this,$method_name)){
                    $fields = $this->$method_name($this->settings[$key]);
                }
                $this->setting_fields[$key] = \apply_filters('uws_settings_fields_'.$key,$fields,$this->settings[$key]);
            }
        }
        $this->setting_fields = \apply_filters('uws_settings_fields',$this->setting_fields);
    }
    private function get_tabs(){
        $this->tabs =  array(
            'general'=>'
                    <span class="icon">
                        <svg width="27" height="23" viewBox="0 0 27 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26.0079 10.6649L25.9279 10.848C25.8391 11.0606 25.681 11.2369 25.4793 11.3482C25.2776 11.4595 25.0441 11.4993 24.8169 11.461C24.5846 11.4223 24.3733 11.303 24.2204 11.1239C24.0675 10.9448 23.9827 10.7175 23.9809 10.482C23.9689 9.74596 23.9809 9.00894 23.9809 8.27294C23.9809 6.96094 23.9809 5.64995 23.9809 4.28695C23.8689 4.35295 23.7859 4.39894 23.7049 4.44994C20.9109 6.18328 18.1172 7.92062 15.3239 9.66195C14.5359 10.1861 13.5956 10.4325 12.6519 10.362C11.9597 10.2916 11.2946 10.0556 10.7129 9.67394C7.93422 7.94461 5.15488 6.21628 2.37488 4.48895C2.27488 4.42795 2.1749 4.36996 2.0379 4.28896V4.59994C2.0379 8.27327 2.0379 11.947 2.0379 15.621C2.01424 15.9182 2.0555 16.2171 2.15887 16.4968C2.26225 16.7765 2.42524 17.0304 2.63654 17.2409C2.84783 17.4513 3.10236 17.6133 3.38251 17.7155C3.66265 17.8177 3.96167 17.8578 4.25885 17.8329C6.83152 17.8329 9.40457 17.8329 11.9779 17.8329C12.1902 17.8132 12.4034 17.8596 12.5883 17.9658C12.7732 18.0719 12.9208 18.2327 13.0109 18.426C13.0795 18.572 13.1121 18.7324 13.1059 18.8937C13.0997 19.055 13.0549 19.2125 12.9752 19.3528C12.8956 19.4932 12.7834 19.6125 12.6481 19.7005C12.5129 19.7886 12.3585 19.8429 12.1979 19.8589C12.1314 19.865 12.0646 19.8673 11.9979 19.866C9.36586 19.866 6.7329 19.873 4.0979 19.866C3.24496 19.8737 2.41136 19.6117 1.71631 19.1172C1.02125 18.6228 0.500317 17.9212 0.227905 17.1129C0.127905 16.8369 0.0788594 16.5459 0.00585938 16.2609V3.60894C0.0969093 3.19992 0.219997 2.7987 0.373901 2.40896C0.689332 1.67086 1.22162 1.04608 1.90027 0.617455C2.57892 0.188832 3.37187 -0.023374 4.17389 0.00896484C10.0646 -0.00103516 15.9548 -0.00103516 21.8448 0.00896484C21.9288 0.00896484 22.0139 0.00896484 22.0989 0.00896484C22.8314 0.0278641 23.5443 0.249567 24.1583 0.649437C24.7723 1.04931 25.2634 1.61166 25.5769 2.27395C25.7578 2.70721 25.9036 3.15433 26.0129 3.61095L26.0079 10.6649ZM23.1129 2.41296C22.7254 2.14185 22.2592 2.00647 21.7869 2.02795C15.9302 2.02795 10.0735 2.02795 4.21686 2.02795C4.10674 2.02555 3.99662 2.02924 3.8869 2.03896C3.52856 2.06726 3.18573 2.19737 2.89886 2.41396C2.98886 2.47296 3.06388 2.52594 3.14288 2.57494C6.01555 4.36094 8.88752 6.14763 11.7589 7.93496C12.1235 8.19295 12.5592 8.3315 13.0059 8.3315C13.4525 8.3315 13.8882 8.19295 14.2529 7.93496C17.1195 6.14029 19.9916 4.35361 22.8689 2.57494C22.9469 2.52994 23.0219 2.47695 23.1129 2.41695V2.41296ZM26.0079 19.047C25.8549 19.395 25.7079 19.747 25.5479 20.09C25.5018 20.225 25.4279 20.3489 25.3311 20.4537C25.2342 20.5585 25.1164 20.6418 24.9854 20.6984C24.8544 20.7549 24.713 20.7833 24.5703 20.7819C24.4276 20.7804 24.2867 20.7491 24.1569 20.69L24.0139 20.6399C23.7317 20.4906 23.4235 20.3969 23.1059 20.3639C22.807 20.4554 22.5333 20.6145 22.3059 20.8289C22.2278 20.8984 22.1762 20.9927 22.1599 21.0959C22.0949 21.4189 22.0599 21.7479 21.9949 22.0709C21.9549 22.3027 21.8346 22.5129 21.655 22.6647C21.4754 22.8165 21.248 22.9002 21.0129 22.901C20.6999 22.911 20.3869 22.91 20.0739 22.901C19.8429 22.8994 19.6196 22.818 19.4419 22.6706C19.2642 22.5231 19.143 22.3186 19.0989 22.0919C19.0239 21.7449 18.9769 21.392 18.8989 21.047C18.8554 20.9471 18.7905 20.858 18.7089 20.7859C18.6839 20.7559 18.6279 20.753 18.5999 20.724C18.4592 20.579 18.2699 20.4911 18.0684 20.4771C17.8669 20.463 17.6673 20.5239 17.5079 20.6479C17.3195 20.744 17.1214 20.8194 16.9169 20.8729C16.7098 20.9421 16.4854 20.9386 16.2806 20.8632C16.0757 20.7878 15.9026 20.6448 15.7899 20.4579C15.5882 20.135 15.4046 19.8012 15.2399 19.4579C15.1386 19.2545 15.1118 19.0221 15.1642 18.801C15.2167 18.5799 15.345 18.3842 15.5269 18.2479C15.7859 18.0299 16.0569 17.827 16.3219 17.616C16.3829 17.5707 16.4322 17.5114 16.4653 17.4431C16.4985 17.3747 16.5146 17.2994 16.5123 17.2234C16.5101 17.1475 16.4895 17.0732 16.4523 17.007C16.4152 16.9407 16.3625 16.8845 16.2989 16.8429C16.0459 16.6739 15.7889 16.5099 15.5379 16.3379C15.3073 16.2036 15.1393 15.9833 15.0709 15.7254C15.0025 15.4675 15.0392 15.1929 15.1729 14.9619C15.3229 14.6593 15.4809 14.3593 15.6469 14.0619C15.7568 13.8388 15.9455 13.6643 16.1765 13.572C16.4074 13.4796 16.6644 13.4761 16.8979 13.5619C17.2129 13.6619 17.5219 13.773 17.8339 13.88C17.881 13.899 17.9295 13.9147 17.9789 13.927C18.3587 13.8041 18.693 13.5704 18.9389 13.2559C18.9959 12.9479 19.0479 12.64 19.1049 12.332C19.1346 12.067 19.2658 11.8239 19.4709 11.6535C19.6759 11.4831 19.939 11.3987 20.2049 11.418C20.4589 11.418 20.7129 11.418 20.9669 11.418C21.2151 11.4059 21.4591 11.4862 21.6517 11.6433C21.8443 11.8004 21.9719 12.0233 22.0099 12.2689C22.0809 12.6079 22.1309 12.9519 22.2099 13.2899C22.2422 13.3779 22.297 13.4558 22.3689 13.516C22.4149 13.562 22.4979 13.57 22.5429 13.616C22.8619 13.94 23.1959 13.853 23.5479 13.68C23.7388 13.5894 23.9364 13.5135 24.1389 13.453C24.3402 13.3801 24.5602 13.3768 24.7636 13.4437C24.967 13.5106 25.1421 13.6438 25.2609 13.822C25.5329 14.227 25.7679 14.6559 26.0189 15.0749V15.4809C25.9017 15.8081 25.6767 16.0856 25.3809 16.268C25.1859 16.402 25.0039 16.553 24.8169 16.697C24.7477 16.7357 24.6904 16.7927 24.6512 16.8617C24.6121 16.9306 24.5925 17.009 24.5947 17.0883C24.5968 17.1676 24.6206 17.2448 24.6635 17.3115C24.7064 17.3783 24.7667 17.432 24.8379 17.4669C25.0089 17.5899 25.1749 17.722 25.3529 17.835C25.6624 18.0123 25.8984 18.2942 26.0189 18.63L26.0079 19.047ZM22.5739 17.215C22.5828 16.8154 22.4735 16.422 22.2596 16.0844C22.0458 15.7467 21.7369 15.4798 21.3718 15.3171C21.0067 15.1544 20.6017 15.1032 20.2076 15.17C19.8136 15.2367 19.448 15.4184 19.1569 15.6923C18.8657 15.9661 18.662 16.3199 18.5713 16.7092C18.4806 17.0985 18.5069 17.5058 18.647 17.8802C18.787 18.2545 19.0346 18.5791 19.3586 18.8132C19.6825 19.0473 20.0685 19.1805 20.4679 19.1959C20.7363 19.2058 21.0039 19.1624 21.2554 19.0682C21.5068 18.974 21.7371 18.8309 21.933 18.6472C22.1288 18.4634 22.2863 18.2427 22.3963 17.9977C22.5063 17.7527 22.5666 17.4884 22.5739 17.22V17.215Z" fill="#1E2327"/>
                        </svg>
                    </span>
                    General Settings',
            'gateway'=>array(
                'label'=>'<span class="icon">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 7.71253e-06C8.08624 -0.00344226 5.29043 1.15067 3.22756 3.20848C1.16469 5.26629 0.00371077 8.05925 0 10.973V11C0 13.1756 0.645139 15.3023 1.85383 17.1113C3.06253 18.9202 4.78049 20.3301 6.79048 21.1627C8.80047 21.9952 11.0122 22.2131 13.146 21.7886C15.2798 21.3642 17.2398 20.3166 18.7782 18.7782C20.3166 17.2398 21.3642 15.2798 21.7886 13.146C22.2131 11.0122 21.9952 8.80048 21.1627 6.79049C20.3301 4.7805 18.9202 3.06254 17.1113 1.85384C15.3023 0.645146 13.1756 7.71253e-06 11 7.71253e-06ZM11 19.068C8.8665 19.0741 6.81795 18.2325 5.30493 16.7283C3.79192 15.2241 2.93836 13.1805 2.932 11.047V11C2.92616 8.86659 3.76785 6.81819 5.27199 5.30523C6.77614 3.79227 8.81959 2.93863 10.953 2.93201H11C13.1335 2.9259 15.1821 3.7675 16.6951 5.27171C18.2081 6.77591 19.0616 8.81951 19.068 10.953V11C19.0712 12.0564 18.8662 13.1032 18.4648 14.0804C18.0634 15.0576 17.4734 15.9462 16.7286 16.6954C15.9838 17.4446 15.0986 18.0397 14.1238 18.4468C13.1489 18.8539 12.1034 19.065 11.047 19.068H11ZM15.987 8.28801C15.9868 8.73748 15.8534 9.1768 15.6035 9.55044C15.3537 9.92408 14.9987 10.2153 14.5834 10.3872C14.1681 10.5591 13.7111 10.604 13.2703 10.5163C12.8295 10.4285 12.4246 10.2121 12.1067 9.89426C11.7889 9.57644 11.5725 9.17152 11.4847 8.73069C11.397 8.28987 11.4419 7.83293 11.6138 7.41763C11.7857 7.00233 12.0769 6.64733 12.4506 6.39749C12.8242 6.14766 13.2635 6.01421 13.713 6.01401C14.3152 6.0169 14.8919 6.25741 15.3178 6.68324C15.7436 7.10907 15.9841 7.6858 15.987 8.28801ZM15.987 13.714C15.9872 14.1636 15.8541 14.6031 15.6045 14.977C15.3549 15.3509 15 15.6424 14.5848 15.8146C14.1695 15.9868 13.7125 16.032 13.2715 15.9445C12.8306 15.857 12.4255 15.6407 12.1075 15.323C11.7894 15.0052 11.5728 14.6003 11.4849 14.1594C11.397 13.7185 11.4418 13.2615 11.6136 12.8461C11.7855 12.4307 12.0767 12.0756 12.4504 11.8256C12.824 11.5757 13.2634 11.4422 13.713 11.442C14.3147 11.4449 14.891 11.685 15.3167 12.1102C15.7425 12.5354 15.9833 13.1123 15.987 13.714ZM10.561 13.714C10.5612 14.1636 10.428 14.6032 10.1783 14.9772C9.92866 15.3511 9.57368 15.6426 9.1583 15.8148C8.74292 15.987 8.28582 16.0321 7.84481 15.9444C7.40379 15.8567 6.99869 15.6402 6.68075 15.3223C6.3628 15.0043 6.1463 14.5992 6.05863 14.1582C5.97095 13.7172 6.01605 13.2601 6.18821 12.8447C6.36038 12.4293 6.65187 12.0743 7.02581 11.8247C7.39976 11.575 7.83936 11.4418 8.289 11.442C8.89035 11.4454 9.46611 11.6857 9.89143 12.1109C10.3167 12.536 10.5573 13.1127 10.561 13.714ZM10.561 8.28801C10.5606 8.73748 10.427 9.17674 10.177 9.55027C9.92696 9.9238 9.57183 10.2148 9.15645 10.3866C8.74108 10.5583 8.28412 10.603 7.84334 10.5151C7.40255 10.4272 6.99773 10.2105 6.68004 9.89255C6.36236 9.57459 6.14607 9.16958 6.05853 8.72871C5.97099 8.28785 6.01612 7.83093 6.18821 7.41571C6.36031 7.00049 6.65165 6.64561 7.0254 6.39594C7.39915 6.14627 7.83853 6.01301 8.288 6.01301C8.89029 6.0159 9.46706 6.25658 9.89276 6.68266C10.3185 7.10874 10.5586 7.68571 10.561 8.28801Z" fill="#1E2327"/>
                        </svg>
                    </span>
                    SMS Gateway',
                'configuration'=>'Configuration'
            ),
        );
        $this->tabs = \apply_filters('uws_settings_tabs',$this->tabs);
    }
    private function general_fields($values){
        $fields = array(
            'basic'=>array(
                'label'=>'Basic',
                'fields'=>array(
                    array(
                        'label'=>'Your Country Code',
                        'value'=>$values['uws-country-code']??"",
                        'helper_text'=>'Select the country you are in, so UWS can convert your number into an international format.',
                        'type'=>'select',
                        'name'=>'uws-country-code',
                        'id'=>'uws-country-code',
                        'options'=>$this->uws->get_country_codes()
                    ),
                    array(
                        'label'=>'Enter Admin Number',
                        'value'=>$values['uws-admin-number']??"",
                        'helper_text'=>'Enter Number to which you want to recieve Notifications.',
                        'type'=>'input',
                        'name'=>'uws-admin-number',
                        'id'=>'uws-admin-number',
                    ),
                )
            ),
            'admin'=>array(
                'label'=>'Admin Settings',
                'fields'=>array(
                    array(
                        'label'=>'Debugging',
                        'helper_text'=>'Enabling debugging will write logs to /ultimate-wp-sms-lite/log/{gateway}-{date}.txt <br/>WARNING: Enabling debugging is useful if you intend to raise a support call, however a large amount of data, which may contain sensitive information, such as phone numbers, is written to the log, therefore it is recommended that debugging is only enabled if you have raised a support call. When debugging is disabled, the log file will be deleted.',
                        'type'=>'checkbox',
                        'value'=>$values['uws-enable-debug']??"",
                        'name'=>'uws-enable-debug',
                        'id'=>'uws-enable-debug',
                        'options'=>array(
                            'yes'=>'Debugging enabled?'
                        ),
                    ),
                )
            )
        );
        return $fields;
    }
    private function configuration_fields($values){
        $gateway = $this->uws->smsGateway;
        $fields = array(
            'basic'=>array(
                'label'=>'Gateway Configuration',
                'fields'=>array(
                    array(
                        'label'=>'Choose the Gateway',
                        'value'=>$values['uws-gateway']??"",
                        'helper_text'=>'Select the Gateway you want to send the notifications From.',
                        'type'=>'select',
                        'name'=>'uws-gateway',
                        'id'=>'uws-gateway',
                        'options'=>$this->uws->smsGatewayList
                    ),
                )
            ),
            'overview'=>array(
                'label'=>'Gateway Overview',
                'fields'=>array(
                    array(
                        'label'=>'Balance / Credit',
                        'value'=>$values['uws-gateway-credit']??"",
                        'helper_text'=>'Current balance in your sms gateway account.',
                        'type'=>'text',
                        'name'=>'uws-gateway-credit',
                        'id'=>'uws-gateway-credit',
                    ),
                )
            ),
            'balance'=>array(
                'label'=>'Account Balance',
                'fields'=>array(
                    array(
                        'label'=>'',
                        'value'=>$values['uws-top-bar-balance']??"",
                        'helper_text'=>'',
                        'type'=>'checkbox',
                        'name'=>'uws-top-bar-balance',
                        'id'=>'uws-top-bar-balance',
                        'options'=>array(
                            'yes'=>'Show in Top Bar
                            <div class="tooltip_wrap">
                                <i class="icon"><img src="'.UWS_URL.'images/tooltip.svg" alt="" /></i>
                                <div class="tooltip_content">
                                    Show your account credit in admin Top Bar.
                                </div>
                            </div>'
                        )
                    ),
                    array(
                        'label'=>'',
                        'value'=>$values['uws-send-screen-balance']??"",
                        'helper_text'=>'',
                        'type'=>'checkbox',
                        'name'=>'uws-send-balance',
                        'id'=>'uws-send-balance',
                        'options'=>array(
                            'yes'=>'Show in send SMS page
                            <div class="tooltip_wrap">
                                <i class="icon"><img src="'.UWS_URL.'images/tooltip.svg" alt="" /></i>
                                <div class="tooltip_content">
                                    Show your account credit in send SMS page.
                                </div>
                            </div>'
                        )
                    ),
                )
            )
        );
        return $fields;
    }
}