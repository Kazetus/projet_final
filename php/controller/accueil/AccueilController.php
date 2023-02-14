<?php

namespace php\controller\accueil;

use php\controller\security\SecurityController;
use php\models\image\Image;

class AccueilController extends SecurityController {
    private $accueil;
    
    public function __construct() {
        $this -> accueil = new Image();
    }
    // Chargement de la page d'accueil.
    public function loadAccueil() {
        if($this -> isConnect()) {
            $followers = $this -> accueil -> loadUserFollowedImage(htmlspecialchars($_SESSION['user']['ID_user']));
        }
        $categories = $this -> accueil -> getCategorie();
        $images = $this -> accueil -> getImage();
        $lastcomment = $this -> accueil -> getLastsComments();
        $template = "php/views/accueil/accueil";
        require "php/views/layout.phtml";
    }
}