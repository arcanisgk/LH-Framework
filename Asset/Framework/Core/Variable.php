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

namespace Asset\Framework\Core;

use Asset\Framework\Trait\SingletonTrait;
use JsonException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Variable
{
    use SingletonTrait;

    /**
     * Converts all array keys to uppercase recursively
     *
     * @param array<string|int, mixed> $array
     * @return array<string, mixed>
     */
    public static function getArrayWithUpperCaseKey(array $array): array
    {
        return array_combine(
            array_map('strtoupper', array_keys($array)),
            array_map(
                fn($value) => is_array($value) ? self::getArrayWithUpperCaseKey($value) : $value,
                $array
            )
        );
    }

    /**
     * Finds a key in a multidimensional array and returns its value
     *
     * @param array|object $array
     * @param string|int $searchKey
     * @return array{key: string|int, value: mixed}|null
     */
    public static function findKeyInArray(array|object $array, string|int $searchKey): ?array
    {
        $data      = is_object($array) ? (array)$array : $array;
        $iterator  = new RecursiveArrayIterator($data);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($recursive as $key => $value) {
            if ($key === $searchKey) {
                return ['key' => $key, 'value' => $value];
            }
        }

        return null;
    }

    /**
     * Generates a random string based on given options
     *
     * @param array{length?: positive-int, characters?: string}|null $options
     * @return string
     */
    public static function getRandomString(?array $options = null): string
    {
        $length = $options['length'] ?? 10;
        $chars  = $options['characters'] ?? '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($chars, (int)ceil($length / strlen($chars)))), 0, $length);
    }

    /**
     * Validates if a string is valid JSON
     *
     * @param string $var
     * @return bool
     */
    public function isJson(string $var): bool
    {
        try {
            json_decode($var, false, 512, JSON_THROW_ON_ERROR);

            return true;
        } catch (JsonException) {
            return false;
        }
    }
}