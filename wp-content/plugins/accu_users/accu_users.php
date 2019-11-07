<?php
/*
Plugin name: Accu Users manager

Description: Manages users login against API validation
Author: Elena Draculet
Author URI: #
Version: 1
*/

define('AccUsers_PLUGIN', __FILE__);
define('AccUsers_PLUGIN_DIR', dirname(AccUsers_PLUGIN));


require_once AccUsers_PLUGIN_DIR . '/inc/functions.php';
require_once AccUsers_PLUGIN_DIR . '/inc/api_functions.php';

add_action('init', 'accuusers_plugin_activation');

function accuusers_plugin_activation()
{
    load_text_domain();
    load_components();
}


