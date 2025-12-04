# 🔧 Variables d'environnement Render - Configuration Aiven

## ⚠️ CRITIQUE : Configuration requise sur Render

Pour que votre application fonctionne sur Render avec Aiven, vous **DEVEZ** définir ces variables d'environnement dans le dashboard Render.

## 📋 Variables à définir dans Render

Allez sur votre dashboard Render : https://dashboard.render.com
1. Sélectionnez votre service web
2. Allez dans l'onglet **Environment**
3. Cliquez sur **Add Environment Variable** pour chaque variable ci-dessous

### ✅ Configuration complète

| Key | Value | Description |
|-----|-------|-------------|
| `DB_HOST` | `mysql-shopfront-shopfrontoffice.b.aivencloud.com` | Host Aiven |
| `DB_PORT` | `22674` | Port Aiven |
| `DB_DATABASE` | `defaultdb` | Nom de la base de données |
| `DB_USERNAME` | `avnadmin` | Utilisateur Aiven |
| `DB_PASSWORD` | `[Votre mot de passe Aiven]` | ⚠️ Mot de passe Aiven (voir votre dashboard Aiven) |
| `DB_SSL_MODE` | `required` | ⚠️ **EN MINUSCULE** (pas REQUIRED) |
| `DB_SSL_CA` | *(laisser vide)* | Optionnel |

## ⚠️ Points critiques

1. **DB_SSL_MODE doit être en minuscule** : `required` (pas `REQUIRED` ou `Required`)
2. **DB_PASSWORD** : Récupérez-le depuis votre dashboard Aiven
3. **DB_SSL_CA** : Laisser vide (optionnel)

## 🔍 Vérification

Après avoir défini les variables :
1. Cliquez sur **Save Changes**
2. Render redéploiera automatiquement votre service
3. Vérifiez les logs Render pour confirmer que la connexion fonctionne
4. Testez l'inscription client

## 🚨 Si l'erreur persiste

Si vous voyez toujours `SQLSTATE[HY000] [2002] No such file or directory` :

1. Vérifiez que **TOUTES** les variables sont définies (sauf DB_SSL_CA)
2. Vérifiez que `DB_SSL_MODE` est bien en **minuscule** : `required`
3. Vérifiez que `DB_PASSWORD` contient bien votre mot de passe Aiven
4. Vérifiez les logs Render pour voir les valeurs récupérées par `getenv()`

## 📝 Note technique

Le fichier `config/database.php` utilise maintenant **uniquement** `getenv()` pour lire les variables d'environnement Render. Aucune valeur par défaut locale n'est utilisée, ce qui garantit que Render utilise toujours les variables d'environnement définies dans le dashboard.

