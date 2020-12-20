<?php
// ############################################################################
// *
// * Copyright (C) xt by hobutech
// *
// ############################################################################

// Chech whether we are indeed included by Piwigo.
if (defined('PHPWG_ROOT_PATH') === false) {
    die('Hacking attempt!');
}

/**
 * class FamilyNotifierConfig
 */
class FamilyNotifierConfig
{

    /**
     *
     * @var array
     */
    public static $receivers = [
        [
            'email' => 'dummy@dummy.de',
            'enable' => true
        ],
        [
            'email' => 'hohmannb@gmx.de',
            'enable' => true
        ],
        [
            'email' => 'sina.hohmann@gmx.de',
            'enable' => false
        ],
        [
            'email' => 'noho123@gmx.de',
            'enable' => false
        ],
        [
            'email' => 'heiho123@gmx.de',
            'enable' => false
        ],
        [
            'email' => 'janina.wittke@gmx.de',
            'enable' => false
        ],
        [
            'email' => 'lea.wittke@gmx.de',
            'enable' => false
        ],
        [
            'email' => 'baerbel@gmx.de',
            'enable' => false
        ],
        [
            'email' => 'detlef@gmx.de',
            'enable' => false
        ]
    ];

    /**
     *
     * @var string
     */
    public static $mailSubject = 'Benachrichtigung: Neue Fotos';

    /**
     *
     * @var string
     */
    public static $mailHeaderTitle = 'Fotos der Familie (ehemals) HKS';

    /**
     *
     * @var string
     */
    public static $mailHeaderSubtitle = 'Die neuen Fotos sind da!';
}