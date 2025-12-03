<?php

/**
 * Exception levée lors d'une erreur de validation
 */
class ValidationException extends Exception
{
    protected $errors = [];

    public function __construct($message = "Erreur de validation", $errors = [], $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Retourne les erreurs de validation
     * 
     * @return array Erreurs de validation
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Retourne un message formaté pour l'utilisateur
     * 
     * @return string Message formaté
     */
    public function getMessageUtilisateur()
    {
        if (!empty($this->errors)) {
            return "Les données fournies ne sont pas valides : " . implode(", ", $this->errors);
        }
        return "Les données fournies ne sont pas valides.";
    }
}

