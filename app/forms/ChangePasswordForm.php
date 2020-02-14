<?php
namespace Growave2\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

class ChangePasswordForm extends Form
{

    public function initialize()
    {
        // Password
        $password = new Password('password');
        $password->setLabel('Password');
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
                'with' => 'confirmPassword'
            ])
        ]);
        $this->add($password);

        // Confirm Password
        $confirmPassword = new Password('confirmPassword');
        $confirmPassword->setLabel('Confirm Password');
        $confirmPassword->addValidators([
            new PresenceOf([
                'message' => 'The confirmation password is required'
            ])
        ]);
        $this->add($confirmPassword);
    }
}