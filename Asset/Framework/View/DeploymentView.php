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

use Asset\Framework\Controller\ResponseController;
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

    }


    /**
     * @var array
     */
    private array $templates = [];

    /**
     * @var array
     */
    private array $data = [];

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
     * @param ResponseController|null $response
     * @return void
     * @throws Exception
     */
    public function showContent(?ResponseController $response): void
    {

        $this->setData($response->getData());
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
        $dir  = $file->getAbsolutePath(
            implode(DS, [PD, 'Asset', 'resource', 'template', 'index.html'])
        );

        $dir_tpl_icon         = implode(DS, [PD, 'Asset', 'resource', 'template', 'icon_link.html']);
        $dir_tpl_css          = implode(DS, [PD, 'Asset', 'resource', 'template', 'styles_link.html']);
        $dir_tpl_html_content = implode(DS, [PD, 'Asset', 'resource', 'template', 'html_content.html']);
        $dir_tpl_app_setting  = implode(DS, [PD, 'Asset', 'resource', 'template', 'app_setting.html']);
        $dir_tpl_js           = implode(DS, [PD, 'Asset', 'resource', 'template', 'javascript_link.html']);

        $css = implode('', $this->getData()['assets']['CSS']);
        $js  = implode('', $this->getData()['assets']['JS']);

        $icon_link = RenderTemplateView::getInstance()->setPath($dir_tpl_icon)
            ->render();

        $styles_link = RenderTemplateView::getInstance()->setPath($dir_tpl_css)
            ->setData(['view_css' => $css])
            ->render();

        $app_setting = RenderTemplateView::getInstance()->setPath($dir_tpl_app_setting)
            ->render();

        $javascript_link = RenderTemplateView::getInstance()->setPath($dir_tpl_js)
            ->setData(['view_js' => $js])
            ->render();


        $html_content = RenderTemplateView::getInstance()->setPath($dir_tpl_html_content)
            ->setData($this->getData())
            ->render();

        $data = [
            'lang'            => CONFIG['APP']['HOST']['LANG'],
            'html_tittle'     => CONFIG['APP']['PROJECT']['PROJECT_NAME'],
            'icon_link'       => $icon_link,
            'styles_link'     => $styles_link,
            'html_content'    => $html_content,
            'app_setting'     => $app_setting,
            'javascript_link' => $javascript_link,
        ];

        $html = RenderTemplateView::getInstance()->setPath($dir)
            ->setData($data)
            ->render();

        $this->outHtml(
            $html
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