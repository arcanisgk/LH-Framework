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
    private array $metaHeader = [];

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
        $this->setMetaHeader();
    }

    /**
     * Set dictionary from a single file
     *
     * @param string $dic Path to dictionary file
     * @return self
     */
    public function setDic(string $dic): self
    {
        return $this->processDictionaryFile($dic);
    }

    /**
     * @param string $dictionaryFile
     * @return self
     */
    private function processDictionaryFile(string $dictionaryFile): self
    {
        if (!file_exists($dictionaryFile)) {
            return $this;
        }

        $jsonContent = file_get_contents($dictionaryFile);
        $rawDic      = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this;
        }

        $lang = Lang::getLang();

        // Process fixed files
        if (isset($rawDic['fixed']['files'])) {
            foreach ($rawDic['fixed']['files'] as $key => $path) {
                $this->dictionary[$key] = $path;
            }
        }

        // Process fixed constants
        if (isset($rawDic['fixed']['const'])) {
            foreach ($rawDic['fixed']['const'] as $key => $value) {
                $result = Variable::findKeyInArray(CONFIG, $value);
                if ($result !== null) {
                    $this->dictionary[$key] = $result['value'];
                }
            }
        }

        // Process translations for current language
        if (isset($rawDic['translations'][$lang])) {
            foreach ($rawDic['translations'][$lang] as $key => $value) {
                $this->dictionary[$key] = $value;
            }
        }

        $this->dictionary['sys-home'] = CONFIG->app->host->getEntry();

        return $this;
    }

    /**
     * Load all JSON dictionary files from a directory
     *
     * @param string $directory Path to directory containing dictionary files
     * @return self
     */
    public function loadDictionariesFromDirectory(string $directory): self
    {
        if (!is_dir($directory)) {
            return $this;
        }

        $jsonFiles = glob($directory.'/*.json');

        if (empty($jsonFiles)) {
            return $this;
        }

        foreach ($jsonFiles as $jsonFile) {
            $this->processDictionaryFile($jsonFile);
        }

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
            $this->inputControl ?? [],
            $this->getMetaHeader() ?? [],
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
     * @return array
     */
    public function getMetaHeader(): array
    {
        return $this->metaHeader;
    }

    /**
     * @return void
     */
    public function setMetaHeader(): void
    {

        $metaHeader = [
            'html-title' => CONFIG->app->project->getProjectName().' by '.CONFIG->app->company->getCompanyName(),
            'meta-autor' => CONFIG->app->company->getCompanyOwner(),
            'url-image'  => 'assets/img/logo/adah-logo.png',
            'alt-image'  => 'ADAH Network',
            'full-url'   => CONFIG->app->host->getProtocol().'://'.CONFIG->app->host->getDomain().UR,
            'site-name'  => 'ADAH Network',
        ];

        $this->metaHeader = $metaHeader;
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