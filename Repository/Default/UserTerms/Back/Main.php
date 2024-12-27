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

namespace Repository\Default\UserTerms\Back;

use Asset\Framework\Base\FrontResource;
use Asset\Framework\Core\Files;
use Asset\Framework\Http\Request;
use Asset\Framework\Http\Response;
use Asset\Framework\Interface\ControllerInterface;
use Asset\Framework\Template\Render;
use Asset\Framework\Trait\SingletonTrait;
use Exception;

/**
 * Class that handles: NotFound URLs
 *
 * @package Repository\Default\UserTerms\Back;
 */
class Main extends FrontResource implements ControllerInterface
{

    use SingletonTrait;

    private const string TEMPLATE_PATH = '/../html/';

    /**
     * @var Render
     */
    private Render $render;

    /**
     * @var Response
     */
    private Response $response;

    /**
     * @var array
     */
    private array $get;

    /**
     * Main constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->initializeComponents();
    }

    private function initializeComponents(): void
    {
        $this->render   = Render::getInstance();
        $this->response = Response::getInstance();
        $this->get      = Request::getInstance()->getGet();
    }

    /**
     * @return array
     */
    public function getGet(): array
    {
        return $this->get;
    }

    /**
     * @param array $get
     * @return self
     */
    public function setGet(array $get): self
    {
        $this->get = $get;

        return $this;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function process(): Response
    {
        $content = $this->buildContent();

        return $this->buildResponse($content);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function buildContent(): string
    {
        try {

            $templatePaths = $this->getTemplatePaths();

            $contentSections = $this->renderContentSections($templatePaths);

            $renderData = $this->buildRenderData($contentSections);

            $mainTemplatePath = Files::getInstance()->getAbsolutePath(
                dirname(__FILE__).self::TEMPLATE_PATH.$this->getTemplateFile()
            );

            if (!file_exists($mainTemplatePath)) {
                throw new Exception("Main template file not found: $mainTemplatePath");
            }

            return $this->render
                ->setPath($mainTemplatePath)
                ->setData($renderData)
                ->loadDictionariesFromDirectory(
                    implode(DS, [PD, 'Repository', 'Default', 'UserTerms', 'dic'])
                )
                ->render();

        } catch (Exception $e) {
            error_log("Error in buildContent: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * @return array
     */
    private function getTemplatePaths(): array
    {
        return [
            'terms'        => implode(DS, [
                PD,
                'Repository',
                'Default',
                'UserTerms',
                'html',
                'content.terms-service.phtml',
            ]),
            'privacy'      => implode(DS, [
                PD,
                'Repository',
                'Default',
                'UserTerms',
                'html',
                'content.privacy-policy.phtml',
            ]),
            'cookie'       => implode(DS, [
                PD,
                'Repository',
                'Default',
                'UserTerms',
                'html',
                'content.cookie-policy.phtml',
            ]),
            'humanitarian' => implode(DS, [
                PD,
                'Repository',
                'Default',
                'UserTerms',
                'html',
                'content.humanitarian-framework.phtml',
            ]),
        ];
    }

    /**
     * @param array $paths
     * @return array
     * @throws Exception
     */
    private function renderContentSections(array $paths): array
    {
        $renderer = Render::getInstance();
        $sections = [];

        foreach ($paths as $key => $path) {
            if (!file_exists($path)) {
                throw new Exception("Template file not found: $path");
            }
            $sections[$key] = $renderer->setPath($path)->render();
        }

        return [
            'content-terms-service'          => $sections['terms'],
            'content-privacy-policy'         => $sections['privacy'],
            'content-cookie-policy'          => $sections['cookie'],
            'content-humanitarian-framework' => $sections['humanitarian'],
        ];
    }

    /**
     * @param array $contentSections
     * @return array
     */
    private function buildRenderData(array $contentSections): array
    {
        return array_merge(
            $this->getActiveSection(),
            $contentSections,
            [
                'eula'        => CONFIG->app->project->getEula(),
                'last-update' => date('F d, Y'),
                //Aquí se deben agregar los links de las secciones externas etc.
            ]
        );
    }

    /**
     * @return string[]
     */
    private function getActiveSection(): array
    {

        $section = $this->get['Section'] ?? 'Terms-of-Service';

        $classes = [
            'active-terms'        => '',
            'active-privacy'      => '',
            'active-cookies'      => '',
            'active-humanitarian' => '',
            'show-terms'          => '',
            'show-privacy'        => '',
            'show-cookies'        => '',
            'show-humanitarian'   => '',
        ];

        switch ($section) {
            case 'Privacy-Policy':
                $classes['active-privacy'] = 'active';
                $classes['show-privacy']   = 'show';
                break;
            case 'Cookie-Policy':
                $classes['active-cookies'] = 'active';
                $classes['show-cookies']   = 'show';
                break;
            case 'Humanitarian-Data-Framework':
                $classes['active-humanitarian'] = 'active';
                $classes['show-humanitarian']   = 'show';
                break;
            default:
                $classes['active-terms'] = 'active';
                $classes['show-terms']   = 'show';
        }

        return $classes;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getTemplateFile(): string
    {
        return 'content.phtml';
    }

    /**
     * @param string $content
     * @return Response
     */
    private function buildResponse(string $content): Response
    {
        return $this->response
            ->setContent(['html-content' => $content])
            ->setShow(true)
            ->setIn('html-content')
            ->setRefresh(false)
            ->setNav(false)
            ->setMail(false);
    }
}