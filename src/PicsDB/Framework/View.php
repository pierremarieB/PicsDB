<?php

namespace PicsDB\Framework;

class View
{
    protected $router;
    protected $log_form;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->parts = array();
        $this->parts["title"] = null;
        $this->parts["content"] = null;
        $this->parts["feedback"] = null;

        $this->parts["twitterCard"] = "summary";
        $this->parts["twitterDescription"] = "This website is a picture database.";
        $this->parts["twitterImage"] = $_SERVER['SCRIPT_URI']."logo/db.png";

        //var_dump($_SERVER);
        $this->parts["url"] = $_SERVER['SCRIPT_URI'].$_SERVER['QUERY_STRING'];
        $this->parts["image"] = $_SERVER['SCRIPT_URI']."logo/db.png";
    }

    public function addPart($name, $content)
    {
        $this->parts[$name] = $content;
    }

    /* Affiche la page créée. */
    public function render()
    {
        if ($this->parts["title"] === null || $this->parts["content"] === null) {
            $this->makeUnexpectedErrorPage();
        }
        $parts = $this->parts;
        $menu = array(
            "Home" => $this->router->getHomeURL(),
            "Gallery" => $this->router->getGalleryURL(),
            "About" => $this->router->getAboutURL()
        );
        if (!array_key_exists('user', $_SESSION)) {
            $authMenu = array(
                "Sign in" => $this->router->getSignInURL(),
                "Sign up" => $this->router->getSignUpURL()
            );
        } else {
            $authMenu = array(
                "Manage (".$_SESSION['user']['login'].")" => $this->router->getManageURL(),
                "Disconnect" => $this->router->getDisconnectURL()
            );
        }
        include(__DIR__.DIRECTORY_SEPARATOR."template.php");
    }

    public static function htmlesc($str) 
    {
    	return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /******************************************************************************/
    /* Méthodes de génération des pages                                           */
    /******************************************************************************/

    public function makeHomePage()
    {
        $this->parts["title"] = "Welcome on Pics Database!";
        $this->parts["content"] .= "<div id='homepage'><p>This website allows every of our users to upload their pictures and modify their metadata!</p>";
        $this->parts["content"] .= "<p>You can't upload anything yet? Sign up now and you'll be able to!</p>";
        $this->parts["content"] .= "<img src='logo/db.png' alt='database'></div>";
    }

    public function makeImageGaleryElement($image)
    {
        $ptitle = $image->getTitle();
        $author = $image->getAuthor();
        $url = $image->getUrl();

        $res = '';
        $res .= "<a href='?o=image&a=affiche&url=".$image->getName()."'><li>\n";
        $res .= "<h4>".self::htmlesc($ptitle)."</h4>";
        $res .= "<img src=\"$url\" alt ='imageRepresentation'>";
        $res .= "</li></a> \n";
        return $res;
    }

    public function makeImageGalleryPage($imageArray)
    {
        $this->parts["title"] = "Gallery";
        $this->parts["content"] = "<h3 id='titleGallery'>Click on a picture to get additionnal informations</h3> \n";
        $this->parts["content"] .= "<div id='gallery'>\n";
        $this->parts["content"] .= "<ul> \n";
        foreach ($imageArray as $image) {
            $this->parts["content"] .= $this->makeImageGaleryElement($image);
        }

        $this->parts["content"] .= "</ul> \n";
        $this->parts["content"] .= "</div>\n";
    }

    public function insertIfExists($exif, $tag)
    {
        if (array_key_exists($tag, $exif)) {
            return $exif[$tag];
        }
        return "";
    }

    public function makeImagePage($image)
    {
        $ptitle = $image->getTitle();
        $url = $image->getUrl();
        $exif = $image->getExif();
        //var_dump($exif);

        $this->parts["title"] = "$ptitle";
        $this->parts["content"] = "<figure id=\"imagePage\">\n<img src=\"$url\" alt=\"pic\" /></figure>";

        $this->parts["content"] .= "<div id='manageAndShare'>";
        //var_dump($exif['Creator']);
        if (array_key_exists('Creator',$exif) && array_key_exists('user', $_SESSION) && $_SESSION['user']['login'] === $exif['Creator']) {
            $this->parts["content"] .= "<div id='managebuttons'><a href='".$this->router->getEditURL($image->getName())."'><button class='managebutton'>Edit picture</button></a>";
            $this->parts["content"] .= "<a href='".$this->router->getDeleteURL($image->getName())."'><button class='managebutton'>Delete picture</button></a></div>";
        }

        $this->parts["content"] .= "<div id='shareButtons'>";
        $this->parts["content"] .= '<a href="http://www.facebook.com/sharer.php?u='.$_SERVER['HTTP_REFERER'].'" target="_blank">
        <img src="logo/facebook.png" alt="Facebook" />
    	</a>';
        $this->parts["content"] .= '<a href="https://plus.google.com/share?url='.$_SERVER['HTTP_REFERER'].'" target="_blank">
        <img src="logo/google.png" alt="Google" />
    	</a>';
        $this->parts["content"] .= '<a href="http://reddit.com/submit?url='.$_SERVER['HTTP_REFERER'].'" target="_blank">
        <img src="logo/reddit.png" alt="Reddit" />
    	</a>';
        $this->parts["content"] .= '<a href="https://twitter.com/share?url='.$_SERVER['HTTP_REFERER'].'" target="_blank">
        <img src="logo/twitter.png" alt="Twitter" />
    	</a>';
        $this->parts["content"] .= "</div>";
        $this->parts["content"] .= "</div>";

        $this->parts["content"] .= "<div id=\"metadata\" itemscope itemtype=\"http://schema.org/Photograph\">";
        $this->parts["content"] .= "<p><u>Photographer:</u> <span itemprop='creator'>".self::htmlesc($this->insertIfExists($exif, 'Artist'))."</span></p>";
        $this->parts["content"] .= "<p><u>Description:</u> <span itemprop='description'>'".self::htmlesc($this->insertIfExists($exif, 'ImageDescription'))."</span></p>";
        $this->parts["content"] .= "<p><u>Copyright:</u> <span itemprop='copyrightHolder'>".self::htmlesc($this->insertIfExists($exif, 'Copyright'))."</span></p>";
        $this->parts["content"] .= "<p><u>Created in:</u> <span itemprop='dateCreated'>".self::htmlesc($this->insertIfExists($exif, 'CreateDate'))."</span></p>";
        $this->parts["content"] .= "<p><u>Software:</u> ".self::htmlesc($this->insertIfExists($exif, 'Software'))."</p>";
        $this->parts["content"] .= "<p><u>Country - City:</u> <span itemprop='contentLocation'>".self::htmlesc($this->insertIfExists($exif, 'Country'))." - ".self::htmlesc($this->insertIfExists($exif, 'City'))."</span></p>";
        $this->parts["content"] .= "<p><u>Uploaded by:</u> <span itemprop='contributor'>".self::htmlesc($this->insertIfExists($exif, 'Creator'))."</span></p>";
        
        //GOOGLE MAP
        if (array_key_exists('GPSLatitude', $exif) && array_key_exists('GPSLongitude', $exif)) {
            $this->parts["content"] .= "<p id='clickable'><u>Localisation</u> <bold>[-]</bold></p>";
            $this->parts["content"] .= '<div id ="map"></div>';
            $this->parts["content"] .= '<script>var exif = '. json_encode($exif) .';</script>';
            $this->parts["content"] .= '<script type="text/javascript" src="js/map.js"></script>';
            $this->parts["content"] .= '<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDIdbDEs4PjayTOvMKVzPp1Qv-8bAw8fA&callback=initMap" async defer></script>';
        } else {
            $this->parts["content"] .= "<p><u>Localisation:</u> non disponible.</p>";
        }

        $this->parts["content"] .= "</div>";

        //TWITTER CARD
        $this->parts["twitterCard"] = "summary_large_image";
        $this->parts["twitterDescription"] = self::htmlesc($this->insertIfExists($exif, 'ImageDescription'));
        $this->parts["twitterImage"] = $_SERVER['SCRIPT_URI'].$url;
        //OPEN GRAPH
        $this->parts["url"] = $_SERVER['HTTP_REFERER'];
        $this->parts["image"] = $_SERVER['SCRIPT_URI'].$url;
    }

    public function makeSignInPage($login, $pwd)
    {
        $this->parts["title"] = 'Sign in';

        $this->parts["content"] = "<form action='".$this->router->getLogingURL()."' method='POST' id='sign'>";

        $this->parts["content"] .= "<label> Username* : </label>";
        $this->parts["content"] .= "<input type='text' name='login' value='$login'>";

        $this->parts["content"] .= "<label> Password* : </label>";
        $this->parts["content"] .= "<input type='password' name='pwd' value='$pwd'>";

        $this->parts["content"] .= "<button type='submit'>Log in !</button>";

        $this->parts["content"] .= "</form>";
    }

    public function makeSignUpPage($login)
    {
        $this->parts["title"] = "Sign up";
        $this->parts["content"] = "<form action='".$this->router->getRegisterURL()."' method='POST' id='sign'>";
        $this->parts["content"] .= "<label> Username* : </label>";
        $this->parts["content"] .= "<input type='text' name='login' value='$login'>";
        $this->parts["content"] .= "<label> Password* : </label>";
        $this->parts["content"] .= "<input type='password' name='pwd'>";
        $this->parts["content"] .= "<label> Confirm your password* : </label>";
        $this->parts["content"] .= "<input type='password' name='secondPwd'>";
        $this->parts["content"] .= "<button type='submit'>Register !</button>";
        $this->parts["content"] .= "</form>";
    }

    public function makeManagePage($imageArray)
    {
        $this->parts["title"] = "Manage your account (".$_SESSION['user']['login'].")";
        $this->parts["content"] = "<div id='manage'>";
        $this->parts["content"] .= "<label id='fileContainer'>Upload your file...";
        $this->parts["content"] .= "<input type='file' id='upload'></label>";
        $this->parts["content"] .= "<input type='button' id='confirm' value='Confirm'>";
        $this->parts["content"] .= "</div>";
        $this->parts["content"] .= "<progress value='0' min='0' max='100'></progress>";
        $this->parts["content"] .= "<script type='text/javascript' src='js/upload.js'></script>";
        $this->parts["content"] .= "<hr>";
        $this->parts["content"] .= "<div id='gallery'>\n";
        $this->parts["content"] .= "<ul> \n";
        foreach ($imageArray as $image) {
            $this->parts["content"] .= $this->makeImageGaleryElement($image, array_search($image, $imageArray));
        }

        $this->parts["content"] .= "</ul> \n";
        $this->parts["content"] .= "</div>\n";

    }

    public function makeEditPage($image)
    {
        $url = $image->getUrl();
        $exif = $image->getExif();
        // WEB COMPONENT HERE
        $this->parts["title"] = "Edit metadata";
        $this->parts["content"] = "<figure id=\"imagePage\">\n<img src=\"$url\" alt=\"pic\" /></figure>";
        $this->parts["content"] .= "<form id='metadata' action='".$this->router->getEditCompleteURL($image->getName())."' method='POST'>";
        $this->parts["content"] .= "<label>Titre: </label>";
        $this->parts["content"] .= "<input type='text' id='pictitle' name='pictitle' value='".self::htmlesc($this->insertIfExists($exif, 'Title'))."'>";
        $this->parts["content"] .= "<label>Photographer: </label>";
        $this->parts["content"] .= "<input type='text' id='photographer' name='photographer' value='".self::htmlesc($this->insertIfExists($exif, 'Artist'))."'>";
        $this->parts["content"] .= "<label>Description: </label>";
        $this->parts["content"] .= "<input type='text' id='description' name='description' value='".self::htmlesc($this->insertIfExists($exif, 'ImageDescription'))."'>";
        $this->parts["content"] .= "<label>Copyright: </label>";
        $this->parts["content"] .= "<input type='text' id='copyright' name='copyright' value='".self::htmlesc($this->insertIfExists($exif, 'Copyright'))."'>";
        $this->parts["content"] .= "<label>Date: </label>";
        $this->parts["content"] .= "<input type='text' id='date' name='date' value='".self::htmlesc($this->insertIfExists($exif, 'CreateDate'))."'>";
        $this->parts["content"] .= "<label>Software: </label>";
        $this->parts["content"] .= "<input type='text' id='software' name='software' value='".self::htmlesc($this->insertIfExists($exif, 'Software'))."'>";
        $this->parts["content"] .= "<label>Country: </label>";
        $this->parts["content"] .= "<input type='text' id='country' name='country' value='".self::htmlesc($this->insertIfExists($exif, 'Country'))."'>";
        $this->parts["content"] .= "<label>City: </label>";
        $this->parts["content"] .= "<input type='text' id='city' name='city' value='".self::htmlesc($this->insertIfExists($exif, 'City'))."'>";
        $this->parts["content"] .= "<label>Latitude (DMS format, example: 32 deg 25' 21.43\" N): </label>";
        $this->parts["content"] .= "<input type='text' id='latitude' name='latitude' value='".self::htmlesc($this->insertIfExists($exif, 'GPSLatitude'))."'>";
        $this->parts["content"] .= "<label>Longitude (DMS format, example: 32 deg 25' 21.43\" W): </label>";
        $this->parts["content"] .= "<input type='text' id='longitude' name='longitude' value='".self::htmlesc($this->insertIfExists($exif, 'GPSLongitude'))."'>";
        $this->parts["content"] .= "<button type='submit'>Submit !</button>";
        $this->parts["content"] .= "</form>";
    }

    public function makeAboutPage()
    {
        $this->parts["title"] = 'About';
        $this->parts["content"] = '<p id="realized">Cette page a été réalisée par BRIEDA Pierre-Marie (21404557) et GRAVOUILLE Alexandre (21400905).</p>';
        $this->parts["content"] .= '<h3 class="about">Remarques sur la réalisation du site</h3>';
        $this->parts["content"] .= '<ul><li>La sécurité du site internet n\'étant pas le but premier de ce projet, elle laisse grandement à désirer.</li>';
        $this->parts["content"] .= '<li>Les comptes utilisateurs sont stockés dans un fichier "auth.txt" (login + password). Il n\'y a aucune autre forme de base de données. On se sert du tag XMP-dc "Creator" afin de stocker le login de l\'uploader (on prenant soin de conserver le nom de l\'artiste dans les autres métadonnées). Ceci permet d\'afficher les images que l\'utilisateur a upload dans l\'onglet "Manage".</li>';
        $this->parts["content"] .= '<li>Les données opengraph et twittercards sont toujours les mêmes si on ne se trouve pas sur la page d\'une image précise. Les microdata sont incluses sur les pages respectives des images.</li>';
        $this->parts["content"] .= '<li>Le design du site est responsive. Néanmoins, il faut noter qu\'il commence à être moins bon sur des écrans de téléphone de moins de 5".</li></ul>';

        $this->parts["content"] .= '<h3 class="about">Améliorations</h3>';
		$this->parts["content"] .= '<ul><li><u>Caching des métadonnées:</u> lors de la création d\'un objet Image, s\'il n\'existe pas encore de fichier cache, on créé un fichier du type "nomImage.cache" qui contient le tableau sérialisé des métadonnées. Si le fichier existe, on dé-sérialise son contenu afin de récupérer le tableau.</li>';
        $this->parts["content"] .= '<li><u>Gestion de la cohérence des métédonnées:</u> lors de la modification des métadonnées d\'une image, on insère les informations dans tous les types de métadonnées (XMP, IPTC, EXIF).</li></ul>';

        $this->parts["content"] .= '<h3 class="about">Comptes utilisateurs pour la correction</h3>';
        $this->parts["content"] .= '<ul><li>Login: lecarpentier / Mot de passe: jeanmarc</li>';
        $this->parts["content"] .= '<li>Login: niveau / Mot de passe: alexandre</li></ul>';
        //photos affichées dans la gallerie par ordre alphabétique
    }

    public function makeUnknownImagePage()
    {
        $this->parts["title"] = "Erreur";
        $this->parts["content"] = "L'image demandée n'existe pas.";
    }

    public function makeUnexpectedErrorPage()
    {
        $this->parts["title"] = "Erreur";
        $this->parts["content"] = "Une erreur inattendue s'est produite.";
    }
}
