<?php

namespace php\controller\image;

use php\controller\security\SecurityController;
use php\models\image\Image;
use php\models\comment\Comment;

class ImageController extends SecurityController {
    private $image;
    private $comment;
    
    public function __construct() {
        $this -> image = new Image();
        $this -> comment = new Comment();
    }
    // charger les images pour JS.
    public function loadImage() :void{
        $images = $this -> image -> getImage();
        echo json_encode($images);
    }
    public function uploadImage() :void{
        if (isset($_POST['title']) &&
            !empty($_POST['title']) &&
            isset($_POST['text']) &&
            !empty($_POST['text']) &&
            isset($_POST['details']) &&
            !empty($_POST['details']) &&
            $this -> isConnect()
            ) {
            if(is_uploaded_file($_FILES['image']['tmp_name'])) {
                $image_direct = "img";
                $tmp_name = htmlspecialchars($_FILES['image']['tmp_name']);
                $image = htmlspecialchars($_FILES['image']['name']);
                $username = htmlspecialchars($_SESSION['user']['pseudo']);
                // Génération d'un nouveau nom si l'image existe déjà.
                do {
                    $verifyimage = $this -> image -> checkExistingImage("$username/$image");
                    if($verifyimage) {
                        $image = rand(0, 1000).$image;
                    }
                }
                while ($verifyimage);
                // Test pour vérifier que le fichier image est bien une image.
                if(mime_content_type($_FILES['image']['tmp_name']) === "image/jpeg" || mime_content_type($_FILES['image']['tmp_name']) === "image/jpg" || mime_content_type($_FILES['image']['tmp_name']) === "image/png" || mime_content_type($_FILES['image']['tmp_name']) === "image/gif") {
                    if(file_exists("$image_direct/$username")) {
                        move_uploaded_file($tmp_name, "$image_direct/$username/$image");
                    }
                    else {
                        mkdir("$image_direct/$username",0777);
                        move_uploaded_file($tmp_name, "$image_direct/$username/$image");
                    }
                    // sécurité ? 
                    $title = htmlspecialchars($_POST['title']);
                    $text = htmlspecialchars($_POST['text']);
                    $tag = htmlspecialchars($_POST['details']);
                    $user = htmlspecialchars($_SESSION['user']['ID_user']);
                    $test = $this -> image -> sendImage($user,"$username/$image",$title,$text,$_POST['categorie'],$tag);
                    if($test) {
                        $message = "Votre image a été téléversée et est disponible dans votre espace.";
                        header('location:index.php?action=userpanel&confirm='.$message);
                    }
                    else {
                        echo "<p> Une erreur est survenue. </p>";
                    }
                }
            }
            else {
                // Fichier trop lourd.
                if($_FILES['image']['size'] === 0) {
                    echo "<p> Votre fichier est trop lourd. Max : 2MO.</p>";
                }
                else {
                echo "<p>Aucun fichier n'a été envoyé.</p>";
                }
            }
        }
    }
    // Affichage des images dans l'accueil.
    public function displayImage() :void{
        if($_GET['id']) {
        $displayed = $this -> image -> loadImageById($_GET['id']);              
        $displaycomments = $this -> comment -> loadCommentByImage($_GET['id']); 
        $template = "php/views/image/image";
        require "php/views/layout.phtml";
        }
        else {
            header('location:index.php');
        }
    }
    // Ajouter un commentaire
    public function addComment() :void{
        if(isset($_POST['comment']) && !empty($_POST['comment']) && $this -> isConnect()) {
            $idimage = $_POST['idimage'];
            $iduser = $_SESSION['user']['ID_user'];
            $comment = htmlspecialchars($_POST['comment']);
            $test = $this -> comment -> insertComment($idimage,$iduser,$comment);
            if($test) {
                header('location:index.php?action=image&id='.$idimage);
            }
            else {
                http_response_code(400);
                echo "<p> une erreur SQL est survenue.</p>";
            }
        }
        else {
            http_response_code(400);
            echo "<p> Vous devez remplir le formulaire de commentaire </p>";
        }
    }
    // Ajouter une star sur une image.
    public function addStar() :void{
        if($this -> isConnect()) {
            if(isset($_GET['id']) && !empty($_GET['id'])) {
                $idimage = htmlspecialchars($_GET['id']);
                $iduser = htmlspecialchars($_SESSION['user']['ID_user']);
                // Vérification si l'image a déjà une star de cet utilisateur.
                if($this -> image -> checkStar($idimage, $iduser)) {
                    // Si oui, on l'efface.
                    $test = $this -> image -> removeStar($idimage, $iduser);
                    header('location:'.$_SERVER['HTTP_REFERER']);
                }
                else {
                    // sinon, on l'ajoute.
                    $test = $this -> image -> registerStar($idimage,$iduser);
                    if($test) {
                        header('location:'.$_SERVER['HTTP_REFERER']);
                    }
                    else {
                        http_response_code(400);
                        echo "<p> Une erreur SQL est survenue.</p>";
                    }
                }
            }
            else {
                echo "<p> une erreur est survenue.</p>";
            }
        }
        else {
            echo "<p> Vous n'êtes pas connecté. </p>";
        }
    }
    // Chargement des images par catégorie en JS.
    public function loadImageByCategorie() :void{
        $id = htmlspecialchars($_GET['id']);
        $test = $this -> image -> searchImageByCategorie($id);
        if($test) {
            echo json_encode($test);
        }
        else {
            http_response_code(400);
            echo "<p> Une erreur SQL est survenue. </p>";
        }
    }
    // Suppression d'une image.
    public function deleteImage() :void{
        if($this -> isConnect()) {
            
            $idimage = htmlspecialchars($_POST['id']);
            $iduser = intval(htmlspecialchars($_SESSION['user']['ID_user']));
            $verif = $this -> image -> loadImageById($idimage);
            // Check si l'utilisateur voulant supprimer une image est bien son auteur.
            if($verif['ID_user'] === $iduser) {
                $test = $this -> image -> deleteImage($idimage);
                unlink ("img/".$verif['image']);
                // Si oui, on efface
                if($test) {
                    // Si l'effacement a réussi, on recharge en JS le contenu.
                    $array = $this -> image -> getImageByUser($iduser);
                    echo json_encode($array);
                }
                else {
                    http_response_code(400);
                    echo json_encode("<p> Une erreur SQL est survenue. </p>");
                }
            }
            else {
                echo json_encode("<p> Une erreur est survenue. </p>");
            }
        }
        else {
            header('location:index.php?action=login');
        }
    }
    // Chargement des infos d'une image pour remplir le formulaire de modification en JS.
    public function loadImageData() :void{
        if($this -> isConnect()) {
            $id = htmlspecialchars($_POST['id']);
            $data = $this -> image -> loadImageById($id);
            if($data) {
                if($data['ID_user'] === $_SESSION['user']['ID_user']) {
                    echo json_encode($data);
                }
                else {
                    http_response_code(400);
                    echo "<p> Cette image ne vous appartient pas. </p>";
                }    
            }
            else {
                http_response_code(400);
                echo "<p> Une erreur SQL est survenue. </p>";
            }
        }
        else {
            header('location:index.php?action=login');
        }
    }
    // On modifie les informations de l'image.
    public function editImage() :void{
        if($this -> isConnect()) {
            $idimage = htmlspecialchars($_POST['ID_image']);
            $iduser = intval(htmlspecialchars($_SESSION['user']['ID_user']));
            $text = htmlspecialchars($_POST['text']);
            $categorie = htmlspecialchars($_POST['categorie']);
            $details = htmlspecialchars($_POST['details']);
            $title = htmlspecialchars($_POST['title']);
            $data = $this -> image -> loadImageById($idimage);
            if($data) {
                if($data['ID_user'] === $iduser) {
                    $test = $this -> image -> modifyImageContent($idimage,$title,$text,$details,$categorie);
                    if($test) {
                        $message = "Votre poste a été modifié avec succès.";
                        header('location:index.php?action=userpanel');
                    }
                }
                else {
                    http_response_code(400);
                    echo "<p> Cette image ne vous appartient pas. </p>";
                }
            }
            else {
                http_response_code(400);
                echo "<p> Une erreur SQL est survenue. </p>";
            }
        }
        else {
            header('location:index.php?action=login');
        }
    }
    // Recherche des images par frappe client et envoie des données pour le JS.
    public function searchImage() :void{
        $data = htmlspecialchars($_POST['data']);
        if($data) {
            $test = $this -> image -> researchImage($data);
            if($test) {
                echo json_encode($test);
            }
            else {
                echo json_encode('Aucun résultat');
            }
        }
        else {
             echo json_encode('Aucun résultat');
        }
    }
}
