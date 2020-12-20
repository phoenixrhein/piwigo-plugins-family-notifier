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
 * class FamilyNotifierException
 */
class FamilyNotifierException extends Exception
{

    /**
     *
     * @var string
     */
    private $guiMessage;

    /**
     * constructor
     *
     * @param string $errorMessage
     * @param string $guiMessage
     */
    public function __construct($errorMessage, $guiMessage)
    {
        $this->guiMessage = $guiMessage;
        parent::__construct($errorMessage);
    }

    /**
     * Get gui message
     *
     * @return string
     */
    public function getGuiMessage()
    {
        return $this->guiMessage;
    }
}