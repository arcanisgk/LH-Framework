<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2017-2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Asset\Helper\Installer;

use Asset\{Framework\Core\Database, Framework\Core\Files};
use PHPMailer\PHPMailer\Exception;

/**
 * Class Installation
 * A simple for the installation and setup of adp
 */
class Installation
{
    /**
     * @var Installation|null Singleton instance of the Installation.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of Installation.
     *
     * @return Installation The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @var array|string[]
     */
    private array $templateConfEmail
        = [
            "mail_name",
            "mail_host",
            "mail_port",
            "mail_user",
            "mail_password",
            "mail_password_re",
            "mail_default",
            "mail_test_smg",
            "mail_protocol",
            "mail_authentication",
            "mail_debug",
            "mail_test",
        ];

    /**
     * @var array|string[]
     */
    private array $templateConfFTP
        = [
            "ftp_name",
            "ftp_host",
            "ftp_port",
            "ftp_user",
            "ftp_password",
            "ftp_password_re",
            "ftp_path",
            "ftp_passive_mode",
        ];

    /**
     * @var array|string[]
     */
    private array $templateConfDatabase
        = [
            "db_name",
            "db_host",
            "db_port",
            "db_user",
            "db_password",
            "db_password_re",
        ];

    /**
     *
     */
    public function __construct()
    {
        if (isset($_POST)) {
            echo $this->handleRequest() ? 'true' : 'false';
        } else {
            $templatePath = implode(DS, ['Asset', 'resource', 'template', 'install.html']);
            echo Files::getInstance()->fileLoader($templatePath);
        }
    }

    /**
     * @return bool
     */
    private function handleRequest(): bool
    {
        if (isset($_POST['Data-Base-Test'])) {
            return $this->testDatabaseConnection();
        } else {
            return $this->setJSON();
        }
    }

    /**
     * @return bool
     */
    private function testDatabaseConnection(): bool
    {
        $dbParams = [
            'host' => $_POST['i-database-host'],
            'db'   => $_POST['i-database-name'],
            'user' => $_POST['i-database-user'],
            'pass' => $_POST['i-database-password'],
        ];

        return Database::getInstance()->testConnection($dbParams);
    }

    /**
     * @param array $configArray
     * @param array $passwordArray
     * @param string $type
     * @param array $nodes
     * @return array
     */
    function replacePlaceholdersWithPasswords(
        array $configArray,
        array $passwordArray,
        string $type,
        array $nodes
    ): array {
        $result        = [];
        $passwordIndex = 0;

        foreach ($configArray as $index => $config) {
            $config = preg_replace_callback('/\*\*\*/', function () use (&$passwordIndex, $passwordArray) {
                return $passwordArray[$passwordIndex++] ?? '';
            }, $config, 2);

            $arrayString = explode(',', $config);
            $configArray = [];
            foreach ($nodes as $key => $nodeName) {
                $value = $arrayString[$key] ?? '';
                $value = is_numeric($value) && !str_contains($value, '.')
                    ? (int)$value
                    : ($value === 'true' ? true : ($value === 'false' ? false : $value));

                $configArray[$nodeName] = $value;
            }
            $result[$type.($index + 1)] = $configArray;
        }

        return $result;
    }

    /**
     * @return void
     */
    private function processSessionConfig(): void
    {
        $hours                     = (int)($_POST['json-session-lifetime-hours'] ?? 0);
        $days                      = (int)($_POST['json-session-lifetime-days'] ?? 0);
        $_POST['session_lifetime'] = ($hours * 3600) + ($days * 86400);

        $hours                            = (int)($_POST['json-session-activity-expire-hours'] ?? 0);
        $days                             = (int)($_POST['json-session-activity-expire-days'] ?? 0);
        $_POST['session_activity_expire'] = ($hours * 3600) + ($days * 86400);
    }

    /**
     * @return void
     */
    private function processMailConfig(): void
    {
        $_POST['json-mail-conf'] = $this->replacePlaceholdersWithPasswords(
            $_POST['json-mail-conf'] ?? [],
            $_POST['json-mail-conf-password'] ?? [],
            'mail',
            $this->templateConfEmail
        );
    }

