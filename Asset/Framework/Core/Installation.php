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

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core;
 */
class Installation
{

    use SingletonTrait;

    private const string CONFIG_PATH = 'Asset'.DS.'resource'.DS.'config';

    private const array CONFIG_TEMPLATES
        = [
            'email'    => [
                'mail_name',
                'mail_host',
                'mail_port',
                'mail_user',
                'mail_password',
                'mail_password_re',
                'mail_default',
                'mail_test_smg',
                'mail_protocol',
                'mail_authentication',
                'mail_debug',
                'mail_test',
            ],
            'ftp'      => [
                'ftp_name',
                'ftp_host',
                'ftp_port',
                'ftp_user',
                'ftp_password',
                'ftp_password_re',
                'ftp_path',
                'ftp_passive_mode',
            ],
            'database' => [
                'db_name',
                'db_host',
                'db_port',
                'db_user',
                'db_password',
                'db_password_re',
            ],
        ];

    /**
     * @var array
     */
    private array $configData = [];

    /**
     * @return bool
     */
    public function saveSetting(): bool
    {
        try {
            $this->processAllConfigurations();
            $this->writeAllConfigFiles();

            return true;
        } catch (Exception $e) {
            error_log("Configuration Error: ".$e->getMessage());

            return false;
        }
    }

    /**
     * @return void
     */
    private function processAllConfigurations(): void
    {
        $this->processAppConfig();
        $this->processEnvironmentConfig();
        $this->processErrorConfig();
        $this->processSessionConfig();
        $this->processMultiInstanceConfigs();
    }

    /**
     * @return void
     */
    private function processAppConfig(): void
    {
        $this->configData['app'] = [
            'company' => [
                'company_name'       => $_POST['company_name'] ?? '',
                'company_owner'      => $_POST['company_owner'] ?? '',
                'company_department' => $_POST['company_department'] ?? '',
            ],
            'project' => [
                'project_name'   => $_POST['project_name'] ?? '',
                'project_config' => true,
            ],
            'host'    => [
                'domain'   => $_POST['domain'] ?? '',
                'lang'     => $_POST['lang'] ?? 'en',
                'm-lang'   => filter_var($_POST['m_lang'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'protocol' => $_POST['protocol'] ?? 'https',
                'entry'    => $_POST['entry'] ?? '',
                'license'  => $_POST['license'] ?? '',
                'free'     => filter_var($_POST['free'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ],
        ];
    }

    /**
     * @return void
     */
    private function processEnvironmentConfig(): void
    {
        $this->configData['environment'] = [
            'environment' => $_POST['environment'] ?? 'dev',
            'app_setting' => filter_var($_POST['app_setting'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'dev_tool'    => filter_var($_POST['dev_tool'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    /**
     * @return void
     */
    private function processErrorConfig(): void
    {
        $this->configData['error'] = [
            'dev'        => filter_var($_POST['error_dev'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'test'       => filter_var($_POST['error_test'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'quality'    => filter_var($_POST['error_quality'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'production' => filter_var($_POST['error_production'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    /**
     * @return void
     */
    private function processSessionConfig(): void
    {
        $lifetime = $this->calculateTimeInSeconds(
            (int)($_POST['session_lifetime_hours'] ?? 0),
            (int)($_POST['session_lifetime_days'] ?? 30)
        );

        $activityExpire = $this->calculateTimeInSeconds(
            (int)($_POST['session_activity_hours'] ?? 0),
            (int)($_POST['session_activity_days'] ?? 2)
        );

        $this->configData['session'] = [
            'session' => [
                'session_name'            => $_POST['session_name'] ?? 'lh-2-session',
                'session_inactivity'      => filter_var($_POST['session_inactivity'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'session_lifetime'        => $lifetime,
                'session_activity_expire' => $activityExpire,
                'session_secure'          => filter_var($_POST['session_secure'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'session_http_only'       => filter_var($_POST['session_http_only'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'session_same_site'       => $_POST['session_same_site'] ?? 'Strict',
            ],
        ];
    }

    /**
     * @param int $hours
     * @param int $days
     * @return int
     */
    private function calculateTimeInSeconds(int $hours, int $days): int
    {
        return ($hours * 3600) + ($days * 86400);
    }

    /**
     * @return void
     */
    private function processMultiInstanceConfigs(): void
    {
        $this->processMultiConfig('mail', self::CONFIG_TEMPLATES['email']);
        $this->processMultiConfig('ftp', self::CONFIG_TEMPLATES['ftp']);
        $this->processMultiConfig('db', self::CONFIG_TEMPLATES['database']);
    }

    /**
     * @param string $type
     * @param array $template
     * @return void
     */
    private function processMultiConfig(string $type, array $template): void
    {
        $instances = $_POST["{$type}_instances"] ?? [];
        $configs   = [];

        foreach ($instances as $index => $instance) {
            $instanceKey           = "$type".($index + 1);
            $configs[$instanceKey] = [];

            foreach ($template as $field) {
                $value                         = $instance[$field] ?? '';
                $configs[$instanceKey][$field] = $this->normalizeValue($value);
            }
        }

        $this->configData[$type] = $configs;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function normalizeValue(mixed $value): mixed
    {
        if (is_numeric($value) && !str_contains((string)$value, '.')) {
            return (int)$value;
        }

        return match (strtolower((string)$value)) {
            'true' => true,
            'false' => false,
            default => $value
        };
    }

    /**
     * @return void
     * @throws Exception
     */
    private function writeAllConfigFiles(): void
    {
        foreach ($this->configData as $filename => $data) {
            $this->writeConfigFile($filename.'.json', $data);
        }
    }

    /**
     * @param string $filename
     * @param array $data
     * @return void
     * @throws Exception
     */
    private function writeConfigFile(string $filename, array $data): void
    {

        $filePath = implode(DS, [PD, self::CONFIG_PATH, $filename]);
        
        Files::getInstance()->ensureDirectoryExists(dirname($filePath));

        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($jsonData === false) {
            throw new Exception("Failed to encode JSON data for $filename");
        }

        if (file_put_contents($filePath, $jsonData) === false) {
            throw new Exception("Failed to write configuration file $filename");
        }
    }
}