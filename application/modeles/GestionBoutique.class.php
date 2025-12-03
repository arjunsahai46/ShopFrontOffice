<?php

require_once 'ModelePDO.class.php';
require_once 'Client.class.php';
require_once __DIR__ . '/GestionClient.class.php';
require_once __DIR__ . '/GestionCategorie.class.php';
require_once __DIR__ . '/GestionProduit.class.php';
require_once __DIR__ . '/GestionFournisseur.class.php';
require_once __DIR__ . '/GestionCommande.class.php';
require_once __DIR__ . '/GestionLigneDeCommande.class.php';
require_once __DIR__ . '/GestionUtilisateur.class.php';
require_once __DIR__ . '/GestionLivraison.class.php';

/**
 * Classe GestionBoutique - Méthodes générales et de compatibilité
 * 
 * Cette classe sert de point d'entrée pour les méthodes générales
 * et fournit des méthodes de compatibilité pour les appels existants.
 * 
 * ⚠️ Les méthodes spécifiques ont été déplacées dans les classes dédiées :
 * - GestionCategorie pour les catégories
 * - GestionProduit pour les produits
 * - GestionFournisseur pour les fournisseurs
 * - GestionCommande pour les commandes
 * - GestionLigneDeCommande pour les lignes de commande
 * - GestionUtilisateur pour les utilisateurs
 * - GestionClient pour les clients
 * - GestionLivraison pour les livraisons
 */
class GestionBoutique extends ModelePDO {

    /**
     * Permet de se connecter à la base de données
     */
    public static function seConnecter() {
        parent::seConnecter();
    }

    /**
     * Se déconnecter de la base de données
     */
    public static function seDeconnecter() {
        self::$pdoCnxBase = null;
    }

    /**
     * Récupère une connexion PDO (pour compatibilité)
     * @return PDO|null
     */
    public static function getPDO() {
        self::seConnecter();
        return self::$pdoCnxBase;
    }

    // ============================================
    // MÉTHODES DE COMPATIBILITÉ - CATÉGORIES
    // ============================================
    
    /**
     * @deprecated Utiliser GestionCategorie::getLesCategories() à la place
     */
    public static function getLesCategories() {
        return GestionCategorie::getLesCategories();
    }

    /**
     * @deprecated Utiliser GestionCategorie::getCategorieById() à la place
     */
    public static function getCategorieById($id) {
        return GestionCategorie::getCategorieById($id);
    }

    /**
     * @deprecated Utiliser GestionCategorie::supprimerCategorieById() à la place
     */
    public static function SupprimerCategorieById($id) {
        return GestionCategorie::supprimerCategorieById($id);
    }

    // ============================================
    // MÉTHODES DE COMPATIBILITÉ - PRODUITS
    // ============================================

    /**
     * @deprecated Utiliser GestionProduit::getLesProduits() à la place
     */
    public static function getLesProduits() {
        return GestionProduit::getLesProduits();
    }

    /**
     * @deprecated Utiliser GestionProduit::getLesProduitsByCategorie() à la place
     */
    public static function getLesProduitsByCategorie($libelleCategorie) {
        return GestionProduit::getLesProduitsByCategorie($libelleCategorie);
    }

    /**
     * @deprecated Utiliser GestionProduit::getProduitById() à la place
     */
    public static function getProduitById($idProduit) {
        return GestionProduit::getProduitById($idProduit);
    }

    /**
     * @deprecated Utiliser GestionProduit::ajouterProduit() à la place
     */
    public static function ajouterProduit($nom, $description, $prix, $image, $idCategorie, $idFournisseur) {
        return GestionProduit::ajouterProduit($nom, $description, $prix, $image, $idCategorie, $idFournisseur);
    }

    /**
     * @deprecated Utiliser GestionProduit::modifierProduit() à la place
     */
    public static function modifierProduit($id, $nom, $description, $prix, $image, $idCategorie, $idFournisseur) {
        return GestionProduit::modifierProduit($id, $nom, $description, $prix, $image, $idCategorie, $idFournisseur);
    }

    /**
     * @deprecated Utiliser GestionProduit::supprimerProduit() à la place
     */
    public static function supprimerProduit($id) {
        return GestionProduit::supprimerProduit($id);
    }

    /**
     * @deprecated Utiliser GestionProduit::verifierStock() à la place
     */
    public static function verifierStock($idProduit, $quantite) {
        return GestionProduit::verifierStock($idProduit, $quantite);
    }

    /**
     * @deprecated Utiliser GestionProduit::getProduitsJamaisCommandes() à la place
     */
    public static function getProduitsJamaisCommandes() {
        return GestionProduit::getProduitsJamaisCommandes();
    }

    /**
     * @deprecated Utiliser GestionProduit::getTopProduitsCommandes() à la place
     */
    public static function getTopProduitsCommandes($limite = 5, $parCA = false) {
        return GestionProduit::getTopProduitsCommandes($limite, $parCA);
    }

