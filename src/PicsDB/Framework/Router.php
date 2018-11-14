<?php

namespace PicsDB\Framework;

use PicsDB\Application\Def\Controller;
use PicsDB\Application\Images\ImageController;
use PicsDB\Framework\Request;

class Router
{
    private $request;
    private $objet;
    private $action;
    private $url;
    private $controllerClass;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function main()
    {
        $this->action = $this->request->getParamGet("a");
        $this->accountAction = $this->request->getParamGet("account");
        $this->objet = $this->request->getParamGet("o");
        $this->url = $this->request->getParamGet("url");

        switch ($this->objet) {
            case "image":
                $this->controllerClass = "PicsDB\Application\Images\ImageController";
                break;
            default:
                $this->controllerClass = "PicsDB\Application\Def\Controller";
        }
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getAccountAction()
    {
        return $this->accountAction;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /* URL de la page d'accueil */
    public function getHomeURL()
    {
        return ".";
    }

    /* URL de la page de l'image d'identifiant $id */
    public function getImageURL($url)
    {
        return ".?o=image&a=affiche&url=$url";
    }

    public function getGalleryURL()
    {
        return ".?o=image&a=gallery";
    }

    public function getAboutURL()
    {
        return ".?a=about";
    }

    public function getSignInURL()
    {
        return ".?a=signin";
    }

    public function getSignUpURL()
    {
        return ".?a=signup";
    }

    public function getLogingURL()
    {
        return ".?a=loging";
    }

    public function getRegisterURL()
    {
        return ".?a=register";
    }

    public function getManageURL()
    {
        return ".?o=image&a=manage";
    }

    public function getEditURL($url)
    {
        return ".?o=image&a=metadata&url=$url";
    }

    public function getDeleteURL($url)
    {
        return ".?o=image&a=delete&url=$url";
    }

    public function getEditCompleteURL($url)
    {
        return ".?o=image&a=complete&url=$url";
    }

    public function getDisconnectURL()
    {
        return "?a=disconnect";
    }
}
