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

namespace Repository\Default\NotFound\Back;

use Asset\Framework\Core\Files;
use Asset\Framework\Http\Response;
use Asset\Framework\I18n\Lang;
use Asset\Framework\Interface\ControllerInterface;
use Asset\Framework\Template\Render;
use Asset\Framework\Trait\SingletonTrait;
use Exception;

/**
 * Class that handles: NotFound URLs
 *
 * @package Repository\Default\NotFound\Back;
 */
class Main implements ControllerInterface
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
     * Main constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->initializeComponents();
    }

    private function initializeComponents(): void
    {
        $this->render   = Render::getInstance();
        $this->response = Response::getInstance();
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

        return $this->render
            ->setPath($templatePath)
            ->render();
    }

    /**
     * @return string
     */
    private function getTemplateFile(): string
    {
        return 'content.'.Lang::getLang().'.phtml';
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