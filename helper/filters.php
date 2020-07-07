<?php

function filterRegisterPage($register_url) {
    $pageId = \LcFramework\Controllers\Paypal\SettingsController::readSettings('register_page');
    return get_permalink($pageId);
}
add_filter('register_url', 'filterRegisterPage');
