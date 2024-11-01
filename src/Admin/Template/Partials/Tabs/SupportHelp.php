<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Tabs;
class SupportHelp{
    public function __construct($args){ 
        $this->html($args);
    }
    private function html($args){ ?>
        <div id="support_help" class="support_help tabcontent">
            <div class="colrows">
                <div class="col-50">
                    <div class="help_content">
                        <h3>Welcome to Support Center</h3>
                        <p>Before submitting a support ticket, we kindly ask that you explore our extensive <a href="https://ultimatewpsms.tawk.help/" target="_blank"><span class="blue-text">Knowledge Base</span></a>, where you can find answers to common questions and troubleshooting guides.</p>
                        <p>Our Knowledge Base is designed to provide quick and comprehensive assistance, and it's often the fastest way to resolve your issue. We've put together a vast library of resources to help you make the most of our products and services.</p>
                    </div>
                </div>
                <div class="col-50">
                    <div class="help_image">
                        <img src="<?php echo esc_url(sprintf("%simages/help/img_support.svg",UWS_URL)); ?>" alt="" />
                    </div>
                </div>
            </div>
            <div class="help_getstarted">
                <h5>Ready to Get Started?</h5>
                <p>If you've checked our Knowledge Base and still need assistance, don't worry – we're here to help! Simply click the "Submit a Support Ticket" button below to fill out the form, and one of our support agents will get back to you as soon as possible. We're committed to providing you with exceptional service and resolving your inquiries promptly. Your satisfaction is our top priority.</p>
                <a class="common-btn" href="https://wordpress.org/support/plugin/ultimate-wp-sms/" target="_blank">
                    <svg width="10" height="17" viewBox="0 0 10 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.77558 8.53896L0.756576 2.52497C0.650843 2.41978 0.56694 2.29472 0.509687 2.15701C0.452434 2.01929 0.422961 1.87161 0.422961 1.72246C0.422961 1.57332 0.452434 1.42564 0.509687 1.28792C0.56694 1.1502 0.650843 1.02515 0.756576 0.919964C0.971044 0.708298 1.26025 0.589619 1.56158 0.589619C1.86291 0.589619 2.15211 0.708298 2.36658 0.919964L9.18658 7.73496C9.3928 7.94186 9.51131 8.22029 9.51746 8.51235C9.52361 8.8044 9.41691 9.08758 9.21958 9.30296L2.37158 16.164C2.26714 16.2739 2.14179 16.3617 2.00288 16.4225C1.86398 16.4832 1.71434 16.5155 1.56275 16.5176C1.41117 16.5196 1.2607 16.4914 1.12021 16.4344C0.979718 16.3774 0.852034 16.293 0.744671 16.1859C0.637308 16.0789 0.552433 15.9515 0.495037 15.8112C0.437642 15.6709 0.408887 15.5205 0.410462 15.3689C0.412037 15.2173 0.443911 15.0676 0.504209 14.9285C0.564508 14.7894 0.652012 14.6637 0.761577 14.559L6.77558 8.53896Z" fill="white" />
                    </svg>
                    Submit a Support Ticket
                </a>
            </div>
        </div>
    <?php }
}