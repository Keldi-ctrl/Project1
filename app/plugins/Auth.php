<?php
declare(strict_types=1);
namespace Growave2\Plugins;

use Phalcon\Di\Injectable;
use Phalcon\Http\Response;
use Growave2\Models\Users;
use Phalcon\Exception;


class Auth extends Injectable
{

    public function check($credentials)
    {
        // Check if the user exist
        $user = Users::findFirstByEmail($credentials['email']);

        if ($user)
        {
            if($credentials['email'] == $user->email &&
                $this->security->checkHash($credentials['password'], $user->password))
            {
                $this->session->set('auth', [
                    'id' => $user->id,
                    'name' => $user->first_name,
                    'activeStatus' => $user->active,
                    'id' => $user->id,
                    'role_id' => $user->role_id,
                ]);
            }
            else
            {
                echo "Wrong email/password combination";

            }

        }

    }


    public function getIdentity()
    {
        return $this->session->get('auth');
    }


    public function getName()
    {
        $identity = $this->session->get('auth');
        return $identity['name'];
    }

    public function getId()
    {
        $identity = $this->session->get('auth');
        return $identity['id'];
    }

    public function getActiveStatus()
    {
        $identity = $this->session->get('auth');
        return $identity['activeStatus'];
    }

    public function getRole()
    {
        $identity = $this->session->get('auth');
        return $identity['role_id'];
    }


    public function getUser()
    {
        $identity = $this->session->get('auth');

        if (!isset($identity['id'])) {
            throw new Exception('Session was broken. Try to re-login');
        }

        $user = Users::findFirstById($identity['id']);
        if ($user == false) {
            throw new Exception('The user does not exist');
        }

        return $user;
    }
    public function remove()
    {
        $this->session->remove('auth');
        echo "Вы вышли";
    }

}