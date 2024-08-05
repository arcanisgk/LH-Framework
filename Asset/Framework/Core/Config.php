<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Requiered).
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

/**
 * Class Config
 * A simple ...
 */
class Config
{
    /**
     * @var Config|null Singleton instance of the Config.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of Config.
     *
     * @return Config The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @var array
     */
    public array $configurations = [];

    /**
     * @return void
     */
    public function loadConfiguration(): void
    {
        if (!defined('CONFIG')) {

            $jsonFile = glob(implode(DS, [PD, 'Asset', 'resource', 'config', '*.json']));
            foreach ($jsonFile as $file) {
                $jsonContent = file_get_contents($file);
                $configData  = $this->configArrayToUpperCase(json_decode($jsonContent, true));
                if ($jsonContent !== false) {
                    $constantName                        = strtoupper(basename($file, '.json'));
                    $this->configurations[$constantName] = $configData;
                }
            }
            define('CONFIG', $this->configurations);
        }
    }

    /**
     * @param mixed $jsonContent
     *
     * @return array
     */
    private function configArrayToUpperCase(mixed $jsonContent): array
    {
        $result = [];
        foreach ($jsonContent as $key => $value) {
            $uppercaseKey = strtoupper($key);
            if (is_array($value)) {
                $result[$uppercaseKey] = $this->configArrayToUpperCase($value);
            } else {
                $result[$uppercaseKey] = $value;
            }
        }

        return $result;
    }
}