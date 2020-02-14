<?php
namespace Growave2\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Growave2\Models\Roles;

class ControllerBase extends Controller
{
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $controllerName = $dispatcher->getControllerName();
        if ($this->acl->isPrivate($controllerName))
        {
            $identity = $this->auth->getIdentity();
            if (!is_array($identity))
            {
                $this->flash->notice('Вы не авторизованы!');
                $dispatcher->forward([
                    'controller' => 'sign',
                    'action' => 'index',
                ]);
                return false;
            }

            $actionName = $dispatcher->getActionName();
            if (!$this->acl->isAllowed($identity['role_id'], $dispatcher->getControllerName(), $actionName))
            {
                $this->flash->notice('Доступ закрыт! Недостаточно привилегий!');
                $dispatcher->forward([
                    'controller' => 'errors',
                    'action' => 'show404'
                ]);
                return false;
            }
        }
    }
}