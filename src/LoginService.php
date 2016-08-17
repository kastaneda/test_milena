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
        // return false;
        return ['id'=>123, 'table'=>'users'];
    }
}
