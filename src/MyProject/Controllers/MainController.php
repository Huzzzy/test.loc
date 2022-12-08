<?php

namespace MyProject\Controllers;



class MainController extends AbstractController
{
    public function main()
    {

        $this->view->renderHtml('main/main.php', []);
    }
}