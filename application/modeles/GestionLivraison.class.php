<?php

require_once 'ModelePDO.class.php';

class GestionLivraison extends ModelePDO {

    /**
     * Récupère une livraison par son ID.
     * @param int $id L'ID de la livraison
     * @return object|null La livraison ou null
     */
    public static function getLivraisonById($id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM livraison WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Récupère un point relais par son ID.
     * @param int $id L'ID du point relais
     * @return object|null Les infos du point relais ou null
     */
    public static function getPointRelaisById($id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM pointrelais WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return $resultat;
    }
}

