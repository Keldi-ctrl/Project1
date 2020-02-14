<?php
namespace Growave2\Forms;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;

class UserTimesForm extends Form
{
    public function initialize()
    {
        $time = new Text('time');
        $time->setLabel('time');
        $time->setFilters(['striptags', 'time']);
        $this->add($time);
        $hiden = new Hidden('time_id');
        $this->add($hiden);
    }

}