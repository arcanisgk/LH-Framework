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
     *
     */
    public function __construct()
    {
        if (isset($_POST)) {
            if (isset($_POST['Data-Base-Test'])) {
                $dbParams = [
                    'host' => $_POST['i-database-host'],
                    'db'   => $_POST['i-database-name'],
                    'user' => $_POST['i-database-user'],
                    'pass' => $_POST['i-database-password'],
                ];
                echo Database::getInstance()->testConnection($dbParams) ? 'true' : 'false';
            } else {
                echo $this->setJSON() ? 'true' : 'false';
            }
        } else {
            $templatePath = implode(DS, ['Asset', 'resource', 'template', 'install.html']);
            echo Files::getInstance()->fileLoader($templatePath);
        }
    }

    private function setJSON(): bool
    {
        try {
            $filePaths = [
                'app.json'     => [
                    'company' => [
                        'company_name'  => $_POST['json-company-name'],
                        'company_owner' => $_POST['json-company-owner'],
                    ],
                    'host'    => [
                        'domain'   => $_POST['json-domain'],
                        'lang'     => $_POST['json-lang'],
                        'm-lang'   => $_POST['json-m-lang'],
                        'protocol' => $_POST['json-protocol'],
                        'entry'    => $_POST['json-entry'],
                        'license'  => $_POST['json-license'],
                    ],
                ],
                'session.json' => [
                    'session' => [
                        'session_name'            => $_POST['json-session-name'],
                        'session_inactivity'      => $_POST['json-session-inactivity'],
                        'session_lifetime'        => $_POST['json-session-lifetime'],
                        'session_activity_expire' => $_POST['json-session-activity-expire'],
                        'session_secure'          => $_POST['json-session-secure'],
                        'session_httponly'        => $_POST['json-session-http-only'],
                        'session_same_site'       => $_POST['json-session-same-site'] ? 'Strict' : 'Lax',
                    ],
                ],
                'db.json'      => [
                    'database' => [
                        'con1' => [
                            'db_name'     => $_POST['json-database-con1-db-name'],
                            'db_host'     => $_POST['json-database-con1-db-host'],
                            'db_user'     => $_POST['json-database-con1-db-user'],
                            'db_password' => $_POST['json-database-con1-db-pass'],
                        ],
                    ],
                ],
            ];

            foreach ($filePaths as $fileName => $postData) {

                $filePath = implode(DS, [PD, 'Asset', 'resource', 'config', $fileName]);
                $config   = json_decode(file_get_contents($filePath), true);
                $config   = array_replace_recursive($config, $postData);

                array_walk_recursive($config, function (&$value) {
                    if (!is_bool($value) && !is_int($value)) {
                        $value = mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value));
                    }
                });

                file_put_contents(
                    $filePath,
                    json_encode(
                        $config,
                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                    )
                );

            }

            $sourceFile      = implode(DS, [PD, 'public', 'setup.php']);
            $destinationFile = implode(DS, [PD, 'Asset', 'resource', 'recicle_bin', 'setup.php']);

            if (!copy($sourceFile, $destinationFile)) {
                throw new Exception('Failed to copy setup.php');
            }

            if (!unlink($sourceFile)) {
                throw new Exception('Failed to delete setup.php');
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}