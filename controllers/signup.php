<?php

namespace LcFramework\Controllers;

class SignUp{

    public function __construct(){
        add_shortcode('bspp_register_form', array($this, 'loadForm'));
        add_action('wp_ajax_nopriv_register_user', [$this, 'storeUser']);
    }

    public static function loadForm($atts){
        bspp_loadView('signup/signup-form');
        wp_enqueue_script('regsiter_validation', bspp_framework_uri.'web/js/form-script.js', array('jquery'), '31052020', true);
    }

    public function storeUser(){
        if(!wp_verify_nonce(getArrayValue($_POST, 'bspp_r3g_ca11'), 'bspp_reg_call')) {
            $firstName = sanitize_text_field(getArrayValue($_POST, 'ufname'));
            $lastName = sanitize_text_field(getArrayValue($_POST, 'ulname'));
            $email = sanitize_text_field(getArrayValue($_POST, 'uemail'));
            $password = sanitize_text_field(getArrayValue($_POST, 'password'));
            $plan = sanitize_text_field(getArrayValue($_POST, 'plan'));

            if(!is_email($email)){
                wp_send_json_error('Please enter a valid email');
            }

            if(email_exists($email)){
                wp_send_json_error('E-mail is already associated with other account');
            }

            $plan = getArrayValue($_POST, 'plan');
            
            $user_id = wp_create_user($email, $password, $email);
            wp_update_user(['ID' => $user_id, 
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'plan' => 'free']);
        }
    }
}

?>
