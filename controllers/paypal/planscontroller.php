<?php

namespace LcFramework\Controllers\Paypal;

use LcFramework\Controllers\Core\ControllerClass;
use LcFramework\Controllers\Paypal\SettingsController;
use LcFramework\Controllers\Paypal\PayPalController;
use LcFramework\Controllers\Paypal\ProductsController;

class PlansController extends ControllerClass {

    public $args = [];
    private $frequency = ''; /* Day, Week, Month, Year */
    private $value = 0;
    private $currency = 'GBP';
    private $failAttempts = 2;
    private $sandboxSecret = 'EPMfU8oyqmTJMiCNrEZDT2ahwi8H5Ks2RLpoZkmaE76N0d8eegtrsn184tH3Xe5lVIq_N8hDGtuUiXvt';

    /**
     * fetch plans list by product_id
     */
    public function plansList() {
        $product = getArrayValue($_GET, 'product');
        $paged = getArrayValue($_GET, 'paged');
        if(empty($product)) {
            echo '<h1 style="color:#b45;">No product selected!</h1>';
            return false;
        }
        $args['endpoint'] = "billing/plans?product_id=$product&page_size=10&page=$paged&total_required=true";
        $plans = (new PayPalController())->getProductRequest($args);
        $plans = getArrayValue($plans, 'plans');
        pr($plans);
        bspp_loadView('paypal/plans/index', compact('plans'));
    }

    /**
     * fetch plan details by plan_id
     */
    public function viewSubscriptionPlan() {
        $plan = getArrayValue($_GET, 'plan');
        if(empty($plan)) {
            echo '<h1 style="color:#b45;">No plan selected!</h1>';
            return false;
        }
        $args['endpoint'] = "billing/plans/$plan";
        $plan = (new PayPalController())->getProductRequest($args);
        bspp_loadView('paypal/plans/view', compact('plan'));
    }

    /**
     * 
     */
    public function executeCharge(){
        return $this->requestPayment();
    }

    /**
     * 
     */
    public function subscribePlan(array $args){
        return $this->sendSubscriptionRequest($args);
    }

    /**
     * 
     */
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

    /**
     * 
     */
    public function createSubscriptionPlan(){
        $product = getArrayValue($_GET, 'product');
        if(empty($product)) {
            echo '<h1 style="color:#b45;">No product selected!</h1>';
            return false;
        }
        bspp_loadView('paypal/plans/create', compact('product'));
    }

    /**
     * sends request to paypal for payment
     */
    public function addSubscriptionPlan(){
        $this->verifyNonce('bspp_pp_create_plan', '_bspp_create_paypal_');
        
        $args['endpoint'] = 'billing/plans';
        $args['datafields'] = [
            'product_id' => getArrayValue($_POST, 'product_id'),
            'name' => getArrayValue($_POST, 'plan_name'),
            'description' => getArrayValue($_POST, 'plan_description'),
            'status' => getArrayValue($_POST, 'plan_status'),
            'payment_preferences' => [
                'auto_bill_outstanding' => "true",
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3
            ],
        ];

        foreach(getArrayValue($_POST, 'billing') as $billing){
            $args['datafields']['billing_cycles'][] = [
                    'frequency' => [
                        'interval_unit' => getArrayValue($billing, 'interval_unit'),
                        'interval_count' => getArrayValue($billing, 'interval_count')
                    ],
                    'tenure_type' => getArrayValue($billing, 'tenure_type'),
                    'sequence' => getArrayValue($billing, 'sequence'),
                    'total_cycles' => (int)getArrayValue($billing, 'total_cycles'),
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => getArrayValue($billing, 'plan_price'),
                            'currency_code' => getArrayValue($billing, 'currency')
                        ]
                    ]
                ];
        }
        
        (new PayPalController())->postProductRequest($args);
    }

    /**
     * 
     */
    public function editSubscriptionPlan(){
        
    }
}