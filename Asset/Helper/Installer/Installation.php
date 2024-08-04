<?php

declare(strict_types=1);

namespace Asset\Helper\Installer;

use Asset\Framework\Core\Database;
use Asset\Framework\Core\Files;
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
            if (!isset($_POST['Data-Base-Test'])) {
                if ($this->setJSON()) {
                    echo 'true';
                } else {
                    echo 'false';
                }
            } else {
                if (Database::getInstance()
                    ->testConnection([
                                         'host' => $_POST['i-database-host'],
                                         'db'   => $_POST['i-database-name'],
                                         'user' => $_POST['i-database-user'],
                                         'pass' => $_POST['i-database-password'],
                                     ])) {
                    echo 'true';
                } else {
                    echo 'false';
                }
            }
        } else {
            $html = Files::getInstance()->fileLoader('Asset' . DS . 'resource' . DS . 'template' . DS . 'install.html');
            echo $html;
        }
    }

    private function setJSON(): bool
    {
        try {
            $filePath = PD . DS . 'Asset' . DS . 'resource' . DS . 'config' . DS . 'app.json';
            $jsonData = file_get_contents($filePath);
            $config   = json_decode($jsonData, true);

            $config['company']['company_name']  = $_POST['json-company-name'];
            $config['company']['company_owner'] = $_POST['json-company-owner'];
            $config['host']['domain']           = $_POST['json-domain'];
            $config['host']['lang']             = $_POST['json-lang'];
            $config['host']['m-lang']           = $_POST['json-m-lang'];
            $config['host']['protocol']         = $_POST['json-protocol'];
            $config['host']['entry']            = $_POST['json-entry'];
            $config['host']['license']          = $_POST['json-license'];

            $config_utf8 = array_map(function ($value) {
                return mb_convert_encoding($value, 'UTF-8', mb_list_encodings());
            }, $config);
            $newJsonData = json_encode($config_utf8, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            file_put_contents($filePath, $newJsonData);

            $filePath = PD . DS . 'Asset' . DS . 'resource' . DS . 'config' . DS . 'session.json';
            $jsonData = file_get_contents($filePath);
            $config   = json_decode($jsonData, true);

            $config['session']['session_name']            = $_POST['json-session-name'];
            $config['session']['session_inactivity']      = $_POST['json-session-inactivity'];
            $config['session']['session_lifetime']        = $_POST['json-session-lifetime'];
            $config['session']['session_activity_expire'] = $_POST['json-session-activity-expire'];
            $config['session']['session_secure']          = $_POST['json-session-secure'];
            $config['session']['session_httponly']        = $_POST['json-session-http-only'];
            $config['session']['session_same_site']       = $_POST['json-session-same-site'] ? 'Strict' : 'Lax';

            $config_utf8 = array_map(function ($value) {
                return mb_convert_encoding($value, 'UTF-8', mb_list_encodings());
            }, $config);
            $newJsonData = json_encode($config_utf8, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            file_put_contents($filePath, $newJsonData);

            $filePath = PD . DS . 'Asset' . DS . 'resource' . DS . 'config' . DS . 'db.json';
            $jsonData = file_get_contents($filePath);
            $config   = json_decode($jsonData, true);

            $config['database']['con1']['db_name']     = $_POST['json-database-con1-db-name'];
            $config['database']['con1']['db_host']     = $_POST['json-database-con1-db-host'];
            $config['database']['con1']['db_user']     = $_POST['json-database-con1-db-user'];
            $config['database']['con1']['db_password'] = $_POST['json-database-con1-db-pass'];


            $config_utf8 = array_map(function ($value) {
                return mb_convert_encoding($value, 'UTF-8', mb_list_encodings());
            }, $config);
            $newJsonData = json_encode($config_utf8, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            file_put_contents($filePath, $newJsonData);

            $sourceFile      = PD . DS . 'public' . DS . 'setup.php';
            $destinationFile = PD . DS . 'Asset' . DS . 'resource' . DS . 'recicle_bin' . DS . 'setup.php';
            copy($sourceFile, $destinationFile);
            unlink($sourceFile);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}