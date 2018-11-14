<?php

namespace Alexandre10;

use PicsDB\Framework\FrontController;
use PicsDB\Framework\Request;
use PicsDB\Framework\Response;

spl_autoload_register(function ($className) {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    $file = ("src/".$className.".php");
    include $file;
});

session_start();


$frontController = new FrontController(new Request($_GET, $_POST, $_FILES, $_SERVER, $_SESSION), new Response());
$frontController->execute();
