<?php

namespace App\Models;

/**
 * Class User
 * @package App\Models
 *
 */
class User extends BaseModel
{
    public $id;
    public $name;
    public $password;
    public $email;
    public $role;

    public function initialize()
    {
        $this->setSource('users');
    }
}
