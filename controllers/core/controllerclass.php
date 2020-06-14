<?php

namespace LcFramework\Controllers\Core;

class ControllerClass {

    /**
     * viewLoader
     */
    protected function loadView($view, $fields = array()) {
        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                $$key = $field;
            }
        }

        $view = twg_framework_view . $view . '.php';
        if (!file_exists($view)) {
            echo 'View not found!';
            return false;
        }
        require $view;
    }

    /**
     * viewLoader
     */
    protected function verifyNonce($actionName, $actionField) {
        $message = 'Nothing to save.';
        $nonce = getArrayValue($_POST, $actionField);

        if(!wp_verify_nonce($nonce, $actionName) && !check_admin_referer($actionName, $actionField)){
            return wp_send_json_error($message);
        }
    }
}