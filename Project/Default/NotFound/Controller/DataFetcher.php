<?php

declare(strict_types=1);

namespace Project\Default\Notfound\DataFetcher;

/**
 * Class DataFetcher
 * A simple ...
 */
class DataFetcher
{
    /**
     * @var DataFetcher|null Singleton instance of the DataFetcher.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of DataFetcher.
     *
     * @return DataFetcher The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }


}