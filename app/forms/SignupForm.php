<?php

namespace App\Form;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password;

use Phalcon\Validation\Validator\PresenceOf;

/**
 * Class SignupForm
 * @package App\Form
 */
class SignupForm extends Form
{

    public function initialize()
    {
        $this->setEntity($this);

        $name = new Text("name");
        $name->addValidator(new PresenceOf(array(
            'message' => 'Name is required'
        )));
        $name->setAttribute('class', 'form-control');

        $email = new Text("email");
        $email->addValidator(new PresenceOf(array(
            'message' => 'Email Address is required'
        )));
        $email->setAttribute('class', 'form-control');

        $password = new Password('password');
        $password->setAttribute('class', 'form-control');
        $password->addValidator(new PresenceOf(array(
            'message' => 'Password is required'
        )));

        $this->add($email);
        $this->add($password);
        $this->add($name);
    }

}
