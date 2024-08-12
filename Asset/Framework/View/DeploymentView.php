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
class DeploymentView
{

    /**
     * @var DeploymentView|null Singleton instance of the class: DeploymentView.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class DeploymentView.
     *
     * @return DeploymentView The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * DeploymentView constructor.
     */
    public function __construct()
    {
        $this->templates['default'] = implode(DS, ['resource', 'template', 'index.html']);
    }


    /**
     * @var array
     */
    private array $templates = [];

    /**
     * @var array
     */
    private array $data;

    /**
     * @return array
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * @param array $templates
     */
    public function setTemplates(array $templates): void
    {
        $this->templates = $templates;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @param array $response
     * @return void
     * @throws Exception
     */
    public function showContent(array $response): void
    {
        $this->setData($response['data']);
        if (!isset($_SESSION['BUILD_UP'])) {
            $this->fullBuild();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function fullBuild(): void
    {
        $file = Files::getInstance();
        $dir  = $file->getAbsolutePath(dirname(__DIR__).'/../'.$this->templates['default']);
        $data = [
            'lang'            => CONFIG['APP']['HOST']['LANG'],
            'html_tittle'     => CONFIG['APP']['PROJECT']['PROJECT_NAME'],
            'icon_link'       => RenderTemplateView::getInstance()->render(
                implode(DS, [PD, 'Asset', 'resource', 'template', 'icon_link.html'])
            ),
            'styles_link'     => RenderTemplateView::getInstance()->render(
                implode(DS, [PD, 'Asset', 'resource', 'template', 'styles_link.html']),
                ['view_css' => implode('', $_SERVER['HTML_ASSETS']['CSS'])],
            ),
            'html_content'    => RenderTemplateView::getInstance()->render(
                implode(DS, [PD, 'Asset', 'resource', 'template', 'html_content.html']),
                $this->getData()
            ),
            'javascript_link' => RenderTemplateView::getInstance()->render(
                implode(DS, [PD, 'Asset', 'resource', 'template', 'javascript_link.html']),
                ['view_js' => implode('', $_SERVER['HTML_ASSETS']['JS'])],
            ),
        ];

        $this->outHtml(
            RenderTemplateView::getInstance()->render($dir, $data)
        );
    }

    /**
     * @param string $content
     * @return void
     */
    private function outHtml(string $content): void
    {
        echo $content;
    }
}