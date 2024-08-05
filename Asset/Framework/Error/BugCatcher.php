<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Requiered).
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

namespace Asset\Framework\Error;

use Closure;
use JetBrains\PhpStorm\NoReturn;
use Throwable;
use Asset\Framework\ToolBox\DrawBoxCLI;

/**
 * Class that handles capturing and displaying errors in the application.
 *
 * @package Asset\Helper\DevTool\Error
 */
class BugCatcher
{
    /**
     * Directory separator based on the operating system.
     *
     * @var string
     */
    private const string DS = DIRECTORY_SEPARATOR;

    /**
     * @var BugCatcher|null Unique instance of the BugCatcher class.
     */
    private static ?self $instance = null;

    /**
     * Get a unique instance of BugCatcher.
     *
     * @return BugCatcher Unique instance of BugCatcher.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Line separator used in the application.
     *
     * @var string
     */
    private string $lineSeparator;

    /**
     * Determine if errors should be displayed on screen.
     *
     * @var bool
     */
    private bool $displayError;

    /**
     * Constructor for the BugCatcher class. Configures error and exception handling.
     */
    public function __construct()
    {
        $this
            ->setLineSeparator($this->detectLineSeparator())
            ->setDisplayError($this->isDisplayErrors());
        register_shutdown_function([$this, "shutdownHandler"]);
        set_exception_handler([$this, "exceptionHandler"]);
        set_error_handler([$this, "errorHandler"]);
    }

    /**
     * Get the line separator used in the application.
     *
     * @return string The line separator.
     */
    public function getLineSeparator(): string
    {
        return $this->lineSeparator;
    }

    /**
     * Set the line separator used in the application.
     *
     * @param string $lineSeparator The line separator.
     *
     * @return BugCatcher This BugCatcher instance.
     */
    public function setLineSeparator(string $lineSeparator): self
    {
        $this->lineSeparator = $lineSeparator;

        return $this;
    }

    /**
     * Get whether errors should be displayed on screen.
     *
     * @return bool True if errors should be displayed, false otherwise.
     */
    public function getDisplayError(): bool
    {
        return $this->displayError;
    }

    /**
     * Set whether errors should be displayed on screen.
     *
     * @param bool $displayError True to display errors, false otherwise.
     *
     * @return BugCatcher This BugCatcher instance.
     */
    public function setDisplayError(bool $displayError): self
    {
        $this->displayError = $displayError;

        return $this;
    }

