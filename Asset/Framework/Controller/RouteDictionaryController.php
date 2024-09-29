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

namespace Asset\Framework\Controller;

use Asset\Framework\Interface\ControllerInterface;


use Repository\Default\{
    Setup as Setup,
    Demo1 as Demo1,
    Demo2 as Demo2,
    Admin as Admin,
    Contact as Contact,
    Dashboard as Dashboard,
    Calendar as Calendar,
    Help as Help,
    Home as Home,
    NotFound as NotFound,
    PrivacyPolicies as PrivacyPolicies,
    Product as Product,
    Services as Services,
    Store as Store,
    TermOfService as TermOfService,
    UserAccess as UserAccess,
    UserActivation as UserActivation,
    UserLogout as UserLogout
};


/**
 * Class that handles:
 *
 * @package Asset\Framework\Controller;
 */
class RouteDictionaryController
{

    /**
     * @var RouteDictionaryController|null Singleton instance of the class: RouteDictionaryController.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class RouteDictionaryController.
     *
     * @return RouteDictionaryController The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * RouteDictionaryController constructor.
     */
    public function __construct()
    {

    }

    /**
     * @var string
     */
    private string $path;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return ControllerInterface
     */
    public function resolveController(): ControllerInterface
    {
        return match (mb_strtolower($this->getPath())) {
            '/setup' => Setup\Back\Main::getInstance(),
            '/home' => Home\Back\Main::getInstance(),
            '/user-access' => UserAccess\Back\Main::getInstance(),
            '/dashboard' => Dashboard\Back\Main::getInstance(),
            '/calendar' => Calendar\Back\Main::getInstance(),
            /*

            '/user-logout' => UserLogout\Back\Main::getInstance(),
            '/user-activation' => UserActivation\Back\Main::getInstance(),
            '/dashboard' => Dashboard\Back\Main::getInstance(),
            '/store' => Store\Back\Main::getInstance(),
            '/terms-of-service' => TermOfService\Back\Main::getInstance(),
            '/privacy-policies' => PrivacyPolicies\Back\Main::getInstance(),
            '/contact' => Contact\Back\Main::getInstance(),
            '/help' => Help\Back\Main::getInstance(),
            '/product' => Product\Back\Main::getInstance(),
            '/services' => Services\Back\Main::getInstance(),
            */

            '/admin' => Admin\Back\Main::getInstance(),
            '/demo1' => Demo1\Back\Main::getInstance(),
            '/demo2' => Demo2\Back\Main::getInstance(),
            default => NotFound\Back\Main::getInstance(),
        };
    }

}