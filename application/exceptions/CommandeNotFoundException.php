<?php

/**
 * Exception levée quand une commande n'est pas trouvée
 */
class CommandeNotFoundException extends Exception
{
    public function __construct($message = "Commande non trouvée", $code = 404, Exception $previous = null)
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
        return "La commande demandée n'existe pas ou n'est plus accessible.";
    }
}

