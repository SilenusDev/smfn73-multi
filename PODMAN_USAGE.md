# Guide d'utilisation Podman - Symfony Multi-sites

## 🚀 Démarrage rapide

### Mode développement (avec watch automatique)
```bash
./scripts/symfony-orchestrator.sh dev
# ou
./scripts/symfony-orchestrator.sh start
```

### Mode production (build assets optimisés)
```bash
./scripts/symfony-orchestrator.sh start --prod
```

### Builder les assets uniquement
```bash
./scripts/symfony-orchestrator.sh build
```

## 📋 Commandes disponibles

### Services essentiels

| Commande | Description |
|----------|-------------|
| `./scripts/symfony-orchestrator.sh dev` | Démarre tous les services en mode développement |
| `./scripts/symfony-orchestrator.sh start --prod` | Démarre tous les services en mode production |
| `./scripts/symfony-orchestrator.sh symfony` | Démarre les 5 services essentiels |
| `./scripts/symfony-orchestrator.sh stop symfony` | Arrête les services essentiels |
| `./scripts/symfony-orchestrator.sh status symfony` | Affiche le statut des services |

### Services individuels

| Service | Démarrer | Arrêter | Statut |
|---------|----------|---------|--------|
| **Apache** | `./scripts/symfony-orchestrator.sh apache` | `./scripts/symfony-orchestrator.sh stop apache` | `./scripts/symfony-orchestrator.sh status apache` |
| **PHP-FPM** | `./scripts/symfony-orchestrator.sh php` | `./scripts/symfony-orchestrator.sh stop php` | `./scripts/symfony-orchestrator.sh status php` |
| **MariaDB** | `./scripts/symfony-orchestrator.sh mariadb` | `./scripts/symfony-orchestrator.sh stop mariadb` | `./scripts/symfony-orchestrator.sh status mariadb` |
| **Redis** | `./scripts/symfony-orchestrator.sh redis` | `./scripts/symfony-orchestrator.sh stop redis` | `./scripts/symfony-orchestrator.sh status redis` |
| **Node.js** | `./scripts/symfony-orchestrator.sh node` | `./scripts/symfony-orchestrator.sh stop node` | `./scripts/symfony-orchestrator.sh status node` |

### Commandes globales

```bash
# Arrêter tous les services
./scripts/symfony-orchestrator.sh stop all

# Voir le statut de tous les services
./scripts/symfony-orchestrator.sh status all

# Nettoyer tous les services (supprime les pods)
./scripts/symfony-orchestrator.sh clean all
```

## 🔧 Commandes Symfony dans les conteneurs

### Accéder au conteneur PHP
```bash
podman exec -it symfony-php-pod-symfony-php sh
```

### Commandes Symfony
```bash
# Vider le cache
podman exec symfony-php-pod-symfony-php php bin/console cache:clear

# Migrations
podman exec symfony-php-pod-symfony-php php bin/console doctrine:migrations:migrate

# Créer une entité
podman exec -it symfony-php-pod-symfony-php php bin/console make:entity
```

### Accéder à la base de données
```bash
# Via le conteneur MariaDB
podman exec -it symfony-mariadb-pod-symfony-mariadb mysql -u symfony_user -p

# Via l'hôte (port 6909)
mysql -h localhost -P 6909 -u symfony_user -p
```

### Logs des services
```bash
# Apache
podman logs -f symfony-apache-pod-symfony-apache

# PHP-FPM
podman logs -f symfony-php-pod-symfony-php

# MariaDB
podman logs -f symfony-mariadb-pod-symfony-mariadb

# Node.js (webpack)
podman logs -f symfony-node-pod-symfony-node
```

## 🌐 URLs des services

| Service | URL | Port |
|---------|-----|------|
| **Application Symfony** | http://localhost:6900 | 6900 |
| **MariaDB** | localhost:6909 | 6909 |
| **Redis** | localhost:6910 | 6910 |
| **PHP-FPM** | localhost:6908 | 6908 |
| **Node/Webpack** | localhost:6904 | 6904 |

## 📦 Différences dev vs prod

### Mode développement (`dev`)
- ✅ Node.js en mode **watch** (recompilation automatique)
- ✅ Assets non minifiés (debugging facile)
- ✅ Source maps activées
- ✅ Hot reload des assets

### Mode production (`--prod`)
- ✅ Assets **buildés et optimisés**
- ✅ Minification CSS/JS
- ✅ Pas de watch (conteneur Node non démarré)
- ✅ Performances maximales

## 🔄 Workflow typique

### Développement local
```bash
# 1. Démarrer l'environnement
./scripts/symfony-orchestrator.sh dev

# 2. Travailler sur le code (les assets se recompilent automatiquement)

# 3. Voir les logs si besoin
podman logs -f symfony-node-pod-symfony-node

# 4. Arrêter quand terminé
./scripts/symfony-orchestrator.sh stop symfony
```

### Déploiement production
```bash
# 1. Builder les assets
./scripts/symfony-orchestrator.sh build

# 2. Démarrer en mode production
./scripts/symfony-orchestrator.sh start --prod

# 3. Vérifier le statut
./scripts/symfony-orchestrator.sh status all
```

## 🐛 Dépannage

### Ports déjà utilisés
Si les ports 6908, 6909, 6910 sont occupés :
```bash
# Voir ce qui utilise les ports
ss -tulpn | grep -E ':(6908|6909|6910)'

# Nettoyer les pods
./scripts/symfony-orchestrator.sh clean all
```

### Rebuild des assets
```bash
# Forcer un rebuild complet
./scripts/symfony-orchestrator.sh build
```

### Réinitialiser complètement
```bash
# Tout nettoyer
./scripts/symfony-orchestrator.sh clean all

# Redémarrer
./scripts/symfony-orchestrator.sh dev
```

## 📚 Équivalences Docker → Podman

| Docker Compose | Podman |
|----------------|--------|
| `docker compose up -d` | `./scripts/symfony-orchestrator.sh dev` |
| `docker compose up node` | `./scripts/symfony-orchestrator.sh node` |
| `docker compose run --rm node npm run build` | `./scripts/symfony-orchestrator.sh build` |
| `docker compose exec web php bin/console` | `podman exec symfony-php-pod-symfony-php php bin/console` |
| `docker compose logs -f web` | `podman logs -f symfony-apache-pod-symfony-apache` |
| `docker compose down` | `./scripts/symfony-orchestrator.sh stop all` |
