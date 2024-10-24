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

namespace Repository\Default\UserAccess\Back;

use Asset\Framework\Controller\EventController;
use Asset\Framework\ToolBox\Password;

/**
 * Class that handles:
 *
 * @package Repository\Default\UserAccess\Back;
 */
class Event extends EventController
{

    /**
     * @var Event|null Singleton instance of the class: Event.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class Event.
     *
     * @return Event The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @var mixed|null
     */
    private mixed $event = null;

    /**
     * @var bool
     */
    public bool $event_exists = false;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if (isset($_POST) && !empty($_POST)) {
            $this->event_exists = true;
            $this->event        = $_POST['event'];
        }
    }

    /**
     * @var Main
     */
    public Main $main;

    /**
     * @var array
     */
    public array $data = [];

    /**
     * @param Main $main
     * @return $this
     */
    public function setMain(Main $main): self
    {
        $this->main = $main;

        return $this;
    }

    /**
     * @return $this|null
     */
    public function listenerEvent(): ?self
    {
        if ($this->event !== null && method_exists($this, $this->event)) {
            $this->{$this->event}();
        }

        return $this;
    }

    private function login()
    {
        ex_c('Test de login');
    }

    private function register()
    {
        $error = [];
        if ($_POST['register-first-name'] === '' || true) {
            $error[] = [
                'field'  => 'register-first-name-smg',
                'status' => 'error',
                'smg'    => '{{register-first-name-smg}}',
            ];
        }

        if ($_POST['register-last-name'] === '' || true) {
            $error[] = [
                'field'  => 'register-last-name-smg',
                'status' => 'error',
                'smg'    => '{{register-last-name-smg}}',
            ];
        }

        if ($_POST['register-email'] === '' || true) {
            $error[] = [
                'field'  => 'register-email-smg',
                'status' => 'error',
                'smg'    => '{{register-email-smg-0}}',
            ];
        }

        if ($_POST['register-re-email'] === '' || true) {
            $error[] = [
                'field'  => 'register-re-email-smg',
                'status' => 'error',
                'smg'    => '{{register-re-email-smg-0}}',
            ];
        }

        if ($_POST['register-re-email'] !== $_POST['register-email'] || true) {
            $error[] = [
                'field'  => 'register-email-smg',
                'status' => 'error',
                'smg'    => '{{register-email-smg-1}}',
            ];
            $error[] = [
                'field'  => 'register-re-email-smg',
                'status' => 'error',
                'smg'    => '{{register-re-email-smg-1}}',
            ];
        }

        if ($_POST['register-password'] === '' || true) {
            $error[] = [
                'field'  => 'register-password-smg',
                'status' => 'error',
                'smg'    => '{{register-password-smg-0}}',
            ];
        }

        $pass_eval = Password::quickCheck($_POST['register-password']);

        if (!empty($pass_eval)) {
            $error[] = [
                'field'  => 'register-password-smg',
                'status' => 'error',
                'smg'    => implode(NL, $pass_eval),
            ];
        }

        if ($_POST['register-agree-conditions'] !== true || true) {
            $error[] = [
                'field'  => 'register-agree-conditions-smg',
                'status' => 'error',
                'smg'    => '{{register-agree-conditions-smg}}',
            ];
        }

        ex_c($error);

        /* Validar si Existe un correo electronico repetido */


        if (!empty($error)) {
            $error[] = [
                'field'  => 'register-smg',
                'status' => 'error',
                'smg'    => '{{register-smg}}',
            ];
            $this->main->smg->setSMG(['error' => $error]);
        } else {
            //success
            ex_c('Test de register right', $error);
        }
    }

    private function loginWithGoogle()
    {
        ex_c('Test de loginWithGoogle');
    }

    private function loginWithFacebook()
    {
        ex_c('Test de loginWithFacebook');
    }

}