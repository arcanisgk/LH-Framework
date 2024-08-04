<?php

namespace Asset\Framework\Core;

class ArgumentLoader
{
    /**
     * @var array
     */
    private static array $arguments = [];

    /**
     * @var ArgumentLoader|null
     */
    private static ?self $instance = null;

    /**
     * @param $args
     */
    public function __construct($args)
    {
        $this->setArguments($args);
    }

    /**
     * @param $args
     *
     * @return self
     */
    public static function getInstance($args): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($args);
        }

        return self::$instance;
    }

    /**
     * @param $args
     *
     * @return void
     */
    private static function setArguments($args): void
    {
        $args       = array_slice($args, 1);
        $parsedArgs = [];
        foreach ($args as $arg) {
            if (str_contains($arg, '=')) {
                [$key, $value] = explode('=', $arg, 2);
                $parsedArgs[trim($key, '-')] = trim($value, '"');
            }
        }

        self::$arguments = $parsedArgs;
    }

    /**
     * @return array
     */
    public static function getArguments(): array
    {
        return self::$arguments;
    }
}

if (isset($argv)) {
    ArgumentLoader::getInstance($argv);
}