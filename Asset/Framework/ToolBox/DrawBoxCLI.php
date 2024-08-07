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

namespace Asset\Framework\ToolBox;

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
        self::setMinTermWidth($this->detectTermWidth());
    }

    /**
     * @var int
     */
    private static int $minTermWidth = 80;

    /**
     * @return int
     */
    public static function getMinTermWidth(): int
    {
        return self::$minTermWidth;
    }

    /**
     * @param int $minTermWidth
     */
    public static function setMinTermWidth(int $minTermWidth): void
    {
        self::$minTermWidth = $minTermWidth;
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
     * @param array|string $source
     * @param int $nlHeader
     * @param int $nlFooter
     * @param bool $highlight
     * @param int $limitLen
     * @param int $typeOutput
     * @param bool $error
     *
     * @return string
     */
    public function drawBoxes(
        array|string $source,
        int $nlHeader = 0,
        int $nlFooter = 0,
        bool $highlight = false,
        int $limitLen = 0,
        int $typeOutput = 0,
        bool $error = false,
    ): string {

        $arguments = ArgumentLoader::getArguments();

        $source = (is_array($source) ? $source : preg_split('/\r\n|\r|\n/', rtrim($source)));

        $isCli       = $this->isCli();
        $boxChars    = $this->getBoxChars();
        $colorScheme = $this->getColorScheme($typeOutput);

        $cliColors = [
            'hf'    => ($highlight && $isCli && $nlHeader !== 0) ? "\033{$colorScheme['c']}" : '',
            'reset' => ($highlight && $isCli) ? "\033{$colorScheme['r']}" : '',
        ];
        $lenS      = max(array_map([$this, 'getStringLengthWithoutANSI'], $source));

        $termWidth = self::getMinTermWidth();

        $longest = ($lenS > $limitLen) ? $lenS : $limitLen;

        if ($limitLen == 0) {
            $longest = $termWidth;
        }

        if ($limitLen == 0 || $this->checkIsFit($longest, $limitLen) || isset($arguments['f'])) {
            $printArea = $longest - 2;
            $result    = '';
            $nLines    = count($source);
            $i         = 0;
            $fTop      = $boxChars['tl'].str_repeat($boxChars['h'], $printArea).$boxChars['tr'];
            $fBottom   = $boxChars['bl'].str_repeat($boxChars['h'], $printArea).$boxChars['br'];
            $lineTxt   = [];
            $lineTxt[] = $cliColors['hf'].$fTop.$cliColors['reset'].PHP_EOL;
            $start     = true;
            foreach ($source as $key => $line) {
                $i++;
                if ($nlHeader > 0 && $i <= $nlHeader) {
                    $line      = str_pad($line, $printArea, ' ', STR_PAD_BOTH);
                    $lineTxt[] = $cliColors['hf'].$boxChars['v'].$line.$boxChars['v'].$cliColors['reset'].PHP_EOL;
                    if ($i == $nlHeader) {
                        $lineTxt[] = $cliColors['hf'].$boxChars['ls'].
                            str_repeat($boxChars['hs'], $printArea).
                            $boxChars['rs'].$cliColors['reset'].PHP_EOL;
                    }
                } elseif ($nlFooter === 0 || $i <= ($nLines - $nlFooter)) {

                    $lineTemp = $this->RemoveANSSequence($line);

                    if ($start) {
                        $lineTxt[] = $cliColors['hf'].$boxChars['v'].$cliColors['reset'].
                            str_pad('', $printArea).
                            $cliColors['hf'].$boxChars['v'].$cliColors['reset'].PHP_EOL;
                        $start     = false;
                    }
                    if ($printArea >= mb_strlen($lineTemp) && $lineTemp !== '') {
                        $expoLine  = explode($lineTemp, str_pad($lineTemp, $printArea));
                        $line      = implode($line, $expoLine);
                        $lineTxt[] = $cliColors['hf'].$boxChars['v'].$cliColors['reset'].$line.
                            $cliColors['hf'].$boxChars['v'].$cliColors['reset'].PHP_EOL;
                    } else {

                        $chunks = $this->limitLineLength($line, $printArea - 2);

                        foreach ($chunks as $chunkLine) {
                            if ($printArea >= mb_strlen($chunkLine)) {
                                $chunkLine = str_pad($chunkLine, $printArea - 2);
                            }
                            $lineTxt[] = $cliColors['hf'].$boxChars['v'].$cliColors['reset'].' '.$chunkLine.' '.
                                $cliColors['hf'].$boxChars['v'].$cliColors['reset'].PHP_EOL;
                        }
                    }
                    if ($i === $nLines || ($nlFooter > 0 && $i === $nLines - $nlFooter)) {
                        $lineTxt[] = $cliColors['hf'].$boxChars['v'].$cliColors['reset'].
                            str_pad('', $printArea).
                            $cliColors['hf'].$boxChars['v'].$cliColors['reset'].PHP_EOL;
                    }
                } elseif ($nlFooter > 0) {
                    $lineTxt[] = $cliColors['hf'].$boxChars['ls'].
                        str_repeat($boxChars['hs'], $printArea).
                        $boxChars['rs'].$cliColors['reset'].PHP_EOL;
                    $line      = str_pad($line, $printArea, ' ', STR_PAD_BOTH);
                    $lineTxt[] = $cliColors['hf'].$boxChars['v'].$line.$boxChars['v'].$cliColors['reset'].PHP_EOL;
                }
            }
            if (isset($arguments['f']) && $error === false) {
                $lineTxt[] = $cliColors['hf'].$fBottom.$cliColors['reset'].PHP_EOL;
                $resultTxt = implode('', $lineTxt);
                $content   = $this->RemoveANSSequence($resultTxt);
                $lineTxt[] = $cliColors['hf'].$boxChars['ls'].
                    str_repeat($boxChars['hs'], $printArea).
                    $boxChars['rs'].$cliColors['reset'].PHP_EOL;
                $created   = $this->createFileOutput($arguments['f'], $content);
                if ($created) {
                    $line = str_pad("File '{$arguments['f']}' successfully created.", $printArea, ' ', STR_PAD_BOTH);
                } else {
                    $line = str_pad("Error creating file '{$arguments['f']}'!!!", $printArea, ' ', STR_PAD_BOTH);
                }
                $lineTxt[] = $cliColors['hf'].$boxChars['v'].$line.$boxChars['v'].$cliColors['reset'].PHP_EOL;
            }
            $lineTxt[] = $cliColors['hf'].$fBottom.$cliColors['reset'].PHP_EOL;
            $result    .= implode('', $lineTxt);
        } else {
            $output = '!!!Your Terminal Windows is too Narrow. Resize It!!!'.PHP_EOL.
                '==> Minimum Expected: '.$longest.PHP_EOL.
                '==> Given Size:       '.$limitLen.PHP_EOL.PHP_EOL.
                'If you cannot Resize the window;'.PHP_EOL.
                'You can Output the data to a file and avoid this error:'.PHP_EOL.
                'php script.php -f="filename"';
            $result = $this->drawBoxes($output, 1, 1, true, 0, 2, true);
        }

        return $result;
    }

    /**
     * @param int $longest
     * @param int $limitLen
     *
     * @return bool|string
     */
    private function checkIsFit(int $longest, int $limitLen): bool|string
    {
        return ($longest <= $limitLen);
    }

    /**
     * @param string $text
     * @param int $limit
     *
     * @return array
     */
    private function limitLineLength(string $text, int $limit): array
    {
        $lines  = explode("\n", $text);
        $result = [];
        foreach ($lines as $line) {
            $delimiter = str_contains($line, '=>') ? ' =>' : (str_contains($line, ':') ? ':' : null);
            $lineTemp  = $this->RemoveANSSequence($line);
            if (mb_strlen($lineTemp) >= $limit) {
                $delimiterChar  = mb_strlen($delimiter);
                $lineArr        = explode($delimiter, $line);
                $lineTempArr    = explode($delimiter, $lineTemp);
                $ansiColorLeft  = $this->extractAnsiCodes($lineArr[0]);
                $ansiColorRight = $this->extractAnsiCodes($lineArr[1]);
                $margin         = mb_strlen($lineTempArr[0]);
                $newLimit       = $limit - $margin - $delimiterChar;
                $lineTempArrCut = $this->getSplitText($lineTempArr[1], $newLimit);
                foreach ($lineTempArrCut as $key => $chunk) {
                    if ($key == 0) {
                        $leftText  = implode(
                            str_pad(trim($lineTempArr[0]), $margin - $delimiterChar, " ", STR_PAD_LEFT),
                            $ansiColorLeft
                        );
                        $rightText = implode(str_pad($chunk, $newLimit + $delimiterChar), $ansiColorRight);

                        $result[] = $leftText.$delimiter.$rightText;
                    } else {
                        $space    = str_pad('', $margin + $delimiterChar);
                        $result[] = implode(str_pad($space.$chunk, $limit), $ansiColorRight);
                    }
                }

            } else {
                $result[] = $line;
            }
        }

        return $result;
    }


    /**
     * @param $string
     * @return array
     */
    private function extractAnsiCodes($string): array
    {
        $startPattern = '/\033\[(\d+(;\d+)*)m/';
        $resetPattern = '/\033\[0m/';
        preg_match($startPattern, $string, $startMatch);
        preg_match_all($resetPattern, $string, $resetMatches);
        $firstCode = $startMatch[0] ?? null;
        $lastReset = end($resetMatches[0]);

        return [
            'start' => $firstCode,
            'reset' => $lastReset,
        ];
    }


    /**
     * @param string $resultTxt
     *
     * @return string
     */
    private function RemoveANSSequence(string $resultTxt): string
    {
        $pattern = "#\x1B(?:[@-Z\\-_]|\[[0-?]*[ -/]*[@-~])#";

        return preg_replace($pattern, '', $resultTxt);
    }

    /**
     * @param string $filename
     * @param string $content
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
            $termWidth = isset($match['cols']) ? (int)$match['cols'] : null;
        } elseif ($termWidth == null && function_exists('shell_exec')) {
            $termResponse = shell_exec('tput cols 2> /dev/tty');
            if ($termResponse !== null) {
                $termWidth = trim($termResponse) ?? null;
                if ($termWidth !== null) {
                    $termWidth = (int)$termWidth;
                }
            }
        }
        if ($termWidth === null) {
            $termWidth = 80;
        }

        return $termWidth;
    }

    /**
     * @param string $string
     * @return int
     */
    private function getStringLengthWithoutANSI(string $string): int
    {
        return mb_strlen(preg_replace('/\e[[^A-Za-z]*[A-Za-z]/', '', $string));
    }

    /**
     * @return bool
     */
    private function isCli(): bool
    {
        return defined('STDIN')
            || php_sapi_name() === "cli"
            || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
            || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);
    }

    /**
     * @return string[]
     */
    private function getBoxChars(): array
    {
        return [
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
    }


    /**
     * @param int $typeOutput
     * @return array
     */
    private function getColorScheme(int $typeOutput): array
    {

        $colorScheme = ['r' => '[0m'];

        $colorScheme['c'] = match ($typeOutput) {
            1 => '[1;42;30m',
            2 => '[1;41m',
            3 => '[1;43;30m',
            4 => '[1;44;30m',
            5 => '[1;32m',
            6 => '[1;31m',
            7 => '[1;46;30m',
            8 => '[1;37m',
            9 => '[1;45m',
            default => '[0m',
        };

        return $colorScheme;
    }

    /**
     * @param string $text
     * @param int $newLimit
     * @return array
     */
    private function getSplitText(string $text, int $newLimit): array
    {
        $splitText    = str_split($text, $newLimit);
        $lastFragment = end($splitText);
        if (strlen($lastFragment) < 8 && $newLimit > 1) {
            return $this->getSplitText($text, $newLimit - 1);
        }

        return $splitText;
    }
}