<?php
namespace Growave2\Models;
use Phalcon\Mvc\Model;
use Growave2\Models\Permissions;

class Roles extends Model
{
    public $id;
    public $name;
    public $display_name;

    public function initialize()
    {
        $this->hasMany('id', 'Growave2\Models\Users', 'role_id', ['alias' => 'User']);
        $this->hasMany(
            'id',
            Permissions::class,
            'role_id',
            [
                'alias' => 'permissions',
            ]
        );
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * @param mixed $display_name
     */
    public function setDisplayName($display_name): void
    {
        $this->display_name = $display_name;
    }

}