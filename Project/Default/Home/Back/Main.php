<?php

declare(strict_types=1);

namespace Repository\Defaults\Home\Back;

use FrameWork\App\{EventResponse, Files, FrontResources, RenderTemplate, User};

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
        //try {
        User::getInstance()->redirectNotLogin();
        $this->declareField();
        $this->declareSMG();
        $this->event = Event::getInstance();
        $this->event->main = $this;
        $this->event_response = $this->event->listenerEvent();
        //} catch (Exception $e) {
        //exDataEX($e);
        //throw new Exception($e->getMessage());
        //}
    }

    private function declareField()
    {
        $app_download = '';
        /*
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'TCG-Player-App') !== false) {
            } else {
                $app_download .= '<div class="col">
                                <div class="card h-100">
                                  <img src="assets/ico/512.png" class="card-img-top" alt="...">
                                  <div class="card-body">
                                    <h5 class="card-title">Official Android App</h5>
                                    <p class="card-text">Official launch of the application for android devices.</p>
                                  </div>
                                  <div class="card-footer">
                                        <a href="https://play.google.com/store/apps/details?id=com.icarosnet.tcgwallet" target="_blank" class="btn btn-primary">Go and Download</a>
                                  </div>
                                </div>
                              </div>';
            }
        }
        */
        $this->form_field = [
            'search' => '',
            'search_enable' => '',
            'head_search' => '',
            'content' => '<div class="row row-cols-1 row-cols-md-4 g-4">
                              ' . $app_download . '
                              <div class="col">
                                <div class="card h-100">
                                  <img src="assets/ico/underdevelpoment.png" class="card-img-top" alt="...">
                                  <div class="card-body">
                                    <h5 class="card-title">Official Announcement</h5>
                                    <p class="card-text">Improvements are currently being made to the website..</p>
                                  </div>
                                </div>
                              </div>
                              <div class="col">
                                <div class="card h-100">
                                  <img src="assets/ico/features.png" class="card-img-top" alt="...">
                                  <div class="card-body">
                                    <h5 class="card-title">Upcoming Features</h5>
                                    <p class="card-text">- Search include TCGPlayer.<br>- Personal Profile.<br>- Personal Binder.<br>- Personal Deck.</p>
                                  </div>
                                </div>
                              </div>
                            </div>',
            'admin_area' => ''
        ];
    }

    private function declareSMG()
    {
        $this->form_smg = [];
    }

    public function index(): array
    {
        /*
         * Agregar aqui el default search... General
         */


        //exDataEX(User::getInstance()->validatePermission(1), $_SESSION);
        //exit();

        if (User::getInstance()->validatePermission(1)) {
            $this->form_field['admin_area'] = '<li><a class="dropdown-item" href="/admin">Admin Dashboard</a></li>';
        }

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