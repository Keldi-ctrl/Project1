<?php
namespace Growave2\Models;
use Phalcon\Mvc\Model;
class Users extends Model
{
    public $id;

    public $first_name;

    public $second_name;

    public $email;

    public $password;

    public $role_id;

    public function setValue()
    {
    }


}
