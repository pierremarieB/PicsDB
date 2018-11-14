<?php

namespace PicsDB\Framework;

class Request
{
    protected $get;
    protected $post;
    protected $files;
    protected $server;
    protected $session;

    public function __construct($get, $post, $files, $server, $session)
    {
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->server = $server;
        $this->session = $session;
    }

    public function getParamGet($nom)
    {
        return ((key_exists($nom, $this->get))? $this->get[$nom] : "");
    }

    public function getParamPost($nom)
    {
        return ((key_exists($nom, $this->post))? $this->post[$nom] : "");
    }

    public function getPost()
    {
        return $this->post;
    }

    public function getCurrentRequestURI()
    {
        return $this->server['REQUEST_URI'];
    }

    public function synchronizeSession($array)
    {
        $_SESSION = $array;
    }

    public function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
