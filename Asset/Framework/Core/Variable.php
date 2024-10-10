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

class Variable
{
    private static ?self $instance = null;

    /**
     * @return Variable
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $var
     *
     * @return bool
     */
    public function isJson(string $var): bool
    {
        return json_decode($var) != null;
    }

    /**
     * Converts all keys in an array to uppercase.
     *
     * @param mixed $array
     * @return array Array with all keys in uppercase.
     */
    public static function getArrayWithUpperCaseKey(mixed $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $uppercaseKey          = strtoupper($key);
            $result[$uppercaseKey] = is_array($value) ? self::getArrayWithUpperCaseKey($value) : $value;
        }

        return $result;
    }

    /**
     * @param $array
     * @param $searchKey
     * @return array|null
     */
    public static function findKeyInArray($array, $searchKey): ?array
    {
        foreach ($array as $key => $value) {
            if ($key === $searchKey) {
                return ['key' => $key, 'value' => $value];
            }
            if (is_array($value)) {
                $result = self::findKeyInArray($value, $searchKey);
            }
            if (!empty($result)) {
                return $result;
            }
        }

        return null;
    }

    public static function getRandomString(mixed $options = null): string
    {
        $length     = $options['length'] ?? 10;
        $characters = $options['characters'] ?? '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($characters, (int)ceil($length / strlen($characters)))), 0, $length);
    }
}