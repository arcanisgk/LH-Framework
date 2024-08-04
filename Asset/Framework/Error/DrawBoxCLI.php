<?php

declare(strict_types=1);

namespace Asset\Helper\Error;

use Asset\Framework\Core\ArgumentLoader;

class DrawBoxCLI
{
    /**
     * @var DrawBoxCLI|null
     */
    private static ?self $instance = null;

    /**
     *
     */
    public function __construct()
    {
        self::setTermWidth($this->detectTermWidth());
    }

    /**
     * @var int
     */
    private static int $termWidth = 80;

    /**
     * @return int
     */
    public static function getTermWidth(): int
    {
        return self::$termWidth;
    }

    /**
     * @param  int  $termWidth
     */
    public static function setTermWidth(int $termWidth): void
    {
        self::$termWidth = $termWidth;
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param  array|string  $source
     * @param  int           $nlHeader
     * @param  int           $nlFooter
     * @param  bool          $highlight
     * @param  int           $limitLen
     * @param  int           $typeOutput
     * @param  bool          $error
     *
     * @return string
     */
    public function drawBoxes(
        array|string $source,
        int          $nlHeader = 0,
        int          $nlFooter = 0,
        bool         $highlight = false,
        int          $limitLen = 0,
        int          $typeOutput = 0,
        bool         $error = false,
    ): string {
        $arguments   = ArgumentLoader::getArguments();
        $source      = (is_array($source) ? $source : preg_split('/\r\n|\r|\n/', rtrim($source)));
        $isCli       = defined('STDIN')
            || php_sapi_name() === "cli"
            || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
            || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);
        $boxChars    = [
            'tl' => '╔',
            'tr' => '╗',
            'bl' => '╚',
            'br' => '╝',
            'v'  => '║',
            'h'  => '═',
            'hs' => '─',
            'ls' => '╟',
            'rs' => '╢',
        ];
        $colorScheme = ['c' => '', 'r' => ''];
        switch ($typeOutput) {
            case 1: //output message
                $colorScheme['c'] = '[1;42m';
                $colorScheme['r'] = '[0m';
                break;
            case 2: //danger
                $colorScheme['c'] = '[1;41m';
                $colorScheme['r'] = '[0m';
                break;
        }
        $cliColors = [
            'hf'    => ($highlight && $isCli && $nlHeader !== 0) ? "\033{$colorScheme['c']}" : '',
            'reset' => ($highlight && $isCli) ? "\033{$colorScheme['r']}" : '',
        ];
        $lenS      = max(
            array_map(function ($el) {
                return mb_strlen(preg_replace('/\e[[^A-Za-z]*[A-Za-z]/', '', $el));
            }, $source),
        );
        $termWidth = self::getTermWidth();
        $longest   = ($lenS > $limitLen) ? $lenS : $limitLen;
        if ($limitLen == 0) {
            $longest = $termWidth;
        }
        if ($limitLen == 0 || $this->checkIsFit($longest, $limitLen) || isset($arguments['f'])) {
            $printArea = $longest - 2;
            $result    = '';
            $nLines    = count($source);
            $i         = 0;
            $fTop      = $boxChars['tl'] . str_repeat($boxChars['h'], $printArea) . $boxChars['tr'];
            $fBottom   = $boxChars['bl'] . str_repeat($boxChars['h'], $printArea) . $boxChars['br'];
            $lineTxt   = [];
            $lineTxt[] = $cliColors['hf'] . $fTop . $cliColors['reset'] . PHP_EOL;
            $start     = true;
            foreach ($source as $key => $line) {
                $i++;
                if ($nlHeader > 0 && $i <= $nlHeader) {
                    $line      = str_pad($line, $printArea, ' ', STR_PAD_BOTH);
                    $lineTxt[] = $cliColors['hf'] . $boxChars['v'] . $line . $boxChars['v'] . $cliColors['reset'] . PHP_EOL;
                    if ($i == $nlHeader) {
                        $lineTxt[] = $cliColors['hf'] . $boxChars['ls'] .
                            str_repeat($boxChars['hs'], $printArea) .
                            $boxChars['rs'] . $cliColors['reset'] . PHP_EOL;
                    }
                } elseif ($nlFooter === 0 || $i <= ($nLines - $nlFooter)) {
                    if ($start) {
                        $lineTxt[] = $cliColors['hf'] . $boxChars['v'] . $cliColors['reset'] .
                            str_pad('', $printArea) .
                            $cliColors['hf'] . $boxChars['v'] . $cliColors['reset'] . PHP_EOL;
                        $start     = false;
                    }
                    if ($printArea >= mb_strlen($line)) {
                        $line      = ' ' . $line;
                        $line      = str_pad($line, $printArea);
                        $lineTxt[] = $cliColors['hf'] . $boxChars['v'] . $cliColors['reset'] . $line .
                            $cliColors['hf'] . $boxChars['v'] . $cliColors['reset'] . PHP_EOL;
                    } else {
                        $chunks = $this->limitLineLength($line, $printArea - 2);
                        foreach ($chunks as $chunkLine) {
                            if ($printArea >= mb_strlen($chunkLine)) {
                                $chunkLine = str_pad($chunkLine, $printArea - 2);
                            }
                            $lineTxt[] = $cliColors['hf'] . $boxChars['v'] . $cliColors['reset'] . ' ' . $chunkLine . ' ' .
                                $cliColors['hf'] . $boxChars['v'] . $cliColors['reset'] . PHP_EOL;
                        }
                    }
                    if ($i === $nLines || ($nlFooter > 0 && $i === $nLines - $nlFooter)) {
                        $lineTxt[] = $cliColors['hf'] . $boxChars['v'] . $cliColors['reset'] .
                            str_pad('', $printArea) .
                            $cliColors['hf'] . $boxChars['v'] . $cliColors['reset'] . PHP_EOL;
                    }
                } elseif ($nlFooter > 0) {
                    $lineTxt[] = $cliColors['hf'] . $boxChars['ls'] .
                        str_repeat($boxChars['hs'], $printArea) .
                        $boxChars['rs'] . $cliColors['reset'] . PHP_EOL;
                    $line      = str_pad($line, $printArea, ' ', STR_PAD_BOTH);
                    $lineTxt[] = $cliColors['hf'] . $boxChars['v'] . $line . $boxChars['v'] . $cliColors['reset'] . PHP_EOL;
                }
            }
            if (isset($arguments['f']) && $error === false) {
                $lineTxt[] = $cliColors['hf'] . $fBottom . $cliColors['reset'] . PHP_EOL;
                $resultTxt = implode('', $lineTxt);
                $content   = $this->RemoveANSSequence($resultTxt);
                $lineTxt[] = $cliColors['hf'] . $boxChars['ls'] .
                    str_repeat($boxChars['hs'], $printArea) .
                    $boxChars['rs'] . $cliColors['reset'] . PHP_EOL;
                $created   = $this->createFileOutput($arguments['f'], $content);
                if ($created) {
                    $line = str_pad("Archivo '{$arguments['f']}' creado exitosamente.", $printArea, ' ', STR_PAD_BOTH);
                } else {
                    $line = str_pad("Error al Crear el Archivo '{$arguments['f']}'!!!", $printArea, ' ', STR_PAD_BOTH);
                }
                $lineTxt[] = $cliColors['hf'] . $boxChars['v'] . $line . $boxChars['v'] . $cliColors['reset'] . PHP_EOL;
            }
            $lineTxt[] = $cliColors['hf'] . $fBottom . $cliColors['reset'] . PHP_EOL;
            $result    .= implode('', $lineTxt);
        } else {
            $output = '!!!Your Terminal Windows is too Narrow. Resize It!!!' . PHP_EOL .
                '==> Minimum Expected: ' . $longest . PHP_EOL .
                '==> Given Size:       ' . $limitLen . PHP_EOL . PHP_EOL .
                'If you cannot Resize the window;' . PHP_EOL .
                'You can Output the data to a file and avoid this error:' . PHP_EOL .
                'php script.php -f="filename"';
            $result = $this->drawBoxes($output, 1, 1, true, 0, 2, true);
        }

        return $result;
    }

