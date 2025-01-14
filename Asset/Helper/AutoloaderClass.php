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

namespace Asset\Helper;

use RuntimeException;

/**
 * Class AutoloaderClass
 * A simple autoloader class for automatically loading classes in a secure manner.
 */
class AutoloaderClass
{

    /**
     * @var AutoloaderClass|null Singleton instance of the AutoloaderClass.
     */
    private static ?self $instance = null;

    /**
     * AutoloaderClass constructor.
     * Registers the autoloader function.
     */
    public function __construct()
    {
        $this->register();
    }

    /**
     * Registers the autoloader function in Composer.
     *
     * @return void
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'autoload']);
    }

    /**
     * Get the singleton instance of AutoloaderClass.
     *
     * @return AutoloaderClass The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Autoload the class.
     *
     * @param string $class The class name.
     *
     * @return void
     * @throws RuntimeException When the class file is not found.
     */
    public function autoload(string $class): void
    {
        $filePath = $this->buildSafeFilePath($class);
        if (!is_bool($filePath) && $this->isValidClassName($class) && file_exists($filePath)) {
            require_once $filePath;

            return;
        }
        $this->handleClassNotFound($class);
    }

    /**
     * Build a safe file path based on the class name.
     *
     * @param string $class The relative class name.
     *
     * @return string The safe file path.
     */
    private function buildSafeFilePath(string $class): string
    {
        $newClass  = str_replace('\\', DS, $class);
        $parts     = explode(DS, $newClass);
        $safeParts = array_map(function ($part) {
            return preg_replace('/[^a-zA-Z0-9]/', '', $part);
        }, $parts);
        $safePath  = implode(DS, $safeParts);
        $filename  = PD.DS.$safePath.'.php';

        return realpath($filename);
    }

    /**
     * Validate the class name.
     *
     * @param string $class The class name.
     *
     * @return bool True if the class name is valid, false otherwise.
     */
    private function isValidClassName(string $class): bool
    {
        return preg_match('/^[a-zA-Z0-9\\\\]+$/', $class) === 1;
    }

    /**
     * Handle the case when the class is not found.
     *
     * @param string $class The class name.
     *
     * @return void
     * @throws RuntimeException
     */
    private function handleClassNotFound(string $class): void
    {
        throw new RuntimeException("Class $class not found");
    }
}