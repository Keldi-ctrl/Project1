<?php
use Phalcon\Session\Manager;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;

class SignController extends Growave2\Controllers\ControllerBase
{

    public function initialize()
    {
        Phalcon\Tag::setTitle('Логин');
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
    public function indexAction ()
    {
        $this->view->form =  new \Growave2\Forms\SignForm();
    }

    public  function signInAction ()
    {
           $form = new \Growave2\Forms\SignForm();
            if ($this->request->isPost())
            {
                if ($form->isValid($this->request->getPost()))
                {
                    $dataFromPost = [
                        'email'    => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password'),
                    ];
                    $this->auth->check($dataFromPost);
                     $auth_user = $this->auth->getIdentity();

                    if (isset($auth_user))
                    {
                        $userRole = $this->auth->getRole();
                        $activeStatus = $this->auth->getActiveStatus();
                        if ($activeStatus == 'yes' && $userRole == "user")
                        {
                            $this->view->name = $this->auth->getName();
                            $this->response->redirect('time-tracker/team');
                        }
                        elseif($userRole == "admin" )
                        {
                            $this->response->redirect('admin/panel');
                        }
                        else
                            {
                                $this->flash->error('Обратитесь к админу!');
                                $this->_redirect('index','index');
                            }
                    }

                    else
                    {
                        $this->flash->error('Пользователь не существует!');
                        $form->clear();
                        $this->_redirect('sign','index');
                    }

                } else
                    {
                        foreach ($form->getMessages() as $message)
                        {
                            echo $message;
                        }
                        $form->clear();
                        $this->_redirect('sign','index');
                     }

            }
            else
            {
                $this->flash->error('Вы не авторизовались!');
                $this->_redirect('sign','index');
            }

    }

    public  function signOutAction ()
    {
        $this->auth->remove();
        $this->_redirect('sign','index');
    }


    public function changeAction()
    {
        $form = new \Growave2\Forms\ChangePasswordForm();
        $this->view->form = $form;
        $this->view->pick('sign/change-password');
    }

    public function changePasswordAction()
    {
       $form = new \Growave2\Forms\ChangePasswordForm();
            if (!$form->isValid($this->request->getPost()))
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
                $this->_redirect('time-tracker','team');
            } else {
                $user_id = $this->auth->getId();
                $user = Growave2\Models\Users::findFirst([
                    'conditions'=> 'id = :id:',
                    'bind' => [
                        'id' => $user_id,
                    ]
                ]);

                $user->password = $this->security->hash($this->request->getPost('password'));
                if ($user->update()) {
                    $this->flash->success('Успешно изменен пароль');
                    $this->response->redirect('/team');
                }
                else {
                    $this->flash->error('Не удалось поменять пароль');
                    $this->response->redirect('/team');
                }
            }
    }

}
?>