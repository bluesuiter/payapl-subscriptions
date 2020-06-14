<?php

/**
 * Plugin Name:       PayPal Subscriptions
 * Plugin URI:
 * Description:       PayPal plugin for managing subscriptions.
 * Version:           0.06.20
 * Author:            blueSuiter
 * Author URI:        https://bluesuiter.github.io
 * Text Domain:       paypal
 * License URI:       LICENSE.txt
 * Tested up to:      5.4.2
 */

/* Include the autoloader so we can dynamically include the rest of the classes. */
require_once trailingslashit(dirname(__FILE__)) . 'inc/autoloader.php';
require_once trailingslashit(dirname(__FILE__)) . 'lib/paypal/autoload.php';

add_action('init', 'bspp_framework_init');

if (!defined('bspp_framework_path')) {
    define('bspp_framework_path', trailingslashit(dirname(__FILE__)));
}

if (!defined('bspp_framework_uri')) {
    define('bspp_framework_uri', (plugin_dir_url( __FILE__ )));
}

if (!defined('bspp_framework_view')) {
    define('bspp_framework_view', trailingslashit(dirname(__FILE__)) . "view/");
}

require_once trailingslashit(dirname(__FILE__)) . 'helper/functions.php';

/**
 * Starts the plugin by initializing the meta box, its display, and then
 * sets the plugin in motion.
 */
function bspp_framework_init() {
    $meta_box = new LcFramework\Controllers\LcFramework();
}