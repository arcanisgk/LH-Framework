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

use Asset\Framework\Trait\SingletonTrait;
use Random\RandomException;

/**
 * Class that handles:
 *
 * @package Asset\Framework\Core;
 */
class SecurityPolicies
{

    use SingletonTrait;

    private const int NONCE_LENGTH = 32;

    private const array CSP_DIRECTIVES
        = [
            'default-src'     => "'self'",
            'script-src'      => "'self' 'unsafe-inline'",
            'style-src'       => "'self' 'unsafe-inline' https://fonts.googleapis.com",
            'img-src'         => "'self' data: https:",
            'font-src'        => "'self' https://fonts.gstatic.com",
            'object-src'      => "'none'",
            'base-uri'        => "'self'",
            'form-action'     => "'self'",
            'frame-ancestors' => "'none'",
            'connect-src'     => "'self'",
            'media-src'       => "'self'",
            'worker-src'      => "'self'",
            'manifest-src'    => "'self'",
        ];

    private const array SECURITY_HEADERS
        = [
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
            'X-Content-Type-Options'    => 'nosniff',
            'X-Frame-Options'           => 'DENY',
            'X-XSS-Protection'          => '1; mode=block',
            'Referrer-Policy'           => 'strict-origin-when-cross-origin',
            'Permissions-Policy'        => 'geolocation=(), microphone=(), camera=()',
        ];

    /**
     * @var string
     */
    private static string $nonce = '';

    /**
     * @var string
     */
    private static string $contentSecurityPolicy = '';

    /**
     * @return string
     */
    public static function getNonce(): string
    {
        return self::$nonce;
    }

    /**
     * @return string
     */
    public static function getCSP(): string
    {
        return self::$contentSecurityPolicy;
    }

    /**
     * @throws RandomException
     */
    public static function initSecurity(): self
    {
        self::generateNonce();
        self::buildCSP();
        self::setSecurityHeaders();

        return self::getInstance();
    }

    /**
     * @throws RandomException
     */
    private static function generateNonce(): void
    {
        self::$nonce = bin2hex(random_bytes(self::NONCE_LENGTH));
    }

    /**
     * @return void
     */
    private static function buildCSP(): void
    {
        $directives = self::CSP_DIRECTIVES;

        $directives['script-src'] .= " 'nonce-".self::$nonce."'";
        $directives['style-src']  .= " 'nonce-".self::$nonce."'";

        $cspParts = [];
        foreach ($directives as $directive => $value) {
            $cspParts[] = "$directive $value";
        }

        self::$contentSecurityPolicy = implode('; ', $cspParts).'; '.
            'block-all-mixed-content; '.
            'upgrade-insecure-requests;';
    }

    /**
     * @return void
     */
    private static function setSecurityHeaders(): void
    {
        foreach (self::SECURITY_HEADERS as $header => $value) {
            header("$header: $value");
        }
        header("Content-Security-Policy: ".self::$contentSecurityPolicy);
    }

    /**
     * @return void
     */
    public static function exposeHumanitariaPorpuse(): void
    {
        foreach (self::getHumanitarianHeaders('web') as $header) {
            header($header);
        }
    }

    /**
     * @param string $purpose
     * @return array
     */
    public static function getHumanitarianHeaders(string $purpose): array
    {
        if (!CONFIG->app->host->getHumanitarian()) {
            return [];
        }

        $domain  = CONFIG->app->host->getProtocol().'://'.CONFIG->app->host->getDomain();
        $contact = CONFIG->mail->mail1->getMailPostmaster();

        $headers = [
            'X-Humanitarian-Protection' => 'This site is protected by international humanitarian law.',
            'X-Humanitarian-Purpose'    => 'Non-commercial',
            'X-Humanitarian-Licenced'   => $domain.'/',
            'X-Humanitarian-Contact'    => $contact,
        ];

        return match ($purpose) {
            'web' => array_map(
                fn($key, $value) => "$key: $value",
                array_keys($headers),
                $headers
            ),
            'mail' => $headers,
            default => []
        };
    }
}