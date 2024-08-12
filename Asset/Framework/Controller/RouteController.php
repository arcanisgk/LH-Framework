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

namespace Asset\Framework\Controller;

use Asset\Framework\Interface\ControllerInterface;
use Asset\Framework\View\DeploymentView;
use Exception;
use Repository\Default\{Admin as Admin,
    Contact as Contact,
    Dashboard as Dashboard,
    Help as Help,
    Home as Home,
    Notfound as Notfound,
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
 * Class that handles: Routing Controller address
 *
 * @package Asset\Framework\Controller;
 */
class RouteController
{
    /**
     * @var RouteController|null Singleton instance of the RequestController.
     */
    private static ?self $instance = null;

    /**
     * @var string
     */
    private string $path;

    /**
     * Get the singleton instance of RequestController.
     *
     * @return RouteController The singleton instance.
     */
    public static function getInstance(string $path): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($path);
        }

        return self::$instance;
    }

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute(): void
    {

        $controller = $this->resolveController();

        try {
            $response = $controller->process();
            // DeploymentView::getInstance()->showContent($response->getData());

        } catch (Exception $e) {
            throw new Exception('Error executing controller: '.$e->getMessage());
        }

    }

    /**
     * @return ControllerInterface
     */
    private function resolveController(): ControllerInterface
    {
        return match (mb_strtolower($this->path)) {
            '/home' => Home\Back\Main::getInstance(),
            /*
            '/user-access' => UserAccess\Back\Main::getInstance(),
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
            '/admin' => Admin\Back\Main::getInstance(),
            default => Notfound\Back\Main::getInstance(),
            */
        };
    }
}