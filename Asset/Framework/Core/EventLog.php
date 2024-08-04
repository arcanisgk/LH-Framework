<?php

declare(strict_types=1);

namespace Asset\Framework\Core;

/**
 * Class EventLog
 * A simple ...
 */
class EventLog
{
    /**
     * @var EventLog|null Singleton instance of the EventLog.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of EventLog.
     *
     * @return EventLog The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

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
    protected const LOG_LIST = ['user', 'cron', 'webservice', 'error'];

    private function initLogs(): void
    {
        $files     = Files::getInstance();
        $directory = PD . DS . 'Asset' . DS . 'resource' . DS . 'log' . DS . 'event' . DS;
        foreach (self::LOG_LIST as $logDirectory) {
            if (!$files->validateDirectory($directory . $logDirectory)) {
                $files->createDirectory($directory . $logDirectory);
            }
        }
    }
}