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

namespace Repository\Default\ChangeLanguage\Back;

use Asset\Framework\Http\Request;
use Asset\Framework\Trait\SingletonTrait;
use Exception;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class that handles:
 *
 * @package Repository\Default\ChangeLanguage\Back;
 */
class Event
{

    use SingletonTrait;

    /**
     * @var Main
     */
    private Main $main;

    /**
     * @var string
     */
    private string $event = '';

    /**
     * @var bool
     */
    private bool $event_exists = false;

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
            ->setMain($main);
    }

    /**
     * @param Main $main
     * @return $this
     */
    public function setMain(Main $main): self
    {
        $this->main = $main;

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
     * @return void
     * @used-by listenerEvent()
     */
    #[NoReturn] private function es(): void
    {
        $_SESSION['SYSTEM']['LANG']              = 'es';
        $_SESSION['USER']['PREFERENCES']['LANG'] = 'es';
        Request::getInstance()->redirectToUri($_POST['uri_current']);
    }

    /**
     * @return void
     * @used-by listenerEvent()
     */
    #[NoReturn] private function fr(): void
    {
        $_SESSION['SYSTEM']['LANG']              = 'fr';
        $_SESSION['USER']['PREFERENCES']['LANG'] = 'fr';
        Request::getInstance()->redirectToUri($_POST['uri_current']);
    }

    /**
     * @return void
     * @used-by listenerEvent()
     */
    #[NoReturn] private function pt(): void
    {
        $_SESSION['SYSTEM']['LANG']              = 'pt';
        $_SESSION['USER']['PREFERENCES']['LANG'] = 'pt';
        Request::getInstance()->redirectToUri($_POST['uri_current']);
    }

    /**
     * @return void
     * @used-by listenerEvent()
     */
    #[NoReturn] private function en(): void
    {
        $_SESSION['SYSTEM']['LANG']              = 'en';
        $_SESSION['USER']['PREFERENCES']['LANG'] = 'en';
        Request::getInstance()->redirectToUri($_POST['uri_current']);
    }

}