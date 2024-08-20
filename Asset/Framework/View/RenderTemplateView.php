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

namespace Asset\Framework\View;

use Asset\Framework\Core\Files;
use Exception;

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core;
 */
class RenderTemplateView
{

    /**
     * @var RenderTemplateView|null Singleton instance of the class: RenderTemplate.
     */
    private static ?self $instance = null;

    private array $input = [];

    private array $smg = [];

    private array $event_response = [];

    private array $data = [];

    private string $path = '';

    private string $file_reader = '';

    private bool $recursive = false;

    /**
     * Get the singleton instance of teh class RenderTemplate.
     *
     * @return RenderTemplateView The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function render(): string
    {
        $data = array_merge(
            $this->input ?? [],
            $this->smg ?? [],
            $this->event_response ?? [],
            $this->data ?? []
        );

        if ($this->recursive === false) {

            if (!file_exists($this->path)) {
                $this->path = Files::getInstance()->getAbsolutePath(
                    implode(DS, [PD, 'Asset', 'resource', 'template', 'not_found.html'])
                );
            }

            $this->file_reader = file_get_contents($this->path);
        }

        foreach ($data as $key => $content) {

            if (is_array($content)) {

                $content = (new self())
                    ->setData($content)
                    ->setRecursive(true)
                    ->setFileReader($this->file_reader)
                    ->render();
            }

            $this->file_reader = str_replace("{{".$key."}}", $content, $this->file_reader);

        }

        return $this->file_reader;

    }

    /**
     * @param FormInput|null $input
     * @return $this
     */
    public function setInput(?FormInput $input): self
    {
        $this->input = $input->inputField;

        return $this;
    }

    /**
     * @param FormSMG|null $smg
     * @return $this
     */
    public function setSMG(?FormSMG $smg): self
    {
        $this->smg = $smg->smgField;

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data = []): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param bool $recursive
     * @param string $file_reader
     * @return $this
     */
    public function setOthers(bool $recursive, string $file_reader): self
    {
        $this->recursive = $recursive;

        $this->file_reader = $file_reader;

        return $this;
    }

    /**
     * @param bool $recursive
     * @return self
     */
    private function setRecursive(bool $recursive = false): self
    {
        $this->recursive = $recursive;

        return $this;
    }

    /**
     * @param string $file_reader
     * @return self
     */
    private function setFileReader(string $file_reader): self
    {
        $this->file_reader = $file_reader;

        return $this;
    }

    public function setEventResponse(array $event_response): self
    {
        $this->event_response = $event_response;

        return $this;
    }

}