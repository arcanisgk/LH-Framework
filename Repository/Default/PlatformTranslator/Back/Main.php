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

namespace Repository\Default\PlatformTranslator\Back;

use Asset\Framework\Base\FrontResource;
use Asset\Framework\Core\Files;
use Asset\Framework\Http\Response;
use Asset\Framework\Interface\ControllerInterface;
use Asset\Framework\Template\Form\FormInput;
use Asset\Framework\Template\Form\FormSMG;
use Asset\Framework\Template\Render;
use Asset\Framework\Trait\SingletonTrait;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class that handles:
 *
 * @package Repository\Default\PlatformTranslator\Back;
 */
class Main extends FrontResource implements ControllerInterface
{

    use SingletonTrait;

    private const array SCAN_PATHS
        = [
            'Asset/resource/dic',
            'Repository/Default',
            'Repository/Project',
        ];

    private const array SUPPORTED_LANGUAGES = ['es', 'en', 'fr', 'pt'];

    private const string TEMPLATE_PATH = '/../html/';

    private const string DIC_PATH = '/../dic/default.json';

    /**
     * @var Render
     */
    private Render $render;

    /**
     * @var Response
     */
    private Response $response;

    /**
     * @var FormInput
     */
    private FormInput $inputs;

    /**
     * @var FormSMG
     */
    private FormSMG $smg;

    /**
     * @var Event
     */
    private Event $event;

    /**
     * @var string
     */
    private string $dic;

    /**
     * Main constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->initializeComponents();
        $this->initializeEvent();
    }

    private function initializeComponents(): void
    {
        $files          = Files::getInstance();
        $dicPath        = $files->getAbsolutePath(dirname(__FILE__).self::DIC_PATH);
        $this->render   = Render::getInstance();
        $this->response = Response::getInstance();
        $this->inputs   = FormInput::getInstance();
        $this->smg      = FormSMG::getInstance();
        $this->dic      = $dicPath;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function initializeEvent(): void
    {
        $this->event = Event::getInstance($this)->eventHandler();
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function process(): Response
    {

        if ($this->event->isEventExists()) {
            return $this->getResponse();
        }

        $content = $this->buildContent();

        return $this->buildResponse($content);
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return Main
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildContent(): string
    {
        $templateFile = $this->getTemplateFile();
        $templatePath = Files::getInstance()->getAbsolutePath(
            dirname(__FILE__).self::TEMPLATE_PATH.$templateFile
        );

        return $this->render
            ->setInput($this->inputs)
            ->setSMG($this->smg)
            ->setDic($this->dic)
            ->setInputControl($this->getInputControls())
            ->setPath($templatePath)
            ->setData()
            ->setOthers(false, '')
            ->render();
    }

    /**
     * @return string
     */
    private function getTemplateFile(): string
    {
        return 'content.phtml';
    }

    /**
     * @return string[]
     */
    private function getInputControls(): array
    {

        return [
            'template' => 'this is a Template File/estructure',

        ];
    }

    /**
     * @param string $content
     * @return Response
     */
    private function buildResponse(string $content): Response
    {
        return $this->response
            ->setContent([
                'html-content' => $content,
                'assets'       => $this->getHtmlAssets(),
            ])
            ->setShow(true)
            ->setIn('html-content')
            ->setRefresh(false)
            ->setNav(false)
            ->setMail(false);
    }

    /**
     * @return Render
     */
    public function getRender(): Render
    {
        return $this->render;
    }

    /**
     * @param Render $render
     * @return Main
     */
    public function setRender(Render $render): self
    {
        $this->render = $render;

        return $this;
    }

    /**
     * @return FormInput
     */
    public function getInputs(): FormInput
    {
        return $this->inputs;
    }

    /**
     * @param FormInput $inputs
     * @return Main
     */
    public function setInputs(FormInput $inputs): self
    {
        $this->inputs = $inputs;

        return $this;
    }

    /**
     * @return FormSMG
     */
    public function getSmg(): FormSMG
    {
        return $this->smg;
    }

    /**
     * @param FormSMG $smg
     * @return Main
     */
    public function setSmg(FormSMG $smg): self
    {
        $this->smg = $smg;

        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string
     */
    public function getDic(): string
    {
        return $this->dic;
    }

    /**
     * @param string $dic
     * @return $this
     */
    public function setDic(string $dic): self
    {
        $this->dic = $dic;

        return $this;
    }

    public function findDictionaryFiles(): array
    {
        $dictionaryFiles = [];

        foreach (self::SCAN_PATHS as $basePath) {
            $fullPath = PD.DS.$basePath;

            if ($basePath === 'Asset/resource/dic') {
                $dictionaryFiles = array_merge(
                    $dictionaryFiles,
                    $this->scanDirectoryForJsonFiles($fullPath)
                );
                continue;
            }

            // For Repository paths, scan for 'dic' directories
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($fullPath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isDir() && !$item->isDot() && $item->getBasename() === 'dic') {
                    $dictionaryFiles = array_merge(
                        $dictionaryFiles,
                        $this->scanDirectoryForJsonFiles($item->getPathname())
                    );
                }
            }
        }

        return $dictionaryFiles;
    }

    private function scanDirectoryForJsonFiles(string $directory): array
    {
        $jsonFiles = [];
        if (!is_dir($directory)) {
            return $jsonFiles;
        }

        $files = glob($directory.DS.'*.json');
        foreach ($files as $file) {
            $relativePath = str_replace(PD.DS, '', $file);
            $jsonFiles[]  = [
                'path'      => $relativePath,
                'name'      => basename($file),
                'directory' => dirname($relativePath),
            ];
        }

        return $jsonFiles;
    }

    public function saveDictionaryContent(string $path, array $content): bool
    {
        $fullPath = PD.DS.$path;

        return file_put_contents(
                $fullPath,
                json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ) !== false;
    }

    public function validateDictionaryStructure(array $content): bool
    {
        if (!isset($content['fixed'], $content['translations'])) {
            return false;
        }

        if (!is_array($content['fixed']) || !is_array($content['translations'])) {
            return false;
        }

        if (isset($content['fixed']['files']) && !is_array($content['fixed']['files'])) {
            return false;
        }

        if (isset($content['fixed']['const']) && !is_array($content['fixed']['const'])) {
            return false;
        }

        foreach ($content['translations'] as $lang => $translations) {
            if (!in_array($lang, self::SUPPORTED_LANGUAGES) || !is_array($translations)) {
                return false;
            }
        }

        return true;
    }

    public function loadDictionaryContent(string $path): ?array
    {
        $fullPath = PD.DS.$path;
        if (!file_exists($fullPath)) {
            return null;
        }

        $content = file_get_contents($fullPath);

        return json_decode($content, true);
    }
}