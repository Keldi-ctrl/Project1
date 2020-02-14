<?php
namespace Growave2\Forms;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Element;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Hidden;

class SelectDateForm extends Form
{
    public function initialize()
    {


        $this->add(
            new Select(
                'year',
                [
                    '2020' => '2020',
                    '2019' => '2019',
                    '2018'  => '2018',
                ]
            )
        );
        $this->add(
            new Select(
                'month',
                [
                    '1' => 'Январь',
                    '2'  => 'Февраль',
                    '3'  => 'Март',
                    '4'  => 'Апрель',
                    '5'  => 'Май',
                    '6'  => 'Июнь',
                    '7'  => 'Июль',
                    '8'  => 'Август',
                    '9'  => 'Сентябрь',
                    '10'  => 'Октябрь',
                    '11'  => 'Ноябрь',
                    '12'  => 'Декабрь',
                ]
            )
        );
        $hiden = new Hidden('hiddenUserId');
        $this->add($hiden);
    }

}