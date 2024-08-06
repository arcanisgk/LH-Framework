<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Requiered).
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

ini_set('opcache.enable', 0);

use Asset\Framework\Core\Kernel;

define('LH_START', [
    'TIME'        => microtime(true),
    'MEMORY'      => memory_get_usage(),
    'MEMORY_PEAK' => memory_get_peak_usage(),
]);

if (!version_compare(phpversion(), '8.3', '>=')) {
    die("This project requires PHP version 8.3 or higher");
}

require_once realpath(implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'vendor', 'autoload.php']));

//Kernel::getInstance()->run();


$array = [
    'null'         => null,
    'int'          => 1,
    'float'        => 1.25,
    'string'       => 'Hello World!!!',
    'array'        => ['ups!!', 'other'],
    'object-empty' => (object)[],
    'object'       => (object)['prop1' => 3.1416, 'prop2' => true],
    'long-text'    => 'asjdghfaskjdgcjhzxcgvhjzxjvhxcvbnxzcvnxbczvmnbxzncvbzcxnvbmxnzvbnmcxbvnmxzcbvxncbvxnzvbxcnbvnmxcvbxnvbxnczbvzxnbvnxzcvbxncvbcxnvbcxnvbxcnvbxnvbxmnvbmzxnbvzxnvbzxnvbxncbvxcnzbvcxnzvxmznbvxzcnbvcxnzvbmzxncbvcxnvbxzcnvbzxnbvzcxnvbzcxnvbzxcmnvbzxnvbcxnvbzxnvbxzmcvnbmnvbshfzvgsjfhgsjgfsdvbzcxn',
    'other-array'  => [
        [],
        ['text' => 'asjdghfaskjdgcjhzxcgvhjzxjvhxcvbnxzcvnxbczvmnbxzncvbzcxnvbmxnzvbnmcxbvnmxzcbvxncbvxnzvbxcnbvnmxcvbxnvbxnczbvzxnbvnxzcvbxncvbcxnvbcxnvbxcnvbxnvbxmnvbmzxnbvzxnvbzxnvbxncbvxcnzbvcxnzvxmznbvxzcnbvcxnzvbmzxncbvcxnvbxzcnvbzxnbvzcxnvbzcxnvbzxcmnvbzxnvbcxnvbzxnvbxzmcvnbmnvbshfzvgsjfhgsjgfsdvbzcxn'],
    ],
    'object2'      => (object)[
        'prop1' => 'asjdghfaskjdgcjhzxcgvhjzxjvhxcvbnxzcvnxbczvmnbxzncvbzcxnvbmxnzvbnmcxbvnmxzcbvxncbvxnzvbxcnbvnmxcvbxnvbxnczbvzxnbvnxzcvbxncvbcxnvbcxnvbxcnvbxnvbxmnvbmzxnbvzxnvbzxnvbxncbvxcnzbvcxnzvxmznbvxzcnbvcxnzvbmzxncbvcxnvbxzcnvbzxnbvzcxnvbzcxnvbzxcmnvbzxnvbcxnvbzxnvbxzmcvnbmnvbshfzvgsjfhgsjgfsdvbzcxn',
        'prop2' => false,
    ],
];

ex([$array]);