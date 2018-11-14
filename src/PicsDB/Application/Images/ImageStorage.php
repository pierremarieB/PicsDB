<?php

namespace PicsDB\Application\Images;

/* Interface représentant un système de stockage des images. */
interface ImageStorage
{
    /* Renvoie l'instance d'image correspondant à l'identifiant donné,
     * ou null s'il n'y en a pas. */
    public function read($id);

    /* Renvoie un tableau associatif id=>image avec toutes les images de la base. */
    public function readAll();
}
