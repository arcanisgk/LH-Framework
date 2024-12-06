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

use Asset\Framework\Http\Request;
use Asset\Framework\Http\Response;
use Asset\Framework\Http\Route;
use Asset\Framework\Trait\SingletonTrait;
use Exception;

/**
 * Class that handles: Kernel of Framework
 *
 * @package Asset\Framework\Core;
 */
class Kernel
{
    use SingletonTrait;

    /**
     * @return void
     * @throws Exception
     */
    public function lh(): void
    {
        if (!IS_CLI) {
            ConfigLoader::getInstance()->loadConfigurations();


            Session::getInstance()->handleSession();


            //Route::getInstance()->initRoute();
            //ex(isset($_POST), isset($_GET));

            //
            //ex_c(Mailer::getInstance());

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