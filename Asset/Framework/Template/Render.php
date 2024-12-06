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

namespace Asset\Framework\Template;

use Asset\Framework\I18n\Lang;
use Asset\Framework\Template\Form\FormInput;
use Asset\Framework\Template\Form\FormSMG;
use Asset\Framework\Trait\SingletonTrait;
use Asset\Framework\Core\{Files, Variable};
use Exception;

/**
 * Class that handles: Render
 *
 * @package Asset\Framework\Template;
 */
class Render
{

    use SingletonTrait;


    private const array DEFAULT_DIC_PATH = ['Asset', 'resource', 'dic', 'default.json'];
    private const array NOT_FOUND_TEMPLATE = ['Asset', 'resource', 'template', 'not_found.html'];

    /**
     * @var array
     */
    private array $input = [];

    /**
     * @var array
     */
    private array $smg = [];

    /**
     * @var array
     */
    private array $inputControl = [];

    /**
     * @var array
     */
    private array $event_response = [];

    /**
     * @var array
     */
    private array $data = [];

    /**
     * @var array
     */
    private array $dictionary = [];

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @var string
     */
    private string $file_reader = '';

    /**
     * @var bool
     */
    private bool $recursive = false;

    /**
     * @var string
     */
    private string $smgContent = '';

    /**
     *
     */
    public function __construct()
    {
        $defaultDic = Files::getInstance()->getAbsolutePath(
            implode(DS, [PD, ...self::DEFAULT_DIC_PATH])
        );
        $this->setDic($defaultDic);
    }

    /**
     * @param string $dic
     * @return $this
     */
    public function setDic(string $dic): self
    {
        if (!file_exists($dic)) {
            return $this;
        }

        $jsonContent = file_get_contents($dic);
        $rawDic      = json_decode($jsonContent, true);
        $lang        = Lang::getLang();

        if (isset($rawDic['fixed']['files'])) {
            foreach ($rawDic['fixed']['files'] as $key => $path) {
                $this->dictionary[$key] = $path;
            }
        }

        if (isset($rawDic['fixed']['const'])) {
            foreach ($rawDic['fixed']['const'] as $key => $value) {
                $result = Variable::findKeyInArray(CONFIG, $value);
                if ($result !== null) {
                    $this->dictionary[$key] = $result['value'];
                }
            }
        }

        if (isset($rawDic['translations'][$lang])) {
            foreach ($rawDic['translations'][$lang] as $key => $value) {
                $this->dictionary[$key] = $value;
            }
        }

        $this->dictionary['sys-home'] = CONFIG->app->host->getEntry();

        return $this;
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
            $this->data ?? [],
            $this->dictionary ?? [],
            $this->inputControl ?? []
        );

        if (!$this->recursive) {
            if (!file_exists($this->path)) {
                $this->path = Files::getInstance()->getAbsolutePath(
                    implode(DS, [PD, ...self::NOT_FOUND_TEMPLATE])
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
     * @param string $file_reader
     * @return self
     */
    private function setFileReader(string $file_reader): self
    {
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
     * @param array $data
     * @return $this
     */
    public function setData(array $data = []): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param FormInput|null $input
     * @return $this
     */
    public function setInput(?FormInput $input): self
    {
        $this->input = $input?->getInputs() ?? [];

        return $this;
    }

    /**
     * @param FormSMG|null $smg
     * @return $this
     */
    public function setSMG(?FormSMG $smg): self
    {
        $this->smg = $smg?->getMessages() ?? [];

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
     * @param bool $recursive
     * @param string $file_reader
     * @return $this
     */
    public function setOthers(bool $recursive, string $file_reader): self
    {
        $this->recursive   = $recursive;
        $this->file_reader = $file_reader;

        return $this;
    }

    /**
     * @param array $event_response
     * @return $this
     */
    public function setEventResponse(array $event_response): self
    {
        $this->event_response = $event_response;

        return $this;
    }

    /**
     * @param array $input_control
     * @return $this
     */
    public function setInputControl(array $input_control): self
    {
        $this->inputControl = $input_control;

        return $this;
    }

    /**
     * @param string $token
     * @return string
     */
    public function translateToken(string $token): string
    {
        return $this->setSmgContent('{{'.$token.'}}')
            ->getTranslateContent();
    }

    /**
     * @return string
     */
    public function getTranslateContent(): string
    {
        $content = $this->smgContent;
        foreach ($this->dictionary as $key => $value) {
            $content = str_replace("{{".$key."}}", $value, $content);
        }

        return $content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setSmgContent(string $content): self
    {
        $this->smgContent = $content;

        return $this;
    }
}