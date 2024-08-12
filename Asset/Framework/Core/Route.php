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

use Asset\Framework\Controller\RouteController;
use Exception;
use Throwable;

/**
 * Class that handles: Routing request
 *
 * @package Asset\Framework\Core;
 */
class Route
{
    /**
     * @var Route|null Singleton instance of the Route.
     */
    private static ?self $instance = null;

    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @var array
     */
    private array $middlewares = [];

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
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        $this->redirectDefault();
        $this->setDefaultRoutes();
        $this->run();
    }

    /**
     * @return void
     */
    private function redirectDefault(): void
    {
        if (UR === '/') {
            Request::getInstance()->redirect(CONFIG['APP']['HOST']['ENTRY']);
        }
    }


    /**
     * @return void
     */
    private function setDefaultRoutes(): void
    {
        $this->addRoute('GET', UR, [RouteController::class, 'execute']);
    }

    /**
     * @param string $method
     * @param string $path
     * @param callable|array $handler
     * @return void
     */
    public function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route['method'], $route['path'])) {

                Middleware::processMiddleware();
                [$controller, $method] = $route['handler'];
                $instance = $controller::getInstance($route['path']);

                try {
                    $instance->$method();
                } catch (Throwable $e) {
                    throw new Exception('Error executing route: '.$e->getMessage());
                }

                return;
            }
        }

        $this->redirectNotFound();
    }

    /**
     * @param string $method
     * @param string $path
     * @return bool
     */
    private function matchRoute(string $method, string $path): bool
    {
        return $method === $_SERVER['REQUEST_METHOD'] && $path === parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * @return void
     */
    private function redirectNotFound(): void
    {
        $this->addRoute('GET', '/not-found', [RouteController::class, 'execute']);
        $route = $this->routes[0];
        [$controller, $method] = $route['handler'];
        $instance = $controller::getInstance($route['path']);
        $instance->$method();
    }
}
