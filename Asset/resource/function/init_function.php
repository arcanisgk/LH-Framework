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

use Asset\Framework\ToolBox\Dumper;
use JetBrains\PhpStorm\NoReturn;

/**
 * @param ...$var
 *
 * @return void
 */
function ex(...$var): void
{
    Dumper::getInstance()::dump(['data' => $var]);
}

/**
 * @param ...$var
 * @return void
 */
#[NoReturn] function ex_c(...$var): void
{
    Dumper::getInstance()::dump(['data' => $var]);
    exit();
}