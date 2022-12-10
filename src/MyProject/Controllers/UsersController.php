<?php

namespace MyProject\Controllers;

use MyProject\Models\Users\UsersAuthService;
use MyProject\Models\Users\User;
use MyProject\Exceptions\InvalidArgumentException;

class UsersController extends AbstractController
{
    public function signUp():void
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }
        }

        if ($user instanceof User) {
            $this->view->renderHtml('main/main.php', []);
            return;
        }

        $this->view->renderHtml('users/signUp.php');
    }

    public function login()
    {
        if (!empty($_COOKIE)) {
            header('Location: /');
            exit;
        }
        if (!empty($_POST)) {
            try {
                $user = User::login($_POST);
                UsersAuthService::createToken($user);
                header('Location: /');
                exit();            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/login.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('users/login.php');
    }

    public function logOut()
    {
        UsersAuthService::deleteToken();
        header('Location: /');
    }
}