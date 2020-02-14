<?php

use Phalcon\Http\Request;

class RegisterController extends Growave2\Controllers\ControllerBase
{
    public function initialize()
    {
        //$this->view->setLayout('index');
    }
    private function _redirect($controller,$method)
    {
        $this->dispatcher->forward(
            [
                'controller' => $controller,
                'action' => $method,
            ]
        );
    }
    public function indexAction()
    {
        $form = new \Growave2\Forms\SignForm();
        if ($form->isValid( $this->request->getPost()))
        {
            $user = new \Growave2\Models\Users();
            $user->first_name = $this->request->getPost('first_name');
            $user->email = $this->request->getPost('email');
            $user->password = $this->security->hash($this->request->getPost('password'));
            $user->role_id = $this->request->getPost('role_id');
            $user->active = $this->request->getPost('activeStatus');

            if ($user->save())
            {
                $this->flash->success('Вы успешно добавили пользователя ');
                $this->_redirect('admin','create');
            }
            else
            {
                $this->flash->success('Что то пошло не так');
                $this->_redirect('admin', 'create');
                $form->clear();
            }
        }
        else
        {
            foreach ($form->getMessages() as $message)
            {
                $this->flash->error($message);
            }
            $this->_redirect('admin', 'create');
            $form->clear();
        }
    }
}


?>