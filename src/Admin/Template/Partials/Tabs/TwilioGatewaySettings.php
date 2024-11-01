<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Tabs;
use UWS\LITE\SMS\Admin\Settings;
class TwilioGatewaySettings{
    private $settings;
    public function __construct($args){ 
        $this->settings = new Settings();
        $this->html($args);
    }
    private function html($args){ ?>
        <div id="twilio_gateway_settings" class="tabcontent">
            <form id="twilio_gateway" class="setting_from">
                <input type="hidden" name="type" value="<?php echo esc_attr($args['hidden_type']); ?>">
                <?php 
                    foreach($args['fields'] as $field):
                        $this->settings->render_row($field);
                    endforeach;
                ?>
                <div class="action_btn_wrapper">
                    <button class="btn primary_btn mr-10">
                        <i class="icon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.413 3.47902L12.433 0.499024C12.2749 0.340322 12.0869 0.214447 11.8799 0.128639C11.673 0.0428311 11.4511 -0.00121801 11.227 -0.000975567H1.70001C1.47626 -0.000976961 1.25471 0.0431928 1.04806 0.129001C0.841414 0.214809 0.653741 0.340567 0.495803 0.499062C0.337865 0.657558 0.212769 0.845673 0.127689 1.05262C0.0426099 1.25957 -0.000779127 1.48127 1.05892e-05 1.70502V14.205C1.05892e-05 14.6559 0.179117 15.0883 0.497929 15.4071C0.816741 15.7259 1.24914 15.905 1.70001 15.905H14.2C14.6509 15.905 15.0833 15.7259 15.4021 15.4071C15.7209 15.0883 15.9 14.6559 15.9 14.205V4.68402C15.9003 4.45997 15.8562 4.23808 15.7704 4.03111C15.6846 3.82414 15.5587 3.63618 15.4 3.47802L15.413 3.47902ZM7.95601 13.639C7.50645 13.639 7.06699 13.5057 6.6932 13.256C6.31941 13.0062 6.02807 12.6512 5.85603 12.2359C5.68399 11.8205 5.63898 11.3635 5.72669 10.9226C5.81439 10.4817 6.03087 10.0767 6.34876 9.75877C6.66664 9.44089 7.07165 9.2244 7.51257 9.1367C7.95349 9.049 8.41051 9.09401 8.82585 9.26605C9.24119 9.43808 9.59618 9.72942 9.84594 10.1032C10.0957 10.477 10.229 10.9165 10.229 11.366C10.229 11.6645 10.1702 11.9601 10.056 12.2359C9.94176 12.5116 9.77433 12.7622 9.56326 12.9733C9.3522 13.1843 9.10162 13.3518 8.82585 13.466C8.55008 13.5802 8.2545 13.639 7.95601 13.639ZM11.366 2.82302V6.39302C11.366 6.50601 11.3211 6.61436 11.2412 6.69425C11.1613 6.77414 11.053 6.81902 10.94 6.81902H2.70001C2.58703 6.81902 2.47867 6.77414 2.39878 6.69425C2.31889 6.61436 2.27401 6.50601 2.27401 6.39302V2.69902C2.27401 2.58604 2.31889 2.47769 2.39878 2.3978C2.47867 2.31791 2.58703 2.27302 2.70001 2.27302H10.817C10.9296 2.27341 11.0375 2.31835 11.117 2.39802L11.241 2.52202C11.3207 2.60158 11.3656 2.71043 11.366 2.82302Z" fill="white"/>
                            </svg>
                        </i>
                        <?php esc_html_e( "Save Settings","ultimate-wp-sms"); ?>
                    </button>
                </div>
            </form>
        </div>
    <?php }
}