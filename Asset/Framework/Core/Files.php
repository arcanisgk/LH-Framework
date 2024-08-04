<?php

declare(strict_types=1);

namespace Asset\Framework\Core;

/**
 * Class Files
 * A simple ...
 */
class Files
{
    /**
     * @var Files|null Singleton instance of the Files.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of Files.
     *
     * @return Files The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param  string  $directory
     *
     * @return bool
     */
    public function validateDirectory(string $directory): bool
    {
        return is_dir($directory);
    }

    /**
     * @param  string  $directory
     *
     * @return void
     */
    public function createDirectory(string $directory): void
    {
        mkdir($directory, 0777, true);
    }


    /**
     * @param  string  $path
     * @param  array   $data
     *
     * @return string
     */
    public function fileLoader(string $path, array $data = []): ?string
    {
        $fullPath = PD . DS . $path;

        if (!file_exists($fullPath)) {
            $fileContent = null;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $fullPath;

        return ob_get_clean();
    }

}