    /**
     * @return void
     */
    private function processFtpConfig(): void
    {
        $_POST['json-ftp-conf'] = $this->replacePlaceholdersWithPasswords(
            $_POST['json-ftp-conf'] ?? [],
            $_POST['json-ftp-conf-password'] ?? [],
            'ftp',
            $this->templateConfFTP
        );
    }

    /**
     * @return void
     */
    private function processDatabaseConfig(): void
    {
        $_POST['json-database-conf'] = $this->replacePlaceholdersWithPasswords(
            $_POST['json-database-conf'] ?? [],
            $_POST['json-database-conf-password'] ?? [],
            'db',
            $this->templateConfDatabase
        );
    }

    /**
     * @return void
     */
    private function processConfigurations(): void
    {
        $this->processSessionConfig();
        $this->processMailConfig();
        $this->processFtpConfig();
        $this->processDatabaseConfig();
    }

    /**
     * @return array[]
     */
    private function getAppConfig(): array
    {
        return [
            'company' => [
                'company_name'  => $_POST['json-company-name'] ?? '',
                'company_owner' => $_POST['json-company-owner'] ?? '',
            ],
            'project' => [
                'project_name'   => $_POST['json-project-name'] ?? '',
                'project_config' => true,
            ],
            'host'    => [
                'domain'   => $_POST['json-domain'] ?? '',
                'lang'     => $_POST['json-lang'] ?? '',
                'm-lang'   => $_POST['json-m-lang'] ?? '',
                'protocol' => $_POST['json-protocol'] ?? '',
                'entry'    => $_POST['json-entry'] ?? '',
                'license'  => $_POST['json-license'] ?? '',
                'free'     => $_POST['json-chk-license'] ?? false,
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getSessionConfig(): array
    {
        return [
            'session' => [
                'session_name'            => $_POST['json-session-name'] ?? '',
                'session_inactivity'      => $_POST['json-session-inactivity'] ?? '',
                'session_lifetime'        => $_POST['session_lifetime'] ?? 0,
                'session_activity_expire' => $_POST['session_activity_expire'] ?? 0,
                'session_secure'          => $_POST['json-session-secure'] ?? false,
                'session_httponly'        => $_POST['json-session-http-only'] ?? false,
                'session_same_site'       => ($_POST['json-session-same-site'] ?? false) ? 'Strict' : 'Lax',
            ],
        ];
    }

    /**
     * @param string $directory
     * @return void
     * @throws Exception
     */
    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new Exception("Directory '$directory' was not created");
        }
    }

    /**
     * @param string $fileName
     * @param array $config
     * @return void
     * @throws Exception
     */
    private function writeConfigFile(string $fileName, array $config): void
    {
        $filePath = implode(DS, [PD, 'Asset', 'resource', 'config', $fileName]);
        $this->ensureDirectoryExists(dirname($filePath));

        $encodedConfig = json_encode(
            $config,
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
        file_put_contents($filePath, $encodedConfig);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function writeConfigFiles(): void
    {
        $configFiles = [
            'app.json'     => $this->getAppConfig(),
            'session.json' => $this->getSessionConfig(),
            'mail.json'    => $_POST['json-mail-conf'] ?? [],
            'ftp.json'     => $_POST['json-ftp-conf'] ?? [],
            'db.json'      => $_POST['json-database-conf'] ?? [],
        ];

        foreach ($configFiles as $fileName => $config) {
            $this->writeConfigFile($fileName, $config);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function moveSetupFile(): void
    {
        $sourceFile      = implode(DS, [PD, 'public', 'setup.php']);
        $destinationFile = implode(DS, [PD, 'Asset', 'resource', 'recycle_bin', 'setup.php']);

        if (!rename($sourceFile, $destinationFile)) {
            throw new Exception('Failed to move setup.php');
        }
    }

    /**
     * @return bool
     */
    private function setJSON(): bool
    {
        try {
            $this->processConfigurations();
            $this->writeConfigFiles();
            $this->moveSetupFile();

            return true;

        } catch (Exception $e) {

            error_log("Error in setJSON: ".$e->getMessage());

            return false;
        }
    }
}