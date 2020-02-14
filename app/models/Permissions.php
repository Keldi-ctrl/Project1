<?php
namespace Growave2\Models;
use Phalcon\Mvc\Model;

class Permissions extends Model
{
    public $id;
    public $role_id;
    public $resource;
    public $action;

    public function initialize()
    {
        $this->belongsTo('role_id', 'Growave2\Models\Roles', 'id', ['alias' => 'role']);
    }
}