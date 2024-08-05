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

use Project\Default\{
    NotFound\Controller\Dispatcher as NotFound,
};

/**
 * Class RequestController
 * A simple ...
 */
class RouteController
{
    /**
     * @var RouteController|null Singleton instance of the RequestController.
     */
    private static ?self $instance = null;
    private string $path;

    private static array $routeMap
        = [
            '/signup'         => '',
            '/useractivation' => '',
            '/useraccess'     => '',
            '/userlogout'     => '',
            '/lostaccount'    => '',
            '',

        ];
    private string $controller_path = '';

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
     */
    public function execute(): void
    {
        $path = mb_strtolower($this->path);

        $controllerClass = NotFound::class;

        /*
        if (isset(self::$routeMap[$path])) {
            $controllerClass = self::$routeMap[$path];
        } elseif (file_exists($this->controller_path)) {
            $controllerClass = $this->controller_path;
        } else {

        }
        */
        $main   = $controllerClass::getInstance();
        $result = $main->index();
        //ex($result);
        //$this->setResponse();
        //DeploymentView::getInstance()->showContent($this->getResponse());
    }
}