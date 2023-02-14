<?php

namespace php\controller\user;

use php\controller\security\SecurityController;
use php\models\user\User;
use php\models\image\Image;

class UserController extends SecurityController {
    private $user;
    private $îmage;
    
    public function __construct() {
        $this -> user = new User();
        $this -> image = new Image();
    }
    // Chercher le contenu d'un utilisateur par ID et l'envoyer vers le JS.
    public function getUser() :void{
        if($this -> isConnect()) {
            $userdata = $this -> user -> searchUser($_SESSION['user']['ID_user']);
            if($userdata) {
                echo json_encode($userdata);
            }
            else {
                echo "<p> Une erreur SQL est survenue. </p>";
            }
        }
        else {
            echo "<p> Vous n'êtes pas connecté. </p>";
        }
    }
    // Fonction pour créer un nouveau compte utilisateur
    public function registerUser() :void{
        if (isset($_POST['pseudo']) && !empty($_POST['pseudo']) && isset($_POST['mail']) && !empty($_POST['mail']) && isset($_POST['password']) && !empty($_POST['password'])) {
            // Sécurité ? 
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $mail = htmlspecialchars($_POST['mail']);
            $password=password_hash(htmlspecialchars($_POST['password']), PASSWORD_DEFAULT);
            $checkmail = $this -> user -> searchMail($mail);
            $checkpseudo = $this -> user -> searchName($pseudo);
            // Une seule utilisation par email.
            if($checkmail != false) {
                $message = "Cette adresse mail est déjà utilisé.";
            }
            // Une seule utilisation d'un pseudo.
            else if($checkpseudo != false) {
                $message = "Ce pseudo est déjà utilisé.";
            }
            else {
                $test = $this -> user -> register($pseudo,$mail,$password);
                if ($test) {
                    $message = "Vous êtes désormais inscrit et pouvez vous connecter.";
                    header('location:index.php?action=login&confirm='.$message);
                }
                else {
                    echo "<p>Une erreur SQL est survenu.";
                }
            }
        }
        else if (isset($_POST['pseudo']) && empty($_POST['pseudo']) || isset($_POST['mail']) && empty($_POST['mail']) || isset($_POST['password']) && empty($_POST['password'])){
            echo "<p class='message_error'>Veuillez remplir tous les champs.</p>";
            $template = "php/views/user/register";
            require "php/views/layout.phtml";
        }
        else {
            $template = "php/views/user/register";
            require "php/views/layout.phtml";
        }
    }
    // Fonction pour connecter un utilisateur
    public function loginUser() :void{
        if($this -> isConnect()) {
            header('location:index.php?action=userpanel');
        }
        else {
            if (isset($_POST['mail']) && !empty($_POST['mail']) && isset($_POST['password']) && !empty($_POST['password'])) {
                $mail = htmlspecialchars($_POST['mail']);
                $check = $this -> user -> searchMail($mail);
                if($check) {
                    if(password_verify($_POST['password'], $check['password'])) {
                        $_SESSION['user']['pseudo'] = $check['pseudo'];
                        $_SESSION['user']['ID_user'] = $check['ID_user'];
                        $_SESSION['user']['avatar'] = $check['avatar'];
                        header('location:'.$_SERVER['HTTP_REFERER']);
                    }
                    else {
                        echo "<p class='message_error'>Adresse mail ou mot de passe incorrect.</p>";
                    }
                }
                else {
                    echo "<p class='message_error'>Adresse mail ou mot de passe incorrect.</p>";
                }
            }
            $template = "php/views/user/login";
            require "php/views/layout.phtml";
        }
    }
    // Fonction déconnexion
    public function logoutUser():void {
        if(isset($_SESSION['user'])) {
            $_SESSION['user'] = null;
            session_destroy();
        }
        session_destroy();
        header('location:'.$_SERVER['HTTP_REFERER']);
    }
    // Affichage du panneau utilisateur
    public function loadPanel() :void {
        if($this -> isConnect()) {
            $id = htmlspecialchars($_SESSION['user']['ID_user']);
            $images = $this -> image -> getImageByUser($id);
            $categorie = $this -> image -> getCategorie();
            $user = $this -> user -> searchUser($id);
            $stared = $this -> user -> loadImageStared($id);
            $followed = $this -> user -> loadFollowed($id);
            $countfollow = $this -> user -> countFollower($id);
            $countstar = $this -> user -> countStar($id);
            if(isset($_SESSION['user'])) {
                $template = "php/views/user/panel";
                require "php/views/layout.phtml";
            }
            else {
                header('location:index.php');
            }
        }
        else {
            header('location:index.php');
        }
    }
    // Affichage du contenu posté par un utilisateur
    public function displayUserContent() :void {
        $id = intval(htmlspecialchars($_GET['id']));
        $countfollow = $this -> user -> countFollower($id);
        $countstar = $this -> user -> countStar($id);
        $images = $this -> image -> getImageByUser($id);
        $template = "php/views/user/user";
        require "php/views/layout.phtml";
    }
    // Ajout d'un suivi d'utilisateur
    public function followUser() :void{
        if($this -> isConnect()) {
            $idsuivi = intval(htmlspecialchars($_GET['id']));
            $idsuiveur = intval(htmlspecialchars($_SESSION['user']['ID_user']));
            if($this -> user -> checkFollower($idsuivi,$idsuiveur)) {
                $this -> user -> deleteFollow($idsuivi,$idsuiveur);
                header('location:'.$_SERVER['HTTP_REFERER']);
            }
            else {
                $test = $this -> user -> addFollower($idsuivi, $idsuiveur);
                if($test) {
                    header('location:'.$_SERVER['HTTP_REFERER']);
                }
                else {
                    echo "<p> Une erreur SQL est survenu.";
                }
            }
        }
        else {
            header('location:index.php');
        }
    }
    // Charger les images de l'utilisateur connecté vers le JS
    public function getUserImage() :void{
        if($this -> isConnect()) {
            $id = htmlspecialchars($_SESSION['user']['ID_user']);
            $images = $this -> image -> getImageByUser($id);
            echo json_encode($images);
        }
    }
    // Modifier le mot de passe de l'utilisateur
    public function editPassword() :void{
        if($this -> isConnect()) {
            $id = htmlspecialchars($_SESSION['user']['ID_user']);
            $checkpass = $this -> getUser();
            if(password_verify($_POST['oldpass'], $checkpass['password'])) {
                if($_POST['password'] === $_POST['passwordconfirm']) {
                    $pass = password_hash(htmlspecialchars($_POST['password']), PASSWORD_DEFAULT);
                    $test = $this -> user -> setPassword($id,$pass);
                    if($test) {
                        $message = "Mot de passe modifié avec succès.";
                        header('location:index.php?action=userpanel&message='.$message);
                        
                    }
                    else {
                        echo "<p> Une erreur SQL est survenue.</p>";
                    }
                }
                else {
                    echo "<p> Les champs mots de passe ne correspondent pas.</p>";
                }
            }
            else {
                echo "<p> Mot de passe incorrect </p>";
            }
        }
        else {
            header('location:index.php?action=login');
        }
    }
    // Modifier les informations d'un utilisateur.
    public function editAccount() :void{
        if($this -> isConnect()) {
            if (isset($_POST['pseudo']) &&
                !empty($_POST['pseudo']) &&
                isset($_POST['mail']) &&
                !empty($_POST['mail'])
                ){
                $id = htmlspecialchars($_SESSION['user']['ID_user']);
                $pseudo = htmlspecialchars($_POST['pseudo']);
                $mail = htmlspecialchars($_POST['mail']);
                $detail = htmlspecialchars($_POST['detailutilisateur']);
                // Gestion de l'avatar, test si un avatar a été upload
                if(is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                    $image_direct = "img";
                    $tmp_name = htmlspecialchars($_FILES['avatar']['tmp_name']);
                    $image = htmlspecialchars($_FILES['avatar']['name']);
                    do {
                        $verifyimage = $this -> image -> checkExistingImage("$pseudo/$image");
                        if($verifyimage) {
                            $image = rand(0, 1000).$image;
                        }
                    }
                    while ($verifyimage);
                    if(mime_content_type($_FILES['avatar']['tmp_name']) === "image/jpg" || mime_content_type($_FILES['avatar']['tmp_name']) === "image/jpeg" || mime_content_type($_FILES['avatar']['tmp_name']) === "image/png" || mime_content_type($_FILES['avatar']['tmp_name']) === "image/gif") {
                        if(file_exists("$image_direct/$pseudo")) {
                            move_uploaded_file($tmp_name, "$image_direct/$pseudo/$image");
                        }
                        else {
                            mkdir("$image_direct/$pseudo",0777);
                            move_uploaded_file($tmp_name, "$image_direct/$pseudo/$image");
                        }
                        $avatar = "$pseudo/$image";
                    }
                }
                // Sinon, récupération de l'ancien.
                else {
                    $avatar = $_SESSION['user']['avatar'];
                }
                // Mise à jour du compte.
                $test = $this -> user -> setAccount($id,$pseudo,$mail,$detail,$avatar);
                if($test) {
                    $message = "Votre compte a été mis à jour.";
                    header('location:index.php?action=userpanel&message='.$message);
                }
                else {
                    echo "<p> une erreur SQL est survenue. </p>";
                }
            }
            else {
                echo "<p> Vous ne pouvez effacer votre pseudo et votre email. </p>";
            }
        }
        else {
            header('location:index.php?action=login');
        }
    }
}