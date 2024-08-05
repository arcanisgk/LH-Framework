<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Requiered).
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

namespace Asset\Framework\Core;

class Variable
{
    private static ?self $instance = null;

    /**
     * @return Variable
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $var
     *
     * @return bool
     */
    public function isJson(string $var): bool
    {
        return json_decode($var) != null;
    }


}