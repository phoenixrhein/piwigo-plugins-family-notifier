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
 * class FamilyNotifierTemplateHelper
 */
class FamilyNotifierTemplateHelper
{

    /**
     * Get the smarty template object
     *
     * @return Template
     */
    public function getTemplate()
    {
        global $template;
        return $template;
    }
}