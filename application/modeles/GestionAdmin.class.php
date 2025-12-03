<?php

class GestionAdmin {
    private static $connexion;

    public static function initConnexion() {
        try {
            self::$connexion = new PDO('mysql:host=' . ModelePDO::$hostname . ';dbname=' . ModelePDO::$database, ModelePDO::$username, ModelePDO::$password);
            self::$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connexion->query("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }
    }

    public static function verifierConnexionAdmin($login, $passe) {
        self::initConnexion();
        
        try {
            $requete = "SELECT * FROM administrateur WHERE login = :login AND passe = :passe";
            $stmt = self::$connexion->prepare($requete);
            
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':passe', $passe);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Erreur lors de la vérification : " . $e->getMessage();
            return false;
        }
    }

    public static function getAdminByLogin($login) {
        self::initConnexion();
        
        try {
            $requete = "SELECT * FROM administrateur WHERE login = :login";
            $stmt = self::$connexion->prepare($requete);
            
            $stmt->bindParam(':login', $login);
            
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération : " . $e->getMessage();
            return null;
        }
    }
}

?> 