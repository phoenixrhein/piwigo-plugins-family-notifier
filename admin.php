<?php
// Chech whether we are indeed included by Piwigo.
if (defined('PHPWG_ROOT_PATH') === false) {
    die('Hacking attempt!');
}

define('FAMILY_NOTIFIER_TEMPLATE_PATH', FAMILY_NOTIFIER_PLUGIN_PATH . 'template' . DIRECTORY_SEPARATOR);
define('FAMILY_NOTIFIER_INCLUDE_PATH', FAMILY_NOTIFIER_PLUGIN_PATH . 'include' . DIRECTORY_SEPARATOR);

include_once FAMILY_NOTIFIER_PLUGIN_PATH . 'FamilyNotifierConfig.php';
include_once FAMILY_NOTIFIER_INCLUDE_PATH . 'FamilyNotifierMailer.php';
include_once FAMILY_NOTIFIER_INCLUDE_PATH . 'FamilyNotifierCategoryHelper.php';

/**
 * Family notifier plugin app
 *
 */
class FamilyNotifierPluginApp
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
     * constructor
     */
    public function __construct() {
        $this->mailer = new FamilyNotifierMailer();
        $this->categoryHelper = new FamilyNotifierCategoryHelper();
    }
    
    /**
     * Get the smarty template object
     * 
     * @return Template
     */
    private function getTemplate()
    {
        global $template;
        return $template;
    }
    
    /**
     * Initialize template
     * 
     * @return void
     */
    private function initTemplateView()
    {
        $this->getTemplate()->set_filenames([
            'plugin_admin_content' => FAMILY_NOTIFIER_TEMPLATE_PATH . 'admin.tpl',
        ]);
        
        $this->getTemplate()->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
    }
    
    /**
     * Assign variables
     * 
     * @return void
     */
    private function assignVariables()
    {

        $this->getTemplate()->assign(array(
            'albums' => $this->categoryHelper->getCategoryList(),
            'receivers' => FamilyNotifierConfig::$receivers,
            'derivative_params' => trigger_change('get_index_album_derivative_params', ImageStdParams::get_by_type(IMG_THUMB))
        ));
    }
    
    /**
     * View
     * 
     * @return void
     */
    public function view()
    {
        $this->assignVariables();
        $this->mailer->send();
        $this->initTemplateView();
    }
}

$app = new FamilyNotifierPluginApp();
$app->view();
