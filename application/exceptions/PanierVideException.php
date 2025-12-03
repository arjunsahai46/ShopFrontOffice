<?php

/**
 * Exception levée quand le panier est vide
 */
class PanierVideException extends Exception
{
    public function __construct($message = "Le panier est vide", $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Retourne un message formaté pour l'utilisateur
     * 
     * @return string Message formaté
     */
    public function getMessageUtilisateur()
    {
        return "Votre panier est vide. Ajoutez des produits avant de passer commande.";
    }
}

