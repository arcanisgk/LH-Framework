<?php

declare(strict_types=1);

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
     * AutoloaderClass constructor.
     * Registers the autoloader function.
     */
    public function __construct()
    {
        $this->register();
    }


    /**
     * Autoload the class.
     *
     * @param  string  $class  The class name.
     *
     * @return void
     * @throws RuntimeException When the class file is not found.
     */
    public function autoload(string $class): void
    {
        $filePath = $this->buildSafeFilePath($class);
        if ($this->isValidClassName($class) && file_exists($filePath)) {
            require_once $filePath;

            return;
        }
        $this->handleClassNotFound($class);
    }

    /**
     * Validate the class name.
     *
     * @param  string  $class  The class name.
     *
     * @return bool True if the class name is valid, false otherwise.
     */
    private function isValidClassName(string $class): bool
    {
        return preg_match('/^[a-zA-Z0-9\\\\]+$/', $class) === 1;
    }

    /**
     * Build a safe file path based on the class name.
     *
     * @param  string  $class  The relative class name.
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
        $filename  = PD . DS . $safePath . '.php';

        return realpath($filename);
    }

    /**
     * Handle the case when the class is not found.
     *
     * @param  string  $class  The class name.
     *
     * @return void
     * @throws RuntimeException
     */
    private function handleClassNotFound(string $class): void
    {
        throw new RuntimeException("Class $class not found");
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
}