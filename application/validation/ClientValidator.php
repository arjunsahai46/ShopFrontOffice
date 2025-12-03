<?php

/**
 * Validateur pour les clients
 * 
 * Centralise la validation des données clients
 */
class ClientValidator
{
    /**
     * Valide les données d'un client
     * 
     * @param array $donnees Données à valider
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function valider($donnees)
    {
        $errors = [];

        // Validation de l'email
        if (empty($donnees['email'])) {
            $errors['email'] = 'L\'email est obligatoire';
        } elseif (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email n\'est pas valide';
        }

        // Validation du mot de passe
        if (isset($donnees['motDePasse'])) {
            if (empty($donnees['motDePasse'])) {
                $errors['motDePasse'] = 'Le mot de passe est obligatoire';
            } elseif (strlen($donnees['motDePasse']) < 6) {
                $errors['motDePasse'] = 'Le mot de passe doit contenir au moins 6 caractères';
            }
        }

        // Validation du nom
        if (empty($donnees['nom'])) {
            $errors['nom'] = 'Le nom est obligatoire';
        } elseif (strlen($donnees['nom']) > 100) {
            $errors['nom'] = 'Le nom ne peut pas dépasser 100 caractères';
        }

        // Validation du prénom
        if (empty($donnees['prenom'])) {
            $errors['prenom'] = 'Le prénom est obligatoire';
        } elseif (strlen($donnees['prenom']) > 100) {
            $errors['prenom'] = 'Le prénom ne peut pas dépasser 100 caractères';
        }

        // Validation du téléphone (optionnel mais doit être valide si présent)
        if (!empty($donnees['telephone']) && !preg_match('/^[0-9+\-\s()]+$/', $donnees['telephone'])) {
            $errors['telephone'] = 'Le format du téléphone n\'est pas valide';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Valide les données de connexion
     * 
     * @param array $donnees Données à valider
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validerConnexion($donnees)
    {
        $errors = [];

        if (empty($donnees['email'])) {
            $errors['email'] = 'L\'email est obligatoire';
        } elseif (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email n\'est pas valide';
        }

        if (empty($donnees['motDePasse'])) {
            $errors['motDePasse'] = 'Le mot de passe est obligatoire';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}

