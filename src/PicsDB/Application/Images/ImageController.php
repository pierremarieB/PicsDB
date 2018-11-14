<?php

namespace PicsDB\Application\Images;

use PicsDB\Application\Images\ImageStorage;
use PicsDB\Framework\Request;
use PicsDB\Framework\Response;
use PicsDB\Framework\View;

class ImageController
{
    protected $request;
    protected $response;
    protected $view;
    protected $db;
    protected $supportedImage;

    public function __construct(Request $request, Response $response, View $view)
    {
        $this->request = $request;
        $this->response = $response;
        $this->view = $view;
        $this->db = new ImageStorageStub();

        $this->supportedImage = array(
            "jpg",
            "jpeg",
            "JPG",
            "JPEG"
        );
    }

    public function showImage($url)
    {
        $image = $this->db->readName($url);
        if ($image !== null) {
            /* L'image existe, on prépare la page */
            $this->view->makeImagePage($image);
        } else {
            $this->view->makeUnknownImagePage();
        }
    }

    public function showHomePage()
    {
        $this->view->makeHomePage();
    }

    public function showGalleryPage()
    {
        $imageArray = $this->db->readAll();

        $this->view->makeImageGalleryPage($imageArray);
    }

    public function uploadFile()
    {
        $uploaddir = 'images/';
        $uploadfile = $uploaddir . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);

        //On utilise le tag Creator pour se souvenir de l'uploader
        exec("exiftool -XMP-dc:Creator=\"".$_SESSION['user']['login']."\" $uploadfile");
    }

    public function showEditPage($url)
    {
        $image = $this->db->readName($url);
        $this->view->makeEditPage($image);
    }

    public function editComplete($url)
    {   
        $picname = $url;
        $arrayMetadata = array(
            "pictitle" => array("-XMP-dc:Title","-XMP-pdf:Title","-XMP-PixelLive"),
            "photographer" => array("-EXIF:Artist","-XMP-tiff:Artist","-XMP-xmpDM:Artist"),
            "description" => array("-EXIF:ImageDescription","-XMP-dc:Description","-EXIF:UserComment","-IPTC:Caption"),
            "copyright" => array("-EXIF:Copyright","-IPTC:Copyright","-XMP-dc:Rights"),
            "date" => array("-XMP-xmp:CreateDate","-XMP-dc:Date","-EXIF:DateTimeOriginal","-IPTC:DateCreated"),
            "software" => array("-EXIF:Software","-XMP-GDepth:Software","-XMP-GPano:CaptureSoftware","-XMP-tiff:Software"),
            "country" => array("-XMP-photoshop:Country","-IPTC:CountryName","-IPTC:Country"),
            "city" => array("-XMP-photoshop:City","-IPTC:City"),
            "latitude" => "-XMP-exif:GPSLatitude",
            "longitude" => "-XMP-exif:GPSLongitude"
        );
        $post = $this->request->getPost();
        foreach ($arrayMetadata as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    exec("exiftool ".$v."=\"".$post[$key]."\" images/$picname", $result, $error);
                }
            }
            //$tmp = "exiftool ".$value."='".$post[$key]."' images/".$post['picname'];
            //var_dump($tmp);
            else {
                exec("exiftool ".$value."=\"".$post[$key]."\" images/$picname", $result, $error);
            }
        }
        //var_dump($result);

        $dir = dirname(__DIR__)."/Images/cache/$picname.cache";
        //var_dump($dir);
        $this->deleteFile($dir);
        $image = new Image("images/$picname");

        $this->view->makeImagePage($image);
    }

    public function deleteFile($dir) {
        if (file_exists($dir)) {
           unlink($dir);
        }
    }

    public function deleteImage($url)
    {
        $dirCache = dirname(__DIR__)."/Images/cache/$url.cache";
        $dirImage = "images/$url";
        $dirImageOriginal = "images/$url"."_original";
        $this->deleteFile($dirCache);
        $this->deleteFile($dirImage);
        $this->deleteFile($dirImageOriginal);

        $imageArray = $this->db->readAll();
        $this->showHomePage();
    }

    public function showManagePage()
    {
        if (array_key_exists('user', $_SESSION)) 
        {
            $imageArray = $this->db->readAll();
            $finalArray = array();
            foreach ($imageArray as $key => $value) {
                if(array_key_exists('Creator',$value->getExif()) && $value->getExif()['Creator'] === $_SESSION['user']['login'])
                {
                    array_push($finalArray,$value);
                }
            }
                $this->view->makeManagePage($finalArray);
        }
    }

    public function execute($action, $url)
    {
        switch ($action) {
            case "affiche":
                $this->showImage($url);
                break;
            case "gallery":
                $this->showGalleryPage();
                break;
            case "upload":
                $this->uploadFile();
                break;
            case "metadata":
                $this->showEditPage($url);
                break;
            case "complete":
                $this->editComplete($url);
                break;
            case "delete":
                $this->deleteImage($url);
                break;
            case "manage":
                $this->showManagePage();
                break;
            default:
                $this->showHomePage();
                break;
        }
    }
}
