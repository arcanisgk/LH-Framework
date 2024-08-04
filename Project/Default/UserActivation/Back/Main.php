<?php

declare(strict_types=1);

namespace Repository\Defaults\UserActivation\Back;

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
            'form_login_show' => 'show',
            'form_register_show' => '',
            'a-token' => '',
            'a-email' => '',
            'a-redirect' => ''
        ];
    }

    private function declareSMG()
    {
        $this->form_smg = [
            'error_email_a' => '',
            'success_activation' => '',
            'error_token_a' => '',
        ];
    }

    public function index(): array
    {
        $user_token = '';

        if (isset($_GET['user_token'])) {
            $user_token = urldecode($_GET['user_token']);
        }

        $this->form_field['a-token'] = $user_token;

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