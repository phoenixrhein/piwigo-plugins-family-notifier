<?php

// Chech whether we are indeed included by Piwigo.
if (defined('PHPWG_ROOT_PATH') === false) {
    die('Hacking attempt!');
}

/**
 * class FamilyNotifierConfig
 */
class FamilyNotifierConfig
{

    public static $receivers = [
        [
            'email' => 'dummy@dummy.de',
            'enable' => true
        ]
    ];
}