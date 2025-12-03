<?php

/**
 * Service de gestion du panier
 * 
 * Couche de logique métier pour le panier
 */
class PanierService
{
    /**
     * Ajoute un produit au panier
     * 
     * @param int $produitId ID du produit
     * @param int $quantite Quantité à ajouter
     * @return bool True si succès, false sinon
     */
    public static function ajouterProduit($produitId, $quantite = 1)
    {
        require_once chemin(Paths::MODELES . 'GestionPanier.class.php');
        return GestionPanier::ajouterAuPanier($produitId, $quantite);
    }

    /**
     * Retire un produit du panier
     * 
     * @param int $produitId ID du produit
     * @return bool True si succès, false sinon
     */
    public static function retirerProduit($produitId)
    {
        require_once chemin(Paths::MODELES . 'GestionPanier.class.php');
        return GestionPanier::retirerDuPanier($produitId);
    }

    /**
     * Récupère le contenu du panier
     * 
     * @return array Contenu du panier
     */
    public static function getContenu()
    {
        require_once chemin(Paths::MODELES . 'GestionPanier.class.php');
        return GestionPanier::getContenuPanier();
    }

    /**
     * Vide le panier
     */
    public static function vider()
    {
        require_once chemin(Paths::MODELES . 'GestionPanier.class.php');
        GestionPanier::viderPanier();
    }

    /**
     * Calcule le total du panier
     * 
     * @return float Total du panier
     */
    public static function calculerTotal()
    {
        require_once chemin(Paths::MODELES . 'GestionPanier.class.php');
        return GestionPanier::calculerTotal();
    }

    /**
     * Vérifie si le panier est vide
     * 
     * @return bool True si vide, false sinon
     */
    public static function estVide()
    {
        $contenu = self::getContenu();
        return empty($contenu);
    }
}

