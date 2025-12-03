# Base de données

## Structure

- `migrations/` : Scripts de migration SQL (versionnés)
- `seeders/` : Données de test/démo (optionnel)
- `init.sql` : Script d'initialisation complète (Docker)

## Migrations

Les migrations sont numérotées pour garantir l'ordre d'exécution :

- `001_init_database.sql` : Création initiale de la base de données
- `002_...` : Futures migrations (ajout de colonnes, tables, etc.)

## Utilisation

### Docker (automatique)
Le script `docker/sql/init.sql` est exécuté automatiquement au démarrage du conteneur MySQL.

### Manuel
```bash
mysql -u root -p prin_boutique < database/migrations/001_init_database.sql
```

## Bonnes pratiques

- ✅ Une migration = une modification atomique
- ✅ Toujours tester en local avant de commit
- ✅ Documenter les migrations complexes
- ✅ Ne jamais modifier une migration déjà commitée (créer une nouvelle)

