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

use Asset\Framework\Controller\EventController;
use Asset\Framework\View\FormInput;
use Asset\Framework\View\FormSMG;
use Exception;

/**
 * Class that handles:
 *
 * @package Repository\Default\ChangeLanguage\Back;
 */
class Main
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
        $this->event = Event::getInstance()->setMain($this);
        if ($this->event->event_exists) {
            $this->event->listenerEvent();
        }
    }
}