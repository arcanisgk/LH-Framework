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

define('LH_START', microtime(true));

if (!version_compare(phpversion(), '8.3', '>=')) {
    die("This project requires PHP version 8.3 or higher");
}

require_once realpath(implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'vendor', 'autoload.php']));

Install();