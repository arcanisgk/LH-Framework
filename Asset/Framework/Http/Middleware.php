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

namespace Asset\Framework\Http;

use Asset\Framework\Trait\SingletonTrait;
use Asset\Framework\Core\{Authentication, ConfigLoader};
use PSpell\Config;

/**
 * Class that handles: Middleware
 *
 * @package Asset\Framework\Http;
 */
class Middleware
{

    use SingletonTrait;

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

        if (!ConfigLoader::checkFullConfig() && UR !== '/Setup') {
            Request::getInstance()->redirect('/Setup');
        } elseif (ConfigLoader::checkFullConfig() && UR === '/Setup' && UR !== CONFIG->app->host->getEntry()) {
            Request::getInstance()->redirect(CONFIG->app->host->getEntry());
        }

        if (!Authentication::check() && UR !== '/User-Access') {
            Request::getInstance()->redirect('/User-Access');
        }

        //User::getInstance()->getUserStatus();

    }
}