<?php

namespace Repository\Defaults\Admin\Back;

use DataBase\CardHandler;
use Exception;
use FrameWork\App\{EventResponse, Files, FrontResources, RenderTemplate, RequestHTTP, User};

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
            User::getInstance()->redirectNotLogin();
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
            'content' => ''
        ];
    }

    private function declareSMG()
    {
        $this->form_smg = [];
    }

    public function index(): array
    {
        if (!User::getInstance()->validatePermission(7)) {
            RequestHTTP::getInstance()->redirect('');
        }


        $json = file_get_contents('https://db.ygoprodeck.com/api/v7/cardinfo.php');
        $obj = json_decode($json, true);
        $card_list_id = CardHandler::getInstance()->getCardIdList();
        $reg_card = array_column($obj['data'], 'id');
        $validator = array_diff($reg_card, $card_list_id);

        $path = Files::getInstance()->getAbsolutePath(dirname(__DIR__) . '/../../../public/assets/img/');
        $image_url_list = CardHandler::getInstance()->getCardImageList();

        $image_file_list = array_map(function ($url) use ($path) {
            return $path . DS . basename($url);
        }, $image_url_list);

        $files = glob($path . DS . '*');
        $missing_images = array_diff($image_file_list, $files);
        $this->form_field['content'] = '<h5>Exist ' . count($validator) . ' Cards pending update</h5>';
        $this->form_field['content'] .= '<h5>Exist ' . count($missing_images) . ' Images pending update</h5>';
        
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