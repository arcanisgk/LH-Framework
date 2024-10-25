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

use Exception;

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
     * @param string $directory
     *
     * @return bool
     */
    public function validateDirectory(string $directory): bool
    {
        return is_dir($directory);
    }

    /**
     * @param string $directory
     *
     * @return void
     */
    public function createDirectory(string $directory): void
    {
        mkdir($directory, 0777, true);
    }


    /**
     * @param string $path
     * @param array $data
     *
     * @return string|null
     */
    public function fileLoader(string $path, array $data = []): ?string
    {
        $fullPath = PD.DS.$path;

        if (!file_exists($fullPath)) {
            $fileContent = null;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $fullPath;

        return ob_get_clean();
    }

    public function getAbsolutePath(string $path): string
    {
        $path               = mb_ereg_replace('\\\\|/', DS, $path);
        $startWithSeparator = $path[0] === DS;
        preg_match('/^[a-z]:/i', $path, $matches);
        $startWithLetterDir = $matches[0] ?? false;

        $subPaths  = array_filter(explode(DS, $path), 'mb_strlen');
        $absolutes = [];

        foreach ($subPaths as $subPath) {
            $absolutes = match ($subPath) {
                '.' => $absolutes,
                '..' => empty($absolutes) || (!$startWithSeparator && !$startWithLetterDir) ?
                    array_merge($absolutes, [$subPath]) :
                    array_slice($absolutes, 0, -1),
                default => array_merge($absolutes, [$subPath]),
            };
        }

        $prefix = $startWithSeparator ? DS : ($startWithLetterDir ? $startWithLetterDir.DS : '');

        return $prefix.implode(DS, $absolutes);
    }

    /**
     * @param array $resources
     * @return bool
     */
    public function fileCopy(array $resources): bool
    {
        $path = pathinfo($resources[1]);
        if (!file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0777, true);
        }
        if (!copy($resources[0], $resources[1])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $directory
     * @return void
     * @throws Exception
     */
    public function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new Exception("Directory '$directory' was not created");
        }
    }

}