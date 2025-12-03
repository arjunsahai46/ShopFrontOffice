<?php

/**
 * Validateur pour les produits
 * 
 * Centralise la validation des données produits
 */
class ProduitValidator
{
    /**
     * Valide les données d'un produit
     * 
     * @param array $donnees Données à valider
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function valider($donnees)
    {
        $errors = [];

        // Validation du nom
        if (empty($donnees['nom'])) {
            $errors['nom'] = 'Le nom du produit est obligatoire';
        } elseif (strlen($donnees['nom']) > 255) {
            $errors['nom'] = 'Le nom ne peut pas dépasser 255 caractères';
        }

        // Validation du prix
        if (empty($donnees['prix'])) {
            $errors['prix'] = 'Le prix est obligatoire';
        } elseif (!is_numeric($donnees['prix']) || $donnees['prix'] < 0) {
            $errors['prix'] = 'Le prix doit être un nombre positif';
        }

        // Validation de la quantité en stock
        if (isset($donnees['stock'])) {
            if (!is_numeric($donnees['stock']) || $donnees['stock'] < 0) {
                $errors['stock'] = 'Le stock doit être un nombre positif';
            }
        }

        // Validation de la catégorie
        if (empty($donnees['categorie'])) {
            $errors['categorie'] = 'La catégorie est obligatoire';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Valide l'ID d'un produit
     * 
     * @param mixed $id ID à valider
     * @return bool True si valide, false sinon
     */
    public static function validerId($id)
    {
        return is_numeric($id) && $id > 0;
    }
}

