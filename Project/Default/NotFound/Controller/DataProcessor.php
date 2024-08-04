<?php

declare(strict_types=1);

namespace Project\Default\Notfound\DataProcessor;

/**
 * Class DataProcessor
 * A simple ...
 */
class DataProcessor
{
    /**
     * @var DataProcessor|null Singleton instance of the DataProcessor.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of DataProcessor.
     *
     * @return DataProcessor The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }


}