<?php

declare(strict_types=1);

namespace Repository\Defaults\UserActivation\Back;

use DataBase\UserAccount;
use Exception;
use FrameWork\App\EventResponse;
use FrameWork\App\Mailer;
use FrameWork\App\RequestHTTP;
use FrameWork\App\Session;

class Event extends EventResponse
{
    /**
     * @var Event|null
     */

    private static ?Event $instance = null;

    /**
     * Description: Auto-Instance Helper for static development class BugCatcher.
     * @return Event
     */

    public static function getInstance(): Event
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $event = null;

    public bool $event_exists = false;

    public bool $event_success;

    public ?EventResponse $event_response;

    public function __construct()
    {
        parent::__construct();

        if (isset($_POST) && !empty($_POST)) {
            $this->event_exists = true;
            $this->event = $_POST['event'];
            $this->event_response = EventResponse::getInstance();
        }
    }

    public Main $main;

    public array $data = [];

    public function listenerEvent(): ?EventResponse
    {
        if ($this->event !== null) {
            $this->event_success = call_user_func([$this, $this->event]);

            $this->data = $this->event_response->getResponseData($this->main->form_field, $this->main->form_smg);
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function activate(): bool
    {
        try {
            $email = $_POST['a-email'];
            $user_token = '';
            if (isset($_POST['a-token'])) {
                $user_token = $_POST['a-token'];
            }
            $this->main->form_field['a-token'] = $user_token;
            $this->main->form_field['a-email'] = $email;
            $user_handler = UserAccount::getInstance();
            $validate = $user_handler->requireValidate($email);
            if (!$validate['need']) {
                $this->event_response->setResponse('error_email_a', '[error]: ' . $validate['smg'], true);
                return false;
            }
            if ($user_handler->validateEmail($email)) {
                if ($user_handler->validateTokenActivation($email, $user_token)) {
                    $user_handler->userActivation($email);
                    $user_handler->userSetDefaultType($email);
                    Session::getInstance()->createUserSession($email);
                    $subject = 'Account was Activated Right!';
                    $content = '<img src="' . $_SERVER['CONFIG']['HOST']['PROTOCOL'] . '://' . $_SERVER['CONFIG']['HOST']['DOMAIN'] . '/assets/ico/128.png" alt=""><br><br>Welcome to the TCG-Wallet<br>
                        Directory for Trading Card Game.<br>
                        User Activation has been made with this email account.<br>
                        <br><br>TCG-Wallet Team';
                    Mailer::getInstance()->sendMail(['to' => ['name' => $email, 'mail' => $email], 'subject' => $subject, 'content' => $content]);
                    RequestHTTP::getInstance()->redirect('', 10);
                    $this->event_response->setResponse('success_activation', 'Welcome, correct activation, this login is executed automatically.');
                    return true;
                } else {
                    $this->event_response->setResponse('error_email_a', '[error]: you cannot be activated, contact for support: <a href="tcg.wallet.s@gmail.com">TCG-Wallet Support</a>', true);
                    return false;
                }
            } else {
                $this->event_response->setResponse('error_email_a', '[error]: The email you are trying to validate does not exist.', true);
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}