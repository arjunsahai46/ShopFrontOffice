<?php

/**
 * Service de gestion des produits
 * 
 * Couche de logique métier pour les produits
 * Sépare la logique métier des contrôleurs
 */
class ProduitService
{
    /**
     * Récupère tous les produits
     * 
     * @return array Liste des produits
     */
    public static function getAllProduits()
    {
        require_once chemin(Paths::MODELES . 'GestionProduit.class.php');
        return GestionProduit::getLesProduits();
    }

    /**
     * Récupère les produits par catégorie
     * 
     * @param string $categorie Nom de la catégorie
     * @return array Liste des produits de la catégorie
     */
    public static function getProduitsByCategorie($categorie)
    {
        require_once chemin(Paths::MODELES . 'GestionProduit.class.php');
        return GestionProduit::getLesProduitsByCategorie($categorie);
    }

    /**
     * Récupère un produit par son ID
     * 
     * @param int $id ID du produit
     * @return object|null Produit ou null si non trouvé
     */
    public static function getProduitById($id)
    {
        require_once chemin(Paths::MODELES . 'GestionProduit.class.php');
        return GestionProduit::getProduitById($id);
    }

    /**
     * Vérifie si un produit existe
     * 
     * @param int $id ID du produit
     * @return bool True si existe, false sinon
     */
    public static function produitExists($id)
    {
        $produit = self::getProduitById($id);
        return $produit !== null;
    }
}