    /**
     * @deprecated Utiliser GestionProduit::reapprovisionnerProduit() à la place
     */
    public static function reapprovisionnerProduit($idProduit, $quantite = 10) {
        return GestionProduit::reapprovisionnerProduit($idProduit, $quantite);
    }

    // ============================================
    // MÉTHODES DE COMPATIBILITÉ - CLIENTS
    // ============================================

    /**
     * @deprecated Utiliser GestionClient::getLesClients() à la place
     */
    public static function getLesClients() {
        return GestionClient::getLesClients();
    }

    /**
     * @deprecated Utiliser GestionClient::getClientById() à la place
     */
    public static function getClientById($id) {
        $resultat = GestionClient::getClientById($id);
        if ($resultat) {
            return new Client(
                $resultat->id, 
                $resultat->nom, 
                $resultat->prenom, 
                $resultat->rue, 
                $resultat->codePostal, 
                $resultat->ville, 
                $resultat->tel, 
                $resultat->email,
                $resultat->mdp
            );
        }
        return null;
    }

    /**
     * @deprecated Utiliser GestionClient::getClientByEmail() à la place
     */
    public static function getClientByEmail($email) {
        $resultat = GestionClient::getClientByEmail($email);
        if ($resultat) {
            return new Client(
                $resultat->id, 
                $resultat->nom, 
                $resultat->prenom, 
                $resultat->rue, 
                $resultat->codePostal, 
                $resultat->ville, 
                $resultat->tel, 
                $resultat->email,
                $resultat->mdp
            );
        }
        return null;
    }

    /**
     * @deprecated Utiliser GestionClient::creerClient() à la place
     */
    public static function ajouterClient($nom, $prenom, $rue, $codePostal, $ville, $tel, $email, $mdp) {
        // Note: Cette méthode ne correspond pas exactement à creerClient
        // mais on la garde pour compatibilité
        return GestionClient::creerClient($nom, $prenom, $email, $mdp, null);
    }

    /**
     * @deprecated Utiliser GestionClient::modifierClient() à la place
     */
    public static function modifierClient($id, $nom, $prenom, $rue, $codePostal, $ville, $tel, $email) {
        return GestionClient::modifierClientSansMdp($id, $nom, $prenom, $email, null, $rue, $codePostal, $ville, $tel);
    }

    /**
     * @deprecated Utiliser GestionClient::supprimerClient() à la place
     */
    public static function supprimerClient($id) {
        return GestionClient::supprimerClient($id);
    }

    // ============================================
    // MÉTHODES DE COMPATIBILITÉ - COMMANDES
    // ============================================

    /**
     * @deprecated Utiliser GestionCommande::getLesCommandes() à la place
     */
    public static function getLesCommandes() {
        return GestionCommande::getLesCommandes();
    }

    /**
     * @deprecated Utiliser GestionCommande::getCommandeById() à la place
     */
    public static function getCommandeById($id) {
        return GestionCommande::getCommandeById($id);
    }

    /**
     * @deprecated Utiliser GestionCommande::getCommandesByClientId() à la place
     */
    public static function getCommandesByClientId($idClient) {
        return GestionCommande::getCommandesByClientId($idClient);
    }

    /**
     * @deprecated Utiliser GestionCommande::ajouterCommande() à la place
     */
    public static function ajouterCommande($idClient, $date, $sousTotal = 0) {
        return GestionCommande::ajouterCommande($idClient, $date, $sousTotal);
    }

    /**
     * @deprecated Utiliser GestionCommande::modifierCommande() à la place
     */
    public static function modifierCommande($id, $date, $idClient, $sousTotal) {
        return GestionCommande::modifierCommande($id, $date, $idClient, $sousTotal);
    }

    /**
     * @deprecated Utiliser GestionCommande::supprimerCommande() à la place
     */
    public static function supprimerCommande($id) {
        return GestionCommande::supprimerCommande($id);
    }

    // ============================================
    // MÉTHODES DE COMPATIBILITÉ - FOURNISSEURS
    // ============================================

    /**
     * @deprecated Utiliser GestionFournisseur::getLesFournisseurs() à la place
     */
    public static function getLesFournisseurs() {
        return GestionFournisseur::getLesFournisseurs();
    }

    /**
     * @deprecated Utiliser GestionFournisseur::getFournisseurById() à la place
     */
    public static function getFournisseurById($id) {
        return GestionFournisseur::getFournisseurById($id);
    }

    /**
     * @deprecated Utiliser GestionFournisseur::ajouterFournisseur() à la place
     */
    public static function ajouterFournisseur($nom, $rue, $codePostal, $ville, $tel, $email) {
        return GestionFournisseur::ajouterFournisseur($nom, $rue, $codePostal, $ville, $tel, $email);
    }

    /**
     * @deprecated Utiliser GestionFournisseur::modifierFournisseur() à la place
     */
    public static function modifierFournisseur($id, $nom, $rue, $codePostal, $ville, $tel, $email) {
        return GestionFournisseur::modifierFournisseur($id, $nom, $rue, $codePostal, $ville, $tel, $email);
    }

