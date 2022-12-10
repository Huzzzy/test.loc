<?php

namespace MyProject\Controllers;



use MyProject\Services\Db;

class MainController extends AbstractController
{
    public function main()
    {
        $this->view->renderHtml('main/main.php', []);
    }
}