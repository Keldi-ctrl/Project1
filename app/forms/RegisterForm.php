<?php
namespace Growave2\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Element;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;


class RegisterForm extends Form
{
    public function initialize()
    {

        // First_name
        $name = new Text('name');
        $name->setLabel('Your Full Name');
        $name->addValidators([
            new PresenceOf([
                'message' => 'Name is required'
            ])
        ]);
        $this->add($name);
        $name->clear();

        // Email
        $email = new Text('email');
        $email->setLabel('E-Mail');
        $email->setFilters('email');
        $email->addValidators([
            new PresenceOf([
                'message' => 'E-mail is required'
            ]),
            new Email([
                'message' => 'E-mail is not valid, So sorry. Please, try again!'
            ])
        ]);
        $this->add($email);
        $email->clear();

        //Role
        $role_id =
            new Select(
                'role_id',
                [
                    'guest' => 'Выберите роль пользователя',
                    'user'  => 'User',
                    'admin' => 'Super User'
                ]
            );
        $role_id->addValidators([
            new PresenceOf([
                'message' => 'Role is required'
            ]),
        ]);
        $this->add($role_id);
        $role_id->clear();

        //Status
        $activeStatus =
            new Select(
                'activateUser',
                [
                    'yes' => 'Активировать',
                    'no'  => 'Диактивировать',
                    'banned'  => 'Забанить',
                ]
            );
        $activeStatus->addValidators([
            new PresenceOf([
                'message' => 'status is required'
            ]),
        ]);
        $this->add($activeStatus);
        $activeStatus->clear();


        // Password
        $password = new Password('newPassword');
        $password->setLabel('newPassword');
        $password->addValidators([
            new PresenceOf([
                'message' => 'Password is required'
            ]),
            new StringLength([
                'min' => 5,
                'messageMinimum' => 'Password is too short. Minimum 5 characters'
            ]),
            new Confirmation([
                'message' => 'Пароли не совпали',
                'with' => 'repeatPassword'
            ])
        ]);
        $this->add($password);
        $password->clear();

        // Confirm Password
        $confirmPassword = new Password('repeatPassword');
        $confirmPassword->setLabel('Repeat password');
        $confirmPassword->addValidators([
            new PresenceOf([
                'message' => 'The confirmation password is rexquired'
            ])
        ]);
        $this->add($confirmPassword);
        $confirmPassword->clear();
    }

}