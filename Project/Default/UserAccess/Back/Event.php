<?php

declare(strict_types=1);

namespace Repository\Defaults\UserAccess\Back;

use DataBase\UserAccount;
use Exception;
use FrameWork\App\{EventResponse, Mailer, RequestHTTP, Session};

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
            call_user_func([$this, $this->event]);

            $this->data = $this->event_response->getResponseData($this->main->form_field, $this->main->form_smg);
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function login(): bool
    {
        $email = $_POST['l-email'];
        $pass = $_POST['l-password'];
        $remember = !isset($_POST['remember']) ? false : $_POST['remember'];
        /*
        if ($email !== 'icarosnet@gmail.com') {
            $this->event_response->setResponse('error_email_l', '[error]: Mail not yet enabled for testing!!!', true);
            return false;
        }
        */
        $this->main->form_field['form_register'] = '';
        $this->main->form_field['form_register_show'] = '';
        $this->main->form_field['l-email'] = $email;
        $this->main->form_field['l-password'] = $pass;
        $this->main->form_field['remember'] = $remember ? 'checked' : '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->event_response->setResponse('error_email_l', '[error]: Invalid email!!!', true);
            return false;
        } else {
            $user_handler = UserAccount::getInstance();
            $validate = $user_handler->requireValidate($email);
            if ($validate['need']) {
                $this->event_response->setResponse('error_email_a', '[error]: ' . $validate['smg'], true);
                return false;
            }
            if ($user_handler->validatePassword($email, $pass)) {
                Session::getInstance()->createUserSession($email);
                RequestHTTP::getInstance()->redirect('', 2);
                $this->event_response->setResponse('success_login', 'Login successful; You will be redirected to Home.');
                return true;
            } else {
                $this->event_response->setResponse('error_password_l', '[error]: Password entered not match!!!', true);
                return false;
            }
        }
    }

    /**
     * @throws Exception
     */
    public function register(): bool
    {
        $email = $_POST['r-email'];
        $pass1 = $_POST['r-password'];
        $pass2 = $_POST['r-password-rep'];
        $this->main->form_field['form_login'] = '';
        $this->main->form_field['form_login_show'] = '';
        $this->main->form_field['form_register'] = 'active';
        $this->main->form_field['form_register_show'] = 'show';
        $this->main->form_field['r-email'] = $email;
        $this->main->form_field['r-password'] = $pass1;
        $this->main->form_field['r-password-rep'] = $pass2;
        $this->main->form_field['approve'] = 'checked';
        /*
        if ($email !== 'icarosnet@gmail.com') {
            $this->event_response->setResponse('error_email_r', '[error]: Mail not yet enabled for testing!!!', true);

            return false;
        }*/
        if ($pass1 !== $pass2) {
            $this->event_response->setResponse('error_password_r_rep', '[error]: password Incorrect!!!', true);

            return false;
        }
        $criteria = [
            'Must contain at least an upper case' => fn($pass1) => !preg_match('@[A-Z]@', (string)$pass1),
            'Must contain at least a lower case' => fn($pass1) => !preg_match('@[a-z]@', (string)$pass1),
            'Must contain at least a number' => fn($pass1) => !preg_match('@[0-9]@', (string)$pass1),
            'Must contain at least a special character (*@#)' => fn($x) => !preg_match('@[^\w]@', (string)$pass1),
            'Is less than 8 characters' => fn($pass1) => strlen((string)$pass1) < 8,
        ];
        $errors = [];
        foreach ($criteria as $message => $failCriterion) {
            if ($failCriterion($pass1)) {
                $errors[] = $message;
            }
        }
        $error_text = '';
        if ($errors) {
            $error_text = "[error]: " . join(", ", $errors);
        }
        if ($error_text !== '') {
            $this->event_response->setResponse('error_password_r', $error_text, true);

            return false;
        }
        $user = UserAccount::getInstance();
        if ($user->validateEmail($email)) {
            $this->event_response->setResponse('error_email_r', '[error]: this email already exists and is registered in an account!!!', true);

            return false;
        }

        $register_response = $user->addUser($email, $pass1);

        if ($register_response['reg'] === false) {
            $this->event_response->setResponse('error_email_r', '[error]: you cannot be registered contact for support: <a href="tcg.wallet.s@gmail.com">TCG-Wallet Support</a>', true);

            return false;
        } else {
            $subject = 'User Account Activation Mail!';
            $content = '<img src="' . $_SERVER['CONFIG']['HOST']['PROTOCOL'] . '://' . $_SERVER['CONFIG']['HOST']['DOMAIN'] . '/assets/ico/128.png" alt=""><br><br>Welcome to the TCG-Wallet<br>
                        Directory for Trading Card Game.<br>
                        User registration has been made with this email account.<br>
                        Use the following Link to activate your account:<br><br>
                        <b><a style="font-size: large" href="' .
                $_SERVER['CONFIG']['HOST']['PROTOCOL'] .
                '://' .
                $_SERVER['CONFIG']['HOST']['DOMAIN'] .
                '/useractivation?user_token=' .
                urlencode($register_response['activation_token']) .
                '">CLICK HERE TO ACTIVATE ACCOUNT!!!</a></b><br><br>TCG-Wallet Team';
            Mailer::getInstance()->sendMail(['to' => ['name' => $register_response['username'], 'mail' => $email], 'subject' => $subject, 'content' => $content]);
            $success = 'Registration done Correctly; there is still one step left; Go to your email to activate your account.';

            $this->event_response->setResponse('success_register', $success);

            return true;
        }
    }

    public function recovery(): bool
    {
        $this->event_response->setResponse('success_recovery', 'Recovery Test Success');

        return true;
    }
}