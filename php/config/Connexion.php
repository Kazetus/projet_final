<?php
declare(strict_types=1);

namespace php\config;

class Connexion {
    
    private const SERVEUR = "localhost";
    private const USER = "root";
    private const MDP = "";
    private const BDD = "projet_3wa";
    private $connexion;
    
    public function getConnexion() : ?\PDO {
        try
        {
            $this -> connexion = new \PDO("mysql:host=".self::SERVEUR.";dbname=".self::BDD,self::USER,self::MDP);
            $this -> connexion -> exec("SET CHARACTER SET utf8mb4");
        
        }
        catch(Exception $message)
        {
            echo ' une erreur au moment de la connexion BDD : '.$message->getMessage();
        }
        return $this -> connexion;
    }
}
// $test = new Connexion();
// var_dump($test -> getConnexion());