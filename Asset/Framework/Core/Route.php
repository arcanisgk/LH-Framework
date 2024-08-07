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

/**
 * Class Route
 * A simple ...
 */
class Route
{
    /**
     * @var Route|null Singleton instance of the Route.
     */
    private static ?self $instance = null;

    private static array $handlers;


    /**
     * Get the singleton instance of Route.
     *
     * @return Route The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     *
     */
    public function __construct()
    {
        self::initRoute();
    }

    /**
     * @return void
     */
    private static function initRoute(): void
    {

        if (isset($_GET)) {
            $method = 'GET';
        } elseif (isset($_POST)) {
            $method = 'POST';
        } else {
            $method = 'GET';
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $callback       = '';
            $uri            = parse_url($_SERVER['REQUEST_URI'])['path'];
            self::$handlers = [$method => [$uri => $callback]];

            ex(self::$handlers);
        }
    }
}