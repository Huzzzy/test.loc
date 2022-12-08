<?php

namespace MyProject\Controllers;

use MyProject\Models\Users\UserActivationService;
use MyProject\Models\Users\UsersAuthService;
use MyProject\Services\EmailSender;
use MyProject\Models\Users\User;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\ActivateException;

class UsersController extends AbstractController
{
    public function signUp()
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }

            
        if ($user instanceof User) {
            $code = UserActivationService::createActivationCode($user);

            EmailSender::send($user, 'Активация', 'userActivation.php', [
                'userId' => $user->getId(),
                'code' => $code
            ]);

            $this->view->renderHtml('mail/userActivation.php',
                [
                    'user' => $user->getId(),
                    'code' => $code = UserActivationService::createActivationCode($user)
                ]);
            return;
        }
        }

        $this->view->renderHtml('users/signUp.php');
    }

    public function activate(int $userId, string $activationCode):void
    {
        try {
            $user = User::getById($userId);

            if ($user === null) {
                throw new ActivateException('Нет такого пользователя!');
            }

            $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);



            if ($user->getIsConfirmed()) {
                throw new ActivateException('Пользователь уже активирован!');
            }

            if ($isCodeValid) {
                $user->activate();
                UserActivationService::deleteActivationCode($userId);
                echo 'OK!';
            } else {
                throw new ActivateException('Код неправильный!');
            }
        } catch(ActivateException $e) {
            $this->view->renderHtml('errors/noId.php', ['error' => $e->getMessage()]);
            return;
        }
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