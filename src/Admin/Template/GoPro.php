<?php

namespace UWS\LITE\SMS\Admin\Template;
use UWS\LITE\SMS\Admin\GroupManager;
class GoPro{
    
    private $fields,$uws;
    public function __construct($args) {
        $this->html($args);
    }

    private function html($args){ 
        new Header(); ?>
        <div class="cs_app_main">
            <?php new Sidebar($args); ?>
            <div class="cs_app_main_outer ">
                <div class="">
                    <div class="left_wraper">
                        <h4 class="heading2 mb-45">Checkout the Ultimate WP SMS Pro and its fab features including :</h4>
                        <div class="white_bg_wrapper">
                            <div class="add_ons_box mb-30">
                                <div class="add_ons_icon">
                                    <img src="http://vinayweb.tech/wp-content/uploads/2023/07/message-scheduler.png" alt="img" />
                                </div>
                                <div class="add_ons_uws">
                                    <h3>Ultimate WP SMS PRO</h3>
                                    <p>Experience the power of SMS, MMS, and Voice Broadcast combined in one versatile plugin. Choose Ultimate WP SMS to elevate your communication, engage your audience, and drive meaningful interactions that help you achieve your marketing goals.</p>
                                </div>
                                <div class="add_ons_btns">
                                    <p class="blue-text">$89.00 / Year</p>

                                    <a class="common_btn" target="_blank" href="https://ultimatewpsms.com/ultimate-wp-sms/"><img src="http://vinayweb.tech/wp-content/uploads/2023/07/icon_purchase.png" alt="img" /> Purchase Pro</a>
                                </div>
                            </div>   
                            <div class="add_ons">
                                <div class="inner_common_heading">
                                    <h5 class="form-title mb-30">Ultimate WP SMS Pro Add-ons</h5>
                                </div>
                                <div class="add_ons_box">
                                    <div class="add_ons_icon">
                                        <img src="http://vinayweb.tech/wp-content/uploads/2023/07/message-scheduler.png" alt="img" />
                                    </div>
                                    <div class="add_ons_uws">
                                        <h3>UWS Message Scheduler Extension</h3>
                                        <p>The UWS Scheduler, also known as the Ultimate WP SMS Scheduler Extension, enables you to conveniently schedule messages to be sent at a later date and time. This feature is particularly useful when you need to send recurring batches of messages or when you prefer to schedule messages overnight, freeing you from the need to remain in front of your computer.</p>
                                    </div>
                                    <div class="add_ons_btns">
                                        <p class="blue-text">$39.00 / Year</p>

                                        <a class="common_btn" target="_blank" href="https://ultimatewpsms.com/uws-scheduler-extension/"><img src="http://vinayweb.tech/wp-content/uploads/2023/07/icon_purchase.png" alt="img" /> Purchase Add-on</a>
                                    </div>
                                </div>
                                <div class="add_ons_box">
                                    <div class="add_ons_icon">
                                        <img src="http://vinayweb.tech/wp-content/uploads/2023/07/message-scheduler.png" alt="img" />
                                    </div>
                                    <div class="add_ons_uws">
                                        <h3>UWS WooCommerce Subscription Extension</h3>
                                        <p>The UWS WooCommerce Subscription, also known as the Ultimate WP SMS WooCommerce Subscription Extension, enables you to create group for subscribed user. Send SMS notifications on different subscription status.</p>
                                    </div>
                                    <div class="add_ons_btns">
                                        <p class="blue-text">$29.00 / Year</p>

                                        <a class="common_btn" target="_blank" href="https://ultimatewpsms.com/uws-woocommerce-subscription-integration/"><img src="http://vinayweb.tech/wp-content/uploads/2023/07/icon_purchase.png" alt="img" /> Purchase Add-on</a>
                                    </div>
                                </div>
                                <div class="add_ons_box">
                                    <div class="add_ons_icon">
                                        <img src="http://vinayweb.tech/wp-content/uploads/2023/07/message-scheduler.png" alt="img" />
                                    </div>
                                    <div class="add_ons_uws">
                                        <h3>UWS Ultimate Members Extension</h3>
                                        <p>The UWS Ultimate Members, also known as the Ultimate WP SMS Ultimate Members Extension, enables you to verify user phone number on register. Send notification on approval, rejection and other action.</p>
                                    </div>
                                    <div class="add_ons_btns">
                                        <p class="blue-text">$29.00 / Year</p>

                                        <a class="common_btn" target="_blank" href="https://ultimatewpsms.com/uws-ultimate-member-extension/"><img src="http://vinayweb.tech/wp-content/uploads/2023/07/icon_purchase.png" alt="img" /> Purchase Add-on</a>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery(window).load(function(){
                jQuery('#preloader').addClass('loading').fadeOut('slow');    
            });
        </script>
    <?php }
}