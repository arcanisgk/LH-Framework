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

use Asset\Framework\Trait\SingletonTrait;
use Exception;

/**
 * Class that handles: Deployment of Mail Templates html
 *
 * @package Asset\Framework\Template;
 */
class MailComposer
{

    use SingletonTrait;

    /**
     * @var Render
     */
    private Render $renderer;

    /**
     * MailComposer constructor.
     */
    public function __construct()
    {
        $this->renderer = Render::getInstance();
        $this->renderer->setDic(implode(DS, [PD, 'Asset', 'resource', 'dic', 'email.json']));
    }

    /**
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function buildEmail(array $data): string
    {
        $templatePath = implode(DS, [PD, 'Asset', 'resource', 'template', 'e_mail.html']);

        return $this->renderer
            ->setPath($templatePath)
            ->setData([
                'title'   => $data['title'] ?? '',
                'intro'   => $data['intro'] ?? '',
                'content' => $data['content'] ?? '',
                'footer'  => $data['footer'] ?? '',
            ])
            ->render();
    }
}