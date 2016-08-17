<?php

namespace Milena;

use Doctrine\DBAL\Connection;

/**
 * Business logic related to login processes.
 */
class LoginService
{
    /** @var Connection */
    protected $db;

    /**
     * Constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Login method. Will check $data['email'], $data['password']
     * and optional constraints ($data['roles'], etc).
     *
     * @param array $data
     * @return array|false
     */
    public function login($data)
    {
        $email = isset($data['email']) ? $data['email'] : false;
        $password = isset($data['password']) ? $data['password'] : false;
        $roles = isset($data['roles']) ? $data['roles'] : false;

        if (!$email || !$password) {
            return false;
        }

        // search in 'users' table
        if (!$roles || in_array(UserRoles::GSM_ADMIN, $roles)) {
            if ($user = $this->getGsmAdmin($email)) {
                if ($this->checkPassword($password, $user['user_password'])) {
                    return [
                        'id' => $user['user_id'],
                        'table' => 'users',
                        'email' => $email,
                    ];
                }
            }
        }

        // search in 'as_users' table
        if ($user = $this->getUser($email)) {
            if ($roles && !in_array($user['user_type'], $roles)) {
                return false;
            }

            if (isset($data['clientAdminHash'])
                && $user['user_type'] == UserRoles::CLIENT_ADMIN
                && $user['hash'] != $data['clientAdminHash']) {
                return false;
            }

            if ($this->checkPassword($password, $user['pwd'], $user['salt'])) {
                return [
                    'id' => $user['id'],
                    'table' => 'as_users',
                    'email' => $email,
                ];
            }
        }

        return false;
    }

    protected function getUser($email)
    {
        $sql = 'SELECT u.id, ut.user_type, u.pwd, u.salt, c.hash'
            . ' FROM as_users u'
            . ' LEFT JOIN as_user_types ut ON u.user_type_id = ut.user_type_id'
            . ' LEFT JOIN as_clients c ON u.clientId = c.id'
            . ' WHERE email = ?';

        return $this->db->fetchAssoc($sql, [$email]);
    }

    protected function getGsmAdmin($email)
    {
        $sql = 'SELECT * FROM `users`'
            . ' WHERE user_email = ? AND user_type_id = 6';

        return $this->db->fetchAssoc($sql, [$email]);
    }

    protected function checkPassword($cleartext, $encrypted, $salt = null)
    {
        //return password_verify($cleartext, $encrypted);
        return $cleartext === $encrypted; // only for testing!
    }
}
