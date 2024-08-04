<?php

declare(strict_types=1);

use Asset\Framework\Core\ArgumentLoader;
use Asset\Framework\Core\EventLog;
use Asset\Framework\Core\Request;
use Asset\Helper\AutoloaderClass;
use Asset\Helper\Installer\Installation;
use IcarosNet\WebCLIToolKit\WebCLIDetector;

require_once PD . DS . 'Asset' . DS . 'Helper' . DS . 'AutoloaderClass.php';

AutoloaderClass::getInstance();

EventLog::getInstance();

if (WebCLIDetector::getInstance()->isCLI()) {
    if (isset($argv)) {
        ArgumentLoader::getInstance($argv);
    }
    require_once PD . DS . 'Asset' . DS . 'Framework' . DS . 'Error' . DS . 'BugCatcher.php';
}

Request::getInstance()->CleanSuperGlobal();

function install(): void
{
    Installation::getInstance();
}