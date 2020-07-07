<?php

namespace LcFramework\Controllers\Paypal;

class SettingsController{
 
    private static $valKey = '_bspp_paypal_credentials_';

    public function paypalSettings(){
        $data = $this->readSettings();
        bspp_loadView('paypal/settings', compact('data'));
    }

    /**
     * reads paypal settings from database
     * @param $key string
     * @return string|json
     */
    public static function readSettings($key=''){
        try{
            $data = get_option(self::$valKey);

            if(!empty($key)){
                $data = json_decode($data);
                return getArrayValue($data, $key);
            }
            return json_decode($data);
        }catch(Exception $e){
            echo $e->getMessage();
        }        
    }

    public function storeSettings(){
        try{
            $message = 'Nothing to save.';
            $nonce = getArrayValue($_POST, '_bspp_settings_paypal_');
            
            if(wp_verify_nonce($nonce, 'bspp_paypal_config_admin') && check_admin_referer('bspp_paypal_config_admin', '_bspp_settings_paypal_')){
                $data['env'] = getArrayValue($_POST, 'paypal_env');
                $data['sb_auth_code'] = getArrayValue($_POST, 'sb_auth_code');
                $data['sandbox_id'] = getArrayValue($_POST, 'sandbox_id');
                $data['sandbox_secret'] = getArrayValue($_POST, 'sandbox_secret');
                $data['lv_auth_code'] = getArrayValue($_POST, 'lv_auth_code');
                $data['live_id'] = getArrayValue($_POST, 'live_id');
                $data['live_secret'] = getArrayValue($_POST, 'live_secret');
                $data['register_page'] = getArrayValue($_POST, 'register_page');
                $data['success_page'] = getArrayValue($_POST, 'success_page');
                $data['failure_page'] = getArrayValue($_POST, 'failure_page');

                if(update_option(self::$valKey, json_encode($data))){
                    return wp_send_json_success('Saved successfully.');
                }
            }
        }catch(Exception $e){
            $message = 'Error: '.$e->getMessage();
        }
        return wp_send_json_error($message);
    }
}