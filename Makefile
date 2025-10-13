.PHONY: install start stop build watch clean help

help: ## Affiche l'aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Installation complète du projet
	@./install.sh

start: ## Démarre tous les conteneurs
	@docker compose up -d
	@echo "✅ Projet démarré"
	@echo "🌐 Web: http://localhost:8000"
	@echo "🗄️  phpMyAdmin: http://localhost:8081"

stop: ## Arrête tous les conteneurs
	@docker compose down

build: ## Build les assets en production
	@docker compose run --rm node npm run build
	@echo "✅ Assets buildés"

watch: ## Lance le watch des assets (mode dev)
	@docker compose up node

dev: ## Démarre le projet en mode dev avec watch
	@docker compose up -d web db phpmyadmin
	@docker compose up node

clean: ## Nettoie les fichiers temporaires et conteneurs
	@docker compose down -v
	@rm -rf node_modules vendor var/cache/* var/log/*
	@echo "✅ Nettoyage terminé"

logs: ## Affiche les logs
	@docker compose logs -f

composer-install: ## Installe les dépendances PHP
	@docker compose run --rm web composer install

npm-install: ## Installe les dépendances Node
	@docker compose run --rm node npm install

db-create: ## Crée la base de données
	@docker compose exec web php bin/console doctrine:database:create --if-not-exists

db-migrate: ## Exécute les migrations
	@docker compose exec web php bin/console doctrine:migrations:migrate --no-interaction

db-reset: ## Reset la base de données
	@docker compose exec web php bin/console doctrine:database:drop --force --if-exists
	@docker compose exec web php bin/console doctrine:database:create
	@docker compose exec web php bin/console doctrine:migrations:migrate --no-interaction

cache-clear: ## Vide le cache Symfony
	@docker compose exec web php bin/console cache:clear

check: ## Vérifie l'installation
	@./check.sh

fix-assets: ## Corrige les problèmes d'assets (yarn/npm)
	@./fix-assets.sh
