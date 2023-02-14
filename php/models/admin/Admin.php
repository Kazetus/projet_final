<?php

namespace php\models\admin;

use php\config\Connexion; 

class Admin extends Connexion {
    private $connexion;
    
    public function __construct() {
        $this -> connexion = $this -> getConnexion();
    }
    //récupération des infos de connexion dans la bdd
    public function getAdmin($pseudo) {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `pseudo`,
                                                `password`
                                            FROM
                                                `admin`
                                            WHERE
                                                `pseudo` = ?
                                            ');
        $query -> execute([$pseudo]);
        $test = $query -> fetch();
        return $test;
    }
    // Liste des utilisateurs 
    public function getUserList() {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_user`,
                                                `pseudo`,
                                                `avatar`
                                            FROM
                                                `user`
                                            ORDER BY
                                                `ID_user` DESC
                                            ');
        $query -> execute();
        $test = $query -> fetchAll();
        return $test;
    }
    // liste des catégories d'images
    public function getCategorie() {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_categorie`,
                                                `CategorieName`
                                            FROM
                                                `categorie`
                                            ');
        $query -> execute();
        $test = $query -> fetchAll();
        return $test;
    }
    //Liste de tout les commentaires postés
    public function getComments() {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_comments`,
                                                `comments`.`ID_image`,
                                                `comments`.`ID_user`,
                                                `comment`,
                                                `pseudo`,
                                                `title`
                                            FROM
                                                `comments`
                                            INNER JOIN
                                                `user`
                                            ON
                                                `comments`.`ID_user` = `user`.`ID_user`
                                            INNER JOIN
                                                `images`
                                            ON 
                                                `comments`.`ID_image` = `images`.`ID_image`
                                            ORDER BY
                                                `ID_comments` DESC
                                            ');
        $query -> execute();
        $test = $query -> fetchAll();
        return $test;
    }
    // Liste de tout le contenu en ligne
    public function getContent() {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_image`,
                                                `images`.`ID_user`,
                                                `images`.`ID_categorie`,
                                                `image`,
                                                `details`,
                                                `title`,
                                                `text`,
                                                `pseudo`,
                                                `categorieName`
                                            FROM
                                                `images`
                                            INNER JOIN
                                                `user`
                                            ON
                                                `images`.`ID_user` = `user`.`ID_user`
                                            INNER JOIN
                                                `categorie`
                                            ON
                                                `images`.`ID_categorie` = `categorie`.`ID_categorie`
                                            ORDER BY
                                                `ID_image` DESC
                                            ');
        $query -> execute();
        $test = $query -> fetchAll();
        return $test;
    }
    // Ajouter une nouvelle catégorie
    public function addNewCategorie($name) {
        $query = $this -> connexion -> prepare('
                                            INSERT INTO
                                                `categorie`(
                                                `CategorieName`
                                                )
                                            VALUES (
                                                ?
                                                )
                                                ');
        $test = $query -> execute([$name]);
        return $test;
    }
    // Fonction visant à supprimer un élément de la BDD
    public function deleteAComment($id) {
        $query = $this -> connexion -> prepare('
                                            DELETE FROM 
                                                    `comment` 
                                                WHERE 
                                                    `ID_comment` = ?
                                            ');
        $test = $query -> execute([$id]);
        return $test;
    }
    public function deleteAUser($id) {
        $query = $this -> connexion -> prepare('
                                            DELETE FROM 
                                                    `user` 
                                                WHERE 
                                                    `ID_user` = ?
                                            ');
        $test = $query -> execute([$id]);
        return $test;
    }
    public function deleteAContent($id) {
        $query = $this -> connexion -> prepare('
                                            DELETE FROM 
                                                    `images` 
                                                WHERE 
                                                    `ID_image` = ?
                                            ');
        $test = $query -> execute([$id]);
        return $test;
    }
    public function deleteACategorie($id) {
        $query = $this -> connexion -> prepare('
                                            DELETE FROM 
                                                    `categorie` 
                                                WHERE 
                                                    `ID_categorie` = ?
                                            ');
        $test = $query -> execute([$id]);
        return $test;
    }
}