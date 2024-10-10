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

namespace Asset\Framework\Core;

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core;
 */
class SecurityPolicies
{

    /**
     * @var SecurityPolicies|null Singleton instance of the class: SecurityPolicies.
     */
    private static ?self $instance = null;


    /**
     * Get the singleton instance of teh class SecurityPolicies.
     *
     * @return SecurityPolicies The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * SecurityPolicies constructor.
     */
    public function __construct()
    {

    }

    private static string $once = '';
    private static string $scp = '';

    public static function getNonce(): string
    {
        return self::$once;
    }

    public static function getScp(): string
    {
        return self::$scp;
    }

    public static function initSecurity(): self
    {
        self::$once = bin2hex(random_bytes(16));

        self::$scp = "default-src 'self'; ".
            "script-src 'self' 'nonce-".self::$once."'; ".
            "style-src 'self' 'nonce-".self::$once."'; ".
            "img-src 'self' data: https:; ".
            "font-src 'self'; ".
            "object-src 'none'; ".
            "base-uri 'self'; ".
            "form-action 'self'; ".
            "frame-ancestors 'none'; ".
            "block-all-mixed-content; ".
            "upgrade-insecure-requests;";

        return self::getInstance();
    }

}