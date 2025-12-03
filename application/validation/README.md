# Validation

## Rôle

Les validateurs centralisent la **validation des données** avant leur traitement.

## Avantages

- ✅ **Centralisation** : Toute la validation au même endroit
- ✅ **Réutilisation** : Même validation dans plusieurs endroits
- ✅ **Cohérence** : Règles de validation uniformes
- ✅ **Maintenabilité** : Facile à modifier

## Validateurs disponibles

- `ProduitValidator` : Validation des données produits
- `ClientValidator` : Validation des données clients

## Utilisation

```php
use ProduitValidator;

$resultat = ProduitValidator::valider($donnees);
if ($resultat['valid']) {
    // Traiter les données
} else {
    // Afficher les erreurs
    foreach ($resultat['errors'] as $champ => $erreur) {
        echo $erreur;
    }
}
```

