<?php

declare(strict_types=1);

define('LH_START', microtime(true));

if (!version_compare(phpversion(), '8.1', '>=')) {
    die("This project requires PHP version 8.1 or higher");
}

require_once realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');


Install();
