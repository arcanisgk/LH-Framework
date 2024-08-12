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

use Asset\Framework\Forms;
use Exception;

/**
 * Class that handles: Kernel of Framework
 *
 * @package Asset\Framework\Core;
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
     * @throws Exception
     */
    public function run(): void
    {
        if (!IS_CLI) {
            //ex('|||====>>>> Kernel Run ))))');
            Config::getInstance()->loadConfiguration();
            Session::getInstance()->handleSession();
            //Route::getInstance()->initRoute();
            //ex(isset($_POST), isset($_GET));

            Route::getInstance()->initialize();


        } else {
            //Ejecutar como CLI
            echo "Is CLI";
        }


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