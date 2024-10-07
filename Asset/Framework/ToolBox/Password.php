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

namespace Asset\Framework\ToolBox;

/**
 * Class that handles:
 *
 * @package Asset\Framework\ToolBox;
 */
class Password
{

    /**
     * @var Password|null Singleton instance of the class: Password.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class Password.
     *
     * @return Password The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Password constructor.
     */
    public function __construct()
    {

    }

    private array $criteria = ['length', 'uppercase', 'lowercase', 'number', 'special'];
    private int $minLength = 13;
    private string $specialChars = '!@#%*';

    private const array ERROR_TOKENS
        = [
            'length'    => '{{register-password-smg-1}}',
            'uppercase' => '{{register-password-smg-2}}',
            'lowercase' => '{{register-password-smg-3}}',
            'number'    => '{{register-password-smg-4}}',
            'special'   => '{{register-password-smg-5}}',
        ];

    public function setCriteria(array $criteriaList): self
    {
        $this->criteria = $criteriaList;

        return $this;
    }

    public function setMinLength(int $length): self
    {
        $this->minLength = $length;

        return $this;
    }

    public function setSpecialChars(string $chars): self
    {
        $this->specialChars = $chars;

        return $this;
    }

    public function check(string $password): array
    {
        $errors = [];
        $checks = [
            'lowercase' => '[a-z]',
            'uppercase' => '[A-Z]',
            'number'    => '[0-9]',
            'special'   => '['.preg_quote($this->specialChars, '/').']',
        ];

        if (in_array('length', $this->criteria) && strlen($password) < $this->minLength) {
            $errors[] = self::ERROR_TOKENS['length'];
        }

        foreach ($this->criteria as $criterion) {
            if (isset($checks[$criterion])) {
                if (!preg_match('/'.$checks[$criterion].'/', $password)) {
                    $errors[] = self::ERROR_TOKENS[$criterion];
                }
            }
        }

        return $errors;
    }

    public static function quickCheck(string $password): array
    {
        return self::getInstance()->check($password);
    }
}