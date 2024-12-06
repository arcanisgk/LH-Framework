<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Asset\Framework\Core;

use Asset\Framework\Trait\SingletonTrait;

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core;
 */
class Authentication
{

    use SingletonTrait;

    /**
     * Authentication constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return bool
     */
    public static function check(): bool
    {
        return true;
    }

}