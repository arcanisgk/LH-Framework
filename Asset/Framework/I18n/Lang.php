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

namespace Asset\Framework\I18n;

use Asset\Framework\Trait\SingletonTrait;

/**
 * Class that handles:
 *
 * @package Asset\Framework\I18n;
 */
class Lang
{

    use SingletonTrait;

    private const string DEFAULT_LANGUAGE = 'en';

    /**
     * Get the current language setting
     *
     * @return string The language code
     */
    public static function getLang(): string
    {
        return self::getFromSession()
            ?? self::getFromHostConfig()
            ?? self::DEFAULT_LANGUAGE;
    }

    /**
     * Get language from session if available
     *
     * @return string|null
     */
    private static function getFromSession(): ?string
    {
        return $_SESSION['USER']['PREFERENCES']['LANG'] ?? null;
    }

    /**
     * Get language from host configuration if available
     *
     * @return string|null
     */
    private static function getFromHostConfig(): ?string
    {
        if (isset(CONFIG->app->host) && method_exists(CONFIG->app->host, 'getLang')) {
            return CONFIG->app->host->getLang();
        }

        return null;
    }

}