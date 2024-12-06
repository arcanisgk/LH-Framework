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

use Asset\Framework\Trait\SingletonTrait;
use Exception;
use RuntimeException;

/**
 * Class that handles: Framework Configuration Usage
 *
 * @package Asset\Framework\Core\ConfigLoader;
 */
class ConfigLoader
{

    use SingletonTrait;

    private const string ENVIRONMENT_FILE = 'environment.json';

    private const array CONFIG_ENVIRONMENTS = ['local', 'dev', 'qa', 'pro'];

    private const string CONFIG_PATH = 'Asset'.DS.'resource'.DS.'config';

    /**
     * @var array
     */
    private array $configCache = [];

    /**
     * @var string
     */
    private string $currentEnvironment;

    /**
     * @return bool
     */
    public static function checkFullConfig(): bool
    {

        return CONFIG->app->project->getProjectConfig();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function loadConfigurations(): void
    {
        if (!defined('CONFIG')) {
            $configs      = $this->loadAndValidateConfigs();
            $configObject = $this->createConfigObject($configs);
            define('CONFIG', $configObject);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    private function loadAndValidateConfigs(): array
    {
        $this->loadEnvironment();

        return $this->loadConfigurationFiles();
    }

    /**
     * @return void
     */
    private function loadEnvironment(): void
    {
        $envFile = PD.DS.self::CONFIG_PATH.DS.self::ENVIRONMENT_FILE;

        if (!file_exists($envFile)) {
            throw new RuntimeException("Environment configuration file not found: $envFile");
        }

        $envContent = file_get_contents($envFile);
        if ($envContent === false) {
            throw new RuntimeException("Unable to read environment configuration file");
        }

        $envData = json_decode($envContent, true);
        if (!isset($envData['environment']) || !in_array($envData['environment'], self::CONFIG_ENVIRONMENTS)) {
            throw new RuntimeException("Invalid environment specified in configuration");
        }

        $this->currentEnvironment = $envData['environment'];
    }

    /**
     * @return array
     */
    private function loadConfigurationFiles(): array
    {
        $configs    = [];
        $configPath = PD.DS.self::CONFIG_PATH.DS.$this->currentEnvironment;

        if (!is_dir($configPath)) {
            throw new RuntimeException(
                "Configuration directory not found for environment: $this->currentEnvironment"
            );
        }

        $jsonFiles = glob($configPath.DS.'*.json');
        if (empty($jsonFiles)) {

            $backupPath = PD.DS.self::CONFIG_PATH.DS.'backup';
            $jsonFiles  = glob($backupPath.DS.'*.json');

            if (empty($jsonFiles)) {
                throw new RuntimeException("No configuration files found in environment or backup directory");
            }
        }

        foreach ($jsonFiles as $file) {
            $jsonContent = file_get_contents($file);
            if ($jsonContent === false) {
                continue;
            }

            $configData = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException("Invalid JSON in configuration file: ".basename($file));
            }

            $section           = strtolower(basename($file, '.json'));
            $configs[$section] = $this->convertKeysToCamelCase($configData);
        }
        $configs['environment'] = $this->currentEnvironment;
        
        return $configs;
    }

    /**
     * @param array $array
     * @return array
     */
    private function convertKeysToCamelCase(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $camelKey          = lcfirst(
                str_replace(
                    ' ',
                    '',
                    ucwords(
                        str_replace(
                            '_',
                            ' ',
                            str_replace('-', ' ', $key)
                        )
                    )
                )
            );
            $result[$camelKey] = is_array($value) ? $this->convertKeysToCamelCase($value) : $value;
        }

        return $result;
    }

    /**
     * @param array $data
     * @return object
     */
    private function createConfigObject(array $data): object
    {

        return new class($data) {

            private array $properties;

            public function __construct(array $data)
            {
                $this->properties = array_map(
                    fn($value) => is_array($value) ? new self($value) : $value,
                    $data
                );
            }

            public function __get(string $name)
            {
                if (isset($this->properties[$name]) && $this->properties[$name] instanceof self) {
                    return $this->properties[$name];
                }

                throw new Exception(
                    "Direct property access to '$name' is not allowed. Use getter methods instead (e.g. get".ucfirst(
                        $name
                    )."())"
                );
            }

            public function __call(string $name, array $arguments): mixed
            {
                if (!str_starts_with($name, 'get')) {
                    throw new Exception("Only getter methods are allowed. Method '$name' not found.");
                }
                $property = lcfirst(substr($name, 3));
                if (isset($this->properties[$property])) {
                    return $this->properties[$property];
                }
                throw new Exception("Getter method '$name' does not exist.");
            }

            public function __debugInfo()
            {
                return ['debug' => '### Only For Implementation Purposes ###', 'Usage' => $this->buildDebugInfo()];
            }

            private function buildDebugInfo(mixed &$debugInfo = null, string $parentPath = null): mixed
            {

                foreach ($this->properties as $key => $value) {

                    if ($value instanceof self) {
                        $currentPath     = $parentPath ? $parentPath.'->'.$key : 'CONFIG->'.$key;
                        $debugInfo[$key] = [];
                        $value->buildDebugInfo($debugInfo[$key], $currentPath);
                    } else {
                        $path            = $parentPath ?? 'CONFIG';
                        $debugInfo[$key] = [
                            'type'     => '[getter]',
                            'value'    => $value,
                            'property' => $key,
                            'path'     => $path.'->get'.ucfirst($key).'()',
                        ];
                    }
                }

                return $debugInfo;
            }
        };
    }
}