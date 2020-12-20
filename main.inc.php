<?php
// ############################################################################
// *
// * Copyright (C) xt by hobutech
// *
// ############################################################################
// *
// * Plugin Name: Family Notifier
// * Version: dev
// * Description: notify by e-mail about new photo albums
// * Plugin URI: http://www.xovatec.de
// * Author: xt
// * Author URI:
// *
// * http://www.hobutech.de
// *
// ****************************************************************************

// Chech whether we are indeed included by Piwigo.
if (defined('PHPWG_ROOT_PATH') === false) {
    die('Hacking attempt!');
}

// Define the path to our plugin.
define('FAMILY_NOTIFIER_PLUGIN_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * class FamilyNotifierPluginMenu
 */
class FamilyNotifierPluginMenu
{

    /**
     * Add menu entry
     *
     * @param array $menu
     * @return []
     */
    public function addMenuEntry($menu)
    {
        array_push($menu, [
            'NAME' => 'Familie benachrichtigen',
            'URL' => get_admin_plugin_menu_link(dirname(__FILE__)) . '/admin.php'
        ]);
        return $menu;
    }
}

load_language('plugin.lang', FAMILY_NOTIFIER_PLUGIN_PATH);

$menu = new FamilyNotifierPluginMenu();

add_event_handler('get_admin_plugin_menu_links', array(
    $menu,
    'addMenuEntry'
));
?>