<?php

//Classe statique, peut aussi être géré avec un singleton

require_once 'ModelePDO.class.php';

class GestionPanier extends ModelePDO { 

// <editor-fold defaultstate="collapsed" desc="région INITS DE PANIER">

    public static function initialiser() {
        if (!isset($_SESSION['produits'])) {
            $_SESSION['produits'] = array();
        }
    }

    public static function vider() {
        $_SESSION['produits'] = array();
    }

    public static function detruire() {
        unset($_SESSION['produits']);
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="région AJOUTS / MODIFS / SUPRESSION">

    public static function enregistrerPanierEnBase($idClient) {
        self::seConnecter();
        $stmt = self::$pdoCnxBase->prepare("SELECT id FROM panier WHERE idClient = ?");
        $stmt->execute([$idClient]);
        $panier = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($panier) {
            $idPanier = $panier['id'];
            self::$pdoCnxBase->prepare("DELETE FROM panier_produit WHERE idPanier = ?")->execute([$idPanier]);
        } else {
            self::$pdoCnxBase->prepare("INSERT INTO panier (idClient) VALUES (?)")->execute([$idClient]);
            $idPanier = self::$pdoCnxBase->lastInsertId();
        }
        if (!empty($_SESSION['produits'])) {
            $stmt = self::$pdoCnxBase->prepare("INSERT INTO panier_produit (idPanier, idProduit, quantite) VALUES (?, ?, ?)");
            foreach ($_SESSION['produits'] as $idProduit => $quantite) {
                $stmt->execute([$idPanier, $idProduit, $quantite]);
            }
        }
    }

    public static function chargerPanierDepuisBase($idClient) {
        self::seConnecter();
        $stmt = self::$pdoCnxBase->prepare("SELECT id FROM panier WHERE idClient = ?");
        $stmt->execute([$idClient]);
        $panier = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['produits'] = [];
        if ($panier) {
            $idPanier = $panier['id'];
            $stmt = self::$pdoCnxBase->prepare("SELECT idProduit, quantite FROM panier_produit WHERE idPanier = ?");
            $stmt->execute([$idPanier]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $_SESSION['produits'][$row['idProduit']] = $row['quantite'];
            }
        }
    }

    public static function ajouterProduit($idProduit, $qte) {
        if (GestionPanier::contains($idProduit))
            $_SESSION['produits'][$idProduit] += $qte;
        else
            $_SESSION['produits'][$idProduit] = $qte;
        if (isset($_SESSION['client_id'])) {
            self::enregistrerPanierEnBase($_SESSION['client_id']);
        }
    }

    public static function modifierQteProduit($idProduit, $qte) {
        if (GestionPanier::contains($idProduit))
            $_SESSION['produits'][$idProduit] = $qte;
        if (isset($_SESSION['client_id'])) {
            self::enregistrerPanierEnBase($_SESSION['client_id']);
        }
    }

    public static function retirerProduit($idProduit) {
        if (GestionPanier::contains($idProduit))
            unset($_SESSION['produits'][$idProduit]);
        if (isset($_SESSION['client_id'])) {
            self::enregistrerPanierEnBase($_SESSION['client_id']);
        }
    }

    public static function fusionnerPanierSessionEtBase($idClient) {
        self::seConnecter();
        // Charger le panier de la base
        $stmt = self::$pdoCnxBase->prepare("SELECT id FROM panier WHERE idClient = ?");
        $stmt->execute([$idClient]);
        $panier = $stmt->fetch(PDO::FETCH_ASSOC);

        $panierBase = [];
        $idPanier = null;
        if ($panier) {
            $idPanier = $panier['id'];
            $stmt = self::$pdoCnxBase->prepare("SELECT idProduit, quantite FROM panier_produit WHERE idPanier = ?");
            $stmt->execute([$idPanier]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $panierBase[$row['idProduit']] = $row['quantite'];
            }
        }

        // Fusionner avec le panier de session
        if (!isset($_SESSION['produits'])) {
            $_SESSION['produits'] = [];
        }
        foreach ($_SESSION['produits'] as $idProduit => $qteSession) {
            if (isset($panierBase[$idProduit])) {
                $panierBase[$idProduit] += $qteSession;
            } else {
                $panierBase[$idProduit] = $qteSession;
            }
        }
        $_SESSION['produits'] = $panierBase;

        // Enregistrer le panier fusionné en base
        self::enregistrerPanierEnBase($idClient);
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="région FONCTIONS GET">

    public static function getProduits() {
        return $_SESSION['produits'];
    }

    public static function getNbProduits() {
        if (isset($_SESSION['produits'])) {
            return array_sum($_SESSION['produits']);
        }
        // ou en 1 ligne : 
        //return isset($_SESSION['produits'])?array_sum($_SESSION['produits']):0;
    }

    public static function getQteByProduit($idProduit) {
        if (GestionPanier::contains($idProduit))
            return $_SESSION['produits'][$idProduit];        
    }

// </editor-fold>    
// <editor-fold defaultstate="collapsed" desc="région FONCTIONS BOOLEENNES">
    public static function isVide() {
        return (self::getNbProduits() == 0);
        // OU
//        if (self::getNbProduits() == 0){
//            return true;
//        }
//        else {
//            return false;
//        }
    }

    public static function contains($idProduit) {
        return (array_key_exists($idProduit, self::getProduits()));

        // OU
//        if (array_key_exists($idProduit, self::getProduits())) {
//            return true;
//        } else {
//            return false;
//        }
    }

// </editor-fold> 
}

//Panier::initialiser();
//Panier::ajouterProduit(4, 2); // 2 Casques Bluetooth
//Panier::ajouterProduit(11, 1); // 1 Clavier Mécanique
//
//var_dump(Panier::getProduits()); // OU var_dump($_SESSION['produits']);
//
//Panier::retirerProduit(1);
//Panier::retirerProduit(4);
//Panier::retirerProduit(11);
//var_dump(Panier::isVide());

//var_dump($_SESSION['produits']);
//echo PanierTestQte::getQteByProduit(8);
//TODO ADAPTER LES CAS ET LA VUE DU PANIER...
?>