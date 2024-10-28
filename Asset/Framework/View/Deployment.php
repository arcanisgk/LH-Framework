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

use Asset\Framework\{Controller\ResponseController, Core\Files, Core\SecurityPolicies, Core\Variable};
use Exception;

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core;
 */
class Deployment
{

    /**
     * @var Deployment|null Singleton instance of the class: Deployment.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class Deployment.
     *
     * @return Deployment The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Deployment constructor.
     */
    public function __construct()
    {

    }

    /**
     * @var array
     */
    private array $data = [];

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
        $dir  = $file->getAbsolutePath(implode(DS, [PD, 'Asset', 'resource', 'template', 'index.html']));

        $dir_tpl_meta         = implode(DS, [PD, 'Asset', 'resource', 'template', 'meta.html']);
        $dir_tpl_icon         = implode(DS, [PD, 'Asset', 'resource', 'template', 'icon_link.html']);
        $dir_tpl_html_content = implode(DS, [PD, 'Asset', 'resource', 'template', 'html_content.html']);
        $dir_tpl_app_setting  = implode(DS, [PD, 'Asset', 'resource', 'template', 'app_setting.html']);
        $dir_tpl_dev_mode     = implode(DS, [PD, 'Asset', 'resource', 'template', 'dev_mode.html']);


        $meta      = RenderTemplate::getInstance()->setPath($dir_tpl_meta)->render();
        $icon_link = RenderTemplate::getInstance()->setPath($dir_tpl_icon)->render();

        $app_setting = '';
        if (CONFIG->environment->getAppSetting() === true) {
            $app_setting = RenderTemplate::getInstance()->setPath($dir_tpl_app_setting)->render();
        }

        $dev_mode = '';
        if (CONFIG->environment->getDevTool() === true) {
            $dev_mode = RenderTemplate::getInstance()->setPath($dir_tpl_dev_mode)->render();
        }

        $html_content = RenderTemplate::getInstance()->setPath($dir_tpl_html_content)
            ->setData($this->getData())
            ->render();

        $data = [
            'lang'        => CONFIG->app->host->getLang(),
            'html-tittle' => CONFIG->app->project->getProjectName(),
            'meta'        => $meta,
            'icon-link'   => $icon_link,
            'html-body'   => $html_content,
            'app-setting' => $app_setting,
            'dev-mode'    => $dev_mode,
        ];

        $security = SecurityPolicies::initSecurity();

        header("Content-Security-Policy: ".$security::getScp());

        $html = RenderTemplate::getInstance()->setPath($dir)
            ->setInputControl(
                ['nonce-key' => $security::getNonce()]
            )
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