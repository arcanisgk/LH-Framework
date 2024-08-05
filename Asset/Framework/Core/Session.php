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
 * Class Session
 * A simple ...
 */
class Session
{
    /**
     * @var Session|null Singleton instance of the Session.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of Session.
     *
     * @return Session The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    public function handleSession(): void
    {
        if (!isset($_SESSION)) {
            session_name(CONFIG['SESSION']['SESSION']['SESSION_NAME']);
            session_set_cookie_params(
                [
                    'lifetime' => CONFIG['SESSION']['SESSION']['SESSION_LIFETIME'],
                    'path'     => '/',
                    'domain'   => CONFIG['APP']['HOST']['DOMAIN'],
                    'secure'   => CONFIG['SESSION']['SESSION']['SESSION_SECURE'],
                    'httponly' => CONFIG['SESSION']['SESSION']['SESSION_HTTPONLY'],
                    'samesite' => CONFIG['SESSION']['SESSION']['SESSION_SAME_SITE'],
                ],
            );
        }
        session_start();
        if (!isset($_SESSION['SYSTEM'])) {
            $_SESSION = [
                'SYSTEM' => [
                    'SESSION_STAR_DATE' => time(),
                    'LANG'              => CONFIG['APP']['HOST']['LANG'],
                ],
                'USER'   => [
                    'LOGIN'       => false,
                    'PREFERENCES' => [
                        'LANG' => CONFIG['APP']['HOST']['LANG'],
                    ],
                ],
            ];
        }
        if ($_SESSION['USER']['LOGIN']) {
            if (CT > $_SESSION['SESSION_LIFETIME'] || CT > $_SESSION['SESSION_ACTIVITY_EXPIRE']) {
                session_destroy();
                # redirecionar
            } else {
                $_SESSION['SESSION_ACTIVITY_EXPIRE'] = (int)$_SERVER['ENVIRONMENT']['SESSION']['SESSION_ACTIVITY_EXPIRE'] + CT;
            }
        }
    }
}