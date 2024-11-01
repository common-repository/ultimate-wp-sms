<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Icons;
class SuccessIcon{
    public function __construct($args){
        $this->html($args);
    }
    private function html($args){ ?>
        <button class="btn icon_btn">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.8879 9.941C19.8879 11.9069 19.305 13.8287 18.2129 15.4634C17.1207 17.0981 15.5685 18.3723 13.7523 19.1248C11.9361 19.8774 9.93754 20.0745 8.00931 19.6914C6.08108 19.3082 4.30976 18.3619 2.91928 16.9721C1.52881 15.5823 0.581628 13.8115 0.197487 11.8835C-0.186653 9.95542 0.00949895 7.95678 0.761143 6.14021C1.51279 4.32364 2.78617 2.77072 4.4203 1.67779C6.05443 0.58486 7.97593 0.000990057 9.94186 1.25753e-06C11.2478 -0.000655563 12.541 0.255991 13.7477 0.755279C14.9543 1.25457 16.0508 1.98671 16.9744 2.90988C17.8981 3.83306 18.6308 4.92917 19.1307 6.13559C19.6306 7.34201 19.8879 8.63511 19.8879 9.941ZM8.78786 15.205L16.1679 7.829C16.2275 7.76947 16.2747 7.69878 16.307 7.62096C16.3393 7.54315 16.3559 7.45974 16.3559 7.3755C16.3559 7.29127 16.3393 7.20786 16.307 7.13004C16.2747 7.05223 16.2275 6.98153 16.1679 6.922L15.2609 6.015C15.2013 5.95541 15.1306 5.90813 15.0528 5.87587C14.975 5.84362 14.8916 5.82701 14.8074 5.82701C14.7231 5.82701 14.6397 5.84362 14.5619 5.87587C14.4841 5.90813 14.4134 5.95541 14.3539 6.015L8.33786 12.03L5.52986 9.222C5.47033 9.16241 5.39964 9.11513 5.32182 9.08287C5.24401 9.05061 5.1606 9.03401 5.07636 9.03401C4.99213 9.03401 4.90872 9.05061 4.8309 9.08287C4.75309 9.11513 4.6824 9.16241 4.62286 9.222L3.71586 10.129C3.65627 10.1885 3.60899 10.2592 3.57673 10.337C3.54448 10.4149 3.52787 10.4983 3.52787 10.5825C3.52787 10.6667 3.54448 10.7501 3.57673 10.828C3.60899 10.9058 3.65627 10.9765 3.71586 11.036L7.88786 15.205C8.0078 15.3232 8.16945 15.3895 8.33786 15.3895C8.50628 15.3895 8.66793 15.3232 8.78786 15.205Z" fill="#00E946"/>
            </svg>
        </button>
   <?php }
}