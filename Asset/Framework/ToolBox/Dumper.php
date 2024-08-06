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

use ReflectionObject;

/**
 * Class that handles:
 *
 * @package Asset\Framework\ToolBox;
 */
class Dumper
{

    /**
     * @var Dumper|null Singleton instance of the class: Dumper.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of teh class Dumper.
     *
     * @return Dumper The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Dumper constructor.
     */
    public function __construct()
    {

    }

    /**
     * Entry Point to Dumper (dump) output of data
     * @param array $param
     * @return string|null
     */
    public static function dump(array $param): ?string
    {
        $stuff = (IS_CLI) ?
            [
                'salE' => '',
                'salC' => '',
                'nl'   => PHP_EOL,
            ] : [
                'salE' => '<pre>',
                'salC' => '</pre>',
                'nl'   => '<br>',
            ];

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller    = $backtrace[1];
        $fileInfo  = "[{$caller['file']}:{$caller['line']}]";
        $output    = 'Output Variable: '.$stuff['nl'].$fileInfo.$stuff['nl'];

        foreach ($param['data'] as $arg) {
            $output .= self::format($arg).$stuff['nl'];
        }

        if (IS_CLI) {

            $drawBox = DrawBoxCLI::getInstance();

            echo $drawBox->drawBoxes($output, 2, 0, true, 0, 4);

        } else {
            echo $stuff['salE'].$output.$stuff['salC'];
        }

        return null;
    }


    /**
     * Format the variable for output.
     *
     * @param mixed $var
     * @return string
     */
    private static function format(mixed $var): string
    {
        return self::export($var);
    }

    /**
     * Recursively export the variable with custom formatting.
     *
     * @param mixed $var
     * @param int $indentLevel
     * @return string
     */
    private static function export(mixed $var, int $indentLevel = 0): string
    {
        $indent = str_repeat('  ', $indentLevel);

        switch (gettype($var)) {
            case 'NULL':
                return self::highlight('null', 'null');
            case 'boolean':
                return self::highlight($var ? 'true' : 'false', 'boolean');
            case 'double':
            case 'integer':
                return self::highlight((string)$var, 'number');
            case 'string':
                return self::highlight('"'.addslashes($var).'"', 'string');
            case 'array':
                if (empty($var)) {
                    return "[]";
                } else {
                    $output = "[\n";
                    foreach ($var as $key => $value) {
                        $output .= $indent.'  '.self::export($key).' => '.self::export($value, $indentLevel + 1).",\n";
                    }
                    $output .= $indent.']';

                    return $output;
                }
            case 'object':
                if ($indentLevel > 5) {
                    return get_class($var).' {...}';
                }
                $reflection = new ReflectionObject($var);
                $properties = $reflection->getProperties();
                if (empty($properties)) {
                    $output = get_class($var)." {}";
                } else {
                    $output = get_class($var)." {\n";
                    foreach ($properties as $property) {
                        $property->setAccessible(true);
                        $name   = $property->getName();
                        $value  = $property->getValue($var);
                        $output .= $indent.'  '.self::highlight($name, 'property').': '.self::export(
                                $value,
                                $indentLevel + 1
                            )."\n";
                    }
                    $output .= $indent.'}';
                }

                return $output;
            case 'resource':
                return self::highlight('resource', 'resource');
            default:
                return self::highlight('unknown type', 'unknown');
        }
    }

    private static function highlight(string $text, string $type): string
    {
        if (IS_CLI) {
            $styles = [
                'null'     => "\033[0;35m", // Magenta
                'boolean'  => "\033[0;33m", // Yellow
                'number'   => "\033[0;32m", // Green
                'string'   => "\033[0;34m", // Blue
                'property' => "\033[0;36m", // Cyan
                'resource' => "\033[0;31m", // Red
                'unknown'  => "\033[1;37m", // White
                'reset'    => "\033[0m", // Reset
            ];

            return $styles[$type].$text.$styles['reset'];
        } else {
            $styles = [
                'null'     => 'color: #c71585;',  // Rosa fuerte
                'boolean'  => 'color: #e6193c;',  // Rojo (red)
                'number'   => 'color: #407ee7;',  // Azul (blue)
                'string'   => 'color: #29a329;',  // Verde (green)
                'property' => 'color: #1999b3;',  // Cian (cyan)
                'resource' => 'color: #3d62f5;',  // Azul (blue)
                'unknown'  => 'color: #72898f;',  // Gris azulado (base0)
            ];

            return '<span style="'.$styles[$type].'">'.htmlspecialchars($text).'</span>';
        }
    }
}