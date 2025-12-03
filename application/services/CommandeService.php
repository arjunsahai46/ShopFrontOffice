<?php

/**
 * Service de gestion des commandes
 * 
 * Couche de logique métier pour les commandes
 */
class CommandeService
{
    /**
     * Crée une nouvelle commande
     * 
     * @param array $donneesCommande Données de la commande
     * @return int|false ID de la commande créée ou false en cas d'erreur
     */
    public static function creerCommande($donneesCommande)
    {
        require_once chemin(Paths::MODELES . 'GestionCommande.class.php');
        return GestionCommande::creerCommande($donneesCommande);
    }

    /**
     * Récupère les commandes d'un client
     * 
     * @param int $clientId ID du client
     * @return array Liste des commandes
     */
    public static function getCommandesByClient($clientId)
    {
        require_once chemin(Paths::MODELES . 'GestionCommande.class.php');
        return GestionCommande::getCommandesByClient($clientId);
    }

    /**
     * Récupère une commande par son ID
     * 
     * @param int $commandeId ID de la commande
     * @return object|null Commande ou null si non trouvée
     */
    public static function getCommandeById($commandeId)
    {
        require_once chemin(Paths::MODELES . 'GestionCommande.class.php');
        return GestionCommande::getCommandeById($commandeId);
    }

    /**
     * Calcule le total d'une commande
     * 
     * @param int $commandeId ID de la commande
     * @return float Total de la commande
     */
    public static function calculerTotalCommande($commandeId)
    {
        require_once chemin(Paths::MODELES . 'GestionCommande.class.php');
        return GestionCommande::calculerTotal($commandeId);
    }
}

