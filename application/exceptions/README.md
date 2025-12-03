# Exceptions

## Rôle

Les exceptions personnalisées permettent une **gestion d'erreurs cohérente** dans toute l'application.

## Avantages

- ✅ **Messages utilisateur** : Messages clairs pour l'utilisateur
- ✅ **Gestion centralisée** : Handler global dans bootstrap.php
- ✅ **Codes HTTP appropriés** : 404, 400, etc.
- ✅ **Logging** : Erreurs loggées automatiquement

## Exceptions disponibles

- `ProduitNotFoundException` : Produit non trouvé (404)
- `PanierVideException` : Panier vide (400)
- `CommandeNotFoundException` : Commande non trouvée (404)
- `ValidationException` : Erreur de validation (400)

## Utilisation

```php
use ProduitNotFoundException;

if (!$produit) {
    throw new ProduitNotFoundException("Produit ID $id introuvable");
}
```

## Gestionnaire global

Le gestionnaire d'exceptions est configuré dans `bootstrap.php` et :
- Log les erreurs
- Affiche un message utilisateur approprié
- Redirige vers une page d'erreur si nécessaire