    /**
     * @deprecated Utiliser GestionFournisseur::supprimerFournisseur() à la place
     */
    public static function supprimerFournisseur($id) {
        return GestionFournisseur::supprimerFournisseur($id);
    }

    // ============================================
    // MÉTHODES DE COMPATIBILITÉ - LIGNES DE COMMANDE
    // ============================================

    /**
     * @deprecated Utiliser GestionLigneDeCommande::getToutesLesLignesCommandes() à la place
     */
    public static function getToutesLesLignesCommandes() {
        return GestionLigneDeCommande::getToutesLesLignesCommandes();
    }

    /**
     * @deprecated Utiliser GestionLigneDeCommande::getLigneCommandeById() à la place
     */
    public static function getLigneCommandeById($idCommande, $idProduit) {
        return GestionLigneDeCommande::getLigneCommandeById($idCommande, $idProduit);
    }

    /**
     * @deprecated Utiliser GestionLigneDeCommande::getLignesCommandeById() à la place
     */
    public static function getLignesCommandeById($idCommande) {
        return GestionLigneDeCommande::getLignesCommandeById($idCommande);
    }

    /**
     * @deprecated Utiliser GestionLigneDeCommande::ajouterLigneCommande() à la place
     */
    public static function ajouterLigneCommande($idCommande, $idProduit, $quantite, $prixUnitaire, $sousTotal) {
        return GestionLigneDeCommande::ajouterLigneCommande($idCommande, $idProduit, $quantite, $prixUnitaire, $sousTotal);
    }

    /**
     * @deprecated Utiliser GestionLigneDeCommande::modifierLigneCommande() à la place
     */
    public static function modifierLigneCommande($idCommande, $idProduit, $quantite) {
        return GestionLigneDeCommande::modifierLigneCommande($idCommande, $idProduit, $quantite);
    }

    /**
     * @deprecated Utiliser GestionLigneDeCommande::supprimerLigneCommande() à la place
     */
    public static function supprimerLigneCommande($idCommande, $idProduit) {
        return GestionLigneDeCommande::supprimerLigneCommande($idCommande, $idProduit);
    }

    // ============================================
    // MÉTHODES DE COMPATIBILITÉ - UTILISATEURS
    // ============================================

    /**
     * @deprecated Utiliser GestionUtilisateur::getLesUtilisateurs() à la place
     */
    public static function getLesUtilisateurs() {
        return GestionUtilisateur::getLesUtilisateurs();
    }

    /**
     * @deprecated Utiliser GestionUtilisateur::getUtilisateurById() à la place
     */
    public static function getUtilisateurById($id) {
        return GestionUtilisateur::getUtilisateurById($id);
    }

    /**
     * @deprecated Utiliser GestionUtilisateur::getUtilisateurByLogin() à la place
     */
    public static function getUtilisateurByLogin($login) {
        return GestionUtilisateur::getUtilisateurByLogin($login);
    }

    /**
     * @deprecated Utiliser GestionUtilisateur::isAdminOK() à la place
     */
    public static function isAdminOK($login, $passe) {
        return GestionUtilisateur::isAdminOK($login, $passe);
    }

    /**
     * @deprecated Utiliser GestionUtilisateur::ajouterUtilisateur() à la place
     */
    public static function ajouterUtilisateur($login, $passe, $email, $isAdmin = 0) {
        return GestionUtilisateur::ajouterUtilisateur($login, $passe, $email, $isAdmin);
    }

    /**
     * @deprecated Utiliser GestionUtilisateur::modifierUtilisateur() à la place
     */
    public static function modifierUtilisateur($id, $login, $passe, $email, $isAdmin = 0) {
        return GestionUtilisateur::modifierUtilisateur($id, $login, $passe, $email, $isAdmin);
    }

    /**
     * @deprecated Utiliser GestionUtilisateur::supprimerUtilisateur() à la place
     */
    public static function supprimerUtilisateur($id) {
        return GestionUtilisateur::supprimerUtilisateur($id);
    }

    /**
     * @deprecated Utiliser GestionUtilisateur::setAdminToken() à la place
     */
    public static function setAdminToken($login, $token) {
        return GestionUtilisateur::setAdminToken($login, $token);
    }

    /**
     * @deprecated Utiliser GestionUtilisateur::getLoginByToken() à la place
     */
    public static function getLoginByToken($token) {
        return GestionUtilisateur::getLoginByToken($token);
    }

    // ============================================
    // MÉTHODES DE COMPATIBILITÉ - LIVRAISONS
    // ============================================

    /**
     * @deprecated Utiliser GestionLivraison::getLivraisonById() à la place
     */
    public static function getLivraisonById($id) {
        return GestionLivraison::getLivraisonById($id);
    }

    /**
     * @deprecated Utiliser GestionLivraison::getPointRelaisById() à la place
     */
    public static function getPointRelaisById($id) {
        return GestionLivraison::getPointRelaisById($id);
    }
}

?>
