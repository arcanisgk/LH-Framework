<?php

declare(strict_types=1);

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

    private static array $routeMap = [
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
     * @param  string  $path
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