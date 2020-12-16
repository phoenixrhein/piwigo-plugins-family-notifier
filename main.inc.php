<?php
/*
 Plugin Name: Family Notifier
 Version: 1.0.0
 Description: My Description
 Plugin URI: http://www.xovatec.de
 Author: XovaTex
 Author URI: http://www.xovatec.de
 */

// Chech whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Define the path to our plugin.
define('SKELETON_PATH', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');

// Add an entry to the 'Plugins' menu.
function skeleton_admin_menu($menu) {
    array_push(
        $menu,
        array(
            'NAME'  => 'Familie benachrichtigen',
            'URL'   => get_admin_plugin_menu_link(dirname(__FILE__)).'/admin.php'
        )
        );
    return $menu;
}


load_language('plugin.lang', PHPWG_PLUGINS_PATH . 'FamilyNotifier' . DIRECTORY_SEPARATOR);


// Hook on to an event to show the administration page.
add_event_handler('get_admin_plugin_menu_links', 'skeleton_admin_menu');
?>