<?php

require_once 'ModelePDO.class.php';
require_once 'GestionProduit.class.php';

class GestionLigneDeCommande extends ModelePDO {

    /**
     * Récupère toutes les lignes de commande de la base de données.
     *
     * @return array Tableau d'objets lignes de commande.
     */
    public static function getToutesLesLignesCommandes() {
        self::seConnecter();
        self::$requete = "SELECT * FROM lignedecommande";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère une ligne de commande par son ID.
     *
     * @param int $idCommande L'ID de la commande.
     * @param int $idProduit L'ID du produit.
     * @return object La ligne de commande correspondant à l'ID de la commande et du produit.
     */
    public static function getLigneCommandeById($idCommande, $idProduit) {
        self::seConnecter();
        self::$requete = "SELECT * FROM lignedecommande WHERE idCommande = :idCommande AND idProduit = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère toutes les lignes d'une commande spécifique.
     *
     * @param int $idCommande L'ID de la commande
     * @return array Tableau d'objets lignes de commande
     */
    public static function getLignesCommandeById($idCommande) {
        self::seConnecter();
        self::$requete = "SELECT ldc.*, p.nom AS nom_produit, p.image AS image_produit
                          FROM lignedecommande ldc
                          JOIN produit p ON ldc.idProduit = p.id
                          WHERE ldc.idCommande = :idCommande";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute une ligne de commande dans la base de données et met à jour le stock.
     * 
     * @param int $idCommande L'ID de la commande.
     * @param int $idProduit L'ID du produit.
     * @param int $quantite La quantité de produit commandée.
     * @param float $prixUnitaire Le prix unitaire du produit.
     * @param float $sousTotal Le sous-total de la ligne de commande.
     * @return bool True si l'ajout a réussi, false sinon
     */
    public static function ajouterLigneCommande($idCommande, $idProduit, $quantite, $prixUnitaire, $sousTotal) {
        // Vérifier d'abord si le stock est suffisant
        if (!GestionProduit::verifierStock($idProduit, $quantite)) {
            return false;
        }

        self::seConnecter();
        try {
            // Démarrer une transaction
            self::$pdoCnxBase->beginTransaction();

            // Ajouter la ligne de commande
            self::$requete = "INSERT INTO lignedecommande (idCommande, idProduit, quantite, prixUnitaire, sousTotal) 
                            VALUES (:idCommande, :idProduit, :quantite, :prixUnitaire, :sousTotal)";
            self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
            self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
            self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
            self::$pdoStResults->bindValue(':quantite', $quantite, PDO::PARAM_INT);
            self::$pdoStResults->bindValue(':prixUnitaire', $prixUnitaire, PDO::PARAM_STR);
            self::$pdoStResults->bindValue(':sousTotal', $sousTotal, PDO::PARAM_STR);
            $resultatLigne = self::$pdoStResults->execute();
            self::$pdoStResults->closeCursor();

            // Mettre à jour le stock
            $resultatStock = self::updateStock($idProduit, $quantite);

            // Si tout s'est bien passé, on valide la transaction
            if ($resultatLigne && $resultatStock) {
                self::$pdoCnxBase->commit();
                return true;
            } else {
                // Sinon on annule
                self::$pdoCnxBase->rollBack();
                return false;
            }
        } catch (Exception $e) {
            // En cas d'erreur, on annule la transaction
            self::$pdoCnxBase->rollBack();
            return false;
        }
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
     * Modifie une ligne de commande dans la base de données.
     *
     * @param int $idCommande L'ID de la commande.
     * @param int $idProduit L'ID du produit.
     * @param int $quantite La nouvelle quantité du produit dans la ligne de commande.
     */
    public static function modifierLigneCommande($idCommande, $idProduit, $quantite) {
        self::seConnecter();
        self::$requete = "UPDATE lignedecommande SET quantite = :quantite WHERE idCommande = :idCommande AND idProduit = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une ligne de commande par son ID.
     * 
     * @param int $idCommande L'ID de la commande.
     * @param int $idProduit L'ID du produit de la ligne de commande.
     */
    public static function supprimerLigneCommande($idCommande, $idProduit) {
        self::seConnecter();
        self::$requete = "DELETE FROM lignedecommande WHERE idCommande = :idCommande AND idProduit = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }
}
