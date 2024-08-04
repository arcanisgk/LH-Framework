<?php

namespace Repository\Defaults\Admin\Back;

use DataBase\CardHandler;
use FrameWork\App\DateTime;
use FrameWork\App\EventResponse;
use FrameWork\App\Files;
use FrameWork\App\RequestHTTP;

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

    public function updateCard(): bool
    {
        $reg_card = [];
        $json = file_get_contents('https://db.ygoprodeck.com/api/v7/cardinfo.php');
        //$json = file_get_contents('https://db.ygoprodeck.com/api/v7/cardinfo.php?fname=Blue-Eyes White Dragon');

        $obj = json_decode($json, true);
        $card_list_id = CardHandler::getInstance()->getCardIdList();
        foreach ($obj['data'] as $key => $card_info) {
            $reg_card[] = $card_info['id'];
        }
        $validator = array_flip(array_diff($reg_card, $card_list_id));
        $new_card_list = [];
        $date_create = DateTime::getInstance()->genDate();
        foreach ($obj['data'] as $key => $card_info) {
            if (isset($validator[$card_info['id']])) {
                $new_card_list[$card_info['id']] = [
                    'card_name' => $card_info['name'],
                    'card_type' => $card_info['type'],
                    'card_desc' => $card_info['desc'],
                    'card_atk' => $card_info['atk'] ?? null,
                    'card_def' => $card_info['def'] ?? null,
                    'card_level' => $card_info['level'] ?? null,
                    'card_scale' => $card_info['scale'] ?? null,
                    'card_race' => $card_info['race'],
                    'card_attribute' => $card_info['attribute'] ?? null,
                    'card_archetype' => $card_info['archetype'] ?? null
                ];
                if (isset($card_info['card_sets'])) {
                    $new_card_list[$card_info['id']]['sets'] = [];
                    foreach ($card_info['card_sets'] as $key_set => $set_info) {
                        $new_card_list[$card_info['id']]['sets'][] = [
                            'fk_card_id' => $card_info['id'],
                            'set_name' => $set_info['set_name'],
                            'set_code' => $set_info['set_code'],
                            'set_rarity' => $set_info['set_rarity'],
                            'set_rarity_code' => $set_info['set_rarity_code'],
                            'set_price' => $set_info['set_price'],
                            'last_update' => $date_create
                        ];
                    }
                }
                if (isset($card_info['card_images'])) {
                    $new_card_list[$card_info['id']]['images'] = [];
                    foreach ($card_info['card_images'] as $key_image => $image_info) {
                        $new_card_list[$card_info['id']]['images'][] = [
                            'fk_card_id' => $card_info['id'],
                            'image_id' => $image_info['id'],
                            'image_url' => $image_info['image_url'],
                            'image_url_small' => $image_info['image_url_small']
                        ];
                    }
                }
            }
        }
        if (!empty($new_card_list)) {
            CardHandler::getInstance()->insertNewCard($new_card_list);
        }
        $this->event_response->setResponse('content', 'test');
        return true;
    }

    public function updateSets(): bool
    {
        $reg_card = [];
        $json = file_get_contents('https://db.ygoprodeck.com/api/v7/cardsets.php');
        $obj = json_decode($json, true);
        CardHandler::getInstance()->insertUpdateSets($obj);
        $count = count($obj);
        $this->event_response->setResponse('content', 'test');
        return true;
    }

    public function updateImage(): bool
    {
        $path = Files::getInstance()->getAbsolutePath(dirname(__DIR__) . '/../../../public/assets/img/');
        $image_url_list = CardHandler::getInstance()->getCardImageList();
        $image_file_list = array_map(function ($url) use ($path) {
            return $path . DS . basename($url);
        }, $image_url_list);
        $files = glob($path . DS . '*');
        $missing_images = array_diff($image_file_list, $files);
        $error_404 = [];
        foreach ($missing_images as $key => $file) {
            $url = 'https://images.ygoprodeck.com/images/cards/' . basename($file);
            if (RequestHTTP::getInstance()->urlExists($url)) {
                copy($url, $path . DS . basename($file));
            } else {
                $error_404[] = $url;
            }
        }
        if (!empty($error_404)) {
            $this->event_response->setResponse('content', '[error]: Cant Get the Following Images:<br>' . implode('<br>', $error_404), true);
            return false;
        }
        $this->event_response->setResponse('content', 'test');
        return true;
    }

    public function generateJsonSearch(): bool
    {
        $list = CardHandler::getInstance()->getCardList();
        $list_json = json_encode($list);
        $path = Files::getInstance()->getAbsolutePath(dirname(__DIR__) . '/../../../public/assets/sources/');
        file_put_contents($path . DS . 'quick_search.json', $list_json);
        $this->event_response->setResponse('content', 'test');
        return true;
    }


}