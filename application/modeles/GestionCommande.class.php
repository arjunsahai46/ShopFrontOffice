<?php

require_once 'ModelePDO.class.php';

class GestionCommande extends ModelePDO {

    /**
     * Récupère toutes les commandes de la base de données.
     * 
     * @return array Tableau d'objets commandes.
     */
    public static function getLesCommandes() {
        self::seConnecter();
        self::$requete = "SELECT * FROM commande";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère une commande par son ID.
     * 
     * @param int $id L'ID de la commande.
     * @return object La commande correspondant à l'ID.
     */
    public static function getCommandeById($id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM commande WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère toutes les commandes d'un client donné.
     * @param int $idClient L'ID du client
     * @return array Tableau d'objets commandes
     */
    public static function getCommandesByClientId($idClient) {
        self::seConnecter();
        self::$requete = "SELECT * FROM commande WHERE idClient = :idClient ORDER BY date DESC";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idClient', $idClient, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute une nouvelle commande dans la base de données.
     * 
     * @param int $idClient L'id du client qui a passé la commande.
     * @param string $date La date de la commande.
     * @param float $sousTotal Le sous-total de la commande.
     * @return int L'ID de la commande créée
     */
    public static function ajouterCommande($idClient, $date, $sousTotal = 0) {
        self::seConnecter();
        self::$requete = "INSERT INTO commande (idClient, date, sousTotal) VALUES (:idClient, :date, :sousTotal)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idClient', $idClient, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':date', $date);
        self::$pdoStResults->bindValue(':sousTotal', $sousTotal, PDO::PARAM_STR);
        self::$pdoStResults->execute();
        return self::$pdoCnxBase->lastInsertId();
    }

    /**
     * Modifie une commande dans la base de données.
     *
     * @param int $id L'ID de la commande à modifier.
     * @param string $date La nouvelle date de la commande.
     * @param int $idClient Le nouvel ID du client.
     * @param float $sousTotal Le nouveau sous-total de la commande.
     */
    public static function modifierCommande($id, $date, $idClient, $sousTotal) {
        self::seConnecter();
        self::$requete = "UPDATE commande SET date = :date, idClient = :idClient, sousTotal = :sousTotal WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':date', $date);
        self::$pdoStResults->bindValue(':idClient', $idClient, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':sousTotal', $sousTotal, PDO::PARAM_STR);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une commande par son ID.
     * 
     * @param int $id L'ID de la commande à supprimer.
     */
    public static function supprimerCommande($id) {
        self::seConnecter();
        self::$requete = "DELETE FROM commande WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }
}
