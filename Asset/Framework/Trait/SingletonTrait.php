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

namespace Asset\Framework\Trait;

/**
 * Trait that handles: Singleton Instance
 *
 * @package Asset\Framework\Trait;
 */
trait SingletonTrait
{
    /**
     * @var self
     */
    private static ?self $instance;

    /**
     * @var array
     */
    private static array $arguments = [];

    /**
     * @param mixed ...$args
     * @return self
     */
    public static function getInstance(mixed ...$args): self
    {
        if (!isset(self::$instance)) {
            self::$arguments = $args;
            self::$instance  = new self(...$args);
        }

        return self::$instance;
    }

    /**
     * @return array
     */
    protected static function getArguments(): array
    {
        return self::$arguments;
    }
}