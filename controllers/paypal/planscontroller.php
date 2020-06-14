<?php

namespace LcFramework\Controllers\Paypal;

use LcFramework\Controllers\Core\ControllerClass;
use LcFramework\Controllers\Paypal\SettingsController;
use LcFramework\Controllers\Paypal\PayPalController;

class PlansController extends ControllerClass {

    public $args = [];
    private $frequency = ''; /* Day, Week, Month, Year */
    private $value = 0;
    private $currency = 'GBP';
    private $failAttempts = 2;
    private $sandboxSecret = 'EPMfU8oyqmTJMiCNrEZDT2ahwi8H5Ks2RLpoZkmaE76N0d8eegtrsn184tH3Xe5lVIq_N8hDGtuUiXvt';

    public function executeCharge(){
        return $this->requestPayment();
    }

    public function subscribePlan(array $args){
        return $this->sendSubscriptionRequest($args);
    }

    public function addSubscriptionPlan(){
        return $this->storeSubscriptionPlan();
    }

    private function sendSubscribeRequest($args = array()) {
        $this->apiUrl = 'https://api.sandbox.paypal.com/v1/billing/subscriptions';
        $secret = $this->sandboxSecret;    

        if(!$sandbox == true){
            $secret = $this->liveSecret;
            $this->apiUrl = 'https://api.paypal.com/v1/billing/subscriptions';
        }

        $args['headers'] = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$secret,
            'PayPal-Request-Id: SUBSCRIPTION-'.time().'-'.rand(0, 9999)
        ];
        
        $args['dataFields'] = [
            'plan_id' => 'P-5ML4271244454362WXNWU5NQ',
            'start_time' => '',
            'quantity' => 20,
            'subscriber' => [
                'name' => [
                    'given_name' => $args['first_name'],
                    'surname' => $args['last_name']
                ],
                'email_address' => $args['email'],
            ],
            'application_context' => [
                'brand_name' => 'LifeCoachNearMe',
                'locale' => 'en-UK',
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
                ],
                'return_url' => $args['return_url'],
                'cancel_url' => $args['cancel_url']
            ]
        ];
    }

     /**
     * sends request to paypal for payment
     */
    public function listSubscriptionPlan(){
        $this->apiUrl = 'https://api.sandbox.paypal.com/v1/billing/plans';
        $secret = $this->sandboxSecret;

        if(get_option('paypal_env') == 'live_api'){
            $secret = $this->liveSecret;
            $this->apiUrl = 'https://api.paypal.com/v1/billing/plans';
        }
 
        $args['headers'] = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$secret,
        ];

        $args['dataFields'] = ['product_id' => 'PROD-XXCD1234QWER65782', 
                                'page_size' => 2,
                                'page' => 1,
                                'total_required' => 'true'];
        
        $data = $this->requestCurl($args);
        bspp_loadView('paypal/index', ['args' => $data]);
    }

    public function createSubscriptionPlan(){
        bspp_loadView('paypal/plans/create');
    }

    /**
     * sends request to paypal for payment
     */
    public function storeSubscriptionPlan(){
        $this->verifyNonce('bspp_pp_create_plan', '_bspp_create_paypal_');

        $this->getPayPalAccessToken();

        $this->apiUrl = 'https://api.sandbox.paypal.com/v1/billing/plans';
        $secret = $this->sandboxSecret;

        if(get_option('paypal_env') == 'live_api'){
            $secret = $this->liveSecret;
            $this->apiUrl = 'https://api.paypal.com/v1/billing/plans';
        }

        $args['headers'] = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$secret,
            'PayPal-Request-Id: PLAN-'.time().'-'.rand(0, 9999)
        ];

        $args['dataFields'] = [
            'product_id' => getArrayValue($args, 'product_id'),
            'name' => getArrayValue($args, 'product_name'),
            'description' => getArrayValue($args, 'product_desc'),
            'status' => getArrayValue($args, 'product_staus'),
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3
            ],
        ];

        foreach(getArrayValue($_POST, 'billing') as $billing){
            $args['dataFields']['billing_cycles'][] = [
                    'frequency' => [
                        'interval_unit' => getArrayValue($fields, 'interval_unit'),
                        'interval_count' => getArrayValue($fields, 'interval_count')
                    ],
                    'tenure_type' => getArrayValue($fields, 'tenure_type'),
                    'sequence' => getArrayValue($fields, 'sequence'),
                    'total_cycles' => getArrayValue($fields, 'total_cycles'),
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => getArrayValue($fields, 'plan_price'),
                            'currency_code' => getArrayValue($fields, 'currency')
                        ]
                    ]
                ];
        }
        pr($this->requestCurl($args));
    }
}