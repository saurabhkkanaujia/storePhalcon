<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $username;
    public $email;
    public $password;
    public $status;
    public $role;
}
