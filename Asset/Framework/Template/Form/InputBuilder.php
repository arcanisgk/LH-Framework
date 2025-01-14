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

/**
 * Class that handles:
 *
 * @package Asset\Framework\Template\Form;
 */
class InputBuilder
{

    use SingletonTrait;


    /**
     * InputBuilder constructor.
     */
    public function __construct()
    {

    }

    /**
     * Builds select options from an array
     *
     * @param array $options Array of options
     * @param array $config Configuration array with following possible keys:
     *                      - useKeyAsValue: bool - Use array key as option value
     *                      - useValueAsKey: bool - Use array value as option key
     *                      - selected: mixed - Value to mark as selected
     *                      - disabled: array - Array of values to mark as disabled
     * @return string HTML string of option elements
     */
    public function buildSelectOptions(array $options, array $config = []): string
    {
        $html = '';

        // Default config
        $defaults = [
            'useKeyAsValue' => false,
            'useValueAsKey' => false,
            'selected'      => null,
            'disabled'      => [],
        ];

        $config = array_merge($defaults, $config);

        foreach ($options as $key => $value) {
            $optionValue = $config['useKeyAsValue'] ? $key : $value;
            $optionKey   = $config['useValueAsKey'] ? $value : $key;

            $attributes = [];

            // Handle selected state
            if ($config['selected'] !== null) {
                if ((is_array($config['selected']) && in_array($optionValue, $config['selected'], true)) ||
                    (!is_array($config['selected']) && $optionValue == $config['selected'])) {
                    $attributes[] = 'selected';
                }
            }

            // Handle disabled state
            if (!empty($config['disabled'])) {
                if ((is_array($config['disabled']) && in_array($optionValue, $config['disabled'], true)) ||
                    (!is_array($config['disabled']) && $optionValue == $config['disabled'])) {
                    $attributes[] = 'disabled';
                }
            }

            $attributeString = !empty($attributes) ? ' '.implode(' ', $attributes) : '';

            // Escape values for HTML output
            $escapedValue   = htmlspecialchars((string)$optionValue, ENT_QUOTES, 'UTF-8');
            $escapedDisplay = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');

            $html .= sprintf(
                '<option value="%s" %s >%s</option>',
                $escapedValue,
                $attributeString,
                $escapedDisplay
            );
        }

        return $html;
    }

    /**
     * Builds a button element with Bootstrap classes and custom attributes
     *
     * @param array $config Configuration array with following possible keys:
     *                      - text: string - Button text content
     *                      - type: string - Button type (button, submit, reset)
     *                      - class: string - Additional CSS classes
     *                      - size: string - Bootstrap button size (lg, sm, xs)
     *                      - variant: string - Bootstrap variant (primary, secondary, success, etc)
     *                      - attributes: array - Additional HTML attributes
     *                      - dataAttributes: array - Data attributes as key-value pairs
     * @return string HTML button element
     */
    public function buildButton(array $config = []): string
    {
        // Default configuration
        $defaults = [
            'text'           => '',
            'type'           => 'button',
            'class'          => '',
            'size'           => '',
            'variant'        => 'primary',
            'attributes'     => [],
            'dataAttributes' => [],
        ];

        $config = array_merge($defaults, $config);

        // Build CSS classes
        $classes   = ['btn'];
        $classes[] = 'btn-'.$config['variant'];

        if ($config['size']) {
            $classes[] = 'btn-'.$config['size'];
        }

        if ($config['class']) {
            $classes[] = $config['class'];
        }

        // Build attributes string
        $attributes   = [];
        $attributes[] = 'type="'.htmlspecialchars($config['type'], ENT_QUOTES, 'UTF-8').'"';
        $attributes[] = 'class="'.htmlspecialchars(implode(' ', $classes), ENT_QUOTES, 'UTF-8').'"';

        // Add custom attributes
        foreach ($config['attributes'] as $attr => $value) {
            $attributes[] = htmlspecialchars($attr, ENT_QUOTES, 'UTF-8').'="'.
                htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"';
        }

        // Add data attributes
        foreach ($config['dataAttributes'] as $key => $value) {
            $dataKey      = 'data-'.$key;
            $attributes[] = htmlspecialchars($dataKey, ENT_QUOTES, 'UTF-8').'="'.
                htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"';
        }

        // Build button HTML
        return sprintf(
            '<button %s >%s</button>',
            implode(' ', $attributes),
            htmlspecialchars($config['text'], ENT_QUOTES, 'UTF-8')
        );
    }
}