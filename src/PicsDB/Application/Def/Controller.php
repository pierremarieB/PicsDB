<?php

namespace PicsDB\Application\Def;

use PicsDB\Framework\View;
use PicsDB\Framework\Authentication\AuthenticationHTML;
use PicsDB\Framework\Authentication\AuthenticationManager;

class Controller
{
    protected $request;
    protected $response;
    protected $view;
    protected $am;

    public function __construct($request, $response, View $view)
    {
        $this->request = $request;
        $this->response = $response;
        $this->view = $view;
        $this->am = new AuthenticationManager($this->request);
    }

    public function showHomePage()
    {
        $this->view->makeHomePage();
    }

    public function showAboutPage()
    {
        $this->view->makeAboutPage();
    }

    public function showSignInPage($login, $pwd)
    {
        $this->view->makeSignInPage($login, $pwd);
    }

    public function showSignUpPage($login)
    {
        $this->view->makeSignUpPage($login);
    }

    public function loging()
    {        
        if (!empty($this->request->getParamPost('login')) && !empty($this->request->getParamPost('pwd'))) {
            $bool = $this->am->checkAuth($this->request->getParamPost('login'), $this->request->getParamPost('pwd'));
            if (!$bool) {
                $this->view->addPart('feedback', 'Incorrect username or password');
                $this->showSignInPage($this->request->getParamPost('login'), $this->request->getParamPost('pwd'));
            } else {
                //$this->view->addPart('feedback','Successfully connected as '.$this->request->getParamPost('login'));
                $this->showHomePage();
            }
        } else {
            $this->view->addPart('feedback', 'Username or password empty!');
            $this->showSignInPage($this->request->getParamPost('login'), $this->request->getParamPost('pwd'));
        }
    }

    public function register()
    {
        if (!empty($this->request->getParamPost('login')) && !empty($this->request->getParamPost('pwd'))) {
            if (!$this->am->checkUsername($this->request->getParamPost('login'))) {
                $this->view->addPart('feedback', 'Username already taken.');
                $this->showSignUpPage($this->request->getParamPost('login'));
            } elseif ($this->request->getParamPost('pwd') !== $this->request->getParamPost('secondPwd')) {
                $this->view->addPart('feedback', 'Wrong password confirmation.');
                $this->showSignUpPage($this->request->getParamPost('login'));
            } else {
                $this->am->addUser($this->request->getParamPost('login'), $this->request->getParamPost('pwd'));
                $this->loging();
            }
        } else {
            $this->view->addPart('feedback', 'Username or password empty!');
            $this->showSignUpPage($this->request->getParamPost('login'));
        }
    }

    public function disconnect()
    {
        $this->am->disconnect();
        $this->showHomePage();
    }

    public function execute($action, $id)
    {
        switch ($action) {
            case "":
                $this->showHomePage();
                break;
            case "about":
                $this->showAboutPage();
                break;
            case "signin":
                $this->showSignInPage(null, null);
                break;
            case "signup":
                $this->showSignUpPage(null);
                break;
            case "loging":
                $this->loging();
                break;
            case "register":
                $this->register();
                break;
            case "disconnect":
                $this->disconnect();
                break;
            default:
                $this->showHomePage();
                // no break
                default:
        }
    }
}
