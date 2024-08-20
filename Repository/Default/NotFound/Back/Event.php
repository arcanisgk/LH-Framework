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

namespace Repository\Default\NotFound\Back;

/**
 * Class that handles:
 *
 * @package Repository\Default\NotFound\Back;
 */
class Event
{

    /**
     * @var Event|null Singleton instance of the class: Event.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class Event.
     *
     * @return Event The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public array $response = [];

    /**
     * Event constructor.
     */
    public function __construct()
    {

    }

}