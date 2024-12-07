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

use Asset\Framework\Core\Files;
use Asset\Framework\Core\SecurityPolicies;
use Asset\Framework\Http\Request;
use Asset\Framework\Http\Response;
use Asset\Framework\Trait\SingletonTrait;
use Exception;

/**
 * Class that handles: Deployment
 *
 * @package Asset\Framework\Template;
 */
class Deployment
{

    use SingletonTrait;

    /**
     * @var array
     */
    private array $data = [];

    /**
     * Deployment constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param Response|null $response
     * @return void
     * @throws Exception
     */
    public function showContent(?Response $response): void
    {

        $this->setData($response->getContent());

        if (Request::getInstance()->isAjax()) {
            header('Content-Type: application/json');
            echo $response->getResponseJson();

            return;
        }

        if (!Request::getInstance()->hasServer('X-UI')) {
            $this->fullBuild();
        } else {
            // Partial build
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


        $meta      = Render::getInstance()->setPath($dir_tpl_meta)->render();
        $icon_link = Render::getInstance()->setPath($dir_tpl_icon)->render();

        $app_setting = '';
        if (CONFIG->tools->getAppSetting() === true) {
            $app_setting = Render::getInstance()->setPath($dir_tpl_app_setting)->render();
        }

        $dev_mode = '';
        if (CONFIG->tools->getDevTool() === true) {
            $dev_mode = Render::getInstance()->setPath($dir_tpl_dev_mode)->render();
        }

        $html_content = Render::getInstance()->setPath($dir_tpl_html_content)
            ->setData($this->getData())
            ->render();

        $data = [
            'lang'        => CONFIG->app->host->getLang(),
            'html-title'  => CONFIG->app->project->getProjectName(),
            'meta'        => $meta,
            'icon-link'   => $icon_link,
            'html-body'   => $html_content,
            'app-setting' => $app_setting,
            'dev-mode'    => $dev_mode,
        ];


        //ex_c($_SESSION, session_get_cookie_params(), CONFIG);

        $security = SecurityPolicies::initSecurity();

        header("Content-Security-Policy: ".$security::getCSP());

        SecurityPolicies::exposeHumanitariaPorpuse();

        $html = Render::getInstance()->setPath($dir)
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
     * @param string $content
     * @return void
     */
    private function outHtml(string $content): void
    {
        echo $content;
    }
}