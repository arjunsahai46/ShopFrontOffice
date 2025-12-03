.PHONY: help install up down test clean logs shell

# Variables
COMPOSE=docker-compose
PHP_CONTAINER=shopfront_app
DB_CONTAINER=shopfront_db

help: ## Affiche cette aide
	@echo "Commandes disponibles :"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

install: ## Installe les dépendances Composer
	composer install

up: ## Démarre les conteneurs Docker
	$(COMPOSE) up -d
	@echo "✅ Application disponible sur http://localhost:8080"

down: ## Arrête les conteneurs Docker
	$(COMPOSE) down

restart: ## Redémarre les conteneurs
	$(COMPOSE) restart

build: ## Reconstruit les images Docker
	$(COMPOSE) up --build -d

logs: ## Affiche les logs des conteneurs
	$(COMPOSE) logs -f

shell: ## Ouvre un shell dans le conteneur PHP
	$(COMPOSE) exec app bash

db-shell: ## Ouvre un shell MySQL
	$(COMPOSE) exec db mysql -u root -p$(shell grep MYSQL_ROOT_PASSWORD docker-compose.yaml | cut -d'"' -f2) prin_boutique

test: ## Lance les tests PHPUnit
	./vendor/bin/phpunit

test-coverage: ## Lance les tests avec couverture de code
	./vendor/bin/phpunit --coverage-html coverage/

lint: ## Vérifie la syntaxe PHP
	find application -name "*.php" -exec php -l {} \;
	find config -name "*.php" -exec php -l {} \;

clean: ## Nettoie les fichiers temporaires
	rm -rf vendor/
	rm -rf coverage/
	$(COMPOSE) down -v
	@echo "✅ Nettoyage terminé"

reset-db: ## Réinitialise la base de données
	$(COMPOSE) down -v
	$(COMPOSE) up -d
	@echo "✅ Base de données réinitialisée"

composer-update: ## Met à jour les dépendances Composer
	composer update

dump-autoload: ## Régénère l'autoload Composer
	composer dump-autoload

status: ## Affiche le statut des conteneurs
	$(COMPOSE) ps

