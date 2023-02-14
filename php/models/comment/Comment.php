<?php
namespace php\models\comment;

use php\config\Connexion;

class Comment extends Connexion {
    private $connexion;
    
    public function __construct() {
        $this -> connexion = $this -> getConnexion();
    }
    // Insertion d'un commentaire en BDD
    public function insertComment($idimage,$iduser,$comments): bool {
        $query = $this -> connexion -> prepare('
                                            INSERT INTO
                                                `comments`(
                                                    `ID_image`,
                                                    `ID_user`,
                                                    `comment`,
                                                    `date`
                                                    )
                                            VALUES (
                                                    ?,
                                                    ?,
                                                    ?,
                                                    now()
                                                    )
                                                ');
        $test = $query -> execute([$idimage,$iduser,$comments]);
        return $test;
    }
    // Chargement des commentaires d'une image par id.
    public function loadCommentByImage($id): ?array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_image`,
                                                `comments`.`ID_user`,
                                                `comment`,
                                                `date`,
                                                `pseudo`,
                                                `avatar`
                                            FROM
                                                `comments`
                                            INNER JOIN 
                                                `user`
                                            ON 
                                                `comments`.`ID_user` = `user`.`ID_user`
                                            WHERE
                                                `ID_image` = ?
                                            ORDER BY
                                                `date` DESC
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetchAll();
        return $test;
    }
    // Chargement des commentaires d'un utilisateur.
    public function loadCommentByUser($id): ?array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `comments`.`ID_image`,
                                                `comments`.`ID_user`,
                                                `comment`,
                                                `comments`.`date`,
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
                                            WHERE
                                                `comments`.`ID_user` = ?
                                            ORDER BY
                                             	`comments`.`date` DESC
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetchAll();
        return $test;
    }
}