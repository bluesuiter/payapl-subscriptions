<?php

namespace LcFramework\Controllers;

use LcFramework\Controllers\Paypal\SettingsController;
use LcFramework\Controllers\Paypal\PayPalController;

class SignUp{

    private $table = 'paypal_result';
    private $subscription = 'bspp_subscription';

    public function registerSignUpApi(){
        add_shortcode('bspp_register_form', array($this, 'loadForm'));
        add_shortcode('bspp_thank_you', array($this, 'storeSubscription'));

        add_action('wp_ajax_nopriv_register_user', [$this, 'storeUser']);
        add_action('init', [$this, 'storeUser']);
        add_action('delete_user', [$this, 'userDelete']);
    }

    public static function loadForm($atts) {
        (new self)->storeUser();
        bspp_loadView('signup/signup-form');
        wp_enqueue_script('regsiter_validation', bspp_framework_uri.'web/js/form-script.js', array('jquery'), '31052020', true);
    }

    public function storeSubscription($args){
        $this->thankYouPage();
        if(isset($_GET['result']) && $_GET['result'] == 'success'){
            echo getArrayValue($args, 'success_message');
            return;
        }
    }

    public function storeUser(){
        if(wp_verify_nonce(getArrayValue($_POST, 'bspp_r3g_ca11'), 'bspp_reg_call')) {
            $firstName = sanitize_text_field(getArrayValue($_POST, 'ufname'));
            $lastName = sanitize_text_field(getArrayValue($_POST, 'ulname'));
            $email = sanitize_text_field(getArrayValue($_POST, 'uemail'));
            $password = sanitize_text_field(getArrayValue($_POST, 'password'));
            $plan = sanitize_text_field(getArrayValue($_POST, 'plan'));
            
            if(!is_email($email)){
                return wp_send_json_error('Please enter a valid email');
            }

            if(email_exists($email)){
                return wp_send_json_error('E-mail is already associated with other account');
            }
            
            $_SESSION['user_data'] = [  'firstName' => $firstName,
                                        'lastName'  => $lastName,
                                        'email'     => $email,
                                        'password'  => $password,
                                        'plan'      => $plan ];

            if($this->selectPlan($plan)){
                return $this>applyPremiumPlan($planId, $userId);
            }
        }
    }

    public function selectPlan($plan){
        $plans['monthly'] = ['name' => 'Gold Monthly',
                             'plan_id' => 'P-7X742350SF667851RL4G5CJY'];

        $plans['yearly'] = ['name' => 'Gold yearly',
                            'plan_id' => 'P-5D985174EE091664ML3ZVGBY'];
        return $plans[$plan]['plan_id'];
    }

    public function applyPremiumPlan($planId, $userId){
        $settings = SettingsController::readSettings();
        $clientId = $settings->env.'_id';
        $clientId = $settings->$clientId;

        $user = get_user_by('ID', $userId);

        $args['endpoint'] = 'billing/subscriptions';
        $args['datafields'] =  [
                                'client-id' => $clientId,
                                'plan_id' => $planId,
                                'start_time' => gmdate("Y-m-d\TH:i:s\Z", time()+120),
                                'subscriber' => [
                                    'name' => [
                                        'given_name' => $user->first_name,
                                        'surname' => $user->last_name
                                    ],
                                    'email_address' => $user->email
                                ],
                                'application_context' => [
                                    'brand_name' => get_bloginfo('name'),
                                    'locale' => 'en-US',
                                    'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                                    'user_action' => 'SUBSCRIBE_NOW',
                                    'payment_method' => [
                                        'payer_selected' => 'PAYPAL',
                                        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                                    ],
                                    'return_url' => get_permalink($settings->thank_page),
                                    'cancel_url' => get_permalink($settings->thank_page)
                                ]
                            ];

        $result = (new PayPalController())->postProductRequest($args);    
        $result = json_decode($result);
        
        if(isset($result->links)){
            foreach($result->links as $link){
                if($link->rel == 'approve'){
                    echo '<script>window.location.href="'.$link->href.'";</script>';
                    return;
                }
            }
        }
    }

    public function userDelete($id){
        
    }

    /**
     * functions to perform on payment success
     */
    private function thankYouPage()
    {
        if(isset($_SESSION)){
            $userData = getArrayValue($_SESSION, 'user_data');
        
            $email = getArrayValue($userData, 'email');
            $password = getArrayValue($userData, 'password');
            $firstName = getArrayValue($userData, 'firstName');
            $lastName = getArrayValue($userData, 'lastName');
            $plan = getArrayValue($userData, 'plan');
            
            global $wpdb;
            if(!empty($email) && !empty($password))
            {
                if($plan == 'monthly'){
                    $plan = 'gold';
                }elseif($plan == 'yearly'){
                    $plan = 'goldyearly';
                }
                
                $user_id = wp_create_user($email, $password, $email);
                wp_update_user(['ID' => $user_id, 
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                                'role' => $plan,
                            ]);

                $settings = SettingsController::readSettings();
                $subscription_id = getArrayValue($_GET, 'subscription_id');
                $ba_token = getArrayValue($_GET, 'ba_token');
                $token = getArrayValue($_GET, 'token');

                $table = $wpdb->prefix.$this->table;
                $data = ['subscription_id' => $subscription_id,
                        'ba_token' => $ba_token,
                        'token' => $token,
                        'created_at' => current_time('mysql') ];
                $wpdb->insert($table, $data);

                $args['endpoint'] = 'billing/subscriptions/'.$subscription_id;
                $result = (new PayPalController())->getProductRequest($args);
            
                $message = 'failure';
                if(getArrayValue($result, 'start_time')){
                    $data = ['user_id' => $user_id,
                            'subscription_id' => $subscription_id,
                            'status' => $result->status,
                            'status_update_time' => $result->status_update_time,
                            'plan_id' => $result->plan_id,
                            'start_time' => $result->start_time,
                            'payer_id' => $result->subscriber->payer_id,
                            'cycle_executions' => $result->billing_info->cycle_executions,
                            'last_payment_currency' => $result->billing_info->last_payment->amount->currency_code,
                            'last_payment_amount' => $result->billing_info->last_payment->amount->value,
                            'last_payment_time' => $result->billing_info->last_payment->time,
                            'next_billing_time' => $result->billing_info->next_billing_time,
                            'failed_payments_count' => $result->billing_info->failed_payments_count,
                            'create_time' => $result->create_time,
                            'update_time' => $result->update_time];            
                    $wpdb->insert(($wpdb->prefix.$this->subscription), $data);
                    $message = 'success';
                }
                unset($_SESSION['user_data']);
                echo '<script>window.location.href="'.get_permalink($settings->thank_page).'?result='.$message.'";</script>';
                return;
                // if($wpdb->insert_id){
                //     return wp_send_json_success();
                // }
            }
        }        
    }
}

?>
