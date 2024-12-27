<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Repository\Default\PlatformTranslator\Back;

use Asset\Framework\Http\Request;
use Asset\Framework\Http\Response;
use Asset\Framework\Template\Form\FormSMG;
use Asset\Framework\Template\Render;
use Asset\Framework\Trait\SingletonTrait;
use Exception;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class that handles: Events of/over User Access
 *
 * @package Repository\Default\PlatformTranslator\Back;
 */
class Event
{

    use SingletonTrait;


    /**
     * @var Render
     */
    private Render $render;

    /**
     * @var bool
     */
    private bool $event_exists = false;

    /**
     * @var string
     */
    private string $event = '';

    /**
     * @var Main
     */
    private Main $main;

    /**
     * @var Response
     */
    private Response $response;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var array
     */
    private array $post;

    private FormSMG $smg;

    /**
     * Event constructor.
     */
    public function __construct(Main $main)
    {
        if (!empty($_POST)) {
            $this->initializeEvent($main);
        }
    }

    /**
     * @param Main $main
     * @return void
     */
    private function initializeEvent(Main $main): void
    {

        $this->setEventExists(true)
            ->setEvent($_POST['event'])
            ->setMain($main)
            ->setRender(Render::getInstance())
            ->setResponse(Response::getInstance())
            ->setRequest(Request::getInstance())
            ->setPost($this->getRequest()->getPost());

        $this->smg = FormSMG::getInstance();
        $this->getResponse()->setEvent($this);
    }

    /**
     * @param Main $main
     * @return self
     */
    private function setMain(Main $main): self
    {
        $this->main = $main;

        return $this;
    }

    /**
     * @return array
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @param array $post
     * @return Event
     */
    public function setPost(array $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return Event
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function eventHandler(): self
    {
        if ($this->isEventExists()) {
            $this->eventListener();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEventExists(): bool
    {
        return $this->event_exists;
    }

    /**
     * @param bool $event_exists
     * @return $this
     */
    public function setEventExists(bool $event_exists): self
    {
        $this->event_exists = $event_exists;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function eventListener(): self
    {
        if (method_exists($this, $this->getEvent())) {
            $this->{$this->event}();
        } else {
            //throw new Exception('Event not found');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     * @return $this
     */
    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Render
     */
    public function getRender(): Render
    {
        return $this->render;
    }

    /**
     * @param Render $render
     * @return $this
     */
    public function setRender(Render $render): self
    {
        $this->render = $render;

        return $this;
    }

    private function getDictionaryList(): void
    {
        $dictionaries = $this->main->findDictionaryFiles();

        $this->response
            ->setContent(['dictionaries' => $dictionaries])
            ->setOutputFormat('json')
            ->setShow(true);
    }

    private function getDictionaryContent(): void
    {
        $path = $this->post['path'] ?? '';
        if (empty($path)) {
            $this->handleError('Invalid dictionary path');

            return;
        }

        $content = $this->main->loadDictionaryContent($path);
        if ($content === null) {
            $this->handleError('Failed to load dictionary content');

            return;
        }

        $this->response
            ->setContent(['content' => $content])
            ->setOutputFormat('json')
            ->setShow(true);
    }

    private function handleError(string $message): void
    {
        $this->response
            ->setContent([
                'error'   => true,
                'message' => $this->smg->setSMG([
                    'type'    => 'error',
                    'content' => $message,
                ])->getLastMessage(),
            ])
            ->setOutputFormat('json')
            ->setIsError(true)
            ->setShow(true);
    }

    private function saveDictionaryContent(): void
    {
        $path    = $this->post['path'] ?? '';
        $content = $this->post['content'] ?? null;

        if (empty($path) || empty($content)) {
            $this->handleError('Invalid save request');

            return;
        }

        if (!$this->main->validateDictionaryStructure($content)) {
            $this->handleError('Invalid dictionary structure');

            return;
        }

        $success = $this->main->saveDictionaryContent($path, $content);

        if (!$success) {
            $this->handleError('Failed to save dictionary');

            return;
        }

        $this->response
            ->setContent([
                'success' => true,
                'message' => $this->smg->setSMG([
                    'type'    => 'success',
                    'content' => 'Dictionary saved successfully',
                ])->getLastMessage(),
            ])
            ->setOutputFormat('json')
            ->setShow(true);
    }

    /**
     * @return void
     */
    #[NoReturn] private function tempateShowInteraction(): void
    {
        ex_c('Test de ShowInteraction');
    }
}