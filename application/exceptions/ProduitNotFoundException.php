<?php

/**
 * Exception levée quand un produit n'est pas trouvé
 */
class ProduitNotFoundException extends Exception
{
    public function __construct($message = "Produit non trouvé", $code = 404, Exception $previous = null)
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
        return "Le produit demandé n'existe pas ou n'est plus disponible.";
    }
}

