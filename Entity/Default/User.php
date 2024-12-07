<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Entity\Default;

use Asset\Framework\Core\Database;
use Asset\Framework\Core\Mailer;
use Asset\Framework\Http\Request;
use Asset\Framework\Template\Render;
use Asset\Framework\Trait\SingletonTrait;
use Random\RandomException;

/**
 * Class that handles: User Entity
 *
 * @package Asset\Framework\Core;
 */
class User
{

    use SingletonTrait;

    /**
     * User constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param array $credentials
     * @return bool
     * @throws RandomException
     */
    public function createAccount(array $credentials): bool
    {

        $db = Database::getInstance();
        $db->connect($db->getDatabaseConfiguration('db1'));

        $builder = $db->getBuilder();

        $activationToken = bin2hex(random_bytes(32));

        $futureTime = strtotime('+6 hours');


        $tokenExpiration        = date('Y-m-d H:i:s', $futureTime);
        $tokenExpirationCompact = date('Ymd-His', $futureTime);

        $query = $builder->insert('users', [
            'first_name'       => $credentials['first_name'],
            'last_name'        => $credentials['last_name'],
            'email'            => $credentials['email'],
            'password'         => $credentials['password'],
            'created_at'       => $credentials['created_at'],
            'status'           => 'inactive',
            'activation_token' => $activationToken,
            'token_expiration' => $tokenExpiration,
        ]);

        $db->setQueryTarget(CONFIG->db->db1->getDbName());
        $db->addQuery($query, $builder->getParameters());

        $results = $db->executeStack();

        if ($results['exec']) {
            $id      = $db->lastInsertId();
            $subject = Render::getInstance()->translateToken('mail-user-0');
            $content = Render::getInstance()->translateToken('mail-user-1');

            $activationLink = Request::getInstance()->buildUrl(
                'User-Activation',
                ['uId' => $id.'-'.$tokenExpirationCompact, 'token' => $activationToken]
            );

            $mailer = Mailer::getInstance();
            $mailer->send(
                $credentials['email'],
                $subject,
                [
                    'title'   => $subject,
                    'intro'   => $subject,
                    'content' => $content.$activationLink,
                    'footer'  => 'Best regards, '.CONFIG->app->company->getCompanyOwner(),
                ],
            );

            return true;
        }

        return false;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $db = Database::getInstance();
        $db->connect($db->getDatabaseConfiguration('db1'));

        $builder = $db->getBuilder();

        $query = $builder->table('users')
            ->select('id')
            ->where('email', '=', $email);

        $db->setQueryTarget(CONFIG->db->db1->getDbName());
        $db->addQuery($query->getQuery(), $builder->getParameters());

        $results = $db->executeStack(true);

        return $results['count_reg'] > 0;
    }
}