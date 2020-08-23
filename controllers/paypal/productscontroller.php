<?php
namespace LcFramework\Controllers\Paypal;

use LcFramework\Controllers\BsppLogger as Logger;
use LcFramework\Controllers\Core\ControllerClass;
use LcFramework\Controllers\Paypal\PayPalController;
use LcFramework\Controllers\Paypal\SettingsController;


class ProductsController extends ControllerClass{

    /**
     * 
     */
    public function addProduct(){
        return bspp_loadView('paypal/products/create');
    }

    /**
     * 
     */
    public function saveProduct(){
        $this->verifyNonce('bspp_pp_add_product', '_bspp_create_pp_product');
        
        $product = [
            'name' => getArrayValue($_POST, 'product_name'),
            'description' => getArrayValue($_POST, 'product_description'),
            'type' => getArrayValue($_POST, 'product_type'),
            'category' => getArrayValue($_POST, 'product_category'),
            'home_url' => getArrayValue($_POST, 'home_url'),
        ];
     
        $args['endpoint'] = 'catalogs/products';
        $args['datafields'] = ($product);

        $result = (new PayPalController())->postProductRequest($args);
        $res = json_decode($result);
        if($res->id){
            return wp_send_json_success('Product created!');
        }
        
        $objLogger = new Logger();
        $objLogger->setModuleName('save_product');
        $objLogger->setModuleContent($res);
        $objLogger->createLog();
        return wp_send_json_error('Some error occurred!');
    }

    /**
     * 
     */
    public function getProductList(){
        $page = isset($_GET['paged']) ? $_GET['paged'] : 1;
        $args['endpoint'] = "catalogs/products/?page_size=20&page=$page&total_required=true";
        $productsData = (new PayPalController())->getProductRequest($args);
        $this->loadView('paypal/products/index', $productsData);
    }

    /**
     * 
     */
    public function editProduct(){
        $args['endpoint'] = 'catalogs/products/'.getArrayValue($_GET, 'product');
        $product = (new PayPalController())->getProductRequest($args);
        $this->loadView('paypal/products/edit', $product);
    }

    /**
     * 
     */
    public function updateProduct(){
        $this->verifyNonce('bspp_pp_update_product', '_bspp_update_pp_product');
        
        $product = [
            'name' => getArrayValue($_POST, 'product_name'),
            'description' => getArrayValue($_POST, 'product_description'),
            'type' => getArrayValue($_POST, 'product_type'),
            'category' => getArrayValue($_POST, 'product_category'),
            'home_url' => getArrayValue($_POST, 'home_url'),
        ];

        $args['method'] = 'PATCH';
        $args['endpoint'] = 'catalogs/products/'.getArrayValue($_GET, 'product_id');
        $args['datafields'] = ($product);

        return (new PayPalController())->postProductRequest($args);
    }
}