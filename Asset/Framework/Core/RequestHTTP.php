<?php

declare(strict_types=1);

namespace Asset\Framework\Core;

/**
 * Class RequestHTTP
 * A simple ...
 */
class RequestHTTP
{
    /**
     * @var RequestHTTP|null Singleton instance of the RequestHTTP.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of RequestHTTP.
     *
     * @return RequestHTTP The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }


}