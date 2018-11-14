<?php

namespace PicsDB\Framework\Authentication;

use PicsDB\Framework\Request;

class AuthenticationManager
{
    private $users;
    private $request;
    private $auth;
    private $isConnected;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->auth = array();
    }

    public function checkAuth($login, $pwd)
    {
        $fileArray = file(dirname(__DIR__)."/Authentication/auth.txt");
        for ($lineCounter = 0; $lineCounter < count($fileArray); $lineCounter++) {
            $exploadedLine = explode("\t", $fileArray[$lineCounter]);
            if ((isset($exploadedLine[0]) && trim($exploadedLine[0]) === $login) && (isset($exploadedLine[1]) && trim($exploadedLine[1]) === $pwd)) {
                $this->auth["user"]["login"] = $login;
                $this->request->synchronizeSession($this->auth);
                return true;
            }
        }
        return false;
    }

    public function checkUsername($login)
    {
        $fileArray = file(dirname(__DIR__)."/Authentication/auth.txt");
        var_dump(dirname(__DIR__)."/Authentication/auth.txt");
        for ($lineCounter = 0; $lineCounter < count($fileArray); $lineCounter++) {
            $exploadedLine = explode("\t", $fileArray[$lineCounter]);
            if ((isset($exploadedLine[0]) && trim($exploadedLine[0]) === $login)) {
                return false;
            }
        }
        return true;
    }

    public function disconnect()
    {
        unset($_SESSION["user"]);
    }

    public function isConnected()
    {
        return !empty($_SESSION["user"]["login"]);
    }

    public function addUser($login, $pwd)
    {
        file_put_contents(dirname(__DIR__)."/Authentication/auth.txt", $login."\t".$pwd."\n", FILE_APPEND);
    }
}
