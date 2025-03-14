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

namespace Asset\Framework\Core;

use Asset\Framework\Trait\SingletonTrait;

class Argument
{

    use SingletonTrait;

    /**
     * @var array
     */
    private static array $arguments = [];

    /**
     * @param $args
     */
    public function __construct($args)
    {
        $this->setArguments($args);
    }

    /**
     * @param $args
     *
     * @return void
     */
    private static function setArguments($args): void
    {
        $args       = array_slice($args, 1);
        $parsedArgs = [];
        foreach ($args as $arg) {
            if (str_contains($arg, '=')) {
                [$key, $value] = explode('=', $arg, 2);
                $parsedArgs[trim($key, '-')] = trim($value, '"');
            }
        }

        self::$arguments = $parsedArgs;
    }

    /**
     * @return array
     */
    public static function getArguments(): array
    {
        return self::$arguments;
    }
}

if (isset($argv)) {
    Argument::getInstance($argv);
}