<?php 

if(!function_exists('bspp_loadView')){
    function bspp_loadView($view, $fields=array()) {
        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                $$key = $field;
            }
        }
    
       $view = bspp_framework_view . $view . '.php';
        if (!file_exists($view)) {
            echo 'View not found!';
            return false;
        }
        require_once($view);
    }
}


if (!function_exists('getArrayValue')) {
    function getArrayValue($arr, $key)
    {
        if (is_array($arr)) {
            if (isset($arr[$key]) && !empty($arr[$key])) {
                return $arr[$key];
            }
        } else if (is_object($arr)) {
            if (isset($arr->$key) && !empty($arr->$key)) {
                return $arr->$key;
            }
        }
        return false;
    }
}