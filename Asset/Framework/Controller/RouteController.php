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

use Asset\Framework\View\DeploymentView;
use Repository\Default\Home as Home;
use Exception;

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
        try {

            $controller = RouteDictionaryController::getInstance()
                ->setPath($this->path)
                ->resolveController();

            $response = $controller->process();
            DeploymentView::getInstance()->showContent($response);

        } catch (Exception $e) {
            throw new Exception('Error executing controller: '.$e->getMessage());
        }
    }
}