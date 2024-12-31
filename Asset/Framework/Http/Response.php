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

namespace Asset\Framework\Http;

use Asset\Framework\Trait\SingletonTrait;

/**
 * Class that handles: Controller Responses
 *
 * @package Asset\Framework\Http;
 */
class Response
{

    use SingletonTrait;

    /**
     * @var bool
     */
    private bool $show = true;

    /**
     * @var string|null
     */
    private ?string $in;

    /**
     * @var string|null
     */
    private ?string $typeTarget;

    /**
     * @var array|null
     */
    private ?array $content;

    /**
     * @var bool|null
     */
    private ?bool $refresh;

    /**
     * @var bool|null
     */
    private ?bool $nav;

    /**
     * @var string|null
     */
    private ?string $outputFormat = 'html';

    /**
     * @var bool
     */
    private bool $mail = false;

    /**
     * @var mixed
     */
    private mixed $event;

    /**
     * @var bool|null
     */
    private ?bool $isError = false;

    /**
     * @return bool
     */
    public function isShow(): bool
    {
        return $this->show;
    }

    /**
     * @param bool $show
     * @return $this
     */
    public function setShow(bool $show): self
    {
        $this->show = $show;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIn(): ?string
    {
        return $this->in;
    }

    /**
     * @param string|null $in
     * @return $this
     */
    public function setIn(?string $in): self
    {
        $this->in = $in;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getContent(): ?array
    {
        return $this->content;
    }

    /**
     * @param array|null $content
     * @return $this
     */
    public function setContent(?array $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRefresh(): ?string
    {
        return $this->refresh;
    }

    /**
     * @param bool|null $refresh
     * @return $this
     */
    public function setRefresh(?bool $refresh): self
    {
        $this->refresh = $refresh;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNav(): ?string
    {
        return $this->nav;
    }

    /**
     * @param bool|null $nav
     * @return $this
     */
    public function setNav(?bool $nav): self
    {
        $this->nav = $nav;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOutputFormat(): ?string
    {
        return $this->outputFormat;
    }

    /**
     * @param string|null $outputFormat
     * @return $this
     */
    public function setOutputFormat(?string $outputFormat): self
    {
        $this->outputFormat = $outputFormat;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMail(): bool
    {
        return $this->mail;
    }

    /**
     * @param bool $mail
     * @return $this
     */
    public function setMail(bool $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsError(): ?bool
    {
        return $this->isError;
    }

    /**
     * @param bool|null $isError
     * @return $this
     */
    public function setIsError(?bool $isError): self
    {
        $this->isError = $isError;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvent(): mixed
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     * @return $this
     */
    public function setEvent(mixed $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Returns the response structure as a JSON string
     *
     * @return string
     */
    public function getResponseJson(): string
    {
        $responseArray = [
            'show'         => $this->show ?? null,
            'in'           => $this->in ?? null,
            'typeTarget'   => $this->typeTarget ?? null,
            'content'      => $this->content ?? null,
            'refresh'      => $this->refresh ?? null,
            'nav'          => $this->nav ?? null,
            'outputFormat' => $this->outputFormat ?? null,
            'mail'         => $this->mail ?? null,
            'event'        => $this->event ?? null,
            'isError'      => $this->isError ?? null,
        ];

        return json_encode($responseArray, JSON_PRETTY_PRINT);
    }

    public function getTypeTarget(): ?string
    {
        return $this->typeTarget;
    }

    public function setTypeTarget(string $string): self
    {
        $this->typeTarget = $string;

        return $this;
    }
}