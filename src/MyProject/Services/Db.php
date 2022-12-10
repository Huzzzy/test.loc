<?php

namespace MyProject\Services;

use MyProject\Exceptions\DbException;
use Jajo\JSONDB;

class Db
{
    private $json_db;

    private static $instance;

    private function __construct()
    {
        $this->json_db = new JSONDB(__DIR__ . '/../../../config/');
    }

    public function insertToDb($data)
    {
        $this->json_db->insert('users.json', $data);
    }

    public function getUserId(int $id)
    {
        return $this->json_db->select('id')
            ->from('users.json')
            ->where(['id' => $id])
            ->get();
    }



    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}