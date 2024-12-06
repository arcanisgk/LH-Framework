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

namespace Asset\Framework\Http;

use Asset\Framework\Routing\RouteDictionary;
use Asset\Framework\Template\Deployment;
use Asset\Framework\Trait\SingletonTrait;
use Exception;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class that handles: Routing request
 *
 * @package Asset\Framework\Http;
 */
class Route
{

    use SingletonTrait;

    /**
     * @var RouteDictionary
     */
    private RouteDictionary $dictionary;

    /**
     *
     */
    public function __construct()
    {
        $this->dictionary = RouteDictionary::getInstance();
    }

    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->handleDefaultRedirect();
        $this->buildDispatch();

    }

    /**
     * @return void
     */
    private function handleDefaultRedirect(): void
    {
        if (UR === '/') {
            Request::getInstance()->redirect(CONFIG->app->host->getEntry());
        }
    }

    /**
     * @return void
     */
    private function buildDispatch(): void
    {
        $currentPath = $this->extractBasePath(parse_url(UR, PHP_URL_PATH));


        if ($this->matchRoute($_SERVER['REQUEST_METHOD'], $currentPath)) {
            Middleware::processMiddleware();
            $this->dispatch($currentPath);

            return;
        }

        $this->dispatch('/not-found');
    }

    /**
     * @param string $path
     * @return string
     */
    private function extractBasePath(string $path): string
    {
        $parts = explode('/', $path);

        return '/'.($parts[1] ?? '');
    }

    /**
     * @param string $method
     * @param string $path
     * @return bool
     */
    private function matchRoute(string $method, string $path): bool
    {
        $requestPath     = parse_url(UR, PHP_URL_PATH);
        $requestBasePath = $this->extractBasePath($requestPath);

        return $method === $_SERVER['REQUEST_METHOD'] && $path === $requestBasePath;
    }

    /**
     * @param $path
     * @return void
     */
    private function dispatch($path): void
    {
        try {
            $controller = $this->dictionary->setPath($path)->resolveController();
            $response   = $controller->process();
            Deployment::getInstance()->showContent($response);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * @param Exception $e
     * @return void
     */
    #[NoReturn] private function handleError(Exception $e): void
    {
        // Manejo de errores específico
        Request::getInstance()->redirect('/not-found');
    }

}
