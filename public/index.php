<?php

declare(strict_types=1);

ini_set('opcache.enable', 0);

use Asset\Framework\Core\Kernel;

define('LH_START', [
    'TIME' => microtime(true),
    'MEMORY' => memory_get_usage(),
    'MEMORY_PEAK' => memory_get_peak_usage(),
]);

if (!version_compare(phpversion(), '8.1', '>=')) {
    die("This project requires PHP version 8.1 or higher");
}

require_once realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

Kernel::getInstance()->run();

//ex(['Hello World']);