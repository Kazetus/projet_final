<?php

namespace php\models\user;

use php\config\Connexion;

class User extends Connexion {
    private $connexion;
    
    public function __construct() {
        $this -> connexion = $this -> getConnexion();
    }
    // Ajout d'un utilisateur
    public function register($pseudo,$mail,$password) {
        $query = $this -> connexion -> prepare('
                                        INSERT INTO
                                            `user`(
                                                `pseudo`,
                                                `mail`,
                                                `password`
                                                )
                                        VALUES (
                                            ?,
                                            ?,
                                            ?)
                                            ');
                                            
        $test = $query -> execute([$pseudo,$mail,$password]);
        
        return $test;
    }
    // Recherche d'un utilisateur par id
    public function searchUser($id) {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_user`,
                                                `pseudo`,
                                                `mail`,
                                                `password`,
                                                `avatar`,
                                                `detailUtilisateur`
                                            FROM 
                                                `user`
                                            WHERE
                                                `ID_user` = ?
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetch();
        return $test;
    }
    // Recherche d'un utilisateur par pseudo
    public function searchName($pseudo) {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_user`,
                                                `pseudo`,
                                                `mail`,
                                                `password`,
                                                `avatar`,
                                                `detailUtilisateur`
                                            FROM
                                                `user`
                                            WHERE
                                                `pseudo` = ?
                                            ');
        $query -> execute([$pseudo]);
        $test = $query -> fetch();
        return $test;
    }
    // Recherche d'un utilisateur par mail
    public function searchMail($mail) {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_user`,
                                                `pseudo`,
                                                `mail`,
                                                `password`,
                                                `avatar`,
                                                `detailUtilisateur`
                                            FROM
                                                `user`
                                            WHERE
                                                `mail` = ?
                                            ');
        $query -> execute([$mail]);
        $test = $query -> fetch();
        return $test;
    }
    // Ajout d'un follow dans la bdd
    public function addFollower($idsuivi,$idsuiveur): bool {
        $query = $this -> connexion -> prepare('
                                            INSERT INTO
                                                `suivi`(
                                                `ID_user`,
                                                `ID_usersuivi`
                                                )
                                            VALUES (
                                                ?,
                                                ?
                                                )
                                            ');
        $test = $query -> execute([$idsuiveur,$idsuivi]);
        return $test;
    }
    // Vérification de l'existence d'un follow entre deux utilisateurs.
    public function checkFollower($idsuivi,$idsuiveur): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_user`,
                                                `ID_usersuivi`
                                            FROM
                                                `suivi`
                                            WHERE
                                                `ID_user` = ? and `ID_usersuivi` = ?
                                                ');
        $query -> execute([$idsuiveur,$idsuivi]);
        $test = $query -> fetch();
        return $test;
    }
    // Supprimer un follow
    public function deleteFollow($idsuivi,$idsuiveur): bool {
        $query = $this -> connexion -> prepare('
                                                DELETE FROM 
                                                    `suivi` 
                                                WHERE 
                                                    `ID_user` = ? and `ID_usersuivi` = ?
                                                ');
        $test = $query -> execute([$idsuiveur, $idsuivi]);
        return $test;
    }
    // Chargement de tout les utilisateurs suivant un utilisateur par id.
    public function loadFollowed($id): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `suivi`.`ID_user`,
                                                `ID_usersuivi`,
                                                `pseudo`,
                                                `avatar`
                                            FROM
                                                `suivi`
                                            INNER JOIN
                                                `user`
                                            ON 
                                                `suivi`.`ID_usersuivi` = `user`.`ID_user`
                                            WHERE 
                                                `suivi`.`ID_user` = ?
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetchAll();
        return $test;
    }
    // Chargement des images qui ont reçus une star par un utilisateur par ID.
    public function loadImageStared($id): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `star`.`ID_image`,
                                                `image`,
                                                `title`,
                                                `text`,
                                                `details`
                                            FROM
                                                `star`
                                            INNER JOIN
                                                `images`
                                            ON 
                                                `star`.`ID_image` = `images`.`ID_image`
                                            WHERE
                                                `star`.`ID_user` = ?
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetchAll();
        return $test;
    }
    // Charger les utilisateurs suivant un utilisateur par id.
    public function loadFollower($id): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `suivi`.`ID_user`,
                                                `pseudo`,
                                                `ID_usersuivi`
                                            FROM
                                                `suivi`
                                            INNER JOIN
                                                `user`
                                            ON
                                                `suivi`.`ID_usersuivi` = `user`.`ID_user`
                                            WHERE
                                                `suivi`.`ID_user` = ?
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetchAll();
        return $test;
    }
    // Compteur de follower par id.
    public function countFollower($id): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                count(`ID_usersuivi`) as number
                                            FROM
                                                `suivi`
                                            WHERE
                                                `ID_usersuivi` = ?
                                            GROUP BY
                                            	`ID_usersuivi`
                                            ');
        $query -> execute([$id]);
        $test =$query -> fetch();
        return $test;
    }
    // Compteur de star par ID.
    public function countStar($id): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `images`.`ID_user`,
                                                count(`star`.`ID_image`) as number
                                            FROM
                                                `images`
                                            INNER JOIN
                                                `star`
                                            ON
                                                `images`.`ID_image` = `star`.`ID_image`
                                            WHERE
                                                `images`.`ID_user` = ?
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetch();
        return $test;
    }
    // Modification du mot de passe de l'utilisateur.
    public function setPassword($id,$password): bool {
        $query = $this -> connexion -> prepare('
                                            UPDATE
                                                `user`
                                            SET
                                                `password` = ?
                                            WHERE
                                                `ID_user` = ?
                                            ');
        $test = $query -> execute([$password,$id]);
        return $test;
    }
    // Modification des infos utilisateur.
    public function setAccount($id,$pseudo,$mail,$detail,$avatar): bool {
        $query = $this -> connexion -> prepare('
                                            UPDATE
                                                `user`
                                            SET
                                                `pseudo` = ?,
                                                `mail` = ?,
                                                `avatar` = ?,
                                                `detailUtilisateur` = ?
                                            WHERE
                                                `ID_user` = ?
                                            ');
        $test = $query -> execute([$pseudo,$mail,$avatar,$detail,$id]);
        return $test;
    }
}