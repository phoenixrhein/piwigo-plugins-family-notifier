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

define('FAMILY_NOTIFIER_TEMPLATE_PATH', FAMILY_NOTIFIER_PLUGIN_PATH . 'template' . DIRECTORY_SEPARATOR);
define('FAMILY_NOTIFIER_INCLUDE_PATH', FAMILY_NOTIFIER_PLUGIN_PATH . 'include' . DIRECTORY_SEPARATOR);

include_once FAMILY_NOTIFIER_INCLUDE_PATH . 'FamilyNotifierException.php';
include_once FAMILY_NOTIFIER_PLUGIN_PATH . 'FamilyNotifierConfig.php';
include_once FAMILY_NOTIFIER_INCLUDE_PATH . 'FamilyNotifierMonitor.php';
include_once FAMILY_NOTIFIER_INCLUDE_PATH . 'FamilyNotifierMailer.php';
include_once FAMILY_NOTIFIER_INCLUDE_PATH . 'FamilyNotifierCategoryHelper.php';
include_once FAMILY_NOTIFIER_INCLUDE_PATH . 'FamilyNotifierTemplateHelper.php';

/**
 * Family notifier plugin app
 */
class FamilyNotifierPluginAdminApp
{

    /**
     *
     * @var FamilyNotifierMailer
     */
    private $mailer;

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
        $this->mailer = new FamilyNotifierMailer();
        $this->categoryHelper = new FamilyNotifierCategoryHelper();
        $this->templateHelper = new FamilyNotifierTemplateHelper();
    }

    /**
     * Initialize template
     *
     * @return void
     */
    private function initTemplateView()
    {
        $this->templateHelper->getTemplate()->set_filenames([
            'plugin_admin_content' => FAMILY_NOTIFIER_TEMPLATE_PATH . 'admin.tpl'
        ]);

        $this->templateHelper->getTemplate()->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
    }

    /**
     * Assign variables
     *
     * @return void
     */
    private function assignVariables()
    {
        $this->templateHelper->getTemplate()->assign(array(
            'albums' => $this->categoryHelper->getCategoryList(),
            'receivers' => FamilyNotifierConfig::$receivers,
            'derivative_params' => trigger_change('get_index_album_derivative_params', ImageStdParams::get_by_type(IMG_THUMB)),
            'monitorLogs' => $this->getPreparedMonitorLogs()
        ));
    }

    /**
     * Get prepared monitor logs
     *
     * @return string[][]
     */
    private function getPreparedMonitorLogs()
    {
        $preparedMonitorLogs = [];
        $albumsList = $this->categoryHelper->getCategoryList();
        $monitor = new FamilyNotifierMonitor();
        foreach ($monitor->getLogs() as $logEntry) {
            $albumIds = explode(',', $logEntry[2]);
            $albumNameList = [];
            foreach ($albumIds as $id) {
                $albumNameList[] = $albumsList[trim($id)]['name'];
            }
            $preparedMonitorLogs[] = [
                'timestamp' => $logEntry[0],
                'emails' => str_replace(',', '<br/>', $logEntry[1]),
                'albums' => implode('<br/>', $albumNameList)
            ];
        }

        return $preparedMonitorLogs;
    }

    /**
     * View
     *
     * @return void
     */
    public function view()
    {
        try {
            $this->mailer->send();
            $this->assignVariables();
            $this->initTemplateView();
        } catch (FamilyNotifierException $e) {
            $this->templateHelper->getTemplate()->assign('errors', array(
                $e->getGuiMessage()
            ));
        }
    }
}

$app = new FamilyNotifierPluginAdminApp();
$app->view();
