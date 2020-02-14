<?php
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Url;

class IndexController extends Growave2\Controllers\ControllerBase
{
    public function indexAction()
    {
        Phalcon\Tag::setTitle('Главная');
        // Shows only the view related to the action
        $this->view->setRenderLevel(
            View::LEVEL_ACTION_VIEW
        );
    }
    public function aboutAction()
    {
        Phalcon\Tag::setTitle('About');
        $this->view->setLayout('about');
    }


}

