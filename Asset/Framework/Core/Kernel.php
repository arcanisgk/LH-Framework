<?php

declare(strict_types=1);

namespace Asset\Framework\Core;

use Asset\Framework\Forms;

/**
 * Class Kernel
 * A simple ...
 */
class Kernel
{
    /**
     * @var Kernel|null Singleton instance of the Kernel.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of Kernel.
     *
     * @return Kernel The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    public function run(): void
    {
        Config::getInstance()->loadConfiguration();
        Session::getInstance()->handleSession();
        Route::getInstance()->initRoute();
        /*
        $forms = Forms::getInstance();

        if (isset($_POST)) {
            echo "Proceso";
            //procesado
        } elseif (isset($_GET)) {
            echo "Nav";
            //navigation
        } else {
            //login o página de inicio por defecto
            //$forms->loadView($_SESSION['USER']['LOGIN'] ? 'Home' : 'Login');
        }

        Route::getInstance()->initRoute();
        */
    }
}