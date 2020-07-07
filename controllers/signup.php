<?php

namespace LcFramework\Controllers;

use LcFramework\Controllers\Paypal\SettingsController;
use LcFramework\Controllers\Paypal\PayPalController;

class SignUp{

    public function registerSignUpApi(){
        add_shortcode('bspp_register_form', array($this, 'loadForm'));
        add_action('wp_ajax_nopriv_register_user', [$this, 'storeUser']);
    }

    public static function loadForm($atts) {
        bspp_loadView('signup/signup-form');
        wp_enqueue_script('regsiter_validation', bspp_framework_uri.'web/js/form-script.js', array('jquery'), '31052020', true);
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

            $plan = getArrayValue($_POST, 'plan');

            $plans['monthly'] = ['name' => 'Gold Monthly',
                                'plan_id' => 'P-7SY05160UH9007648L3ZVEAA'];
            
            $plans['yearly'] = ['name' => 'Gold yearly',
                                'plan_id' => 'P-5D985174EE091664ML3ZVGBY'];
            /*
                "plan_id": "P-2UF78835G6983425GLSM44MA",
                "start_time": "2020-02-27T06:00:00Z",
                "subscriber": {
                    "name": {
                        "given_name": "John",
                        "surname": "Doe"
                    },
                    "email_address": "customer@example.com"
                },
                "application_context": {
                    "brand_name": "example",
                    "locale": "en-US",
                    "shipping_preference": "SET_PROVIDED_ADDRESS",
                    "user_action": "SUBSCRIBE_NOW",
                    "payment_method": {
                        "payer_selected": "PAYPAL",
                        "payee_preferred": "IMMEDIATE_PAYMENT_REQUIRED"
                    },
                    "return_url": "https://example.com/returnUrl",
                    "cancel_url": "https://example.com/cancelUrl"
            */
            $settings = SettingsController::readSettings();
            
            $clientId = $settings->env.'_id';
            $clientId = $settings->$clientId;

            $args['endpoint'] = 'billing/subscriptions';
            $args['datafields'] =  [
                                    'client-id' => $clientId,
                                    'plan_id' => $plans[$plan]['plan_id'],
                                    'start_time' => gmdate("Y-m-d\TH:i:s\Z", time()+120),
                                    'subscriber' => [
                                        'name' => [
                                            'given_name' => $firstName,
                                            'surname' => $lastName
                                        ],
                                        'email_address' => $email
                                    ],
                                    'application_context' => [
                                        'brand_name' => bloginfo('name'),
                                        'locale' => 'en-US',
                                        'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                                        'user_action' => 'SUBSCRIBE_NOW',
                                        'payment_method' => [
                                            'payer_selected' => 'PAYPAL',
                                            'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                                        ],
                                        'return_url' => get_permalink($settings->success_page),
                                        'cancel_url' => get_permalink($settings->failure_page)
                                    ]
                                ];

            $result = (new PayPalController())->postProductRequest($args);    
            $result = json_decode($result);

            $user_id = wp_create_user($email, $password, $email);
            wp_update_user(['ID' => $user_id, 
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'role' => 'free',
                        ]);
            
            if($result->name == 'RESOURCE_NOT_FOUND'){
               return wp_send_json_error();
            }
                        
            $data = ['id' => $result->id,
                    'user_id' => $user_id,
                    'status' => $result->status,
                    'create_time' => $result->create_time,
                    'approve_href' => $result->links[0]->href,
                    'approve_method' => $result->links[0]->method,
                    'edit_href' => $result->links[1]->href, 
                    'edit_method' => $result->links[1]->method,
                    'self_href' => $result->links[2]->href, 
                    'self_method' => $result->links[2]->method
                ];

            global $wpdb;
            $table = $wpdb->prefix.'paypal_result';
            $wpdb->insert($table, $data);
            if($wpdb->insert_id){
                return wp_send_json_success();
            }
        }
    }
}

?>
