<?php

declare(strict_types=1);

use IcarosNet\WebCLIToolKit\WebCLIDetector;

if (!defined('DS')) {
    /**
     * Directory Separator
     *
     * Description: This constant represents the directory separator for the file system paths.
     */

    define('DS', DIRECTORY_SEPARATOR);
}

$path = implode(DS, array_slice(explode(DS, dirname(__DIR__)), 0, -2));

if (!defined('PD')) {
    /**
     * Project Directory
     *
     * Description: This constant represents the path in which the project is located.
     */

    define('PD', $path);
}

if (!defined('WD')) {
    /**
     * Web Directory
     *
     * Description: This constant represents the path that the web entry point is located on.
     */

    define('WD', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . DS);
}

if (!defined('NL')) {
    /**
     *
     * New Line
     *
     * Description: This constant represents a new line for system on request or command execution.
     */

    define('NL', WebCLIDetector::getInstance()->isCLI() ? PHP_EOL : nl2br(PHP_EOL));
}

if (!defined('CT')) {
    /**
     * Current time
     *
     * Description: This constant represents the local server time.
     */

    define('CT', time());
}

if (!defined('IS_CLI')) {
    $isCLI = defined('STDIN')
        || php_sapi_name() === 'cli'
        || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
        || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);

    define('IS_CLI', $isCLI);
}


if (!defined('TW') && IS_CLI) {
    /**
     * Terminal Width
     *
     * Description: This constant represents the local server time.
     */
    $termWidth = null;

    if (str_contains(PHP_OS, 'WIN')) {
        $termWidth = shell_exec('mode con');
        preg_match('/CON.*:(\n[^|]+?){3}(?<cols>\d+)/', $termWidth, $match);
        $termWidth = isset($match['cols']) ? (int) $match['cols'] : null;
    } elseif (function_exists('shell_exec')) {
        $termResponse = shell_exec('tput cols 2> /dev/tty');
        if ($termResponse !== null) {
            $termWidth = trim($termResponse) ?? null;
            if ($termWidth !== null) {
                $termWidth = (int) $termWidth;
            }
        }
    }

    if ($termWidth === null) {
        $termWidth = 80;
    }

    define('TW', $termWidth);
}

if (!defined('RQ')) {
    define('RQ', $_SERVER['REQUEST_METHOD']);
}

require_once 'paths.php';