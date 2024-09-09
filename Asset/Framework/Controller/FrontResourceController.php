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

use Asset\Framework\Core\Error;
use Asset\Framework\Core\Files;
use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Class that handles: General, Generic and Static resources from backend to fronted like JS and CSS
 *
 * @package Asset\Framework\Controller;
 */
class FrontResourceController
{

    /**
     * @var FrontResourceController|null Singleton instance of the class: FrontResources.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class FrontResources.
     *
     * @return FrontResourceController The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @var array
     */
    private array $link = [];

    /**
     * @var array Stores generated HTML assets.
     */
    private array $htmlAssets = [];

    /**
     * FrontResources constructor.
     * @throws Exception
     */
    public function __construct()
    {
        try {
            $called_class = get_called_class();
            $dir          = $this->getDir($called_class);
            $this->setPath($dir)
                ->deployJs()
                ->deployCss()
                ->generateLink();
        } catch (Exception $e) {
            Error::getInstance()->throwError($e->getMessage());
        }
    }

    /**
     * @param string $called_class
     * @return string|array
     * @throws Exception
     */
    private function getDir(string $called_class): string|array
    {
        try {
            $reflector = new ReflectionClass($called_class);

            return str_replace(
                ['\Back', '/Back'],
                '',
                Files::getInstance()->getAbsolutePath(dirname($reflector->getFileName()))
            );

        } catch (ReflectionException $e) {
            Error::getInstance()->throwError($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return FrontResourceController
     */
    public function setPath(string $path): FrontResourceController
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getLink(string $type): string
    {
        return $this->link[$type];
    }

    /**
     * @param string $type
     * @param string $link
     * @return FrontResourceController
     */
    private function setLink(string $type, string $link): FrontResourceController
    {
        $this->link[$type] = $link;

        return $this;
    }

    /**
     * Deploys the JavaScript file.
     *
     * @return $this
     */
    private function deployJs(): self
    {
        return $this->deployResource('script', 'js');
    }

    /**
     * Deploys the CSS file.
     *
     * @return $this
     */
    private function deployCss(): self
    {
        return $this->deployResource('style', 'css');
    }


    /**
     * Deploys a resource file (JS or CSS) by type.
     *
     * @param string $type
     * @param string $extension
     * @return $this
     */
    private function deployResource(string $type, string $extension): self
    {
        $workDir = strtolower($this->getPathDirectory($this->getPath()));
        $files   = Files::getInstance();

        $directories = [
            $files->getAbsolutePath("{$this->getPath()}/front/$type.$extension"),
            $files->getAbsolutePath(PATHS['PUBLIC_SOURCES']."/$extension/work/$workDir/$type.$extension"),
        ];

        $this->setLink($extension, "/assets/$extension/work/$workDir/$type.$extension")
            ->deployFile($directories);

        return $this;
    }

    /**
     * @param string $dir
     * @return string|false
     */
    private function getPathDirectory(string $dir): string|false
    {
        $slash    = str_contains($dir, '/') ? '/' : '\\';
        $segments = explode($slash, $dir);

        return end($segments) ?: false;
    }

    /**
     * @param array $directories
     * @return void
     */
    private function deployFile(array $directories): void
    {
        [$source, $destination] = $directories;

        if (!file_exists($destination) || filesize($source) !== filesize($destination)) {
            Files::getInstance()->fileCopy($directories);
        }
    }

    /**
     * Generates and stores the HTML links for CSS and JS.
     *
     * @return void
     */
    private function generateLink(): void
    {
        $this->htmlAssets = [
            'CSS' => [sprintf(/** @lang html */ '<link rel="stylesheet" href="%s">', $this->getLink('css'))],
            'JS'  => [],
        ];
    }

    /**
     * Retrieves the stored HTML assets.
     *
     * @return array
     */
    public function getHtmlAssets(): array
    {
        return $this->htmlAssets;
    }
}