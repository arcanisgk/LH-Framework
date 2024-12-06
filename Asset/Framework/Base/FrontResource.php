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

namespace Asset\Framework\Base;

use Asset\Framework\Trait\SingletonTrait;
use Asset\Framework\Core\{Error, Files};
use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Class that handles: General, Generic and Static resources from backend to fronted like JS and CSS
 *
 * @package Asset\Framework\Base;
 */
class FrontResource
{

    use SingletonTrait;

    private const array RESOURCE_TYPES
        = [
            'script' => 'js',
            'style'  => 'css',
        ];

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @var array Stores generated HTML assets.
     */
    private array $htmlAssets = [];

    /**
     * @var Files
     */
    private Files $fileManager;

    /**
     * FrontResources constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->fileManager = Files::getInstance();

        try {

            $calledClass = get_called_class();
            $directory   = $this->resolveDirectory($calledClass);
            $this->setPath($directory)
                ->deployResources();
        } catch (Exception $e) {
            Error::getInstance()->throwError($e->getMessage());
        }
    }

    /**
     * @param string $calledClass
     * @return string
     * @throws Exception
     */
    private function resolveDirectory(string $calledClass): string
    {
        try {
            $reflector = new ReflectionClass($calledClass);

            return str_replace(
                ['\Back', '/Back'],
                '',
                $this->fileManager->getAbsolutePath(dirname($reflector->getFileName()))
            );
        } catch (ReflectionException $e) {
            Error::getInstance()->throwError($e->getMessage());
        }
    }

    /**
     * @return self
     */
    private function deployResources(): self
    {
        foreach (self::RESOURCE_TYPES as $type => $extension) {
            $this->processResourceDeployment($type, $extension);
        }

        return $this;
    }

    /**
     * @param string $type
     * @param string $extension
     * @return void
     */
    private function processResourceDeployment(string $type, string $extension): void
    {
        $workDir       = $this->getWorkingDirectory();
        $resourcePaths = $this->buildResourcePaths($type, $extension, $workDir);
        $this->deployFile($resourcePaths);
    }

    /**
     * @return string
     */
    private function getWorkingDirectory(): string
    {
        $pathComponents = explode(DIRECTORY_SEPARATOR, $this->path);

        return strtolower(end($pathComponents) ?: '');
    }

    /**
     * @param string $type
     * @param string $extension
     * @param string $workDir
     * @return array
     */
    private function buildResourcePaths(string $type, string $extension, string $workDir): array
    {
        return [
            $this->fileManager->getAbsolutePath($this->path."/front/$type.$extension"),
            $this->fileManager->getAbsolutePath(PATHS['PUBLIC_SOURCES']."/$extension/work/$workDir/$type.$extension"),
        ];
    }

    /**
     * @param array $paths
     * @return void
     */
    private function deployFile(array $paths): void
    {
        [$source, $destination] = $paths;

        if (!$this->isFileUpToDate($source, $destination)) {
            $this->fileManager->fileCopy($paths);
        }
    }

    /**
     * @param string $source
     * @param string $destination
     * @return bool
     */
    private function isFileUpToDate(string $source, string $destination): bool
    {
        return file_exists($destination) && filesize($source) === filesize($destination);
    }

    /**
     * @return array
     */
    public function getHtmlAssets(): array
    {
        return $this->htmlAssets;
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
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
}