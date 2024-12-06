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

use Asset\Framework\Trait\SingletonTrait;
use Exception;

/**
 * Class Files
 * A simple ...
 */
class Files
{
    use SingletonTrait;

    private const int DIRECTORY_PERMISSIONS = 0755;

    private const int FILE_PERMISSIONS = 0644;

    /**
     * Creates a directory with proper permissions
     * @param string $directory
     * @param int $permissions
     * @return void
     */
    public function createDirectory(string $directory, int $permissions = self::DIRECTORY_PERMISSIONS): void
    {
        if (!$this->validateDirectory($directory)) {
            mkdir($directory, $permissions, true);
        }
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
     * Enhanced file loader with better error handling and type safety
     * @param string $path
     * @param array $data
     *
     * @return string|null
     * @throws Exception
     */
    public function fileLoader(string $path, array $data = []): ?string
    {
        $fullPath = $this->buildFullPath($path);

        if (!is_file($fullPath)) {
            return null;
        }

        try {
            extract($data, EXTR_SKIP);
            ob_start();
            include $fullPath;

            return ob_get_clean();
        } catch (Exception $e) {
            ob_end_clean();
            throw new Exception("Failed to load file: $fullPath", 0, $e);
        }
    }

    /**
     * Private helper methods
     * @param string $path
     * @return string
     */
    private function buildFullPath(string $path): string
    {
        return PD.DS.$path;
    }


    /**
     * Builds absolute path with proper directory separators
     * @param string $path
     * @return string
     */
    public function getAbsolutePath(string $path): string
    {
        $normalizedPath = $this->normalizePath($path);
        $pathInfo       = $this->parsePathComponents($normalizedPath);

        return $this->buildNormalizedPath($pathInfo);
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string
    {
        return mb_ereg_replace('\\\\|/', DS, $path);
    }

    /**
     * @param string $normalizedPath
     * @return array
     */
    private function parsePathComponents(string $normalizedPath): array
    {
        $startWithSeparator = $normalizedPath[0] === DS;
        preg_match('/^[a-z]:/i', $normalizedPath, $matches);
        $startWithLetterDir = $matches[0] ?? false;

        return [
            'parts'              => array_filter(explode(DS, $normalizedPath), 'mb_strlen'),
            'startWithSeparator' => $startWithSeparator,
            'startWithLetterDir' => $startWithLetterDir,
        ];
    }

    /**
     * @param array $pathInfo
     * @return string
     */
    private function buildNormalizedPath(array $pathInfo): string
    {
        $absolutes = $this->resolvePathParts(
            $pathInfo['parts'],
            $pathInfo['startWithSeparator'],
            $pathInfo['startWithLetterDir']
        );
        $prefix    = $this->determinePathPrefix($pathInfo['startWithSeparator'], $pathInfo['startWithLetterDir']);

        return $prefix.implode(DS, $absolutes);
    }

    /**
     * @param array $parts
     * @param bool $startWithSeparator
     * @param $startWithLetterDir
     * @return array
     */
    private function resolvePathParts(array $parts, bool $startWithSeparator, $startWithLetterDir): array
    {
        $absolutes = [];
        foreach ($parts as $part) {
            if ($part === '.') {
                continue;
            }
            if ($part === '..' && !empty($absolutes) &&
                ($startWithSeparator || $startWithLetterDir)) {
                array_pop($absolutes);
                continue;
            }
            $absolutes[] = $part;
        }

        return $absolutes;
    }

    /**
     * @param bool $startWithSeparator
     * @param $startWithLetterDir
     * @return string
     */
    private function determinePathPrefix(bool $startWithSeparator, $startWithLetterDir): string
    {
        if ($startWithSeparator) {
            return DS;
        }

        return $startWithLetterDir ? $startWithLetterDir.DS : '';
    }

    /**
     * Enhanced file copy with better error handling
     * @param array $resources
     * @return bool
     * @throws Exception
     */
    public function fileCopy(array $resources): bool
    {
        if (!isset($resources[0], $resources[1])) {
            throw new Exception('Source and destination paths are required');
        }

        $source      = $resources[0];
        $destination = $resources[1];

        if (!file_exists($source)) {
            throw new Exception("Source file does not exist: $source");
        }

        $destinationDir = dirname($destination);
        $this->ensureDirectoryExists($destinationDir);

        return copy($source, $destination);
    }

    /**
     * Ensures directory exists with proper error handling
     * @param string $directory
     * @return void
     * @throws Exception
     */
    public function ensureDirectoryExists(string $directory): void
    {
        if (!$this->validateDirectory($directory) &&
            !mkdir($directory, self::DIRECTORY_PERMISSIONS, true) &&
            !is_dir($directory)) {
            throw new Exception("Failed to create directory: $directory");
        }
    }
}