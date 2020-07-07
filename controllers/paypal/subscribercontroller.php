<?php
namespace LcFramework\Controllers\Paypal;

use LcFramework\Controllers\Paypal\PayPalController;
use LcFramework\Controllers\Paypal\SettingsController;
use LcFramework\Controllers\Paypal\SubscriberController;

class SubscriberController{

    public function listSubscribers(){
        global $wpdb;
        $sqlQry = 'SELECT * FROM '.$wpdb->prefix.'paypal_result LIMIT 25 ';
        if(isset($_GET['index']) && $_GET['index'] > 0){
            $sqlQry .= 'OFFSET '.($_GET['index'] * 25);
        }
        $result = $wpdb->get_results($sqlQry);
        $totalCount = $wpdb->get_var('SELECT count(id) FROM '.$wpdb->prefix.'paypal_result');
        return bspp_loadView('paypal/subscriptions/index', compact(['result', 'totalCount']));
    }

    private function getSubscription(){
        $id = getArrayValue($_GET, 'id');
        $args['endpoint'] = 'billing/subscriptions/'.$id;
        return (new PayPalController())->getProductRequest($args);
    }

    public function edit(){
        $result = $this->getSubscription();
        return bspp_loadView('paypal/subscriptions/edit', compact('result'));
    }

    public function view(){
        $result = $this->getSubscription();
        return bspp_loadView('paypal/subscriptions/view', compact('result'));
    }

    public function cancelSubscription(){
        $id = getArrayValue($_POST, 'id');
        if(!empty($id)){
            $args['endpoint'] = "billing/subscriptions/$id/cancel";
            $args['reason'] = getArrayValue($_POST, 'reason');
            (new PayPalController())->postProductRequest($args);
        } 
    }

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

        foreach($records as $record){
            $args['endpoint'] = "billing/subscriptions/$id/capture";

        }
    }
}