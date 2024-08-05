<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

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

    define('WD', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\').DS);
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
        $termWidth = isset($match['cols']) ? (int)$match['cols'] : null;
    } elseif (function_exists('shell_exec')) {
        $termResponse = shell_exec('tput cols 2> /dev/tty');
        if ($termResponse !== null) {
            $termWidth = trim($termResponse) ?? null;
            if ($termWidth !== null) {
                $termWidth = (int)$termWidth;
            }
        }
    }

    if ($termWidth === null) {
        $termWidth = 80;
    }

    define('TW', $termWidth);
}

if (!defined('RQ') && isset($_SERVER['REQUEST_METHOD'])) {
    define('RQ', $_SERVER['REQUEST_METHOD']);
}

require_once 'paths.php';