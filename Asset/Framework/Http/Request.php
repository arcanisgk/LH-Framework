<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Asset\Framework\Http;

use Asset\Framework\Core\Variable;
use Asset\Framework\Trait\SingletonTrait;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class that handles: Controller Request
 *
 * @package Asset\Framework\Http;
 */
class Request
{
    use SingletonTrait;

    private const array BOOLEAN_STRINGS = ['true', 'false'];

    private const array VALID_GLOBALS = ['POST', 'GET', 'SERVER'];

    /**
     * @var array
     */
    private array $superGlobals = [];

    /**
     *
     */
    public function __construct()
    {
        $this->initializeSuperGlobals();
    }

    /**
     * @return void
     */
    private function initializeSuperGlobals(): void
    {
        foreach (self::VALID_GLOBALS as $global) {
            $this->superGlobals[$global] = $GLOBALS["_$global"] ?? [];
        }
    }

    /**
     * @return $this
     */
    public function CleanSuperGlobal(): self
    {
        foreach (['POST', 'GET'] as $global) {
            if (empty($this->superGlobals[$global])) {
                unset($GLOBALS["_$global"]);
                continue;
            }
            $GLOBALS["_$global"] = $this->evaluateSuperGlobal($this->superGlobals[$global]);
        }

        return $this;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function evaluateSuperGlobal($data): mixed
    {
        if (!is_array($data)) {
            return $this->processSingleValue($data);
        }

        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->evaluateSuperGlobal($value);
                continue;
            }

            if (Variable::getInstance()->isJson($value)) {
                $result[$key] = $this->evaluateSuperGlobal(json_decode($value, true));
                continue;
            }

            $result[$key] = $this->processSingleValue($value);
        }

        return $result;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function processSingleValue(mixed $value): mixed
    {
        if (is_string($value)) {
            if ($this->isStringBoolean($value)) {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            if (is_numeric($value)) {
                return $this->convertNumericString($value);
            }

            return self::sanitizeInput($value);
        }

        return $value;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isStringBoolean(string $value): bool
    {
        return in_array(strtolower($value), self::BOOLEAN_STRINGS, true);
    }

    /**
     * @param string $value
     * @return int|float|string
     */
    private function convertNumericString(string $value): int|float|string
    {
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int)$value;
        }

        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
            return (float)$value;
        }

        return $value;
    }

    /**
     * @param mixed $input
     * @return string
     */
    public static function sanitizeInput(mixed $input): string
    {
        return trim(
            htmlspecialchars(
                htmlentities(
                    addslashes(
                        strip_tags((string)$input)
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ),
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }

    /**
     * @param string $location
     * @return void
     */
    #[NoReturn]
    public function redirect(string $location): void
    {
        header("Location: $location");
        exit;
    }

    /**
     * @param string $location
     * @return void
     */
    #[NoReturn]
    public function redirectToUri(string $location): void
    {
        header("Content-Type: application/json");
        echo json_encode(['nav' => $location]);
        exit;
    }

    /**
     * @return void
     */
    public function ContentType(): void
    {
        $_SERVER["CONTENT_TYPE"] = $this->getServer("CONTENT_TYPE") ? trim($this->getServer("CONTENT_TYPE")) : null;
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getServer(?string $key = null): mixed
    {
        return $this->getGlobalValue('SERVER', $key);
    }

    /**
     * @param string $global
     * @param string|null $key
     * @return mixed
     */
    private function getGlobalValue(string $global, ?string $key): mixed
    {
        if ($key === null) {
            return $GLOBALS["_$global"] ?? [];
        }

        return $GLOBALS["_$global"][$key] ?? null;
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return strtolower($this->getServer('HTTP_X_REQUESTED_WITH') ?? '') === 'xmlhttprequest';
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getPost(?string $key = null): mixed
    {
        return $this->getGlobalValue('POST', $key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasPost(string $key): bool
    {
        return isset($this->superGlobals['POST'][$key]);
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getGet(?string $key = null): mixed
    {
        //ex_c($_GET);

        return $this->getGlobalValue('GET', $key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasGet(string $key): bool
    {
        return isset($this->superGlobals['GET'][$key]);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasServer(string $key): bool
    {
        return isset($this->superGlobals['SERVER'][$key]);
    }

    /**
     * @param string $node
     * @param array $params
     * @return string
     */
    public function buildUrl(string $node, array $params = []): string
    {
        $protocol = CONFIG->app->host->getProtocol();
        $domain   = CONFIG->app->host->getDomain();
        $baseUrl  = sprintf('%s://%s/%s', $protocol, $domain, $node);

        if (empty($params)) {
            return $baseUrl;
        }

        return $baseUrl.'/'.str_replace('&', '&', http_build_query($params, '', '&'));
    }
}