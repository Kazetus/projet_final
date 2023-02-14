<?php

namespace php\models\image;

use php\config\Connexion; 

class Image extends Connexion {
    private $connexion;
    
    public function __construct() {
        $this -> connexion = $this -> getConnexion();
    }
    // Chargement des 10 dernières images postées.
    public function getImage(): ?array {
        $query = $this -> connexion -> prepare('
                                    SELECT
                                        `images`.`ID_image`,
                                        `images`.`ID_user`,
                                        `image`,
                                        `title`,
                                        `text`,
                                        `images`.`ID_categorie`,
                                        `date`,
                                        `details`,
                                        `pseudo`,
                                        `avatar`,
                                        `categorieName`,
                                        COUNT(`star`.`ID_image`) as number
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
                                    LEFT JOIN
                                        `star`
                                    ON
                                        `images`.`ID_image` = `star`.`ID_image`
                                    GROUP BY
                                    	`images`.`ID_image`
                                    ORDER BY
                                        `date` DESC
                                    LIMIT 12
                                        ');
        $query -> execute();
        $test = $query -> fetchAll();
        return $test;
    }
    // Chargement des images d'un utilisateur par ID.
    public function getImageByUser(int $id): ?array {
        $query = $this -> connexion -> prepare('
                                        SELECT
                                            `images`.`ID_image`,
                                            `images`.`ID_user`,
                                            `image`,
                                            `title`,
                                            `text`,
                                            `images`.`ID_categorie`,
                                            `date`,
                                            `details`,
                                            `pseudo`,
                                            `avatar`,
                                            `detailUtilisateur`,
                                            `categorieName`,
                                            COUNT(`star`.`ID_image`) as number
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
                                        LEFT JOIN
                                                `star`
                                        ON
                                            `images`.`ID_image` = `star`.`ID_image`
                                        WHERE
                                            `images`.`ID_user` = ?
                                        GROUP BY
                                            `images`.`ID_image`
                                        ORDER BY
                                            `date` DESC
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetchAll();
        return $test;
    }
    // Récupération des catégorie pour filtre JS.
    public function getCategorie(): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_categorie`,
                                                `categorieName`
                                            FROM
                                                `categorie`
                                            ');
        $query -> execute();
        $test = $query -> fetchAll();
        return $test;
    }
    // Poster une image sur le site.
    public function sendImage($user,$image,$title,$text,$cat,$tag): bool {
        $query = $this -> connexion -> prepare('
                                            INSERT INTO
                                                `images`(
                                                `ID_user`,
                                                `image`,
                                                `title`,
                                                `text`,
                                                `ID_categorie`,
                                                `details`
                                                )
                                            VALUES  
                                                (
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?
                                                )
                                            ');
        $test = $query -> execute([$user,$image,$title,$text,$cat,$tag]);
        return $test;
    }
    // Vérification si une image avec le même nom existe déjà.
    public function checkExistingImage($name): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_image`,
                                                `image`
                                            FROM
                                                `images`
                                            WHERE
                                                `image` = ?
                                            ');
        $query -> execute([$name]);
        $test = $query -> fetch();
        return $test;
    }
    // Recherche d'une image par ID.
    public function loadImageById($id): bool|array {
        $query = $this -> connexion -> prepare('
                                           SELECT
                                                `images`.`ID_image`,
                                                `images`.`ID_user`,
                                                `image`,
                                                `title`,
                                                `text`,
                                                `images`.`ID_categorie`,
                                                `date`,
                                                `details`,
                                                `pseudo`,
                                                `avatar`,
                                                `categorieName`,
                                                COUNT(`star`.`ID_image`) as number
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
                                            LEFT JOIN
                                                `star`
                                            ON
                                                `images`.`ID_image` = `star`.`ID_image`
                                            WHERE
                                                `images`.`ID_image` = ?
                                                ');
        $query -> execute([$id]);
        $test = $query -> fetch();
        return $test;
    }
    // Enregistrement d'une star sur un image.
    public function registerStar($idimage,$iduser): bool {
        $query = $this -> connexion -> prepare('
                                            INSERT INTO
                                                `star`(
                                                    `ID_image`,
                                                    `ID_user`
                                                    )
                                            VALUES (
                                                ?,
                                                ?
                                                )
                                                ');
        $test = $query -> execute([$idimage,$iduser]);
        return $test;
    }
    // Compteur de star par image.
    public function countStarForImage($idimage): ?array {
        $query = $this -> connexion -> prepare('
                                            SELECT 
                                                COUNT(ID_image = ?) as number 
                                            FROM 
                                            star
                                            ');
        $query -> execute([$idimage]);
        $test = $query -> fetch();
        return $test;
    }
    // Vérification si un utilisateur a déjà star une étoile.
    public function checkStar($idimage,$iduser): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `ID_image`,
                                                `ID_user`
                                            FROM
                                                `star`
                                            WHERE
                                                `ID_image` = ? and `ID_user` = ?
                                                ');
        $query -> execute([$idimage,$iduser]);
        $test = $query -> fetch();
        return $test;
    }
    // Suppression d'une star sur une image.
    public function removeStar($idimage,$iduser): bool {
        $query = $this -> connexion -> prepare('
                                                DELETE FROM 
                                                    `star` 
                                                WHERE 
                                                    `star`.`ID_image` = ? and `star`.`ID_user` = ?
                                                ');
        $test = $query -> execute([$idimage, $iduser]);
        return $test;
    }
    // Chargement des images des utilisateurs suivi par un utilisateur par id.
    public function loadUserFollowedImage($id): ?array {
        $query = $this -> connexion -> prepare('
                                            SELECT 
                                                `image`,
                                                `images`.`ID_image`,
                                                `ID_usersuivi`,
                                                `user`.`ID_user`,
                                                `pseudo`,
                                                `title`,
                                                `text`,
                                                `images`.`ID_categorie`,
                                                `date`,
                                                `details`,
                                                `avatar`,
                                                `categorieName`,
                                                COUNT(`star`.`ID_image`) as number
                                            FROM 
                                                `suivi` 
                                            INNER JOIN 
                                                `user`
                                            ON 
                                                `user`.`ID_user` = `suivi`.`ID_usersuivi`
                                            INNER JOIN 
                                                `images` 
                                            ON 
                                                `suivi`.`ID_usersuivi` = `images`.`ID_user`
                                            INNER JOIN
                                                `categorie`
                                            ON
                                                `images`.`ID_categorie` = `categorie`.`ID_categorie`
                                            LEFT JOIN
                                                `star`
                                            ON
                                                `images`.`ID_image` = `star`.`ID_image`
                                            WHERE
                                            	`suivi`.`ID_user` = ?
                                            GROUP BY
                                                `images`.`ID_image`
                                            ORDER BY
                                                `date` DESC
                                                ');
        $query -> execute([$id]);
        $test = $query -> fetchAll();
        return $test;
    }
    // Chargement des derniers commentaires posté sur le site.
    public function getLastsComments(): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `comment`,
                                                `comments`.`date`,
                                                `comments`.`ID_image`,
                                                `comments`.`ID_user`,
                                                `pseudo`,
                                                `avatar`,
                                                `title`
                                            FROM
                                                `comments`
                                            INNER JOIN
                                                `images`
                                            ON
                                                `comments`.`ID_image` = `images`.`ID_image`
                                            INNER JOIN
                                                `user`
                                            ON
                                                `comments`.`ID_user` = `user`.`ID_user`
                                            ORDER BY
                                                `comments`.`date` DESC
                                            LIMIT 
                                                10
                                            ');
        $query -> execute();
        $test = $query -> fetchAll();
        return $test;
    }
    // Recherche des images par catégories.
    public function searchImageByCategorie($id): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `images`.`ID_image`,
                                                `title`,
                                                `image`,
                                                `date`,
                                                `text`,
                                               `images`.`ID_user`,
                                               `images`.`ID_categorie`,
                                               `pseudo`,
                                               `avatar`,
                                               `categorieName`,
                                               COUNT(`star`.`ID_image`) as number
                                            FROM
                                                images
                                            INNER JOIN
                                                `user`
                                            ON
                                                `images`.`ID_user` = `user`.`ID_user`
                                            INNER JOIN
                                                `categorie`
                                            ON
                                                `images`.`ID_categorie` = `categorie`.`ID_categorie`
                                            LEFT JOIN
                                                `star`
                                            ON
                                                `images`.`ID_image` = `star`.`ID_image`
                                            WHERE
                                                `images`.`ID_categorie` = ?
                                            GROUP BY
                                                `images`.`ID_image`
                                            ORDER BY
                                                `date` DESC
                                            ');
        $query -> execute([$id]);
        $test = $query -> fetchAll();
        return $test;
    }
    // Suppression d'une image par l'utilisateur
    public function deleteImage($id): bool {
        $query = $this -> connexion -> prepare('
                                            DELETE FROM 
                                                `images` 
                                            WHERE 
                                                `images`.`ID_image` = ?
                                            ');
        $test = $query -> execute([$id]);
        return $test;
    }
    // Modifier les détails d'une image postée.
    public function modifyImageContent($idimage,$title,$text,$details,$categorie): bool {
        $query = $this -> connexion -> prepare('
                                            UPDATE
                                                `images`
                                            SET
                                                `images`.`title` = ?,
                                                `images`.`text` = ?,
                                                `images`.`details` = ?,
                                                `images`.`ID_categorie` = ?
                                            WHERE
                                                `images`.`ID_image` = ?
                                                ');
        $test = $query -> execute([$title,$text,$details,$categorie,$idimage]);
        return $test;
    }
    // Fonction de recherche par frappe utilisateur.
    public function researchImage($data): bool|array {
        $query = $this -> connexion -> prepare('
                                            SELECT
                                                `images`.`ID_image`,
                                                `title`,
                                                `image`,
                                                `date`,
                                                `text`,
                                               `images`.`ID_user`,
                                               `images`.`ID_categorie`,
                                               `pseudo`,
                                               `avatar`,
                                               `categorieName`,
                                               COUNT(`star`.`ID_image`) as number
                                            FROM
                                                images
                                            INNER JOIN
                                                `user`
                                            ON
                                                `images`.`ID_user` = `user`.`ID_user`
                                            INNER JOIN
                                                `categorie`
                                            ON
                                                `images`.`ID_categorie` = `categorie`.`ID_categorie`
                                            LEFT JOIN
                                                `star`
                                            ON
                                                `images`.`ID_image` = `star`.`ID_image`
                                            WHERE
                                                `text`
                                            LIKE
                                                ?
                                            GROUP BY
                                                `images`.`ID_image`
                                            ORDER BY
                                                `date` DESC
                                            ');
        $query -> execute(['%'.$data.'%']);
        $test = $query -> fetchAll();
        return $test;
    }
}