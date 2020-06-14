<?php
namespace LcFramework\Controllers\Paypal;

use LcFramework\Controllers\Paypal\PlansController;
use LcFramework\Controllers\Paypal\ProductsController;
use LcFramework\Controllers\Paypal\SettingsController;


class PayPalController{

    public function addAdminMenu(){
        $objPlans = new PlansController();
        add_menu_page('PayPal', 'PayPal Plans', 'delete_pages', 'bspp_list_paypal', array($objPlans, 'listSubscriptionPlan'), 'dashicons-book', '12');
        add_submenu_page('bspp_list_paypal', 'Add PayPal Plan', 'Add PayPal Plan', 'delete_pages', 'bspp_add_paypal_plan', array($objPlans, 'createSubscriptionPlan'));

        $objProducts = new ProductsController();
        add_submenu_page('bspp_list_paypal', 'PayPal Products', 'Products', 'delete_pages', 'bspp_paypal_proucts', array($objProducts, 'getProductList'));
        add_submenu_page('bspp_list_paypal', 'Add Products', 'Add Products', 'delete_pages', 'bspp_paypal_add_proucts', array($objProducts, 'addProduct'));
        
        $objSettings = new SettingsController();
        add_submenu_page('bspp_list_paypal', 'Settings', 'Settings', 'delete_pages', 'bspp_paypal_settings', array($objSettings, 'paypalSettings'));
        //add_submenu_page($menu_slug, 'Lead Logs', 'Lead Log', $capability, 'leadLog', array($this, 'leadLogs'));
    }

    public function addPaypalAjaxEndpoint(){
        $objPlans = new PlansController();
        add_action('wp_ajax_bspp_paypal_add_plan', array($objPlans, 'addSubscriptionPlan'));

        $objProducts = new ProductsController();
        add_action('wp_ajax_bspp_add_pp_product', array($objProducts, 'saveProduct'));

        $objSettings = new SettingsController();
        add_action('wp_ajax_bspp_paypal_config_admin', array($objSettings, 'storeSettings'));
    }

    public function scheduledTask(){
        /** hourly Job */
        if (!wp_next_scheduled('bspp_pp_hourly_job')) {
            wp_schedule_event(time(), 'hourly', 'bspp_pp_hourly_job');
        }
        add_action('bspp_pp_hourly_job', [$this, 'refreshPayPalToken']); 
    }

    public function getAccessToken(){
        $data = get_option('_bspp_pp_secret_details_');
        $data = json_decode($data);

        if(time() > $data->expiry){
            $this->getPayPalAccessToken();
            $data = get_option('_bspp_pp_secret_details_');
            $data = json_decode($data);
        }
        return $data;
    }

    /**
     * get paypal access token
     * @return json
     */
    public static function getPayPalAccessToken(){
        $settings = SettingsController::readSettings();

        $headers = ['Accept: application/json', 'Accept-Language: en_US'];

        $url = 'https://api.sandbox.paypal.com/v1/oauth2/token';
        $clientId = $settings->sandbox_id;
        $secret = $settings->sandbox_secret;

        if($settings->env !== 'sandbox'){
            $url = 'https://api.paypal.com/v1/oauth2/token';
            $clientId = $settings->live_id;
            $secret = $settings->live_secret;
        }
        
        try{
            $ch = curl_init();
            $timeout = 5;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSLVERSION , 6); //NEW ADDITION
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            
            $data = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($data);
            /** error case */
            if(isset($data->error)){
               return wp_send_json_error($data->error);
            }

            /** success case */
            $data->expiry = (time() + getArrayValue($data, 'expires_in'));
            if(update_option('_bspp_pp_secret_details_', json_encode($data))){
                return wp_send_json_success('Saved successfully.');
            }
        }catch(Exception $e){
            return wp_send_json_error($e->getMessage());
        }
    }

    protected function refreshPayPalToken(){
        $settings = SettingsController::readSettings();
        $curl = curl_init();

        if(!isset($settings->env)){
            return true;
        }

        /** sandbox details */
        $url = 'https://api.sandbox.paypal.com/v1/';
        $clientId = $settings->sandbox_id;
        $secret = $settings->sandbox_secret;
        $auth_code = $settings->sb_auth_code;

        /** live details */
        if($settings->env !== 'sandbox'){
            $url = 'https://api.paypal.com/v1/';
            $clientId = $settings->live_id;
            $secret = $settings->live_secret;
            $auth_code = $settings->lv_auth_code;
        }

        $authData = json_decode(get_option('_bspp_pp_secret_details_'));

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'identity/openidconnect/tokenservice',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code='.$auth_code,
            CURLOPT_HTTPHEADER => array("authorization: Basic ".base64_encode($clientId.':'.$secret)),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION => 6, //NEW ADDITION
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        pr($err);
        exit;
    }
}