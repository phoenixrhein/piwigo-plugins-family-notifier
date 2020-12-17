<?php

// Chech whether we are indeed included by Piwigo.
if (defined('PHPWG_ROOT_PATH') === false) {
    die('Hacking attempt!');
}

include_once PHPWG_ROOT_PATH.'include/functions_mail.inc.php';

/**
 * class FamilyNotifierMailer
 *
 */
class FamilyNotifierMailer
{
    public function send()
    {
        if (isset($_POST['send_notifier']) === false)
        {
            return;
        }
        $errors = array();
        
        if (array_key_exists('email', $_POST) === false
            && (array_key_exists('email_custom_checked', $_POST) === true && $_POST['email_custom_checked'] != 1
                || array_key_exists('email_custom', $_POST) === true && strlen($_POST['email_custom']) === 0)) {
                    $errors[] = 'Es wurde kein Empfänger ausgewählt.';
                }
                
                if (array_key_exists('album', $_POST) === false) {
                    $errors[] = 'Es wurde kein Album ausgewählt.';
                }
                
                if (count($errors) > 0) {
                    $this->getTemplate()->assign('errors', $errors);
                    return;
                }
                
                $args = [
                    'subject' => 'Benachrichtigung: Neue Fotos',
                    'mail_title' => 'Fotos der Familie (ehemals) HKS',
                    'mail_subtitle' => 'Die neuen Fotos sind da!'
                ];
                
                $albumList = array();
                
                foreach ($_POST['album'] as $albumId) {
                    $albumList[] = self::$albumList[$albumId];
                }
                
                $tpl = [
                    'filename' => 'notifier_mail',
                    'dirname' => dirname(__FILE__).DIRECTORY_SEPARATOR.'mail',
                    'assign' => [
                        'albums' => $albumList,
                        'derivative_params' => trigger_change('get_index_album_derivative_params', ImageStdParams::get_by_type(IMG_THUMB)),
                        'url' => 'https://fotos.xovatec.de/',
                        'notes' => nl2br($_POST['notes'])
                    ]
                ];
                
                // empfanger richtig setzen
                // protokollieren (Monitor)
                // config datei z.B. fuer Empfaengerliste
                
                
                if(pwg_mail(array('email' => 'bastianhohmann01@gmail.com'), $args, $tpl) === false) {
                    $this->getTemplate()->assign('errer', array('Beim Versand ist ein Fehler aufgetreten.'));
                }
                
                $this->getTemplate()->assign('infos', array('E-Mail wurde erfolgreich versendet.'));
    }
}