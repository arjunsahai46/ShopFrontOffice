# Services

## Rôle

Les services contiennent la **logique métier** de l'application. Ils servent d'intermédiaire entre les contrôleurs et les modèles.

## Avantages

- ✅ **Contrôleurs allégés** : Les contrôleurs se contentent d'orchestrer
- ✅ **Réutilisation** : La logique métier peut être réutilisée
- ✅ **Testabilité** : Plus facile à tester unitairement
- ✅ **Séparation des responsabilités** : Chaque couche a un rôle clair

## Services disponibles

- `ProduitService` : Gestion des produits
- `CommandeService` : Gestion des commandes
- `AuthService` : Authentification (client et admin)
- `PanierService` : Gestion du panier

## Utilisation

```php
// Dans un contrôleur
use ProduitService;

$produits = ProduitService::getAllProduits();
$produit = ProduitService::getProduitById($id);
```

