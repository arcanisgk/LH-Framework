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

namespace Repository\Default\UserAccess\Back;

use Asset\Framework\Controller\{
    EventController,
    FrontResourceController,
    ResponseController
};
use Asset\Framework\View\{
    FormInput,
    FormSMG,
    RenderTemplate
};
use Asset\Framework\Core\Files;
use Asset\Framework\Interface\ControllerInterface;
use Exception;

/**
 * Class that handles:
 *
 * @package Repository\Default\UserAccess\Back;
 */
class Main extends FrontResourceController implements ControllerInterface
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
     * @var ResponseController|null
     */
    private ?ResponseController $response;

    /**
     * @var EventController|null
     */
    private ?EventController $event;

    /**
     * @var FormInput|null
     */
    public ?FormInput $input;

    /**
     * @var FormSMG|null
     */
    public ?FormSMG $smg;

    /**
     * Main constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->response = ResponseController::getInstance();
        $this->event    = Event::getInstance()->setMain($this);
        $this->input    = FormInput::getInstance();
        $this->smg      = FormSMG::getInstance();
        $this->input->setInput($this->form_input);
        $this->smg->setSMG($this->form_smg);
        if ($this->event->event_exists) {
            $this->response->setData($this->event->listenerEvent());
        }
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
     * @return ResponseController
     * @throws Exception
     */
    public function process(): ResponseController
    {

        $form = RenderTemplate::getInstance()
            ->setInput($this->input)
            ->setSMG($this->smg)
            ->setDic(Files::getInstance()->getAbsolutePath(dirname(__FILE__).'/../dic/view.json'))
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