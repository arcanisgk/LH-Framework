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

namespace Repository\Default\Validar\Admin\Back;

use Asset\Framework\Controller\{BaseEvent, FrontResource, Response};
use Asset\Framework\Core\Files;
use Asset\Framework\Interface\ControllerInterface;
use Asset\Framework\View\{FormInput, FormSMG, Render};
use Exception;

/**
 * Class that handles:
 *
 * @package Repository\Default\Admin\Back;
 */
class Main extends FrontResource implements ControllerInterface
{

    /**
     * @var Main|null Singleton instance of the class: Main.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class Main.
     *
     * @return Main The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @var Response|null
     */
    private ?Response $response;

    /**
     * @var BaseEvent|null
     */
    private ?BaseEvent $event;

    /**
     * @var FormInput|null
     */
    private ?FormInput $input;

    /**
     * @var FormSMG|null
     */
    private ?FormSMG $smg;

    /**
     * Main constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->response = Response::getInstance();
        $this->event    = BaseEvent::getInstance();
        $this->input    = FormInput::getInstance();
        $this->smg      = FormSMG::getInstance();
        $this->input->setInput($this->form_input);
        $this->smg->setSMG($this->form_smg);
    }

    /**
     * Declare on it inputs for form.
     *
     * @var array
     */
    private array $form_input = [];

    /**
     * Declare on it smg for input.
     *
     * @var array
     */
    private array $form_smg = [];

    /**
     * @return Response
     * @throws Exception
     */
    public function process(): Response
    {
        $form = Render::getInstance()
            ->setInput($this->input)
            ->setSMG($this->smg)
            ->setEventResponse($this->event->response)
            ->setPath(Files::getInstance()->getAbsolutePath(dirname(__FILE__).'/../html/content.phtml'))
            ->setData()
            ->setOthers(false, '')
            ->render();

        return $this->response->setData(['html_content' => $form, 'assets' => $this->getHtmlAssets()])
            ->setShow(true)
            ->setIn('html_content')
            ->setRefresh(false)
            ->setNav(false)
            ->setMail(false);

    }
}