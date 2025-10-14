.PHONY: install start stop build watch clean help status logs composer-install npm-install db-create db-migrate db-reset cache-clear check fix-assets dev prod

help: ## Affiche l'aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Installation complète du projet
	@./install.sh

start: ## Démarre tous les pods en mode développement
	@./scripts/symfony-orchestrator.sh start

dev: ## Démarre le projet en mode dev avec watch (alias de start)
	@./scripts/symfony-orchestrator.sh dev

prod: ## Démarre le projet en mode production
	@./scripts/symfony-orchestrator.sh start --prod

stop: ## Arrête tous les pods
	@./scripts/symfony-orchestrator.sh stop all

stop-symfony: ## Arrête uniquement les services essentiels Symfony
	@./scripts/symfony-orchestrator.sh stop symfony

build: ## Build les assets en production
	@./scripts/symfony-orchestrator.sh build
	@echo "✅ Assets buildés"

watch: ## Lance le watch des assets (mode dev)
	@./scripts/symfony-orchestrator.sh node

clean: ## Nettoie les pods et fichiers temporaires
	@./scripts/symfony-orchestrator.sh clean all
	@rm -rf var/cache/* var/log/*
	@echo "✅ Nettoyage terminé"

clean-symfony: ## Nettoie uniquement les services essentiels Symfony
	@./scripts/symfony-orchestrator.sh clean symfony

status: ## Affiche le statut de tous les pods
	@./scripts/symfony-orchestrator.sh status all

status-symfony: ## Affiche le statut des services essentiels Symfony
	@./scripts/symfony-orchestrator.sh status symfony

logs: ## Affiche les logs des pods
	@podman pod ps
	@echo ""
	@echo "Pour voir les logs d'un pod spécifique:"
	@echo "  podman logs -f symfony-multi-web-pod"
	@echo "  podman logs -f symfony-multi-mariadb-pod"

composer-install: ## Installe les dépendances PHP
	@podman exec -it symfony-multi-web-container composer install

npm-install: ## Installe les dépendances Node
	@podman exec -it symfony-multi-node-container npm install

db-create: ## Crée la base de données
	@podman exec -it symfony-multi-web-container php bin/console doctrine:database:create --if-not-exists

db-migrate: ## Exécute les migrations
	@podman exec -it symfony-multi-web-container php bin/console doctrine:migrations:migrate --no-interaction

db-reset: ## Reset la base de données
	@podman exec -it symfony-multi-web-container php bin/console doctrine:database:drop --force --if-exists
	@podman exec -it symfony-multi-web-container php bin/console doctrine:database:create
	@podman exec -it symfony-multi-web-container php bin/console doctrine:migrations:migrate --no-interaction

cache-clear: ## Vide le cache Symfony
	@podman exec -it symfony-multi-web-container php bin/console cache:clear

check: ## Vérifie l'installation
	@./scripts/check-podman.sh

fix-assets: ## Corrige les problèmes d'assets
	@./scripts/fix-assets-podman.sh

# Commandes individuelles pour les services
mariadb: ## Démarre MariaDB
	@./scripts/symfony-orchestrator.sh mariadb

redis: ## Démarre Redis
	@./scripts/symfony-orchestrator.sh redis

web: ## Démarre le pod Web (Apache + PHP)
	@./scripts/symfony-orchestrator.sh web

node: ## Démarre Node.js
	@./scripts/symfony-orchestrator.sh node

phpmyadmin: ## Démarre phpMyAdmin
	@./scripts/symfony-orchestrator.sh phpmyadmin
