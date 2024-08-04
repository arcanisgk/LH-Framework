<?php

declare(strict_types=1);

namespace Repository\Defaults\Home\Back;

use FrameWork\App\EventResponse;
use FrameWork\Common\CardAnalyzer;
use FrameWork\Common\CardViewer;

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
        if (isset($_POST['event']) || isset($_GET['event'])) {
            $event = $_POST['event'] ?? $_GET['event'];
            $this->event_exists = true;
            $this->event = $event;
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

    public function search()
    {
        $search = $_GET['search'];

        $output = CardViewer::getInstance()->buildSearchView(
            CardAnalyzer::getInstance()->getCardsData($search)
        );

        $output = 'Processing maintenance and updates, check back in 2 hours.';


        /*

        $price_response = TrollAndToadScrapper::getInstance()->getCardPrices($search);
        $output = [];
        $titulo = '';
        foreach ($price_response as $key => $node) {
            if ($key === 'code') {
                $btn_share = '<button class="btn btn-success fw-semibold ms-2" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;" type="button" id="share-url"><i class="fa-solid fa-share"></i></button>';
                $titulo = 'Search: ' . $node . ' ' . $btn_share;
            } else {
                $output[] = '<div class="col-12 text-center fw-bold">' . $node['tittle'] . '</div>';
                $image = $node['img_card'];
                $prices = [];
                $count_line = 1;
                foreach ($node as $key2 => $card_data) {
                    if ($key2 !== 'tittle' && $key2 !== 'img_card') {
                        $prices[] = '
                                        <tr class="align-middle">
                                            <th scope="row">' . $count_line . '</th>
                                            <td>' . $card_data['store'] . '</td>
                                            <td style="word-wrap:;min-width: 100px;max-width: 100px;">' . $card_data['status'] . '</td>
                                            <td>' . $card_data['price'] . '</td>
                                            <td>' . ($card_data['stock'] ? '<i class="fa-solid fa-2x fa-boxes-stacked text-success"></i>' : '<i class="fa-sharp fa-2x fa-solid fa-empty-set text-danger"></i>') . '</td>
                                        </tr>';
                        $count_line++;
                    }
                }

                if (empty($prices)) {
                    $table = '<div class="col-12">No hay a la Venta en TrollAndToad, usa otra Tienda como Referencia.</div>';
                } else {
                    $table = '
                                    <table class="table table-responsive table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                  <th scope="col">#</th>
                                                  <th scope="col">Tienda</th>
                                                  <th scope="col">Estado</th>
                                                  <th scope="col">Precio</th>
                                                  <th scope="col">Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>' . implode($prices) . '</tbody>
                                    </table>';
                }

                $output[] = '<div class="row text-center">
                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4">' . $image . '</div>
                                    <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                                        <div class="table-responsive"> ' . $table . '</div>
                                    </div>
                              </div>';
            }
        }

        $content = '<div class="col-12 fs-4 fw-bold fst-italic text-center d-flex justify-content-center align-items-center">' . $titulo . '</div>' . implode('', $output);

        */


        $this->main->form_field['content'] = '<div class="fixed-top float-right ms-1" style="margin-top: 12rem">
                              <button type="button" class="btn btn-primary" onclick="history.go(-1)"><i class="fa-solid fa-arrow-left"></i></button>
                            </div>' . $output;
        $this->main->form_field['search'] = $search;

        $this->event_response->setResponse('success_login', 'Login successful; You will be redirected to Home.');
        return true;
    }
}