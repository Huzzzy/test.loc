<?php

namespace MyProject\Models\Users;

use MyProject\Models\ActiveRecordEntity;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Services\Db;

class User extends ActiveRecordEntity
{
    /** @var string */
    protected $login;

    /** @var string */
    protected $email;

    /** @var string */
    protected $name;


    /** @var string */
    protected $passwordHash;

    /** @var string */
    protected $authToken;



    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    protected static function getTableName(): string
    {
        return 'users';
    }


    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }


    public static function signUp(array $userData)
    {
        //NICKNAME
        if (empty($userData['login'])) {
            throw new InvalidArgumentException('Не передан логин');
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $userData['login'])) {
            throw new InvalidArgumentException('логин может состоять только из символов латинского алфавита и цифр');
        }


        //Name
        if (empty($userData['name'])) {
            throw new InvalidArgumentException('Не передано имя');
        }
//        if (static::findOneByColumn('login', $userData['login']) !== null) {
//            throw new InvalidArgumentException('Пользователь с таким nickname уже существует');
//        }
    
        
        //EMAIL
        if (empty($userData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email некорректен');
        }
//        if (static::findOneByColumn('email', $userData['email']) !== null) {
//            throw new InvalidArgumentException('Пользователь с таким email уже существует');
//        }
        
        //PASSWORD
        if (empty($userData['password'])) {
            throw new InvalidArgumentException('Не передан пароль');
        }
        if (mb_strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
        }
        if (($userData['password']) !== ($userData['confirm_password'])) {
            throw new InvalidArgumentException('Пароли не совпадают');
        }


        //После всех проверок создаем пользователя
        $user = new User();
        $user->id = uniqid();
        $user->login = $userData['login'];
        $user->email = $userData['email'];
        $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->name = $userData['name'];
        $user->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
        $user->save();

        return $user;
    }

    public static function login(array $loginData): User
    {
        //$user = User::getById( );

        if (empty($loginData['login'])) {
            throw new InvalidArgumentException('Логин');
        }

        if (empty($loginData['password'])) {
            throw new InvalidArgumentException('Не передан пароль');
        }

//        $user = User::findOneByColumn('email', $loginData['email']);
//        if ($user === null) {
//            throw new InvalidArgumentException('Нет пользователя с таким email');
//        }

        if (!password_verify($loginData['password'], $user->getPasswordHash())) {
            throw new InvalidArgumentException('Неправильный пароль');
        }

        $user->refreshAuthToken();
        $user->refresh();

        return $user;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    private function refreshAuthToken()
    {
        $this->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
    }
}
