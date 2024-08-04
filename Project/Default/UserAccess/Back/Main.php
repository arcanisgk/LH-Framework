<?php

declare(strict_types=1);

namespace Repository\Defaults\UserAccess\Back;

use Exception;
use FrameWork\App\{EventResponse, Files, FrontResources, RenderTemplate};

class Main extends FrontResources
{
    /**
     * @var Main|null
     */

    private static ?Main $instance = null;

    /**
     * Description: Auto-Instance Helper for static development class BugCatcher.
     * @return Main
     */

    public static function getInstance(): Main
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private ?Event $event;

    private ?EventResponse $event_response;

    public array $form_field = [];

    public array $form_smg = [];

    private function existEventResponse(): bool
    {
        return (isset($this->event_response['response']) && $this->event_response['response'] !== null);
    }

    private function getFullForm(array $data): string
    {
        return RenderTemplate::getInstance()->render(
            Files::getInstance()->getAbsolutePath(dirname(__FILE__) . '/../html/form.phtml'),
            $data
        );
    }

    public function __construct()
    {
        parent::__construct();
        try {
            $this->declareField();
            $this->declareSMG();
            $this->event = Event::getInstance();
            $this->event->main = $this;
            $this->event_response = $this->event->listenerEvent();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function declareField()
    {
        $this->form_field = [
            'form_login' => 'active',
            'form_register' => '',
            'form_login_show' => 'show',
            'form_register_show' => '',
            'l-email' => '',
            'l-password' => '',
            'remember' => '',
            'r-email' => '',
            'r-password' => '',
            'r-password-rep' => '',
            'approve' => '',
            'rec-email' => ''
        ];
    }

    private function declareSMG()
    {
        $s = file_get_contents(BD . DS . 'Repository/Defaults/TermOfService/html/form.phtml');
        $p = file_get_contents(BD . DS . 'Repository/Defaults/PrivacyPolicies/html/form.phtml');
        $this->form_smg = [
            'error_email_l' => '',
            'error_password_l' => '',
            'error_email_r' => '',
            'error_password_r' => '',
            'error_password_r_rep' => '',
            'success_login' => '',
            'success_register' => '',
            'term-of-service' => $s,
            'privacy-policies' => $p,
            'success_recovery' => '',
            'error_recovery' => '',
        ];
    }

    public function index(): array
    {
        $form = $this->getFullForm(array_merge($this->form_field, $this->form_smg, $this->event_response->data));

        return [
            'data' => ['html_content' => $form],
            'show' => true,
            'in' => 'html_content',
            'refresh' => false,
            'nav' => false,
            'mail' => false,
        ];
    }
}