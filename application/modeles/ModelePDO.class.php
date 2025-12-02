<?php

class ModelePDO {

//Attributs utiles pour la connexion
    protected static $serveur = MySqlConfig::HOSTNAME;
    protected static $base = MySqlConfig::DATABASE;
    protected static $utilisateur = MySqlConfig::USERNAME;
    protected static $passe = MySqlConfig::PASSWORD;
//Attributs utiles pour la manipulation PDO de la BD
    protected static $pdoCnxBase = null;
    protected static $pdoStResults = null;
    protected static $requete = "";
    protected static $resultat = null;

    public static function seConnecter() {
        if (!isset(self::$pdoCnxBase)) { //S'il n'y a pas encore eu de connexion
            try {
                self::$pdoCnxBase = new PDO('mysql:host=' . self::$serveur . ';dbname=' . self::$base, self::$utilisateur,
                        self::$passe);
                self::$pdoCnxBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdoCnxBase->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                self::$pdoCnxBase->query("SET CHARACTER SET utf8"); //méthode de la classe PDO
            } catch (Exception $e) {
                echo 'Erreur : ' . $e->getMessage() . '<br />'; // méthode de la classe Exception
                echo 'Code : ' . $e->getCode(); // méthode de la classe Exception
            }
        }
    }

    protected static function seDeconnecter() {
        self::$pdoCnxBase = null;
// Si on n'appelle pas la méthode, la déconnexion a lieu en fin de script
    }

    protected static function getLesTuples($table) {
        self::seConnecter();
        self::$requete = "SELECT * FROM " . $table;
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    protected static function getPremierTupleByChamp($table, $nomChamp, $valeurChamp) {
        self::seConnecter();
        self::$requete = "SELECT * FROM " . $table . " WHERE " . $nomChamp . " = :valeurChamp";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':valeurChamp', $valeurChamp);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    // Méthode pour modifier un tuple dans une table
    protected static function modifierTuple($table, $champsValeurs, $conditions) {
        // $champsValeurs est un tableau associatif avec les champs à modifier
        // $conditions est une chaîne de conditions pour spécifier quel(s) enregistrement(s) à modifier
        // Construction de la partie SET de la requête SQL
        $setParts = [];
        foreach ($champsValeurs as $champ => $valeur) {
            $setParts[] = $champ . " = :" . $champ;
        }
        $setString = implode(", ", $setParts);

        // Construction de la requête SQL complète
        self::seConnecter();
        self::$requete = "UPDATE " . $table . " SET " . $setString . " WHERE " . $conditions;

        // Préparation de la requête
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);

        // Liaison des valeurs des champs
        foreach ($champsValeurs as $champ => $valeur) {
            self::$pdoStResults->bindValue(':' . $champ, $valeur);
        }

        // Exécution de la requête
        self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
    }

    protected static function SupprimerTupleByChamp($table, $nomChamp, $valeurChamp) {
        self::seConnecter();
        self::$requete = "DELETE FROM " . $table . " WHERE " . $nomChamp . " = :valeurChamp";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':valeurChamp', $valeurChamp);
        self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
    }

    protected static function select($champs, $tables, $conditions = null) {
        self::seConnecter();
        self::$requete = "SELECT " . $champs . " FROM " . $tables;
        if ($conditions != null)
            self::$requete .= " WHERE " . $conditions;
    }

    protected static function getNbProduits() {
        self::seConnecter();
        self::$requete = "SELECT Count(*) AS nbProduits FROM Produit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch();
        self::$pdoStResults->closeCursor();
        return self::$resultat->nbProduits;
    }

    protected static function getLesTuplesByTable($table) {
        self::seConnecter();
        self::$requete = "SELECT * FROM $table";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoResults->fetchAll();
        self::$pdoResults->closeCursor();
        return self::$resultat;
    }

    protected static function getLeTupleTableById($table, $id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM $table WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoResults->fetch();
        self::$pdoResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Vérifie si l'utilisateur est un administrateur présent dans la base
     * @param type $login Login de l'utilisateur
     * @param type $passe Passe de l'utilisateur
     * @return type Booléen
     */
    protected static function isAdminOK($login, $passe) {
        self::seConnecter();
        self::$requete = "SELECT * FROM Utilisateur where login=:login and passe=:passe";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue('login', $login);
        self::$pdoStResults->bindValue('passe', $passe);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch();
        self::$pdoStResults->closeCursor();
        if ((self::$resultat != null) and (self::$resultat->isAdmin))
            return true;
        else
            return false;
    }
    
    public static function getPDO() {
        self::seConnecter();
        return self::$pdoCnxBase;
    }
}
