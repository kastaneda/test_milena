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

        if (isset($data['clientAdminHash'])) {
            // FIXME
            return false;
        }

        // 

        return false;
    }

    protected function getUser($email)
    {
        // FIXME
    }

    protected function getGsmAdmin($email)
    {
        $sql = 'SELECT * FROM `users` '
            . 'WHERE `user_email` = ? AND `user_type_id` = 6';

        return $this->db->fetchAssoc($sql, [$email]);
    }

    protected function checkPassword($cleartext, $encrypted, $salt = null)
    {
        // return password_verify($cleartext, $encrypted);
        return $cleartext === $encrypted; // only for testing!
    }
}
