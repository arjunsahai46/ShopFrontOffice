<?php

require_once ("ModelePDO.class.php");

class GestionCategorie extends modelePDO {

    public static function GetLesCategories() {
        return self::getLesTuples('categorie');
    }

    /**
     * Ajoute une nouvelle catégorie dans la base de données.
     * 
     * @param string $libelle Le libellé de la catégorie.
     */
    public static function ajouterCategorie($libelle) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "INSERT INTO Categorie (libelle) VALUES (:libelle)"; // Requête d'insertion
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':libelle', $libelle);
        self::$pdoStResults->execute();
    }

    /**
     * Modifie une catégorie dans la base de données.
     *
     * @param int $id L'ID de la catégorie à modifier.
     * @param string $libelle Le nouveau libellé de la catégorie.
     */
    public static function modifierCategorie($id, $libelleCateg) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "UPDATE categorie SET libelle = :libelle WHERE id = :id"; // Requête de mise à jour
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue('libelle', $libelleCateg);
        self::$pdoStResults->bindValue('id', $id);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une catégorie par son ID.
     * 
     * @param int $id L'ID de la catégorie à supprimer.
     */
    public static function supprimerCategorie($libelleCateg) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "DELETE FROM categorie WHERE libelle = :libelle"; // Requête de suppression
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue('libelle', $libelleCateg);
        self::$pdoStResults->execute();
    }
}
