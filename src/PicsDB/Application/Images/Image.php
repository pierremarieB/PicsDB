<?php

namespace PicsDB\Application\Images;

/* Représente un poème. */
class Image
{
    protected $title;
    protected $url;
    protected $author;
    protected $exif;

    public function __construct($url)
    {
        $this->url = $url;
        $this->createMetadata();

        if (array_key_exists('Title', $this->exif)) {
            $this->title = $this->exif['Title'];
        } else {
            $this->title = 'Unspecified';
        }
    }

    /* Renvoie le titre de l'image */
    public function getTitle()
    {
        return $this->title;
    }

    /* Renvoie le nom du fichier contenant l'image */
    public function getUrl()
    {
        return $this->url;
    }

    public function getName()
    {
    	return explode("/",$this->url)[1];
    }

    /* Renvoie le nom de l'auteur */
    public function getAuthor()
    {
        return $this->author;
    }

    /* Renvoie le tableau des metadonnées */
    public function getExif()
    {
        return $this->exif;
    }

    /* Va chercher les metadonnées dans l'image et les met en cache */
    public function createMetadata()
    {
        $dir = dirname(__DIR__)."/Images/cache/".explode("/", $this->url)[1].".cache";
        if (!file_exists($dir)) {
            $cmd = exec("exiftool ".$this->url, $result);

            $result2 = array();
            foreach ($result as $value) {
                $tmp = preg_split("#\s*\:\s*#", $value);
                $result2[str_replace(" ", "", $tmp[0])] = $tmp[1];
            }
            $this->exif = $result2;
            file_put_contents($dir, serialize($this->exif));
        } else {
            $this->exif = unserialize(file_get_contents($dir));
        }
    }
}
