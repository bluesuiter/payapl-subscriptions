<?php 

namespace LcFramework\Controllers;

use LcFramework\Controllers\Paypal\PayPalController;

class LcFramework{

    public function __construct(){
        $this->callAppActions();

        $objPayPal = new PayPalController();
        /** paypal add-menu */
        add_action('admin_menu', [$objPayPal, 'addAdminMenu']);

        /** paypal add-menu */
        $objPayPal->addPaypalAjaxEndpoint();

        /** paypal add-scheduled tasks */
        $objPayPal->scheduledTask();

        add_action('admin_enqueue_scripts', array($this, 'loadAdminPanelStyle'));
    }

    public function callAppActions(){
        new \LcFramework\Controllers\SignUp();
    }

    public function loadAdminPanelStyle($hook) 
    {
        wp_register_style('_adminPanelCss', bspp_framework_uri . 'web/css/style.css', false, '0.6.25');

        /* / Load only on ?page=mypluginname */
        $hookList = array('bspp_paypal_settings', 'bspp_add_paypal_plan', 'bspp_list_paypal',);
        wp_enqueue_style('_adminPanelCss');
    }

    
}

?>