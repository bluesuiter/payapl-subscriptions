<?php

function filterRegisterPage($register_url) {
    $pageId = \LcFramework\Controllers\Paypal\SettingsController::readSettings('register_page');
    return get_permalink($pageId);
}
add_filter('register_url', 'filterRegisterPage');


//function custom_login_url() {
//    return site_url().'/login';
//}
//add_filter('login_headerurl', 'custom_login_url');

add_filter( 'login_url', 'my_login_page', 10, 2 );
function my_login_page( $login_url, $redirect ) {
    return str_replace("wp-login.php","login",$login_url);
}
