<?php
namespace LcFramework\Controllers\Paypal;

use LcFramework\Controllers\SignUp;
use LcFramework\Controllers\Paypal\PayPalController;
use LcFramework\Controllers\Paypal\SettingsController;
use LcFramework\Controllers\Paypal\SubscriberController;

class SubscriberController{

    public function defineActions(){
        add_action('wp_ajax_bspp_suspend_subscription', array($this, 'suspendSubscriptionPlan'));
        add_action('wp_ajax_nopriv_bspp_suspend_subscription', array($this, 'suspendSubscriptionPlan'));

        add_shortcode('suspend_subscription', [$this, 'suspendSubscriptionForm']);
        add_action('admin_post_get_transactions', [$this, 'getTransactions']);
        add_action('admin_post_upgradePlan', [$this, 'upgradeSubscription']);
    }

    public function listSubscribers(){
        global $wpdb;
        $paypalResult = $wpdb->prefix.'paypal_result';
        $subscripiton = $wpdb->prefix.'bspp_subscription';
        $sqlQry = "SELECT * FROM $paypalResult as pr 
                    JOIN $subscripiton as s ON s.subscription_id=pr.subscription_id
                    GROUP BY pr.subscription_id ORDER BY pr.created_at DESC LIMIT 25 ";
        if(isset($_GET['index']) && $_GET['index'] > 0){
            $sqlQry .= 'OFFSET '.($_GET['index'] * 25);
        } 
        $result = $wpdb->get_results($sqlQry);  /*echo '<pre>'; print_r($result); echo '</pre>';*/
        $totalCount = $wpdb->get_var('SELECT count(id) FROM '.$wpdb->prefix.'paypal_result');
        return bspp_loadView('paypal/subscriptions/index', compact(['result', 'totalCount']));
    }

    public function edit(){
        $result = $this->getSubscription();
        return bspp_loadView('paypal/subscriptions/edit', compact('result'));
    }

    /**
     * suspend subscription
     * when you suspend an account, you can reactivate it later
     */
    public function suspendSubscriptionPlan(){
        if(is_user_logged_in()){
            global $wpdb;
            $user = wp_get_current_user();
            $subscripiton = $wpdb->prefix.'bspp_subscription';
            $subID = $wpdb->get_var("SELECT subscription_id FROM $subscripiton WHERE user_id=".$user->ID);
            $role = (array)$user->roles;
            wp_update_user(['previous_role' => $role[0], 'role' => 'free']);
            $args['endpoint'] = "/billing/subscriptions/$subID/suspend";
            $args['datafields'] = ['reason' => getArrayValue($_POST, 'reason')];
            return (new PayPalController())->postProductRequest($args);
        }        
    }

    public function suspendSubscriptionForm(){
        return bspp_loadView('paypal/subscriptions/suspend-subscription');
    }

    /**
     * get transactions list by date
     */
    public function getTransactions(){
        if(is_user_logged_in()){
            global $wpdb;
            $table = $wpdb->prefix.'bspp_subscription';
            $query = "SELECT subscription_id, start_time FROM $table WHERE user_id=".get_current_user_id();
            //$query = "SELECT subscription_id, start_time FROM Life_bspp_subscription WHERE user_id=43";
            $row = $wpdb->get_row($query); 
            $subID = getArrayValue($row, 'subscription_id');
            $startTime = getArrayValue($row, 'start_time');
            $endTime = date('Y-m-d').'T23:00:00Z';
            if($subID){
                $args['endpoint'] = "billing/subscriptions/$subID/transactions?start_time=$startTime&end_time=$endTime";
                $result = (new PayPalController())->getProductRequest($args);
                bspp_loadView('paypal/subscriptions/front-transactions', compact('result'));
            }
        }
    }

    /**
     * view subscription details
     */
    public function view(){
        $id = getArrayValue($_GET, 'id');
        $args['endpoint'] = 'billing/subscriptions/'.$id;
        $result = (new PayPalController())->getProductRequest($args);
        return bspp_loadView('paypal/subscriptions/view', compact('result'));
    }

    /**
     * view subscription details
     */
    public function viewTransaction(){
        $id = getArrayValue($_GET, 'id');
        global $wpdb;
        $table = $wpdb->prefix.'bspp_subscription';
        $result = $wpdb->get_results('SELECT * FROM '.$table);
        return bspp_loadView('paypal/subscriptions/transactions', compact('result'));
    }
    
    /**
     * subsription cancellation method
     */
    public function cancelSubscription(){
        $id = getArrayValue($_GET, 'subscription_id');
        if(!empty($id)){
            $args['endpoint'] = "billing/subscriptions/$id/cancel";
            $args['reason'] = getArrayValue($_POST, 'reason');
            (new PayPalController())->postProductRequest($args);
        } 
    }

    /**
     * activtae subscription
     */
    public function activateSubscription(){
        $id = getArrayValue($_POST, 'id');
        if(!empty($id)){
            $args['endpoint'] = "billing/subscriptions/$id/activate";
            $args['reason'] = getArrayValue($_POST, 'reason');
            (new PayPalController())->postProductRequest($args);
        }
    }

    /**
     * capture payment
     */
    public function capturePayment(){
        global $wpdb;
        $recordsTable = $wpdb->prefix.'paypal_result';
        $logTable = $wpdb->prefix.'paypal_payment_logs';

        $sqlQry = "SELECT id FROM $recordsTable WHERE status IN('APPROVAL_PENDING', 'PENDING')";
        $records = $wpdb->get_results($sqlQry);

        foreach($records as $record) {
            $args['endpoint'] = "billing/subscriptions/$id/capture";
        }
    }

    /**
     * uupgrade subscription
     */
    public function upgradeSubscription(){
        if(is_user_logged_in()){
            $plan = getArrayValue($_GET, 'plan');
            $objSignUp = new SignUp();
            $planId = $objSignUp->selectPlan($plan);
            $objSignUp->applyPremiumPlan($planId, get_current_user_id());
        }
    }
}