<?php
/**
 * Created by PhpStorm.
 * User: Probook
 * Date: 12.02.2020
 * Time: 7:27
 */

namespace Growave2\Forms;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Element;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;

class CheckBoxForm extends Form
{
    public function initialize()
    {
        $description =
            new Text(
                'description',
                [
                    'maxlength'   => 50,
                    'placeholder' => 'Type holiday',
                ]
        );
        $this->add($description);
        $description->clear();

        $controls = (new Check('checkBox',['value'=>'on','class'=>'form-control w-25 d-inline']))
            ->setLabel('Checkbox')
            ->setDefault('off');
        $this->add($controls);
        $controls->clear();
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
        $day =
            new Text(
                'day',
                [
                    'maxlength'   => 2,
                    'placeholder' => 'Type holiday',
                ]
            );
        $this->add($day);
        $day->clear();
    }
}