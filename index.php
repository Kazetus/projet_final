<?php
session_start();

use php\controller\security\SecurityController;
use php\controller\accueil\AccueilController;
use php\controller\user\UserController;
use php\controller\image\ImageController;
use php\controller\admin\AdminController;

function chargerClasse($classe) {
    $classe=str_replace('\\','/',$classe);
    require $classe.'.php';
}
spl_autoload_register('chargerClasse');

$accueil = new AccueilController();
$user = new UserController();
$image = new ImageController();
$admin = new AdminController();

if (array_key_exists("action",$_GET)) {
    switch($_GET['action']) {
        case 'login' :
            $user -> loginUser();
        break;
        case 'register' :
            $user -> registerUser();
        break;
        case 'logout' :
            $user -> logoutUser();
        break;
        case 'userpanel' :
            $user -> loadPanel();
        break;
        case 'user' :
            $user -> displayUserContent();
        break;
        case 'suivre' :
            $user -> followUser();            
        break;
        case 'upload' :
            $image -> uploadImage();
        break;
        case 'image' :
            $image -> displayImage();
        break;
        case 'loadcontent' :
            $image -> loadImageByCategorie();
        break;
        case 'comment' :
            $image -> addComment();
        break;
        case 'star' :
            $image -> addStar();
        break;
        case 'load' :
            $image -> loadImage();
        break;
        case 'editaccount' :
            $user -> getUser();
        break;
        case 'editcontent' :
            $user -> getUserImage();
        break;
        case 'newpass' :
            $user -> editPassword();
        break;
        case 'updateaccount' :
            $user -> editAccount();
        break;
        case 'deleteimage' :
            $image -> deleteImage();
        break;
        case 'getimage' :
            $image -> loadImageData();
        break;
        case 'modifyimage' :
            $image -> editImage();
        break;
        case 'search' :
            $image -> searchImage();
        break;
        case 'admin' :
            $admin -> loadPanel();
        break;
        case 'logAdmin' :
            $admin -> logInAdmin();
        break;
        case 'admincontent' :
            $admin -> adminContent();
        break;
        case 'admincategorie' :
            $admin -> adminCategorie();
        break;
        case 'admincomment' :
            $admin -> adminComment();
        break;
        case 'adminaddcat' :
            $admin -> addCategorie();
        break;
        case 'admindeletecomment' :
            $admin -> deleteComment();
        break;
        case 'admindeleteuser' :
            $admin -> deleteUser();
        break;
        case 'admindeletecategorie' :
            $admin -> deleteCategorie();
        break;
        case 'admindeletecontent' :
            $admin -> deleteContent();
        break;
        default:
            $accueil -> loadAccueil();
        break;
    }
}
else {
    $accueil -> loadAccueil();
}