    /**
     * @param  int  $longest
     * @param  int  $limitLen
     *
     * @return bool|string
     */
    private function checkIsFit(int $longest, int $limitLen): bool|string
    {
        return ($longest <= $limitLen);
    }

    /**
     * @param  string  $text
     * @param  int     $limit
     *
     * @return array
     */
    private function limitLineLength(string $text, int $limit): array
    {
        $lines  = explode("\n", $text);
        $result = [];

        foreach ($lines as $line) {
            $chunks      = preg_split('/({[^}]+}|[\/\\\])/', $line, -1, PREG_SPLIT_DELIM_CAPTURE);
            $currentLine = '';

            foreach ($chunks as $chunk) {
                if (mb_strlen($currentLine) + mb_strlen($chunk) <= $limit) {
                    $currentLine .= $chunk;
                } else {
                    $result[]    = $currentLine;
                    $currentLine = $chunk;
                }
            }

            if (!empty($currentLine)) {
                $result[] = $currentLine;
            }
        }

        return $result;
    }

    /**
     * @param  string  $resultTxt
     *
     * @return string
     */
    private function RemoveANSSequence(string $resultTxt): string
    {
        $pattern = "/\x1b\[[0-9;]*[A-Za-z]/";

        return preg_replace($pattern, '', $resultTxt);
    }

    /**
     * @param  string  $filename
     * @param  string  $content
     *
     * @return bool
     */
    private function createFileOutput(string $filename, string $content): bool
    {
        return file_put_contents($filename, $content) !== false;
    }

    /**
     * @return int
     */
    private function detectTermWidth(): int
    {
        $termWidth = TW ?? null;
        if ($termWidth == null && str_contains(PHP_OS, 'WIN')) {
            $termWidth = shell_exec('mode con');
            preg_match('/CON.*:(\n[^|]+?){3}(?<cols>\d+)/', $termWidth, $match);
            $termWidth = isset($match['cols']) ? (int) $match['cols'] : null;
        } elseif ($termWidth == null && function_exists('shell_exec')) {
            $termResponse = shell_exec('tput cols 2> /dev/tty');
            if ($termResponse !== null) {
                $termWidth = trim($termResponse) ?? null;
                if ($termWidth !== null) {
                    $termWidth = (int) $termWidth;
                }
            }
        }
        if ($termWidth === null) {
            $termWidth = 80;
        }

        return $termWidth;
    }
}