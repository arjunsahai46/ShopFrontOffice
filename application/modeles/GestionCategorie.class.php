<?php

require_once 'ModelePDO.class.php';

class GestionCategorie extends ModelePDO {

    /**
     * Récupère toutes les catégories de la base de données.
     * 
     * @return array Tableau d'objets catégories.
     */
    public static function getLesCategories() {
        self::seConnecter();
        self::$requete = "SELECT * FROM categorie";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère une catégorie par son ID.
     * 
     * @param int $id L'ID de la catégorie.
     * @return object La catégorie correspondant à l'ID.
     */
    public static function getCategorieById($id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM categorie WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute une nouvelle catégorie dans la base de données.
     * 
     * @param string $libelle Le libellé de la catégorie.
     */
    public static function ajouterCategorie($libelle) {
        self::seConnecter();
        self::$requete = "INSERT INTO categorie (libelle) VALUES (:libelle)";
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
    public static function modifierCategorie($id, $libelle) {
        self::seConnecter();
        self::$requete = "UPDATE categorie SET libelle = :libelle WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':libelle', $libelle);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une catégorie par son libellé.
     * 
     * @param string $libelle Le libellé de la catégorie à supprimer.
     */
    public static function supprimerCategorie($libelle) {
        self::seConnecter();
        self::$requete = "DELETE FROM categorie WHERE libelle = :libelle";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':libelle', $libelle);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une catégorie par son ID.
     * 
     * @param int $id L'ID de la catégorie à supprimer.
     */
    public static function supprimerCategorieById($id) {
        return self::SupprimerTupleByChamp('categorie', 'id', $id);
    }
}
