# Configuration Base de Données Aiven pour Render

## ⚠️ CONFIGURATION REQUISE SUR RENDER

Pour que votre application fonctionne sur Render, vous **DEVEZ** définir la variable d'environnement `DB_PASSWORD` dans le dashboard Render.

## 📋 Étapes de Configuration

1. Allez sur votre dashboard Render : https://dashboard.render.com
2. Sélectionnez votre service web
3. Allez dans l'onglet **Environment**
4. Cliquez sur **Add Environment Variable**
5. Ajoutez la variable suivante :

```
Key: DB_PASSWORD
Value: [Votre mot de passe Aiven - voir votre dashboard Aiven]
```

6. Cliquez sur **Save Changes**
7. Render redéploiera automatiquement votre service

## ✅ Variables d'environnement complètes pour Render

Pour une configuration complète, définissez ces variables dans Render :

```
DB_HOST=mysql-shopfront-shopfrontoffice.b.aivencloud.com
DB_PORT=22674
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=[Votre mot de passe Aiven]
DB_SSL_MODE=REQUIRED
```

## 🔍 Vérification

Après le redéploiement, vérifiez les logs Render pour confirmer que la connexion fonctionne :
- Dashboard Render > Logs
- Cherchez "Connexion à la base de données établie" ou des erreurs de connexion

## 🚨 Si l'erreur persiste

Si vous voyez toujours `SQLSTATE[HY000] [2002] No such file or directory` :

1. Vérifiez que `DB_PASSWORD` est bien défini dans Render
2. Vérifiez que votre IP est autorisée dans Aiven (IP Filtering)
3. Vérifiez les logs Render pour plus de détails

