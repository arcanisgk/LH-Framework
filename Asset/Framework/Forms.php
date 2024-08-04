<?php

declare(strict_types=1);

namespace Asset\Framework;

/**
 * Class Forms
 * A simple ...
 */
class Forms
{
    /**
     * @var Forms|null Singleton instance of the Forms.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of Forms.
     *
     * @return Forms The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function loadView(string $view)
    {
        //ex($view);
    }


}