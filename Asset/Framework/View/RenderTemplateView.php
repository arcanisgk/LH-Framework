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

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core;
 */
class RenderTemplateView
{

    /**
     * @var RenderTemplateView|null Singleton instance of the class: RenderTemplate.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class RenderTemplate.
     *
     * @return RenderTemplateView The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $path
     * @param array $data
     * @param bool $recursive
     * @param string $file_reader
     * @return string
     */
    public function render(string $path, array $data = [], bool $recursive = false, string $file_reader = ''): string
    {
        if ($recursive === false) {

            //ex($path, file_exists($path));
            if (!file_exists($path)) {

                $string = dirname(__DIR__).'/../resource/template/not_found.html';

                //ex($string);

                $path = Files::getInstance()->getAbsolutePath($string);
            }

            //ex_c($path);


            $file_reader = file_get_contents($path);
        }

        foreach ($data as $key => $content) {
            if (is_array($content)) {
                $file_reader = $this->render('', $content, true, $file_reader);
            } else {
                $file_reader = str_replace("{{".$key."}}", $content, $file_reader);
            }
        }

        return $file_reader;
    }

}