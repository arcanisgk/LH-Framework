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
use DateTime;
use Exception;

/**
 * Class that handles:
 * DateUtils Class - Handles date formatting and manipulation with multilanguage support
 * @package Asset\Framework\Core;
 */
class DateUtils
{

    use SingletonTrait;


    /**
     * Month translations for supported languages
     */
    private const array MONTH_TRANSLATIONS
        = [
            'es' => [
                1  => 'enero',
                2  => 'febrero',
                3  => 'marzo',
                4  => 'abril',
                5  => 'mayo',
                6  => 'junio',
                7  => 'julio',
                8  => 'agosto',
                9  => 'septiembre',
                10 => 'octubre',
                11 => 'noviembre',
                12 => 'diciembre',
            ],
            'en' => [
                1  => 'January',
                2  => 'February',
                3  => 'March',
                4  => 'April',
                5  => 'May',
                6  => 'June',
                7  => 'July',
                8  => 'August',
                9  => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ],
            'fr' => [
                1  => 'janvier',
                2  => 'février',
                3  => 'mars',
                4  => 'avril',
                5  => 'mai',
                6  => 'juin',
                7  => 'juillet',
                8  => 'août',
                9  => 'septembre',
                10 => 'octobre',
                11 => 'novembre',
                12 => 'décembre',
            ],
            'pt' => [
                1  => 'janeiro',
                2  => 'fevereiro',
                3  => 'março',
                4  => 'abril',
                5  => 'maio',
                6  => 'junho',
                7  => 'julho',
                8  => 'agosto',
                9  => 'setembro',
                10 => 'outubro',
                11 => 'novembro',
                12 => 'dezembro',
            ],
        ];

    /**
     * Day translations for supported languages
     */
    private const array DAY_TRANSLATIONS
        = [
            'es' => [
                'Monday'    => 'Lunes',
                'Tuesday'   => 'Martes',
                'Wednesday' => 'Miércoles',
                'Thursday'  => 'Jueves',
                'Friday'    => 'Viernes',
                'Saturday'  => 'Sábado',
                'Sunday'    => 'Domingo',
            ],
            'en' => [
                'Monday'    => 'Monday',
                'Tuesday'   => 'Tuesday',
                'Wednesday' => 'Wednesday',
                'Thursday'  => 'Thursday',
                'Friday'    => 'Friday',
                'Saturday'  => 'Saturday',
                'Sunday'    => 'Sunday',
            ],
            'fr' => [
                'Monday'    => 'Lundi',
                'Tuesday'   => 'Mardi',
                'Wednesday' => 'Mercredi',
                'Thursday'  => 'Jeudi',
                'Friday'    => 'Vendredi',
                'Saturday'  => 'Samedi',
                'Sunday'    => 'Dimanche',
            ],
            'pt' => [
                'Monday'    => 'Segunda-feira',
                'Tuesday'   => 'Terça-feira',
                'Wednesday' => 'Quarta-feira',
                'Thursday'  => 'Quinta-feira',
                'Friday'    => 'Sexta-feira',
                'Saturday'  => 'Sábado',
                'Sunday'    => 'Domingo',
            ],
        ];

    /** @var string Default language for date formatting */
    private string $defaultLanguage = 'en';

    /** @var string Default timezone */
    private string $defaultTimezone = 'UTC';

    /** @var string Default date separator */
    private string $defaultSeparator = '-';

    /** @var bool Whether to include time in date formatting */
    private bool $includeTime = false;

    /** @var int Precision for milliseconds (0-3) */
    private int $millisecondsPrecision = 0;

    /**
     * Constructor - Sets default timezone
     */
    public function __construct()
    {
        date_default_timezone_set($this->defaultTimezone);
    }

    /**
     * Calcula la cantidad de días específicos en un mes y los separa por día.
     *
     * @param int $year Año del calendario
     * @param int $month Mes del calendario (1-12)
     * @param array $daysOfWeek Días de la semana a contar (0=domingo, 1=lunes, ..., 6=sábado)
     * @return array Desglose del conteo por cada día indicado
     */
    public static function countDaysInMonth(int $year, int $month, array $daysOfWeek): array
    {
        $date       = new DateTime("$year-$month-01");
        $endOfMonth = $date->format('t'); // Número de días en el mes
        $counts     = array_fill_keys($daysOfWeek, 0); // Inicializar el conteo en 0 para los días indicados

        // Iterar por cada día del mes
        for ($day = 1; $day <= $endOfMonth; $day++) {
            $date->setDate($year, $month, $day);
            $weekday = (int)$date->format('w'); // Día de la semana (0=domingo, 6=sábado)

            if (in_array($weekday, $daysOfWeek)) {
                $counts[$weekday]++;
            }
        }

        return $counts;
    }

