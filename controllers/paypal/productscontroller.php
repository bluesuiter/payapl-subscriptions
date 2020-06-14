<?php
namespace LcFramework\Controllers\Paypal;

use LcFramework\Controllers\Core\ControllerClass;
use LcFramework\Controllers\Paypal\PayPalController;
use LcFramework\Controllers\Paypal\SettingsController;


class ProductsController extends ControllerClass{

    public function addProduct(){
        bspp_loadView('paypal/products/create');
    }

    public function saveProduct(){
        $this->verifyNonce('bspp_pp_add_product', '_bspp_create_pp_product');
        $objPayPal = new PayPalController();
        $objPayPal->getAccessToken();
        
        $data = [
            'name' => getArrayValue($_POST, 'product_name'),
            'description' => getArrayValue($_POST, 'product_description'),
            'type' => getArrayValue($_POST, 'product_type'),
            'category' => getArrayValue($_POST, 'product_category'),
            'home_url' => getArrayValue($_POST, 'home_url'),
        ];

        $objSettings = new SettingsController();
        $this->savePayPalProduct($data);       
    }

    public function savePayPalProduct($product){
        $data = get_option('_bspp_pp_secret_details_');
        $data = json_decode($data);

        $settings = SettingsController::readSettings();
        $headers = ['Accept: application/json',
                   'Accept-Language: en_US',
                   'Authorization: Bearer '.$data->access_token,
                   'PayPal-Request-Id: PRODUCT-'.date('YmdHis').'-'.rand(0, 999)];

        $url = 'https://api.sandbox.paypal.com/v1/catalogs/products';

        if($settings->env !== 'sandbox'){
            $url = 'https://api.paypal.com/v1/catalogs/products';
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($product),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer ".$data->access_token,
            ),
        ));

        $response = curl_exec($curl);
        var_dump($response);

        curl_close($curl);
        echo 'done';
    }

    public function getProductList(){

    }
}