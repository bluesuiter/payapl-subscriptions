<?php
namespace LcFramework\Controllers\Paypal;

use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;
use LcFramework\Controllers\SignUp;
use LcFramework\Controllers\Paypal\PlansController;
use LcFramework\Controllers\Paypal\ProductsController;
use LcFramework\Controllers\Paypal\SettingsController;
use LcFramework\Controllers\Paypal\SubscriberController;


class PayPalController{

    public function addAdminMenu(){
        $objSubscribers = new SubscriberController();
        add_menu_page('Subscribers', 'PayPal Subscribers', 'delete_pages', 'bspp_paypal_subscribers', array($objSubscribers, 'listSubscribers'), 'dashicons-book', '12');
        add_submenu_page('', 'View Subscription', 'View Subscription', 'delete_pages', 'bspp_view_sub', array($objSubscribers, 'view'));
        add_submenu_page('', 'Edit Subscription', 'Edit Subscription', 'delete_pages', 'bspp_edit_sub', array($objSubscribers, 'edit'));
        // add_submenu_page('', 'Subscription', 'Subscription', 'delete_pages', 'bspp_subscriptions', array($objSubscribers, 'viewTransaction'));

        /** products */
        $objProducts = new ProductsController();
        add_submenu_page('bspp_paypal_subscribers', 'PayPal Products', 'PayPal Products', 'delete_pages', 'bspp_paypal_proucts', array($objProducts, 'getProductList'));
        add_submenu_page('', 'Add Product', 'Add Product', 'delete_pages', 'bspp_paypal_add_product', array($objProducts, 'addProduct'));
        add_submenu_page('', 'Edit Product', 'Edit Products', 'delete_pages', 'bspp_paypal_edit_product', array($objProducts, 'editProduct'));

        $objPlans = new PlansController();
        $objPlans->planRoutes();
        add_action('admin_post_bspp_updateSubscription', array($objPlans, 'updateSubscriptionPlan'));

        $objSettings = new SettingsController();
        add_submenu_page('bspp_paypal_subscribers', 'Settings', 'Settings', 'delete_pages', 'bspp_paypal_settings', array($objSettings, 'paypalSettings'));
        //add_submenu_page($menu_slug, 'Lead Logs', 'Lead Log', $capability, 'leadLog', array($this, 'leadLogs'));

        add_submenu_page('bspp_paypal_subscribers', 'Export E-Mails', 'Export E-Mails', 'delete_pages', 'bspp_export_users', array($this, 'exportUserEmail'));
    }

    public function addPaypalAjaxEndpoint(){
        $objPlans = new PlansController();
        add_action('wp_ajax_bspp_paypal_add_plan', array($objPlans, 'addSubscriptionPlan'));
        add_action('wp_ajax_bspp_change_plan_status', array($objPlans, 'activateSubscriptionPlan'));        

        $objProducts = new ProductsController();
        add_action('wp_ajax_bspp_add_pp_product', array($objProducts, 'saveProduct'));
        add_action('wp_ajax_bspp_pp_update_product', array($objProducts, 'updateProduct'));
        
        $objSettings = new SettingsController();
        add_action('wp_ajax_bspp_paypal_config_admin', array($objSettings, 'storeSettings'));

        $objSubscribers = new SubscriberController();
        $objSubscribers->defineActions();

        $objSignup = new SignUp();
        $objSignup->registerSignUpApi();

        add_action('admin_post_bspp_updateSubscription', array((new PlansController()), 'updateSubscriptionPlan'));

        add_action('set_user_role', [$this, 'user_role_update'], 10, 2);
        //add_action('admin_post_nopriv_email_csv', [$this, 'writeCsvFile']);
        add_action('admin_post_email_csv', [$this, 'writeCsvFile']);
    }

    /**
     * export user emails
     */
    function exportUserEmail() {
        //pr(get_users());
        bspp_loadView('paypal/export');
    } 

    function writeCsvFile(){
        $filename = 'user_emails';
        $date = date("Y-m-d H:i:s");
        header('Content-Type: application/csv');
        header('Pragma: no-cache');
        header("Content-Disposition: attachment; filename=\"" . $filename . " " . $date . ".csv\";" );
        header("Content-Transfer-Encoding: binary");


        $users = get_users();
        $date = date("Y-m-d H:i:s");
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'E-Mail', 'Role'));
        foreach ($users as $user) {
            $line = [$user->data->ID, $user->data->user_email, getArrayValue($user->roles, 0)];
            fputcsv($output, $line, ',');
        }
        fclose($output);        
    }

    /**
     * mail sent on user role change
     */
    function user_role_update($user_id, $new_role) {
        $site_url = get_bloginfo('wpurl');
        $user_info = get_userdata( $user_id );
        $to = $user_info->user_email;
        $subject = "Role changed: ".$site_url."";
        $message = "Hello " .$user_info->display_name . " your role has changed on ".$site_url.", you are now on " . $new_role . "plan.";
        wp_mail($to, $subject, $message);
    }

    /**
     * schedule tasks
     */
    public function scheduledTask(){
        /** hourly Job */
        if (!wp_next_scheduled('bspp_pp_hourly_job')) {
            wp_schedule_event(time(), 'hourly', 'bspp_pp_hourly_job');
        }
        add_action('bspp_pp_hourly_job', [(new SubscriberController()), 'capturePayment']); 
    }

    /**
     * get saved access token
     */
    public static function getAccessToken(){
        $data = get_option('_bspp_pp_secret_details_');
        $data = json_decode($data);

        if(!isset($data->expiry) || time() > $data->expiry){
            self::getPayPalAccessToken();
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
                return  true;
            }
        }catch(Exception $e){
            return wp_send_json_error($e->getMessage());
        }
    }

    /**
    *
    */
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
        //pr($err);
        exit;
    }

    /**
     * @param array $args
     */
    public function postProductRequest(array $args){
        $data = PayPalController::getAccessToken();

        $settings = SettingsController::readSettings();
        $headers = ['Accept: application/json',
                   'Accept-Language: en_US',
                   'Authorization: Bearer '.$data->access_token,
                   'PayPal-Request-Id: PRODUCT-'.date('YmdHis').'-'.rand(0, 999)];

        $url = 'https://api.sandbox.paypal.com/v1/';
        if($settings->env !== 'sandbox'){
            $url = 'https://api.paypal.com/v1/';
        }

        //echo $data->access_token, json_encode($args['datafields']); exit;

        $method = isset($args['method']) ? $args['method'] : 'POST';
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.$args['endpoint'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($args['datafields']),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer ".$data->access_token,
            ),
        ));
      
        $res = curl_exec($curl);
        if($res){
            return $res;
        }

        $err = curl_error($curl);
        if($err){
            return $err;
        }
        curl_close($curl);

        if($err){
            return wp_send_json_error(json_decode($err));
        }
    }

    public function getProductRequest(array $args){
        $data = PayPalController::getAccessToken();

        $settings = SettingsController::readSettings();
        $headers = ['Accept: application/json',
                   'Accept-Language: en_US',
                   'Authorization: Bearer '.$data->access_token];

        $url = 'https://api.sandbox.paypal.com/v1/';

        if($settings->env !== 'sandbox'){
            $url = 'https://api.paypal.com/v1/';
        }

        $curl = curl_init();

        $page = isset($_GET['paged']) ? $_GET['paged'] : 1;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.$args['endpoint'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".$data->access_token,
            ),
        ));

        $res = curl_exec($curl);    
        if($res){
            return json_decode($res);
        }

        $err = curl_error($curl);
        if($err){
            return json_decode($err);
        }
        curl_close($curl);
    }
}