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

namespace Asset\Framework\I18n;

use Asset\Framework\Core\Files;
use Asset\Framework\Trait\SingletonTrait;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class that handles:
 *
 * @package Asset\Framework\I18n;
 */
class Lang
{

    use SingletonTrait;

    private const string DEFAULT_LANGUAGE = 'en';

    private const array WORK_SPACES_PATH
        = [
            'Repository/Default',
            'Repository/Project',
        ];

    private const string DEFAULT_DIC = 'Asset/resource/dic';

    private const array SUPPORTED_LANGUAGES = ['es', 'en', 'fr', 'pt'];

    /**
     * Get the current language setting
     *
     * @return string The language code
     */
    public static function getLang(): string
    {
        return self::getFromSession()
            ?? self::getFromHostConfig()
            ?? self::DEFAULT_LANGUAGE;
    }

    /**
     * Get language from session if available
     *
     * @return string|null
     */
    private static function getFromSession(): ?string
    {
        return $_SESSION['USER']['PREFERENCES']['LANG'] ?? null;
    }

    /**
     * Get language from host configuration if available
     *
     * @return string|null
     */
    private static function getFromHostConfig(): ?string
    {
        if (isset(CONFIG->app->host) && method_exists(CONFIG->app->host, 'getLang')) {
            return CONFIG->app->host->getLang();
        }

        return null;
    }

    public function getWorkSpacesList(): array
    {
        $workspaces = [];

        foreach (self::WORK_SPACES_PATH as $basePath) {

            $directory = Files::getInstance()->getAbsolutePath(
                implode(DS, [PD, $basePath, ''])
            );

            if (is_dir($directory)) {
                $directories = array_filter(
                    glob($directory.'/*', GLOB_ONLYDIR),
                    function ($directory) {
                        $dicPath = $directory.'/dic';
                        if (!is_dir($dicPath)) {
                            return false;
                        }

                        $jsonFiles = glob($dicPath.'/*.json');

                        return !empty($jsonFiles);
                    }
                );

                foreach ($directories as $dir) {
                    $workspaces[] = implode(DS, [$basePath, basename($dir)]);
                }
            }
        }

        return array_unique($workspaces);
    }


    /**
     * Finds and retrieves all JSON dictionary files from the workspaces 'dic' directory
     *
     * @param string $workspace The workspace path to search in
     * @return array Returns an array containing file paths and names of found dictionaries
     * @throws Exception If workspace or dic directory is not accessible
     */
    public function findDictionaryFiles(string $workspace): array
    {
        $dictionaryFiles = [];


        try {

            $dir = implode(DS, [PD, $workspace, 'dic']);

            if (!is_dir($dir)) {
                throw new Exception("Invalid workspace directory: $dir");
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'json') {
                    $relativePath      = implode(DS, [$dir, $file->getBasename()]);
                    $dictionaryFiles[] = [
                        'path'          => $file->getPathname(),
                        'name'          => pathinfo($relativePath, PATHINFO_FILENAME),
                        'relative_path' => $relativePath,
                    ];
                }
            }

            usort($dictionaryFiles, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return $dictionaryFiles;

        } catch (Exception $e) {
            throw new Exception("Error scanning dictionary files: ".$e->getMessage());
        }
    }

    public function parseDictionaryToTable(array $dictionaryFiles): string
    {

        $tableData = ['data' => []];
        $rowId     = 1;


        foreach ($dictionaryFiles as $file) {
            $jsonContent = file_get_contents($file['path']);
            $dictionary  = json_decode($jsonContent, true);

            if (!isset($dictionary['translations'])) {
                continue;
            }

            // Get all translation keys from first language
            $firstLang       = reset($dictionary['translations']);
            $translationKeys = array_keys($firstLang);

            foreach ($translationKeys as $key) {
                $translations = [];

                // Build translations string for data-lh-content attribute
                foreach (self::SUPPORTED_LANGUAGES as $lang) {
                    if (isset($dictionary['translations'][$lang][$key])) {
                        $translations[] = $lang.':'.$dictionary['translations'][$lang][$key];
                    }
                }
                $translationsStr = implode(',', $translations);

                // Determine status and priority based on translations completeness
                $completedCount = count($translations);
                $status         = $completedCount === count(self::SUPPORTED_LANGUAGES) ? 'completed' : 'pending';
                $priority       = $completedCount < 2 ? 'high' : ($completedCount < 3 ? 'medium' : 'low');

                $tableData['data'][] = [
                    'row'           => $rowId,
                    'key'           => $key,
                    'status'        => $status,
                    'priority'      => $priority,
                    'btn-translate' => sprintf(
                        "<button class=\"btn btn-primary btn-xs translate-btn\" data-key=\"%s\" data-lh-pl=\"translate-content\" data-lh-content=\"%s\">{{translate}}</button>",
                        htmlspecialchars($key),
                        htmlspecialchars($translationsStr)
                    ),
                    'btn-report'    => sprintf(
                        "<button class=\"btn btn-warning btn-xs report-btn\" data-key=\"%s\">{{report}}</button>",
                        htmlspecialchars($key)
                    ),
                ];

                $rowId++;
            }
        }

        return json_encode($tableData);

    }

}