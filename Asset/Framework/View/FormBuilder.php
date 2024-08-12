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
class FormBuilder
{

    /**
     * @var FormBuilder|null Singleton instance of the class: FormBuilder.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class FormBuilder.
     *
     * @return FormBuilder The singleton instance.
     */
    public static function getInstance($action, $method, $formClasses = ''): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($action, $method, $formClasses);
        }

        return self::$instance;
    }

    /**
     * @var array
     */
    private array $form = [];

    /**
     * FormBuilder constructor.
     */
    public function __construct($action, $method = 'POST', $formClasses = '')
    {
        $this->form[] = "<form action='$action' method='$method' class='$formClasses'>";

    }

    /**
     * @param string $type
     * @param string $name
     * @param string $label
     * @param string $value
     * @param string $inputClasses
     * @param string $labelClasses
     * @param string $colClasses
     * @param array $attributes
     * @return void
     */
    public function addInput(
        string $type,
        string $name,
        string $label,
        string $value = '',
        string $inputClasses = '',
        string $labelClasses = '',
        string $colClasses = '',
        array $attributes = []
    ): void {
        $attrString   = $this->parseAttributes($attributes);
        $this->form[] = "<div class='$colClasses'>";
        $this->form[] = "<label for='$name' class='$labelClasses'>$label</label>";
        $this->form[] = "<input type='$type' name='$name' id='$name' value='$value' class='form-control $inputClasses' $attrString>";
        $this->form[] = "</div>";
    }

    /**
     * @param string $name
     * @param string $label
     * @param array $options
     * @param string|null $selected
     * @param string $selectClasses
     * @param string $labelClasses
     * @param string $colClasses
     * @param array $attributes
     * @return void
     */
    public function addSelect(
        string $name,
        string $label,
        array $options = [],
        string $selected = null,
        string $selectClasses = '',
        string $labelClasses = '',
        string $colClasses = '',
        array $attributes = []
    ): void {
        $attrString   = $this->parseAttributes($attributes);
        $this->form[] = "<div class='$colClasses'>";
        $this->form[] = "<label for='$name' class='$labelClasses'>$label</label>";
        $this->form[] = "<select name='$name' id='$name' class='form-select $selectClasses' $attrString>";
        foreach ($options as $value => $text) {
            $isSelected   = ($value === $selected) ? 'selected' : '';
            $this->form[] = "<option value='$value' $isSelected>$text</option>";
        }
        $this->form[] = "</select>";
        $this->form[] = "</div>";
    }

    /**
     * @param string $type
     * @param string $text
     * @param string $buttonClasses
     * @param string $colClasses
     * @param array $attributes
     * @return FormBuilder
     */
    public function addButton(
        string $type,
        string $text,
        string $buttonClasses = '',
        string $colClasses = '',
        array $attributes = []
    ): self {
        $attrString   = $this->parseAttributes($attributes);
        $this->form[] = "<div class='$colClasses'>";
        $this->form[] = "<button type='$type' class='btn $buttonClasses' $attrString>$text</button>";
        $this->form[] = "</div>";

        return $this;
    }

    /**
     * @param string $rowClasses
     * @return FormBuilder
     */
    public function addRowStart(string $rowClasses = ''): self
    {
        $this->form[] = "<div class='row $rowClasses'>";

        return $this;
    }

    /**
     * @return void
     */
    public function addRowEnd(): void
    {
        $this->form[] = "</div>";
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $this->form[] = "</form>";

        return implode('\n', $this->form);
    }

    /**
     * @param array $attributes
     * @return string
     */
    private function parseAttributes(array $attributes): string
    {
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString = "$key='$value' ";
        }

        return trim($attrString);
    }
}