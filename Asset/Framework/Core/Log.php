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

namespace Asset\Framework\Core;

use Asset\Framework\Trait\SingletonTrait;

/**
 * Class EventLog
 * A simple ...
 */
class Log
{
    use SingletonTrait;

    public function __construct()
    {
        $this->initLogs();
    }

    /**
     * List of logs (TAKE CARE NOT REMOVE IT)
     * error: used when an error occurs caused by: the system itself, the user, the cronjob subsystem, or webservice consumption
     * user: user interaction log (gps navigation or event crud)
     * cronjob: occurs when a cron is executed correctly
     * webservice: occurs when a webservice is consumed correctly
     */
    protected const array LOG_LIST = ['user', 'cron', 'webservice', 'error'];

    private function initLogs(): void
    {
        $files     = Files::getInstance();
        $directory = implode(DS, [PD, 'Asset', 'resource', 'log', 'event', '']);
        foreach (self::LOG_LIST as $logDirectory) {
            if (!$files->validateDirectory($directory.$logDirectory)) {
                $files->createDirectory($directory.$logDirectory);
            }
        }
    }
}