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

namespace Asset\Framework\Template\Form;

use Asset\Framework\Trait\SingletonTrait;
use InvalidArgumentException;

/**
 * Class that handles:
 *
 * @package Asset\Framework\View;
 */
class FormInput
{

    use SingletonTrait;

    // Create a text type input
    /*
    $inputArray = [
        'element' => 'input',          // HTML element type (input, select, textarea)
        'marker'=> '123456'            // Marker for the input point in dev
        'type' => 'text',              // Input type (text, password, email, etc.)
        'prop' => [                    // Element Properties
            'name' => 'username',
            'id' => 'user_input',
            'placeholder' => 'Enter username',
            'required' => true
        ],
        'value' => '',                 // Default input value
        'text' => 'Username',          // Text for label if necessary
        'css' => [                     // CSS Classes
            'form-control',
            'custom-input'
        ],
        'style' => [                   // Inline styles
            'width' => '100%',
            'margin-bottom' => '10px'
        ]
    ];
    */

    /**
     * @var array
     */
    private array $inputField = [];

    /**
     * @var int
     */
    private int $inputCount = 0;

    /**
     * @var array
     */
    private array $currentInput = [];

    /**
     * @param array $input
     * @return void
     */
    public function setInput(array $input): void
    {
        $this->validateInput($input);
        $this->currentInput = $input;
        $this->buildElement();
    }

    /**
     * @param array $input
     * @return void
     */
    private function validateInput(array $input): void
    {
        if (!isset($input['element'])) {
            throw new InvalidArgumentException('Element type is required');
        }

        if (!isset($input['marker'])) {
            throw new InvalidArgumentException('Element marker is required');
        }
    }

    /**
     * @return void
     */
    private function buildElement(): void
    {
        $html = "<{$this->currentInput['element']}";

        if (isset($this->currentInput['type'])) {
            $html .= " type=\"{$this->currentInput['type']}\"";
        }

        $cssClasses = $this->buildCssClasses();
        if ($cssClasses) {
            $html .= " class=\"$cssClasses\"";
        }

        $styles = $this->buildInlineStyles();
        if ($styles) {
            $html .= " style=\"$styles\"";
        }

        $html .= $this->buildProperties();

        if (isset($this->currentInput['value'])) {
            $html .= " value=\"{$this->currentInput['value']}\"";
        }

        $html .= $this->closeElement();

        $this->inputField[] = $html;
    }

    /**
     * @return string
     */
    private function buildCssClasses(): string
    {
        return isset($this->currentInput['css'])
            ? implode(' ', $this->currentInput['css'])
            : '';
    }

    /**
     * @return string
     */
    private function buildInlineStyles(): string
    {
        if (!isset($this->currentInput['style'])) {
            return '';
        }

        $styleArray = [];
        foreach ($this->currentInput['style'] as $property => $value) {
            $styleArray[] = "$property: $value";
        }

        return implode('; ', $styleArray);
    }

    /**
     * @return string
     */
    private function buildProperties(): string
    {
        if (!isset($this->currentInput['prop'])) {
            return '';
        }

        $properties = '';
        foreach ($this->currentInput['prop'] as $prop => $value) {
            $properties .= is_bool($value) && $value
                ? " $prop"
                : " $prop=\"$value\"";
        }

        return $properties;
    }

    /**
     * @return string
     */
    private function closeElement(): string
    {
        $selfClosingElements = ['input', 'img', 'br', 'hr'];

        if (in_array($this->currentInput['element'], $selfClosingElements)) {
            return '>';
        }

        return ">{$this->currentInput['value']}</{$this->currentInput['element']}>";
    }

    /**
     * @return array
     */
    public function getInputs(): array
    {
        return $this->inputField;
    }

    /**
     * @param string $marker
     * @return string
     */
    public function getInput(string $marker): string
    {
        return $this->inputField[$marker];
    }

    public function getInputCount(): int
    {
        return count($this->inputField) ?? 0;
    }

}