    /**
     * Check if the application is running in a command-line environment.
     *
     * @return bool True if running in CLI, false otherwise.
     */
    private function isCLI(): bool
    {
        return defined('STDIN')
            || php_sapi_name() === "cli"
            || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
            || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);
    }

    /**
     * Detect the appropriate line separator based on the environment (CLI or browser).
     *
     * @return string The line separator.
     */
    private function detectLineSeparator(): string
    {
        return ($this->isCLI()) ? PHP_EOL : '<br>';
    }

    /**
     * Check if errors should be displayed on screen.
     *
     * @return bool True if errors should be displayed, false otherwise.
     */
    private function isDisplayErrors(): bool
    {
        return in_array(ini_get('display_errors'), [1, 'On', 'on']);
    }

    /**
     * Shutdown handler. Captures errors at the end of execution.
     */
    public function shutdownHandler(): void
    {
        $error = error_get_last();
        if (!is_null($error)) {
            $this->cleanOutput();
            $trace = array_reverse(debug_backtrace());
            array_pop($trace);
            if (!strpos('Stack trace:', $error['message'])) {
                $errorClean       = explode('Stack trace:', $error['message']);
                $error['message'] = $errorClean[0];
            }
            $errorArray              = [
                'class'       => 'ShutdownHandler',
                'type'        => $error['type'],
                'description' => $error['message'],
                'file'        => $error['file'],
                'line'        => $error['line'],
                'trace'       => $trace,
            ];
            $errorArray['trace_msg'] = $this->getBacktrace($errorArray);
            $this->output($errorArray);
        }
    }

    /**
     * Exception handler. Captures and handles exceptions thrown in the application.
     *
     * @param Throwable $e The captured exception.
     */
    #[NoReturn] public function exceptionHandler(Throwable $e): void
    {
        $this->cleanOutput();
        $errorArray              = [
            'class'       => 'ExceptionHandler',
            'type'        => ($e->getCode() == 0 ? 'Not Set' : $e->getCode()),
            'description' => $e->getMessage(),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'trace'       => $e->getTrace(),
        ];
        $errorArray['trace_msg'] = $this->getBacktrace($errorArray);
        $this->output($errorArray);
    }

    /**
     * Error handler. Captures and handles errors generated in the application.
     *
     * @param mixed|null $errorLevel Error level.
     * @param mixed|null $errorDesc Error description.
     * @param mixed|null $errorFile File where the error occurred.
     * @param mixed|null $errorLine Line where the error occurred.
     */
    #[NoReturn] public function errorHandler(
        mixed $errorLevel = null,
        mixed $errorDesc = null,
        mixed $errorFile = null,
        mixed $errorLine = null,
    ): void {
        $this->cleanOutput();
        $trace = array_reverse(debug_backtrace());
        array_pop($trace);
        $trace                   = array_reverse($trace);
        $errorArray              = [
            'class'       => 'ErrorHandler',
            'type'        => $errorLevel,
            'description' => $errorDesc,
            'file'        => $errorFile,
            'line'        => $errorLine,
            'trace'       => $trace,
        ];
        $errorArray['trace_msg'] = $this->getBacktrace($errorArray);
        $this->output($errorArray);
    }

    /**
     * Clean the output buffer if active.
     */
    private function cleanOutput(): void
    {
        if (ob_get_contents() || ob_get_length()) {
            ob_end_clean();
            flush();
        }
    }

    /**
     * Generate a backtrace message from error information.
     *
     * @param array $errorArray Error information.
     *
     * @return string The backtrace message.
     */
    private function getBacktrace(array $errorArray): string
    {
        $backtraceMessage = [];

        if (!empty($errorArray['trace'])) {
            foreach ($errorArray['trace'] as $track) {
                $args = '';
                if (isset($track['args']) && !empty($track['args'])) {
                    $args = $this->formatArguments($track['args']);
                }

                $route              = $this->getRouteDescription($track);
                $backtraceMessage[] = sprintf('%s%s(%s)', $route, $track['function'], $args);
            }
        } else {
            $backtraceMessage[] = sprintf('No backtrace data in the %s.', $errorArray['class']);
        }

        return implode($this->getLineSeparator(), $backtraceMessage);
    }

    /**
     * Format arguments for display.
     *
     * @param array $args Arguments array.
     *
     * @return string Formatted arguments.
     */
    private function formatArguments(array $args): string
    {
        $formattedArgs = [];

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $formattedArgs[] = 'Array';
            } elseif (is_object($arg)) {
                if ($arg instanceof Closure) {
                    $formattedArgs[] = 'Closure';
                } else {
                    $formattedArgs[] = get_class($arg);
                }
            } else {
                $formattedArgs[] = is_string($arg) ? "'".$arg."'" : (string)$arg;
            }
        }

        return implode(',', $formattedArgs);
    }

    /**
     * Get description of the route (file and line) or magic call method.
     *
     * @param array $track Stack trace information.
     *
     * @return string Route description.
     */
    private function getRouteDescription(array $track): string
    {
        if (!isset($track['file']) && !isset($track['line'])) {
            return sprintf('Magic Call Method: (%s)->', $track['class']);
        }

        return sprintf('%s %s calling Method: ', $track['file'], $track['line']);
    }

    /**
     * Generate and display the output corresponding to the error.
     *
     * @param array $errorArray Error information.
     */
    #[NoReturn] private function output(array $errorArray): void
    {
        $errorArray['micro_time'] = $this->toLog($errorArray);
        if ($this->isCLI()) {
            echo $this->getCLIOutput($errorArray);
        } else {
            echo $this->getWebOutput($errorArray);
        }
        $this->clearLastError();
    }


    /**
     * Generate output for CLI environment.
     *
     * @param array $errorArray
     *
     * @return string The generated output.
     */
    private function getCLIOutput(array $errorArray): string
    {
        $output = '';
        $nl     = $this->getLineSeparator();
        if ($this->getDisplayError()) {
            $output .= "Class: {$errorArray['class']}".$nl
                ."Description:".$nl."{$errorArray['description']}".$nl.$nl
                ."File: {$errorArray['file']}".$nl
                ."Line: {$errorArray['line']}".' '."Type: {$errorArray['type']}".' '."Time: {$errorArray['micro_time']}".$nl.$nl
                ."Backtrace:".$nl."{$errorArray['trace_msg']}".$nl
                ."Development by: W. Nunez";
        } else {
            $output .= "Micro Time: {$errorArray['micro_time']}";
        }
        //require_once 'DrawBoxCLI.php';
        $drawBox = DrawBoxCLI::getInstance();

        return $drawBox->drawBoxes($output, 1, 1, true, 0, 2);
    }

    /**
     * Get the path to the error template file based on whether a handler is available.
     *
     * @param bool $hasHandler True if a handler is available, false otherwise.
     *
     * @return string The path to the error template file.
     */
    private function getErrorTemplatePath(bool $hasHandler): string
    {
        $templateFolder = __DIR__.self::DS.'template'.self::DS;
        $templateFile   = $hasHandler ? 'handler_error.php' : 'no_handler_error.php';

        return $templateFolder.$templateFile;
    }

    /**
     * Generate output for web environment.
     *
     * @param array $errorArray Error information.
     *
     * @return string The generated output.
     */
    private function getWebOutput(array $errorArray): string
    {
        $errorSkin   = $this->getErrorTemplatePath($this->getDisplayError());
        $fileContent = file_get_contents($errorArray['file']);
        $lines       = explode("\n", $fileContent);

        if (isset($lines[$errorArray['line'] - 1])) {
            $lines[$errorArray['line'] - 1] .= '    // error detected in this line!!!';
        }

        $source = implode("\n", $lines);
        $source = highlight_string($source, true);

        ob_start();
        require_once $errorSkin;

        return ob_get_clean();
    }

    /**
     * Log the error to the log file and return the timestamp.
     *
     * @param array $errorArray Error information.
     *
     * @return int The log timestamp.
     */
    private function toLog(array $errorArray): int
    {
        $description = preg_replace("/(\r\n|\n|\r|\t|<br>)/", '', $errorArray['description']);
        $microTime   = time();
        $smgError    = $microTime.' '.date('Y-m-d H:i:s').' '.$description.PHP_EOL;
        $logPath     = dirname(__FILE__)
            .self::DS.'..'
            .self::DS.'..'
            .self::DS.'resource'
            .self::DS.'log'
            .self::DS.'error_log.log';
        error_log($smgError, 3, $logPath);

        return $microTime;
    }

    /**
     * Clear the last recorded error and exit execution.
     */
    #[NoReturn] private function clearLastError(): void
    {
        error_clear_last();
        exit();
    }
}

BugCatcher::getInstance();
ob_start();