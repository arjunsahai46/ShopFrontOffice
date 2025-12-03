<?php

require_once 'ModelePDO.class.php';

class GestionProduit extends ModelePDO {

    /**
     * Récupère tous les produits de la base de données.
     *
     * @return array Tableau d'objets produits.
     */
    public static function getLesProduits() {
        self::seConnecter();
        self::$requete = "SELECT P.id, P.nom, P.description, P.prix, P.image, P.QteStockProduit, C.libelle 
                         FROM produit P 
                         JOIN categorie C ON P.idCategorie = C.id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll();
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère les produits d'une catégorie spécifique.
     * 
     * @param string $libelleCategorie Le libellé de la catégorie.
     * @return array Tableau de produits.
     */
    public static function getLesProduitsByCategorie($libelleCategorie) {
        self::seConnecter();
        // Convertir les underscores en espaces pour la comparaison avec la base de données
        $libelleCategorie = str_replace('_', ' ', $libelleCategorie);
        
        self::$requete = "SELECT P.id, P.nom, P.description, P.prix, P.image, P.QteStockProduit, C.libelle 
                         FROM produit P, categorie C 
                         WHERE P.idCategorie = C.id AND libelle = :libCateg";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue('libCateg', $libelleCategorie);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll();
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }
    
    /**
     * Récupère un produit par son ID.
     * 
     * @param int $idProduit L'ID du produit.
     * @return array Détails du produit.
     */
    public static function getProduitById($idProduit) {
        self::seConnecter();
        self::$requete = "SELECT P.id, P.nom, P.prix, P.description, P.image, C.libelle 
                         FROM produit P, categorie C 
                         WHERE P.idCategorie = C.id AND P.id = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_ASSOC);
        self::$pdoStResults->closeCursor();
        
        return self::$resultat;
    }

    /**
     * Ajoute un nouveau produit dans la base de données.
     *
     * @param string $nom Le nom du produit.
     * @param string $description La description du produit.
     * @param float $prix Le prix du produit.
     * @param string $image L'image du produit.
     * @param int $idCategorie L'ID de la catégorie du produit.
     * @param int $idFournisseur L'ID du fournisseur du produit.
     */
    public static function ajouterProduit($nom, $description, $prix, $image, $idCategorie, $idFournisseur) {
        self::seConnecter();
        self::$requete = "INSERT INTO produit (nom, description, prix, image, idCategorie, idFournisseur)
                          VALUES (:nom, :description, :prix, :image, :idCategorie, :idFournisseur)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':description', $description);
        self::$pdoStResults->bindValue(':prix', $prix);
        self::$pdoStResults->bindValue(':image', $image);
        self::$pdoStResults->bindValue(':idCategorie', $idCategorie);
        self::$pdoStResults->bindValue(':idFournisseur', $idFournisseur);
        self::$pdoStResults->execute();
    }

    /**
     * Modifie un produit existant dans la base de données.
     *
     * @param int $id L'ID du produit à modifier.
     * @param string $nom Le nouveau nom du produit.
     * @param string $description La nouvelle description du produit.
     * @param float $prix Le nouveau prix du produit.
     * @param string $image La nouvelle image du produit.
     * @param int $idCategorie Le nouvel ID de la catégorie du produit.
     * @param int $idFournisseur Le nouvel ID du fournisseur du produit.
     */
    public static function modifierProduit($id, $nom, $description, $prix, $image, $idCategorie, $idFournisseur) {
        self::seConnecter();
        self::$requete = "UPDATE produit 
                          SET nom = :nom, description = :description, prix = :prix, image = :image, 
                              idCategorie = :idCategorie, idFournisseur = :idFournisseur 
                          WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':description', $description);
        self::$pdoStResults->bindValue(':prix', $prix);
        self::$pdoStResults->bindValue(':image', $image);
        self::$pdoStResults->bindValue(':idCategorie', $idCategorie);
        self::$pdoStResults->bindValue(':idFournisseur', $idFournisseur);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime un produit de la base de données.
     *
     * @param int $id L'ID du produit à supprimer.
     */
    public static function supprimerProduit($id) {
        self::seConnecter();
        self::$requete = "DELETE FROM produit WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Vérifie si un produit est disponible en stock
     * 
     * @param int $idProduit L'ID du produit
     * @param int $quantite La quantité demandée
     * @return bool True si le produit est disponible en quantité suffisante
     */
    public static function verifierStock($idProduit, $quantite) {
        self::seConnecter();
        self::$requete = "SELECT QteStockProduit FROM produit WHERE id = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        
        return $resultat && $resultat->QteStockProduit >= $quantite;
    }

    /**
     * Met à jour la quantité en stock d'un produit
     * 
     * @param int $idProduit L'ID du produit
     * @param int $quantite La quantité à soustraire du stock
     * @return bool True si la mise à jour a réussi, false sinon
     */
    private static function updateStock($idProduit, $quantite) {
        self::seConnecter();
        self::$requete = "UPDATE produit 
                         SET QteStockProduit = QteStockProduit - :quantite 
                         WHERE id = :idProduit AND QteStockProduit >= :quantite";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        $result = self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
        return $result && (self::$pdoStResults->rowCount() > 0);
    }

    /**
     * Retourne les produits qui n'ont jamais été commandés.
     * @return array
     */
    public static function getProduitsJamaisCommandes() {
        self::seConnecter();
        self::$requete = "SELECT * FROM produit WHERE id NOT IN (SELECT DISTINCT idProduit FROM lignedecommande)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Retourne le top des produits les plus commandés (quantité ou CA)
     * @param int $limite
     * @param bool $parCA true = par chiffre d'affaires, false = par quantité
     * @return array
     */
    public static function getTopProduitsCommandes($limite = 5, $parCA = false) {
        self::seConnecter();
        $order = $parCA ? 'totalCA' : 'totalQte';
        self::$requete = "SELECT p.*, SUM(ldc.quantite) AS totalQte, SUM(ldc.quantite * ldc.prixUnitaire) AS totalCA 
                         FROM produit p 
                         JOIN lignedecommande ldc ON p.id = ldc.idProduit 
                         GROUP BY p.id 
                         ORDER BY $order DESC 
                         LIMIT :limite";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':limite', $limite, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Appelle la procédure stockée de réapprovisionnement pour un produit donné.
     * @param int $idProduit
     * @param int $quantite
     * @return void
     */
    public static function reapprovisionnerProduit($idProduit, $quantite = 10) {
        self::seConnecter();
        self::$requete = "CALL ReapprovisionnerProduit(:idProduit, :quantite)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
    }
}
