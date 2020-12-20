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
 * class FamilyNotifierMonitor
 */
class FamilyNotifierMonitor
{

    /**
     *
     * @var string
     */
    private $filePath;

    /**
     * Constructor
     *
     * @throws FamilyNotifierException
     */
    public function __construct()
    {
        global $conf;

        $rootDir = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR;
        $directory = $rootDir . $conf['data_location'] . 'familyNotifier';

        if (file_exists($directory) === false && mkdir($directory) === false) {
            throw new FamilyNotifierException("Could not create directory '{$directory}'", "Beim Erstellen des Vereichnis für Daten ist ein Fehler aufgetreten. Das Verzeíchnis '{$directory}' konnte nicht angelegt werden.");
        }

        if (is_writable($directory) === false) {
            throw new FamilyNotifierException("Directory '{$directory}' is not writable.", "Das Verzeichnis '{$directory}' ist nicht beschreibar.");
        }

        $this->filePath = $directory . DIRECTORY_SEPARATOR . 'monitor.csv';
        ;
    }

    /**
     * Log sent mail
     *
     * @param array $mailAddresses
     * @param array $albums
     */
    public function log($mailAddresses, $albums)
    {
        $date = new DateTime();
        $lineData = [
            $date->format('d.m.Y H:i:s'),
            implode(',', $mailAddresses),
            implode(',', $albums)
        ];

        file_put_contents($this->filePath, implode(";", $lineData) . PHP_EOL, FILE_APPEND);
    }

    /**
     * Get logs
     *
     * @return string[][]
     * @throws FamilyNotifierException
     */
    public function getLogs()
    {
        $rows = [];

        if (file_exists($this->filePath) === false) {
            return $rows;
        }

        $handle = @fopen($this->filePath, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $rows[] = explode(";", $buffer);
            }
            if (! feof($handle)) {
                throw new FamilyNotifierException('Error: unexpected fgets() error', "Beim Auslesen der Monitor-Datei '{$this->filePath}' ist ein Fehler aufgetreten.");
            }
            fclose($handle);
        } else {
            throw new FamilyNotifierException("Could not open file '{$this->filePath}'.", "Beim Öffnen der Monitor-Datei '{$this->filePath}' ist ein Fehler aufgetreten.");
        }

        return $rows;
    }
}