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

include_once PHPWG_ROOT_PATH . 'include/functions_mail.inc.php';

/**
 * class FamilyNotifierMailer
 */
class FamilyNotifierMailer
{

    /**
     *
     * @var FamilyNotifierMonitor
     */
    private $monitor;

    /**
     *
     * @var FamilyNotifierCategoryHelper
     */
    private $categoryHelper;

    /**
     *
     * @var FamilyNotifierTemplateHelper
     */
    private $templateHelper;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->monitor = new FamilyNotifierMonitor();
        $this->categoryHelper = new FamilyNotifierCategoryHelper();
        $this->templateHelper = new FamilyNotifierTemplateHelper();
    }

    /**
     * Validate requirements
     *
     * @return boolean
     */
    private function isValid()
    {
        $errors = array();

        if (array_key_exists('email', $_POST) === false && (array_key_exists('email_custom_checked', $_POST) === true && $_POST['email_custom_checked'] != 1 || array_key_exists('email_custom', $_POST) === true && strlen($_POST['email_custom']) === 0)) {
            $errors[] = 'Es wurde kein Empfänger ausgewählt.';
        }

        if (array_key_exists('album', $_POST) === false) {
            $errors[] = 'Es wurde kein Album ausgewählt.';
        }

        if (count($errors) > 0) {
            $this->templateHelper->getTemplate()->assign('errors', $errors);
            return false;
        }

        return true;
    }

    /**
     * Prepare mail template
     *
     * @return string[][]
     */
    private function prepareTemplate()
    {
        $selectedAlbumList = array();
        $albumList = $this->categoryHelper->getCategoryList();

        foreach ($_POST['album'] as $albumId) {
            $selectedAlbumList[] = $albumList[$albumId];
        }

        $tpl = [
            'filename' => 'notifier_mail',
            'dirname' => dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'mail',
            'assign' => [
                'albums' => $selectedAlbumList,
                'derivative_params' => trigger_change('get_index_album_derivative_params', ImageStdParams::get_by_type(IMG_THUMB)),
                'url' => 'https://fotos.xovatec.de/',
                'notes' => nl2br($_POST['notes'])
            ]
        ];

        return $tpl;
    }

    /**
     * Get mail receivers
     *
     * @return string[][]
     */
    private function getMailReceivers()
    {
        $mailReceivers = [];

        if (array_key_exists('email', $_POST) == false) {
            return $mailReceivers;
        }

        foreach ($_POST['email'] as $email) {
            $mailReceivers[] = [
                'name' => $email,
                'email' => $email
            ];
        }

        return $mailReceivers;
    }

    /**
     * Send mail
     *
     * @return void
     */
    public function send()
    {
        if (isset($_POST['send_notifier']) === false) {
            return;
        }

        if ($this->isValid() === false) {
            return;
        }

        $args = [
            'subject' => FamilyNotifierConfig::$mailSubject,
            'mail_title' => FamilyNotifierConfig::$mailHeaderTitle,
            'mail_subtitle' => FamilyNotifierConfig::$mailHeaderSubtitle
        ];

        $tpl = $this->prepareTemplate();
        $mailReceivers = $this->getMailReceivers();

        if (pwg_mail($mailReceivers, $args, $tpl) === false) {
            $this->templateHelper->getTemplate()->assign('errors', array(
                'Beim Versand ist ein Fehler aufgetreten.'
            ));
            return;
        }

        $this->monitor->log($_POST['email'], $_POST['album']);

        $this->templateHelper->getTemplate()->assign('infos', array(
            'E-Mail wurde erfolgreich versendet.'
        ));
    }
}