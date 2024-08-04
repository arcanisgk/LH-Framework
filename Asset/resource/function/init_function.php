<?php

declare(strict_types=1);

/**
 * function displays resource content.
 */

if (!function_exists('exDataEx')) {
    function exDataEx(...$data): void
    {
        foreach ($data as $var) {
            echo '<pre>';
            echo var_dump($var);
            echo '</pre>';
        }
    }
}
if (!function_exists('exDataReturn')) {
    function exDataReturn(...$data): string
    {
        ob_start();
        foreach ($data as $var) {
            echo '<pre>';
            echo var_dump($var);
            echo '</pre>';
        }
        $var = ob_get_contents();
        ob_end_clean();

        return $var;
    }
}

/**
 * @param ...$var
 *
 * @return void
 */

function ex(...$var): void
{
    $stuff = (IS_CLI) ?
        [
            'salE' => '',
            'salC' => '',
            'nl'   => PHP_EOL,
        ] : [
            'salE' => '<pre>',
            'salC' => '</pre>',
            'nl'   => '<br>',
        ];
    foreach ($var as $arg) {
        echo $stuff['salE'];
        echo var_dump($arg);
        echo $stuff['salC'];
        echo $stuff['nl'];
    }
}