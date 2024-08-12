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

namespace Asset\Framework\Controller;

/**
 * Class that handles: Controller Responses
 *
 * @package Asset\Framework\Controller;
 */
class ResponseController
{

    /**
     * @var ResponseController|null Singleton instance of the class: ControllerResponse.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class ControllerResponse.
     *
     * @return ResponseController The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @var array
     */
    private array $data = [];

    /**
     * @var bool
     */
    private bool $show = true;

    /**
     * @var string
     */
    private string $in = 'htm_content';

    /**
     * @var bool
     */
    private bool $refresh = false;

    /**
     * @var bool
     */
    private bool $nav = false;

    /**
     * @var bool
     */
    private bool $mail = false;

    /**
     * ControllerResponse constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function shouldShow(): bool
    {
        return $this->show;
    }

    /**
     * @return string
     */
    public function getIn(): string
    {
        return $this->in;
    }

    /**
     * @return bool
     */
    public function shouldRefresh(): bool
    {
        return $this->refresh;
    }

    /**
     * @return bool
     */
    public function shouldNav(): bool
    {
        return $this->nav;
    }

    /**
     * @return bool
     */
    public function shouldMail(): bool
    {
        return $this->mail;
    }

    /**
     * @param array $data
     * @return ResponseController
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param bool $show
     * @return ResponseController
     */
    public function setShow(bool $show): self
    {
        $this->show = $show;

        return $this;
    }

    /**
     * @param string $in
     * @return ResponseController
     */
    public function setIn(string $in): self
    {
        $this->in = $in;

        return $this;
    }

    /**
     * @param bool $refresh
     * @return ResponseController
     */
    public function setRefresh(bool $refresh): self
    {
        $this->refresh = $refresh;

        return $this;
    }

    /**
     * @param bool $nav
     * @return ResponseController
     */
    public function setNav(bool $nav): self
    {
        $this->nav = $nav;

        return $this;
    }

    /**
     * @param bool $mail
     * @return ResponseController
     */
    public function setMail(bool $mail): self
    {
        $this->mail = $mail;

        return $this;
    }
}