    /**
     * Sets the default timezone
     *
     * @param string $timezone Valid PHP timezone string
     * @return self
     */
    public function setTimezone(string $timezone): self
    {
        date_default_timezone_set($timezone);
        $this->defaultTimezone = $timezone;

        return $this;
    }

    /**
     * Sets the default language for date formatting
     *
     * @param string $lang Language code (es|en|fr|pt)
     * @return self
     */
    public function setLanguage(string $lang): self
    {
        if (isset(self::MONTH_TRANSLATIONS[$lang])) {
            $this->defaultLanguage = $lang;
        }

        return $this;
    }

    /**
     * Sets the default separator for date formatting
     *
     * @param string $separator Separator character
     * @return self
     */
    public function setSeparator(string $separator): self
    {
        $this->defaultSeparator = $separator;

        return $this;
    }

    /**
     * Sets whether to include time in date formatting
     *
     * @param bool $include Whether to include time
     * @return self
     */
    public function includeTime(bool $include): self
    {
        $this->includeTime = $include;

        return $this;
    }

    /**
     * Sets the precision for milliseconds
     *
     * @param int $precision Precision (0-3)
     * @return self
     */
    public function setMillisecondsPrecision(int $precision): self
    {
        $this->millisecondsPrecision = min(max($precision, 0), 3);

        return $this;
    }

    /**
     * Formats a date with month name in specified language
     *
     * @param DateTime|string|null $date Date to format
     * @param bool $longFormat Whether to use long format
     * @return string Formatted date
     * @throws Exception
     */
    public function formatWithMonthName(DateTime|string|null $date = null, bool $longFormat = false): string
    {
        $dateObj   = $this->getDateTimeObject($date);
        $monthNum  = (int)$dateObj->format('n');
        $monthName = self::MONTH_TRANSLATIONS[$this->defaultLanguage][$monthNum];

        if ($longFormat) {
            $dayName = self::DAY_TRANSLATIONS[$this->defaultLanguage][$dateObj->format('l')];

            return $this->formatLongDate($dateObj, $dayName, $monthName);
        }

        return $this->formatShortDate($dateObj, $monthName);
    }

    /**
     * Helper method to get DateTime object from various input types
     *
     * @param DateTime|string|null $date Input date
     * @return DateTime
     * @throws Exception
     */
    private function getDateTimeObject(DateTime|string|null $date = null): DateTime
    {
        if ($date === null) {
            return new DateTime();
        }
        if ($date instanceof DateTime) {
            return $date;
        }

        return new DateTime($date);
    }

    /**
     * Helper method to format long date
     */
    private function formatLongDate(DateTime $dateObj, string $dayName, string $monthName): string
    {
        $format = "$dayName, {$dateObj->format('d')} de $monthName de {$dateObj->format('Y')}";

        if ($this->includeTime) {
            $format .= $this->getTimeFormat($dateObj);
        }

        return $format;
    }

    /**
     * Helper method to get time format string
     */
    private function getTimeFormat(DateTime $dateObj): string
    {
        $format = " {$dateObj->format('H:i:s')}";

        if ($this->millisecondsPrecision > 0) {
            $format .= ".{$dateObj->format(str_repeat('v', $this->millisecondsPrecision))}";
        }

        return $format;
    }

    /**
     * Helper method to format short date
     */
    private function formatShortDate(DateTime $dateObj, string $monthName): string
    {
        $format = "{$dateObj->format('d')} $monthName {$dateObj->format('Y')}";

        if ($this->includeTime) {
            $format .= $this->getTimeFormat($dateObj);
        }

        return $format;
    }

    /**
     * Formats a date numerically
     *
     * @param DateTime|string|null $date Date to format
     * @param bool $yearFirst Whether to put year first
     * @return string Formatted date
     * @throws Exception
     */
    public function formatNumeric(DateTime|string|null $date = null, bool $yearFirst = true): string
    {
        $dateObj   = $this->getDateTimeObject($date);
        $separator = $this->defaultSeparator;

        $format = $yearFirst ?
            "Y{$separator}m{$separator}d" :
            "d{$separator}m{$separator}Y";

        if ($this->includeTime) {
            $format .= " H:i:s";
            if ($this->millisecondsPrecision > 0) {
                $format .= '.'.str_repeat('v', $this->millisecondsPrecision);
            }
        }

        return $dateObj->format($format);
    }

    /**
     * Formats date for database storage (Y-m-d H:i:s[.v])
     *
     * @param DateTime|string|null $date Date to format
     * @return string Database formatted date
     * @throws Exception
     */
    public function formatForDatabase(DateTime|string|null $date = null): string
    {
        $dateObj = $this->getDateTimeObject($date);
        $format  = 'Y-m-d H:i:s';

        if ($this->millisecondsPrecision > 0) {
            $format .= '.'.str_repeat('v', $this->millisecondsPrecision);
        }

        return $dateObj->format($format);
    }


}