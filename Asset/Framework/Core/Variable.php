<?php

namespace Asset\Framework\Core;

class Variable
{
    private static ?self $instance = null;

    /**
     * @return Variable
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param  string  $var
     *
     * @return bool
     */
    public function isJson(string $var): bool
    {
        return json_decode($var) != null;
    }


}