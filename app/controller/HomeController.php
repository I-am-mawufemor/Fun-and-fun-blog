<?php

namespace Mawufemor\Techandfun\Controller;

if(!defined('ROOT')){
    die("Direct access not allowed");
}

use PDO;
class HomeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index()
    {
        require_once ROOT . '/app/view/public/home.php';
    }
}

?>