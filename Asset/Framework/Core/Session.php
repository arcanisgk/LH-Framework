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

/**
 * Class Session
 * A simple ...
 */
class Session
{
    use SingletonTrait;

    /**
     * Session manager.
     *
     * @return void
     */
    public function handleSession(): void
    {
        $this->initializeSession();
        $this->initializeSessionData();
        $this->checkSessionLifetime();
    }

    /**
     * @return void
     */
    private function initializeSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {

            //ex_c(CONFIG);
            session_name(CONFIG->session->session->getSessionName());
            session_set_cookie_params([
                'lifetime' => CONFIG->session->session->getSessionLifeTime(),
                'path'     => '/',
                'domain'   => CONFIG->app->host->getDomain(),
                'secure'   => CONFIG->session->session->getSessionSecure(),
                'httponly' => CONFIG->session->session->getSessionHttpOnly(),
                'samesite' => CONFIG->session->session->getSessionSameSite(),
            ]);
            session_start();
        }
    }

    /**
     * @return void
     */
    private function initializeSessionData(): void
    {

        $_SESSION['SYSTEM'] ??= [
            'SESSION_START_DATE' => time(),
            'LANG'               => CONFIG->app->host->getLang(),
        ];

        $_SESSION['USER'] ??= [
            'LOGIN'       => false,
            'PREFERENCES' => [
                'LANG' => CONFIG->app->host->getLang(),
            ],
        ];

    }

    /**
     * @return void
     */
    private function checkSessionLifetime(): void
    {
        if (!$_SESSION['USER']['LOGIN']) {
            return;
        }

        if (CT > $_SESSION['SESSION_LIFETIME'] || CT > $_SESSION['SESSION_ACTIVITY_EXPIRE']) {
            session_destroy();

            return;
        }

        $_SESSION['SESSION_ACTIVITY_EXPIRE'] = (int)$_SERVER['ENVIRONMENT']['SESSION']['SESSION_ACTIVITY_EXPIRE'] + CT;
    }

    /**
     * @return array
     */
    public function getSession(): array
    {
        return $_SESSION;
    }

    /**
     * @return void
     */
    public function destroy(): void
    {
        session_destroy();
    }
}