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

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core;
 */
class Middleware
{

    /**
     * @var Middleware|null Singleton instance of the class: Middleware.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class Middleware.
     *
     * @return Middleware The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Middleware constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return void
     */
    public static function processMiddleware(): void
    {

        if (!Config::checkFullConfig() && UR !== '/Setup') {
            Request::getInstance()->redirect('/Setup');
        } elseif (Config::checkFullConfig() && UR === '/Setup' && UR !== CONFIG['APP']['HOST']['ENTRY']) {
            Request::getInstance()->redirect(CONFIG['APP']['HOST']['ENTRY']);
        }

        if (!Authentication::check() && UR !== '/User-Access') {
            Request::getInstance()->redirect('/User-Access');
        }

        //User::getInstance()->getUserStatus();

    }
}