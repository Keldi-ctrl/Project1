<?php
namespace Growave2\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Element;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;


class SignForm extends Form
{
    public function initialize()
    {

        // Email
        $email = new Text('email');
        $email->setLabel('E-Mail');
        $email->setFilters('email');
        $email->addValidators([
            new PresenceOf([
                'message' => 'Поля для логина была пуста!'
            ]),
            new Email([
                'message' => 'Неверный логин!'
            ])
        ]);
        $this->add($email);

        $this->add(
            new Select(
                'role_id',
                [
                    '0' => 'Выберите роль пользователя',
                    '1' => 'Super User',
                    '2'  => 'User',
                ]
            )
        );


        //
        $password = new Password('password');
        $password->setLabel('password');
        $password->addValidators([
            new PresenceOf([
                'message' => 'Password is required'
            ]),
            new StringLength([
                'min' => 5,
                'messageMinimum' => 'Password is too short. Minimum 5 characters'
            ])
        ]);
        $this->add($password);

    }

}