<?php
namespace PicsDB\Application\Images;

use PicsDB\Application\Images\Image;
use PicsDB\Application\Images\ImageStorage;

class ImageStorageStub implements ImageStorage
{
    protected $db;

    public function __construct()
    {
        $this->db = array();

        $dirname = "images/";
        $images = glob($dirname."*.{jpg,jpeg,JPG,JPEG}", GLOB_BRACE);

        foreach ($images as $image) {
            $this->db[] = new Image($image);
        }
    }

    public function read($id)
    {
        if (key_exists($id, $this->db)) {
            return $this->db[$id];
        }
        return null;
    }

    public function readName($name)
    {
        foreach ($this->db as $image) {
            if (explode("/", $image->getURL())[1] === $name) {
                return $image;
            }
        }
        return null;
    }

    public function readAll()
    {
        return $this->db;
    }
}
