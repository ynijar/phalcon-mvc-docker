<?php

namespace App\Controllers;

use App\Form\LoginForm;
use App\Form\SignupForm;
use App\Models\User;

/**
 * Class IndexController
 * @package App\Controllers
 */
class IndexController extends ControllerBase
{
    /**
     * @Get("/")
     * @Get("/index")
     * @Post("/")
     */
    public function indexAction()
    {
        $form = new LoginForm();

        if ($this->request->isPost()) {

            if (!$form->isValid($this->request->getPost())) {

                // If the form failed validation, add the errors to the flash error message.
                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message->getMessage());
                }

                return true;
            }

            $user = User::findFirstByEmail($form->email);

            if(!$user) {
                $this->flash->error('User not found');
                return true;
            }

            if (!$this->security->checkHash($form->password, $user->password)) {
                $this->flash->error('Wrong password!');
                return true;
            }

            $user->role = 'User';

            $this->session->set('auth', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'User'
            ]);

            $this->flash->success('Welcome ' . $user->name);

            $this->dispatcher->forward(
                [
                    'controller' => 'admin',
                    'action' => 'index',
                ]
            );
        }

        return true;
    }

    /**
     * @Get("/signup")
     * @Post("/signup")
     */
    public function signupAction()
    {
        $form = new SignupForm();

        if ($this->request->isPost()) {

            if (!$form->isValid($this->request->getPost())) {

                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message->getMessage());
                }

                return true;
            }

            $user = User::findFirstByEmail($form->email);

            if($user) {
                $this->flash->error('User already exist!');
                return true;
            }

            $password = $this->security->hash($form->password);
            $user = new User();
            $user->email = $form->email;
            $user->name = $form->name;
            $user->password = $password;

            $user->save();
            $this->response->redirect("/");
        }

        return true;
    }

}

