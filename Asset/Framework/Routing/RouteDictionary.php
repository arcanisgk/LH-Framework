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

namespace Asset\Framework\Routing;

use Asset\Framework\Interface\ControllerInterface;


use Asset\Framework\Trait\SingletonTrait;
use Repository\Default\{
    Shortcut as Shortcut,
    PlatformTranslator as PlatformTranslator,
    Template as Template,
    ClearSession as ClearSession,
    Test as Test,
    ChangeLanguage as ChangeLanguage,
    UserTerms as UserTerms,
    NotFound as NotFound,
    UserAccess as UserAccess,
};


/**
 * Class that handles:
 *
 * @package Asset\Framework\Routing;
 */
class RouteDictionary
{

    use SingletonTrait;

    private const array ROUTES
        = [
            '/shortcut'            => Shortcut\Back\Main::class,
            '/platform-translator' => PlatformTranslator\Back\Main::class,
            '/template'            => Template\Back\Main::class,
            '/clear-session'       => ClearSession\Back\Main::class,
            '/test'                => Test\Back\Main::class,
            '/change-language'     => ChangeLanguage\Back\Main::class,
            '/user-terms'          => UserTerms\Back\Main::class,
            '/user-access'         => UserAccess\Back\Main::class,
            'default'              => NotFound\Back\Main::class,
        ];

    /**
     * @var string
     */
    private string $path;

    public function resolveController(): ControllerInterface
    {
        $normalizedPath  = $this->normalizePath();
        $controllerClass = self::ROUTES[$normalizedPath] ?? self::ROUTES['default'];

        return $controllerClass::getInstance();
    }

    private function normalizePath(): string
    {
        return mb_strtolower($this->getPath());
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = trim($path);

        return $this;
    }
}