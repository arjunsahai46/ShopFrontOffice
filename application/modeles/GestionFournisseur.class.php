<?php

require_once 'ModelePDO.class.php';

class GestionFournisseur extends ModelePDO {

    /**
     * Récupère tous les fournisseurs de la base de données.
     * 
     * @return array Tableau d'objets fournisseurs.
     */
    public static function getLesFournisseurs() {
        self::seConnecter();
        self::$requete = "SELECT * FROM fournisseur";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère un fournisseur par son ID.
     * 
     * @param int $id L'ID du fournisseur.
     * @return object Le fournisseur correspondant à l'ID.
     */
    public static function getFournisseurById($id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM fournisseur WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute un nouveau fournisseur dans la base de données.
     * 
     * @param string $nom Le nom du fournisseur.
     * @param string $rue L'adresse du fournisseur.
     * @param string $codePostal Le code postal du fournisseur.
     * @param string $ville La ville du fournisseur.
     * @param string $tel Le téléphone du fournisseur.
     * @param string $email L'email du fournisseur.
     */
    public static function ajouterFournisseur($nom, $rue, $codePostal, $ville, $tel, $email) {
        self::seConnecter();
        self::$requete = "INSERT INTO fournisseur (nom, rue, codePostal, ville, tel, email)
                          VALUES (:nom, :rue, :codePostal, :ville, :tel, :email)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':rue', $rue);
        self::$pdoStResults->bindValue(':codePostal', $codePostal);
        self::$pdoStResults->bindValue(':ville', $ville);
        self::$pdoStResults->bindValue(':tel', $tel);
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->execute();
    }

    /**
     * Modifie un fournisseur dans la base de données.
     *
     * @param int $id L'ID du fournisseur à modifier.
     * @param string $nom Le nom du fournisseur.
     * @param string $rue L'adresse du fournisseur.
     * @param string $codePostal Le code postal du fournisseur.
     * @param string $ville La ville du fournisseur.
     * @param string $tel Le numéro de téléphone du fournisseur.
     * @param string $email L'email du fournisseur.
     */
    public static function modifierFournisseur($id, $nom, $rue, $codePostal, $ville, $tel, $email) {
        self::seConnecter();
        self::$requete = "UPDATE fournisseur SET nom = :nom, rue = :rue, codePostal = :codePostal, ville = :ville, tel = :tel, email = :email WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':rue', $rue);
        self::$pdoStResults->bindValue(':codePostal', $codePostal);
        self::$pdoStResults->bindValue(':ville', $ville);
        self::$pdoStResults->bindValue(':tel', $tel);
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime un fournisseur par son ID.
     * 
     * @param int $id L'ID du fournisseur à supprimer.
     */
    public static function supprimerFournisseur($id) {
        self::seConnecter();
        self::$requete = "DELETE FROM fournisseur WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }
}
