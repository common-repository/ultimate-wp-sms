<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Tabs;
class DocumentationHelp{
    private $pro_tab,$pro_doc_tab,$uws_doc_tab;
    public function __construct($args){ 
        $this->pro_tab = array(
            array('link'=>'https://ultimatewpsms.tawk.help/category/get-started/send-sms-campaign','text'=>'Send Messages Tab'),
            array('link'=>'https://ultimatewpsms.tawk.help/category/get-started/group-manager','text'=>'Group Manager Tab'),
            array('link'=>'https://ultimatewpsms.tawk.help/category/get-started/conversation','text'=>'Conversation Tab'),
            array('link'=>'https://ultimatewpsms.tawk.help/category/get-started/settings-pro','text'=>'Settings Tab'),
        );
        $this->uws_doc_tab = array(
            array('link'=>'https://ultimatewpsms.tawk.help/article/how-to-setup-uws-sending-service','text'=>'How to setup UWS Sending Service'),
            array('link'=>'https://ultimatewpsms.tawk.help/category/send-with-uws','text'=>'Send Using UWS-Sending Service'),
            array('link'=>'https://ultimatewpsms.tawk.help/article/how-to-create-a-10dlc-campaign','text'=>'How to create a 10DLC campaign'),
            array('link'=>'https://ultimatewpsms.tawk.help/article/10dlc-fees-and-charges','text'=>'10DLC Fees and Charges'),
        );
        $this->pro_doc_tab = array(
            
            array('link'=>'https://ultimatewpsms.tawk.help/category/gateway','text'=>'Configuring Messaging Services'),
            array('link'=>'https://ultimatewpsms.tawk.help/article/ultimate-wp-sms-supported-merge-tags','text'=>'Ultimate WP SMS supported merge tags'),
            array('link'=>'https://ultimatewpsms.tawk.help/article/woocommerce-subscription-integration','text'=>'UWS WooCommerce Integration'),
            array('link'=>'https://ultimatewpsms.tawk.help/category/get-started/uws-shortcode','text'=>'UWS Shortcode'),
            array('link'=>'https://ultimatewpsms.tawk.help/article/wordpress-user-integration-settings','text'=>'WordPress User Integration Settings'),
        );
        $this->html($args);
    }
    private function html($args){ ?>
        <div id="documentation_help" class="documentation_help tabcontent">
            <h2 class="common_heading">Ultimate WordPress SMS plugin - The UWP SMS Pro Documentation</h2>
            <div class="colrows documentation_row">
                <div class="col-100">
                    <div class="documentation_box">
                        <h5>The Ultimate WP SMS Sending Service Documentation</h5>
                        <ul>
                            <?php foreach($this->uws_doc_tab as $tab): ?>
                                <li>
                                    <a href="<?php echo esc_url( $tab['link'] ); ?>" target="_blank">
                                        <?php echo esc_attr( $tab['text'] ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-50">
                    <div class="documentation_box">
                        <h5>The Ultimate WP SMS Pro Tabs</h5>
                        <ul>
                            <?php foreach($this->pro_tab as $tab): ?>
                                <li>
                                    <a href="<?php echo esc_url( $tab['link'] ); ?>" target="_blank">
                                        <?php echo esc_attr( $tab['text'] ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="col-50">
                    <div class="documentation_box">
                        <h5>The Ultimate WP SMS Pro Documentation</h5>
                        <ul>
                            <?php foreach($this->pro_doc_tab as $tab): ?>
                                <li>
                                    <a href="<?php echo esc_url( $tab['link'] ); ?>" target="_blank">
                                        <?php echo esc_attr( $tab['text'] ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php }
}