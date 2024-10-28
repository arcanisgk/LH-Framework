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

namespace Asset\Framework\Core;

use BadMethodCallException;

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core\ConfigLoader;
 */
class ConfigLoader
{

    /**
     * @var ConfigLoader|null Singleton instance of the class: ConfigLoader.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class ConfigLoader.
     *
     * @return ConfigLoader The singleton instance.
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * @return bool
     */
    public static function checkFullConfig(): bool
    {

        return CONFIG->app->project->getProjectConfig();
    }

    /**
     * @return void
     */
    public function loadConfigurations(): void
    {
        if (!defined('CONFIG')) {
            $jsonFiles = glob(implode(DS, [PD, 'Asset', 'resource', 'config', '*.json']));
            $configs   = [];

            foreach ($jsonFiles as $file) {
                $jsonContent = file_get_contents($file);
                if ($jsonContent === false) {
                    continue;
                }

                $configData        = json_decode($jsonContent, true);
                $section           = strtolower(basename($file, '.json'));
                $configs[$section] = $this->createConfigObject($this->convertKeysToCamelCase($configData));
            }

            $configurations = $this->createConfigObject($configs);
            define('CONFIG', $configurations);
        }
    }

    private function convertKeysToCamelCase(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $camelKey          = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            $result[$camelKey] = is_array($value) ? $this->convertKeysToCamelCase($value) : $value;
        }

        return $result;
    }


    private function createConfigObject(array $data): object
    {
        return new class($data) {
            private array $data;

            public function __construct(array $data)
            {
                $this->data = array_map(
                    fn($value) => is_array($value) ? new self($value) : $value,
                    $data
                );
            }

            public function __get(string $name)
            {
                return $this->data[$name] ?? null;
            }

            public function __call(string $name, array $arguments)
            {
                if (str_starts_with($name, 'get')) {
                    $property = lcfirst(substr($name, 3));

                    return $this->data[$property] ?? null;
                }
                throw new BadMethodCallException("Method $name not found");
            }
        };
    }

}