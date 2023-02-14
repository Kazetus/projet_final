<?php

namespace php\controller\admin;

use php\controller\security\SecurityController;
use php\models\admin\Admin;

class AdminController extends SecurityController {
    private $admin;
    
    public function __construct() {
        $this -> admin = new Admin();
    }
    //Chargement du panneau admin ou du formulaire de connexion
    public function loadPanel() {
        if($this -> isAdmin()) {
            $users = $this -> admin -> getUserList();
        }
        $template = "php/views/admin/admin";
        require "php/views/layout.phtml";
    }
    // Connexion au panneau admin
    public function logInAdmin() {
        $pseudo = htmlspecialchars($_POST['pseudo']);
        $password = htmlspecialchars($_POST['password']);
        $checkAdmin = $this -> admin -> getAdmin($pseudo);
        if($checkAdmin) {
            if(password_verify($password, $checkAdmin['password'])) {
                $_SESSION['admin']['pseudo'] = $checkAdmin['pseudo'];
                header('location: index.php?action=admin');
            }
            else {
                echo "<p> Mot de passe incorrect.";
            }
        }
        else {
            echo "<p> Nom d'utilisateur incorrect.</p>";
        }
    }
    // Fonction pour charger les différentes pages du panneau admin.
    public function adminContent() {
        if($this -> isAdmin()) {
            $contents = $this -> admin -> getContent();
        }
        $template = "php/views/admin/adminContent";
        require "php/views/layout.phtml";
    }
    public function adminCategorie() {
        if($this -> isAdmin()) {
            $categories = $this -> admin -> getCategorie();
        }
        $template = "php/views/admin/adminCategorie";
        require "php/views/layout.phtml";
    }
    public function adminComment() {
        if($this -> isAdmin()) {
            $comments = $this -> admin -> getComments();
        }
        $template = "php/views/admin/adminComment";
        require "php/views/layout.phtml";
    }
    // Ajouter une nouvelle catégorie
    public function addCategorie() {
        if($this -> isAdmin()) {
            $name = htmlspecialchars($_POST['categorieName']);
            $test = $this -> admin -> addNewCategorie($name);
            if($test) {
                header('location:index.php?action=admincategorie');
            }
            else {
                echo "<p> Une erreur SQL est survenue.</p>";
            }
        }
        else {
            header('location:index.php');
        }
    }
    // Fonctions pour supprimer un élément posté.
    public function deleteComment() {
        if($this -> isAdmin()) {
            $id = $_GET['id'];
            $test = $this -> admin -> deleteAComment($id);
            if($test) {
                header('location:index.php?action=admincomment');
            }
            else {
                echo "<p> Une erreur SQL est survenue.</p>";
            }
        }
        else {
            header('location:index.php');
        }
    }
    public function deleteUser() {
        if($this -> isAdmin()) {
            $id = $_GET['id'];
            $test = $this -> admin -> deleteAUser($id);
            if($test) {
                header('location:index.php?action=admin');
            }
            else {
                echo "<p> Une erreur SQL est survenue.</p>";
            }
        }
        else {
            header('location:index.php');
        }
    }
    public function deleteContent() {
        if($this -> isAdmin()) {
            $id = $_GET['id'];
            $test = $this -> admin -> deleteAContent($id);
            if($test) {
                header('location:index.php?action=admincontent');
            }
            else {
                echo "<p> Une erreur SQL est survenue.</p>";
            }
        }
        else {
            header('location:index.php');
        }
    }
    public function deleteCategorie() {
        if($this -> isAdmin()) {
            $id = $_GET['id'];
            $test = $this -> admin -> deleteACategorie($id);
            if($test) {
                header('location:index.php?action=admincategorie');
            }
            else {
                echo "<p> Une erreur SQL est survenue.</p>";
            }
        }
        else {
            header('location:index.php');
        }
    }
}