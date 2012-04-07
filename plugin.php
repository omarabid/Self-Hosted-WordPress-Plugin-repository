<?php
/*
 Plugin Name: Auto-Update Plugin
 Plugin URI: http://omarabid.com
 Description: Plugin with auto-updates
 Author: Abid Omar
 Version: 1.0
 */

// Load the auto-update class
add_action('init', 'wptuts_activate_au');
function wptuts_activate_au()
{
    require_once ('wp_autoupdate.php');
    $wptuts_plugin_current_version = '1.0';
    $wptuts_plugin_remote_path = 'http://localhost/update.php';
    $wptuts_plugin_slug = plugin_basename(__FILE__);
    new wp_auto_update ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
}