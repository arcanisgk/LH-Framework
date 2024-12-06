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
use Asset\Framework\I18n\Lang;
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

        $templateFile = $this->getTemplateFile();

        $templatePath = Files::getInstance()->getAbsolutePath(
            dirname(__FILE__).self::TEMPLATE_PATH.$templateFile
        );

        $renderData = array_merge(
            $this->getActiveSection(),
            [
                'last-update' => date('F d, Y'),
            ]
        );

        return $this->render
            ->setPath($templatePath)
            ->setData($renderData)
            ->render();
    }

    /**
     * @return string
     */
    private function getTemplateFile(): string
    {
        return 'content.'.Lang::getLang().'.phtml';
    }

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