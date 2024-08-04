<?php

namespace Repository\Defaults\Services\Back;

use FrameWork\App\EventResponse;

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
}