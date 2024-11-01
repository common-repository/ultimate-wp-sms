<?php
namespace UWS\LITE\SMS\Admin\Template;
use UWS\LITE\SMS\Admin\GroupManager;
use UWS\LITE\SMS\Admin\Template\Partials\Placeholder\SendSmsPlaceholder;
class SendSmsTemplate{
    
    private $fields,$uws;
    public function __construct($args) {
        global $uws;
        $this->uws = $uws;
        $this->get_fields($args);
        $this->html($args);
    }
    private function get_fields($args){
        $this->fields = array(
            'uws-message-type'=>array(   
                'label'=>'Send Message as',
                'name'=>'uws-message-type',
                'type'=>'radio',
                'value'=>'uws-sms',
                'class'=>'mesaage-types',
                'option'=>array(
                        array(
                            'label'=>'<i class="icon">
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.8 0H1.2C0.882781 0.00158611 0.579095 0.128714 0.355348 0.353586C0.1316 0.578458 0.00599608 0.882777 0.00600004 1.2L0 12L2.4 9.6H10.8C11.1183 9.6 11.4235 9.47357 11.6485 9.24853C11.8736 9.02348 12 8.71826 12 8.4V1.2C12 0.88174 11.8736 0.576516 11.6485 0.351472C11.4235 0.126428 11.1183 0 10.8 0ZM4.2 5.4H3V4.2H4.2V5.4ZM6.6 5.4H5.4V4.2H6.6V5.4ZM9 5.4H7.8V4.2H9V5.4Z" fill="#3481C4"/> 
                                        </svg>
                                    </i>
                                    <span>SMS</span>',
                            'value'=>'uws-sms',
                            'disabled'=>false,
                        ),
                        array(
                            'label'=>'<i class="icon">
                                        <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.889648 12.013V10.75C0.889648 9.071 4.51765 8.224 6.33465 8.224C8.15165 8.224 11.7796 9.071 11.7796 10.75V12.014L0.889648 12.013ZM12.3826 7.813C12.8307 7.36913 13.1864 6.84087 13.4291 6.25875C13.6718 5.67664 13.7968 5.05219 13.7968 4.4215C13.7968 3.79081 13.6718 3.16636 13.4291 2.58425C13.1864 2.00213 12.8307 1.47387 12.3826 1.03L13.4926 0C14.1193 0.553639 14.6211 1.23415 14.9648 1.99643C15.3085 2.7587 15.4862 3.58533 15.4862 4.4215C15.4862 5.25767 15.3085 6.0843 14.9648 6.84657C14.6211 7.60885 14.1193 8.28936 13.4926 8.843L12.3826 7.813ZM4.01965 4.437C4.01965 3.97202 4.15753 3.51747 4.41586 3.13085C4.67419 2.74423 5.04137 2.4429 5.47096 2.26496C5.90055 2.08702 6.37326 2.04046 6.82931 2.13117C7.28536 2.22189 7.70426 2.4458 8.03306 2.77459C8.36185 3.10339 8.58576 3.52229 8.67647 3.97834C8.76719 4.43439 8.72063 4.9071 8.54269 5.33669C8.36475 5.76628 8.06341 6.13345 7.67679 6.39179C7.29017 6.65012 6.83563 6.788 6.37065 6.788C5.74712 6.788 5.14914 6.54031 4.70824 6.09941C4.26734 5.65851 4.01965 5.06052 4.01965 4.437ZM10.0996 5.647C10.3777 5.29789 10.5291 4.86479 10.5291 4.4185C10.5291 3.97221 10.3777 3.53911 10.0996 3.19L11.2396 2.122C11.5639 2.41021 11.8234 2.76383 12.0011 3.15956C12.1788 3.5553 12.2707 3.98419 12.2707 4.418C12.2707 4.85181 12.1788 5.2807 12.0011 5.67643C11.8234 6.07217 11.5639 6.42579 11.2396 6.714L10.0996 5.647Z" fill="#3481C4"/>
                                        </svg>                                                         
                                    </i>
                                    <span>A text-to-voice call/audio file</span><span class="uws-pro">Available In Pro</span>',
                            'value'=>'uws-call',
                            'disabled'=>true,
                        ),
                        array(
                            'label'=>'<i class="icon">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.8 0H1.2C0.882781 0.00158611 0.579095 0.128714 0.355348 0.353586C0.1316 0.578458 0.00599608 0.882777 0.00600004 1.2L0 12L2.4 9.6H10.8C11.1183 9.6 11.4235 9.47357 11.6485 9.24853C11.8736 9.02348 12 8.71826 12 8.4V1.2C12 0.88174 11.8736 0.576516 11.6485 0.351472C11.4235 0.126428 11.1183 0 10.8 0ZM1.8 7.2L3.9 4.5L5.4 6.306L7.5 3.6L10.2 7.2H1.8Z" fill="#3481C4"/>
                                            </svg>                                                         
                                    </i>
                                    <span>MMS</span><span class="uws-pro">Available In Pro</span>',
                            'value'=>'uws-mms',
                            'disabled'=>true,
                        ),
                    )
                ),
                'uws-reciptant-number'=>array(   
                    'label'=>'Select Number',
                    'name'=>'uws-reciptant-number[]',
                    'id'=>'uws-reciptant-number',
                    'type'=>'select',
                    'class'=>'ctsm-selctpicker groupselect_wrapper',
                    'value'=>'uws-sms',
                    'input_class'=>'groupselect_wrapper uws_select',
                    'placeholder'=>'<span class="helper_text">You can select multiple Numbers.</span>',
                    'args'=>array(
                        'multiple'=>'multiple',
                        'placeholder'=>'Select Number'
                    ),
                    'option'=>$this->get_members_options(),
                    'before'=>'<h5 class="form-title mb-4">Send To</h5>'
                ),
                'uws-reciptant-custom-number'=>array(   
                    'label'=>'Enter Custom Number',
                    'name'=>'uws-reciptant-custom-number',
                    'class'=>'input_wrap',
                    'type'=>'textarea',
                    'value'=>'',
                    'placeholder'=>'<span class="helper_text">Please input numbers separated by commas or enter each number on a new line.</span>',
                    'args'=>array(
                        'rows'=>'8',
                    ),
                ),
                'uws-message'=>array(   
                    'label'=>'Enter Your Message',
                    'name'=>'uws-message',
                    'type'=>'textarea',
                    'value'=>get_option('uws_last_text',""),
                    'class'=>'input_wrap',
                    'args'=>array(
                        "rows"=>"4"
                    ),
                    'placeholder'=>'<span class="helper_text">(You can include <a href="https://ultimatewpsms.com/knowledge-base/ultimate-wp-sms-supported-merge-tags/" target="_blank">merge tags</a> like %firstname%, %lastname%, %number% into your message.) (<span id="uws-used-letter"></span>)(Units:<span id="uws-used-unit"></span>/4) (1 Unit = Max 160 Characters. You can send up to 640 Characters.)</span>',
                    'after'=>''
                ),
            );
            $this->fields = apply_filters( 'uws_send_message_feilds', $this->fields, $args );
    }
    private function html($args){ 
        new Header(); ?>
        <div class="cs_app_main">
            <?php new Sidebar($args); ?>
            <div class="cs_app_main_outer ">
                <div class="send_message_wrapper">
                    <div class="left_wraper">
                        <h4 class="heading2 mb-45"><?php esc_html_e("Send New Message","ultimate-wp-sms"); ?></h4>
                        <div class="white_bg_wrapper">
                            <?php new SendSmsPlaceholder(); ?>
                            <h5 class="form-title mb-30"><?php esc_html_e("Message Details","ultimate-wp-sms"); ?></h5>    
                            <form id="uws_send_message">
                                <div class="form_fields">
                                    <?php foreach($this->fields as $field):
                                        $this->row($field);
                                    endforeach; ?>
                                    <div class="form-group uws_hide">
                                        <div class="radio_listing">
                                            <ul>
                                                <li>
                                                    <input type="checkbox" name="uws-message-service" />
                                                    <span class="radio_icon"></span>
                                                    <label><?php esc_html_e("Using Messaging Services (%s)","ultimate-wp-sms"); ?></label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="action_btn_wrapper">
                                        <button class="btn primary_btn mr-10">
                                            <i class="icon">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.8021 0.104978C15.9324 0.0285395 16.0824 -0.00751368 16.2332 0.00138731C16.384 0.0102883 16.5287 0.0637422 16.6491 0.154978C16.779 0.23482 16.8817 0.351963 16.944 0.491111C17.0062 0.63026 17.0251 0.784948 16.9981 0.934978L14.6111 15.28C14.5896 15.3995 14.5411 15.5124 14.4691 15.6102C14.3972 15.708 14.3038 15.7879 14.1961 15.844C14.0958 15.9089 13.9802 15.9464 13.8609 15.9528C13.7416 15.9591 13.6227 15.934 13.5161 15.88L9.3651 14.15L7.27309 16.706C7.20744 16.7989 7.12037 16.8747 7.01924 16.927C6.91812 16.9792 6.80591 17.0063 6.69209 17.006C6.47216 17.0113 6.25816 16.9343 6.09209 16.79C6.00745 16.7127 5.94116 16.6174 5.89808 16.5112C5.85499 16.4049 5.83621 16.2904 5.84309 16.176V13.519L13.8431 3.75698C13.8591 3.73923 13.871 3.71823 13.8781 3.69542C13.8852 3.67261 13.8872 3.64854 13.8841 3.62486C13.8809 3.60118 13.8727 3.57847 13.8599 3.55829C13.8472 3.53811 13.8302 3.52095 13.8101 3.50798C13.7767 3.47103 13.7302 3.44858 13.6806 3.44541C13.6309 3.44223 13.5819 3.45858 13.5441 3.49098L4.01509 11.891L0.502095 10.431C0.358177 10.3779 0.233546 10.2827 0.144384 10.1579C0.0552214 10.0331 0.00564626 9.88434 0.00209485 9.73098C-0.0142148 9.57899 0.0190339 9.4258 0.0968839 9.29425C0.174734 9.1627 0.293016 9.05983 0.434095 9.00098L15.8021 0.104978Z" fill="white"/>
                                                </svg>
                                            </i>
                                            <span id="send_btn_text"><?php esc_html_e("Send Your Message","ultimate-wp-sms"); ?></span>
                                        </button>
                                        <button class="btn secondary_btn" type="button" id="reset_form">
                                            <i class="icon">
                                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.631 0.417009L14.764 3.16501C13.1249 1.43351 10.8847 0.395732 8.504 0.26501H8.494C6.57697 0.265712 4.72028 0.935369 3.24414 2.15849C1.768 3.38161 0.764955 5.0815 0.408 6.96501C0.403418 6.98943 0.400743 7.01417 0.4 7.03901C0.4 7.1451 0.442143 7.24684 0.517157 7.32185C0.592172 7.39687 0.693913 7.43901 0.8 7.43901H2.428C2.51676 7.43559 2.60212 7.40399 2.6717 7.34879C2.74129 7.29359 2.79148 7.21766 2.815 7.13201C3.16544 5.88103 3.90421 4.7737 4.92472 3.96977C5.94523 3.16583 7.19474 2.70684 8.493 2.65901C9.43996 2.71152 10.3641 2.9697 11.2012 3.4156C12.0382 3.8615 12.7681 4.48442 13.34 5.24101L9.973 5.07501H9.954C9.84791 5.07501 9.74617 5.11715 9.67116 5.19217C9.59614 5.26718 9.554 5.36892 9.554 5.47501V7.04901C9.554 7.1551 9.59614 7.25684 9.67116 7.33185C9.74617 7.40687 9.84791 7.44901 9.954 7.44901H16.6C16.7061 7.44901 16.8078 7.40687 16.8828 7.33185C16.9579 7.25684 17 7.1551 17 7.04901V0.39701C17 0.290923 16.9579 0.189182 16.8828 0.114167C16.8078 0.0391527 16.7061 -0.00299072 16.6 -0.00299072H15.029C14.9229 -0.00299072 14.8212 0.0391527 14.7462 0.114167C14.6711 0.189182 14.629 0.290923 14.629 0.39701C14.631 0.40401 14.631 0.412009 14.631 0.417009ZM8.494 14.344C7.54676 14.2923 6.62225 14.0343 5.78506 13.5881C4.94786 13.142 4.21818 12.5184 3.647 11.761L7.027 11.923H7.046C7.15209 11.923 7.25383 11.8809 7.32884 11.8059C7.40386 11.7308 7.446 11.6291 7.446 11.523V9.95001C7.446 9.84392 7.40386 9.74218 7.32884 9.66717C7.25383 9.59215 7.15209 9.55001 7.046 9.55001H0.4C0.293913 9.55001 0.192172 9.59215 0.117157 9.66717C0.0421427 9.74218 0 9.84392 0 9.95001V16.602C0 16.6545 0.0103463 16.7066 0.0304482 16.7551C0.0505501 16.8036 0.0800139 16.8477 0.117157 16.8849C0.154301 16.922 0.198396 16.9515 0.246927 16.9716C0.295457 16.9917 0.347471 17.002 0.4 17.002H1.971C2.07709 17.002 2.17883 16.9599 2.25384 16.8849C2.32886 16.8098 2.371 16.7081 2.371 16.602C2.371 16.596 2.371 16.588 2.371 16.582L2.231 13.84C3.87017 15.5683 6.10858 16.6038 8.487 16.734H8.495C10.4122 16.7335 12.2691 16.064 13.7455 14.8409C15.2218 13.6177 16.225 11.9177 16.582 10.034C16.5861 10.0102 16.5885 9.98615 16.589 9.96201C16.589 9.90948 16.5787 9.85747 16.5586 9.80894C16.5384 9.76041 16.509 9.71631 16.4718 9.67917C16.4347 9.64202 16.3906 9.61256 16.3421 9.59246C16.2935 9.57236 16.2415 9.56201 16.189 9.56201H14.561C14.4722 9.56543 14.3869 9.59703 14.3173 9.65223C14.2477 9.70743 14.1975 9.78336 14.174 9.86901C13.8245 11.12 13.0866 12.2275 12.0668 13.0319C11.047 13.8362 9.79795 14.2957 8.5 14.344H8.494Z" fill="#3481C4"/>
                                                </svg>  
                                            </i>
                                            <?php esc_html_e("Reset","ultimate-wp-sms"); ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="phone_wraper">
                        <div class="top-content_ui">
                            <label class="tips_icon">
                                <i class="icon"><img src="<?php echo esc_url(sprintf("%simages/icons/tips_icon.png",UWS_URL)); ?>" alt="" /></i> Tips:
                            </label>
                            <p><?php esc_html_e("This is just a preview final message might be different from this.","ultimate-wp-sms"); ?></p>
                        </div>
                        <div class="phone_image_wrap">
                            <div class="phone_inner_wrap">
                                <div class="scroll_wrap">
                                    <div class="top_content_wraper">
                                        <div class="message_card">
                                            <p></p>
                                            <div id="mms_imgs_preview"></div>
                                            <span class="timer_ui"><?php echo date('m-d-Y H:i:s') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    private function get_members_options(){
        $members = [];
        $group_manager = new GroupManager();
        $member_list = $group_manager->get_all_members();
        foreach($member_list as $member){
           array_push( $members,array('value'=>$member->grpmemnum,'label'=>$member->grpmemname.'('.$member->grpmemnum.')'));
        }
        $all_members = array('Members'=>$members);
        return $all_members;
    }
    private function row($row_args){ ?>
        <?php echo wp_kses($row_args['before']??"",array(
                'div' => array(
                    'class' => array()
                ),
                'h5' => array(
                    'class' => array()
                )
            )); ?>
        <div class="form-group send-message-wrap" id="<?php echo esc_attr($row_args['name']); ?>-row">
            <label class="form-label"><?php echo esc_html($row_args['label']); ?> 
                <?php echo wp_kses(
                    $row_args['placeholder']??"", 
                        array(
                            'a'=>array('href'=>array(),'class'=>array(),'id'=>array(),'target'=>array()),
                            'span'=>array('class'=>array(),'id'=>array())
                        )
                    ); ?>
            </label>
            
            <div class="<?php echo esc_attr($row_args['class']); ?>">
                <?php $function_name = 'field_'.$row_args['type'];
                if(method_exists($this,$function_name)):
                $this->$function_name($row_args);
                endif; ?>
            </div>
        </div>
        <?php echo wp_kses($row_args['after']??"",array(
                'div' => array(
                    'id' => array(),
                    'class' => array(),
                ),
                'audio' => array(
                    'id' => array(),
                    'class' => array(),
                    'controls' => array(),
                    'autoplay' => array(),
                ),
                'source' => array(
                    'src' => array(),
                    'type' => array(),
                    'id' => array(),
                    'class' => array(),
                ),
                'h5' => array(
                    'id' => array(),
                    'class' => array(),
                )
            )); ?>
    <?php }
    private function field_radio($field_args){ 
        foreach($field_args['option'] as $input): 
            $checked = '';
            if($input['value']==$field_args['value']):
                $checked = 'checked=""';
            endif;
        ?>
        <div class="radio-card">
            <input type="radio" name="<?php echo esc_attr($field_args['name']); ?>" value="<?php echo esc_attr($input['value']); ?>" <?php echo esc_attr($checked); ?> <?php if($input['disabled']):?> disabled <?php endif; ?>/>
            <label>
            <?php echo wp_kses($input['label'],
                array(
                    'i'      => array(
                        'class'  => array(),
                    ),
                    'span'      => array(
                        'class'  => array(),
                    ),
                    'svg'     => array("width"=>array(), "height"=>array(), "viewBox"=>array(), "fill"=>array(), "xmlns"=>array()),
                    'path'     => array('d'=>array(),'fill'=>array()),
                )); ?>
            </label>
        </div>
        <?php endforeach; ?>
    <?php }
    
    private function field_checkbox($field_args){ 
        foreach($field_args['option'] as $input): 
            $checked = '';
            if($input['value']==$field_args['value']):
                $checked = 'checked=""';
            endif;
        ?>
        <div class="radio-card">
            <input type="radio" name="<?php echo esc_attr($field_args['name']); ?>[]" value="" <?php echo esc_attr($checked); ?> />
            <label>
                <?php echo esc_html($input['label']); ?>
            </label>
        </div>
        <?php endforeach; ?>
    <?php }
    private function field_select($field_args){ ?>
        <select id="<?php echo esc_attr($field_args['id']??$field_args['name']); ?>" name="<?php echo esc_attr($field_args['name']); ?>" class="<?php echo esc_attr($field_args['input_class']??""); ?>"
        <?php if(!empty($field_args['args'])){
            foreach($field_args['args'] as $args_key => $args):
                echo esc_attr($args_key).'="'.esc_attr($args).'"';
            endforeach; 
        } ?>
        >
            <?php foreach($field_args['option'] as $option_value => $option_label): ?>
                <?php if(isset($option_label[0]) && is_array($option_label[0])): ?>
                    <optgroup label="<?php echo esc_attr( $option_value) ?>" data-selectall="true" data-selectalltext="Select all!">
                    <?php foreach($option_label as $opt_Val => $opt): ?>
                        <option value="<?php echo esc_attr( $opt['value']) ?>"><?php echo esc_html(  $opt['label']) ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                <?php elseif(!empty($option_label)): ?>
                    <option value="<?php echo esc_attr( $option_label['value']) ?>"><?php echo esc_html( $option_label['label']) ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    <?php }
    private function field_input($field_args){ ?>
        <input type="text" id="<?php echo esc_attr($field_args['name']); ?>" name="<?php echo esc_attr($field_args['name']); ?>" class="<?php echo esc_attr($field_args['input_class']??""); ?>"
        <?php if(isset($field_args['args']) && is_array($field_args['args'])): 
            foreach($field_args['args'] as $args_key => $args):
                echo esc_attr($args_key).'="'.esc_attr($args).'"';
            endforeach; 
        endif; ?>
        >
    <?php }
    private function field_textarea($field_args){ ?>
        <textarea id="<?php echo esc_attr($field_args['name']); ?>" name="<?php echo esc_attr($field_args['name']); ?>" class="<?php echo esc_attr($field_args['input_class']??""); ?>"
        <?php if(isset($field_args['args']) && is_array($field_args['args'])): 
            foreach($field_args['args'] as $args_key => $args):
                echo esc_attr($args_key).'="'.esc_attr($args).'"';
            endforeach; 
        endif; ?>
        ><?php echo esc_html( stripslashes($field_args['value'])); ?></textarea>
    <?php }
    private function field_media($field_args){ ?>
        <button class="btn primary_btn mr-10" id="<?php echo esc_attr($field_args['name']); ?>" type="button"><?php echo esc_html($field_args['label']); ?></button>
        <input type="hidden" id="<?php echo esc_attr($field_args['name']); ?>-hidden" name="<?php echo esc_attr($field_args['name']); ?>">
    <?php }
}