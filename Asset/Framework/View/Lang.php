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

/**
 * Class that handles:
 *
 * @package Asset\Framework\View;
 */
class Lang
{

    /**
     * @var Lang|null Singleton instance of the class: Lang.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class Lang.
     *
     * @return Lang The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function loadDictionary()
    {

    }

    /**
     * @return string
     */
    public static function getLang(): string
    {

        if (isset($_SESSION['USER']['PREFERENCES']['LANG'])) {
            return $_SESSION['USER']['PREFERENCES']['LANG'];
        }
        if (isset(CONFIG['APP']['HOST']['LANG'])) {
            return CONFIG['APP']['HOST']['LANG'];
        }

        return 'en';
    }

}