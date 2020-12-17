<?php

// Chech whether we are indeed included by Piwigo.
if (defined('PHPWG_ROOT_PATH') === false) {
    die('Hacking attempt!');
}

/**
 * class FamilyNotifierConfig
 *
 */
class FamilyNotifierConfig
{
    public static $receivers = array(
        array(
            'email' => 'hohmannb@gmx.de',
            'enable' => true
        ),
        array(
            'email' => 'sina.hohmann@gmx.de',
            'enable' => false
        ),
        array(
            'email' => 'noho123@gmx.de',
            'enable' => false
        ),
        array(
            'email' => 'heiho123@gmx.de',
            'enable' => false
        ),
        array(
            'email' => 'janina.wittke@gmx.de',
            'enable' => false
        ),
        array(
            'email' => 'lea.wittke@gmx.de',
            'enable' => false
        ),
        array(
            'email' => 'baerbel@gmx.de',
            'enable' => false
        ),
        array(
            'email' => 'detlef@gmx.de',
            'enable' => false
        ),
    );
}