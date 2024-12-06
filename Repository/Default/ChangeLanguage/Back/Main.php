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

use Asset\Framework\Interface\ControllerInterface;
use Asset\Framework\Trait\SingletonTrait;

/**
 * Class that handles:
 *
 * @package ChangeLanguage;
 */
class Main implements ControllerInterface
{

    use SingletonTrait;

    /**
     * @var Event
     */
    private Event $event;

    /**
     * Main constructor.
     */
    public function __construct()
    {
        $this->initializeEvent();
    }

    private function initializeEvent(): void
    {
        $this->event = Event::getInstance($this)->eventHandler();
    }
}