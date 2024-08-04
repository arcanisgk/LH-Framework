<?php

declare(strict_types=1);

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

    private array $handlers;

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
     * @param  string  $path
     *
     * @return void
     */
    private function setHandler(string $path): void
    {
        $handler                    = [RouteController::class, 'execute'];
        $this->handlers[RQ . $path] = [
            'path'    => $path,
            'method'  => RQ,
            'handler' => $handler,
        ];
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $routeBuilder = null;
        foreach ($this->handlers as $handler) {
            if (RQ === $handler['method']) {
                $routeBuilder = $handler;
            }
        }
        /**
         * Normally run on:
         * namespace: Framework\Core\RequestController
         * Method: execute
         */
        if (is_array($routeBuilder)) {
            $path     = $routeBuilder['path'];
            $instance = $routeBuilder['handler'][0]::getInstance($path);
            $method   = $routeBuilder['handler'][1];
            $callback = [$instance, $method];
            call_user_func($callback);
        }
    }

    /**
     * @return void
     */
    public function initRoute(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
        $this->redirectDefault($uri);
        $this->setHandler($uri);
        $this->run();
    }

    private function redirectDefault(string $uri): void
    {
        if ($uri === '/') {
            Request::getInstance()->redirect(CONFIG['APP']['HOST']['ENTRY']);
        }
    }
}