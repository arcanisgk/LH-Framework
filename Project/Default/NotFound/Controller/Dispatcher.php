<?php

namespace Project\Default\NotFound\Controller;

#use Exception;
#use FrameWork\App\{EventResponse, Files, FrontResources, RenderTemplate};

class Dispatcher # extends FrontResources
{
    /**
     * @var Dispatcher|null
     */

    private static ?self $instance = null;

    /**
     * Description: Auto-Instance Helper for static development class BugCatcher.
     *
     * @return Dispatcher
     */

    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /*
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
            $data,
        );
    }

    public function __construct()
    {
        parent::__construct();
        try {
            $this->declareField();
            $this->declareSMG();
            $this->event          = Event::getInstance();
            $this->event->main    = $this;
            $this->event_response = $this->event->listenerEvent();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function declareField()
    {
        $this->form_field = [];
    }

    private function declareSMG()
    {
        $this->form_smg = [];
    }
    */
    public function index(): array
    {
        //$form = $this->getForm();

        //$form = $this->getFullForm(array_merge($this->form_field, $this->form_smg, $this->event_response->data));

        $form = 'Hello World';

        return [
            //'head'    => '<link rel="stylesheet" type="text/css" href="/assets/css/over-user-agent.css">',
            'data'    => ['html_content' => $form],
            'show'    => true,
            'in'      => 'html_content',
            'refresh' => false,
            'nav'     => false,
            'mail'    => false,
        ];
    }

}