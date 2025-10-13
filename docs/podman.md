# Documentation Podman - Environnement Symfony Multi-sites

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture des pods](#architecture-des-pods)
3. [Convention de ports](#convention-de-ports)
4. [Structure des répertoires](#structure-des-répertoires)
5. [Configuration des pods](#configuration-des-pods)
6. [Scripts d'orchestration](#scripts-dorchestration)
7. [Déploiement](#déploiement)

---

## 🎯 Vue d'ensemble

### Objectif
Déployer l'application Symfony multi-sites dans un environnement Podman conteneurisé, avec une architecture modulaire inspirée de **dagda-lite**.

### Principe
- **Coexistence Docker/Podman** : Docker-compose reste en place, Podman est ajouté en parallèle
- **1 pod par service** : Isolation et gestion indépendante
- **Réseau partagé** : Communication inter-pods via `symfony-network`
- **Persistance locale** : Volumes montés dans `/pods/{service}/data`

### Architecture globale

```
smfn_73_multi/
├── docker-compose.yml          # Configuration Docker (existante)
├── podman-compose.yml          # Configuration Podman (nouvelle)
├── pods/                       # Définitions des pods Podman (1 pod = 1 image)
│   ├── apache/                 # Apache 2.4 (port 6900)
│   ├── php/                    # PHP 8.3-FPM (port 6901)
│   ├── mariadb/                # MariaDB 10.11 (port 6902)
│   ├── redis/                  # Redis cache (port 6903)
│   ├── node/                   # Node 20-alpine (port 6904)
│   ├── qdrant/                 # Qdrant vector DB (port 6905)
│   ├── fastapi/                # FastAPI (port 6906)
│   ├── phpmyadmin/             # phpMyAdmin (port 6907)
│   └── composer/               # Composer (utilitaire)
├── scripts/                    # Scripts d'orchestration
│   ├── symfony-orchestrator.sh # Orchestrateur principal (comme taranis.sh)
│   ├── pod-engine.sh           # Moteur de gestion pods (comme teine_engine.sh)
│   └── utils.sh                # Fonctions utilitaires (comme ollamh.sh)
└── docs/
    └── podman.md               # Cette documentation
```

---

## 🏗️ Architecture des pods

### Pods essentiels (démarrage automatique)

| Pod | Service | Image | Port | Rôle |
|-----|---------|-------|------|------|
| **apache** | Apache 2.4 | `httpd:2.4-alpine` | 6900 | Serveur web |
| **php** | PHP 8.3 FPM | `php:8.3-fpm-alpine` | 6901 | Runtime PHP |
| **mariadb** | MariaDB 10.11 | `mariadb:10.11` | 6902 | Base de données |
| **redis** | Redis | `redis:alpine` | 6903 | Cache Symfony |
| **node** | Node.js 20 | `node:20-alpine` | 6904 | Build assets (Vite/Webpack) |

### Pods optionnels

| Pod | Service | Image | Port | Rôle |
|-----|---------|-------|------|------|
| **qdrant** | Qdrant | `qdrant/qdrant:latest` | 6905 | Vector database (RAG/IA) |
| **fastapi** | FastAPI | `tiangolo/fastapi:python3.11` | 6906 | API Python |
| **phpmyadmin** | phpMyAdmin | `phpmyadmin:latest` | 6907 | Interface DB |
| **composer** | Composer | `composer:latest` | - | Gestion dépendances PHP |

---

## 🔌 Convention de ports

### Plage : 69XX

```bash
# Services essentiels
APACHE_PORT=6900       # Serveur web Apache
PHP_PORT=6901          # PHP-FPM
MARIADB_PORT=6902      # Base de données
REDIS_PORT=6903        # Cache Redis
NODE_PORT=6904         # Vite dev server

# Services optionnels
QDRANT_PORT=6905       # Qdrant API
FASTAPI_PORT=6906      # FastAPI
PHPMYADMIN_PORT=6907   # phpMyAdmin
```

### Réseau Podman
- **Nom** : `symfony-network`
- **Type** : Bridge
- **Isolation** : Tous les pods communiquent via ce réseau

---

## 📁 Structure des répertoires

### Structure type d'un pod (inspirée de dagda-lite)

```bash
pods/{service}/
├── pod.yml           # Définition Kubernetes YAML du pod
├── manage.sh         # Script de gestion individuel (optionnel)
├── data/             # Données persistantes (volumes)
├── config/           # Fichiers de configuration
├── init/             # Scripts d'initialisation
└── logs/             # Logs du service (optionnel)
```

### Exemple : MariaDB

```bash
pods/mariadb/
├── pod.yml           # Configuration du pod MariaDB
├── manage.sh         # Script de gestion MariaDB
├── data/             # /var/lib/mysql (persistant)
├── config/           # /etc/mysql/conf.d
├── init/             # /docker-entrypoint-initdb.d
│   ├── 01-create-databases.sql
│   └── 02-init-schema.sql
└── logs/             # Logs MariaDB
```

### Exemple : Symfony Web

```bash
pods/symfony-web/
├── pod.yml           # Configuration du pod Apache+PHP
├── data/             # Uploads, cache Symfony
├── config/           # php.ini, apache.conf
│   ├── php.ini
│   └── apache2.conf
└── logs/             # Logs Apache + PHP
```

---

## ⚙️ Configuration des pods

Voir les fichiers détaillés dans `/pods/{service}/pod.yml`

### Points clés de configuration

#### 1. Montage du code Symfony (Volume)
```yaml
volumes:
  - name: symfony-code
    hostPath:
      path: ${PROJECT_ROOT}
      type: Directory
```
**Avantage** : Hot-reload, modifications instantanées, pas de rebuild

#### 2. Persistance des données
```yaml
volumes:
  - name: mariadb-data
    hostPath:
      path: ${PODS_DIR}/mariadb/data
      type: DirectoryOrCreate
```

#### 3. Communication inter-pods
```yaml
env:
  - name: DATABASE_URL
    value: "mysql://user:pass@mariadb:3306/db"  # Nom du pod comme hostname
```

---

## 🚀 Scripts d'orchestration

### Architecture des scripts (inspirée de dagda-lite)

```
scripts/
├── symfony-orchestrator.sh    # Orchestrateur principal (équivalent taranis.sh)
├── pod-engine.sh              # Moteur de gestion pods (équivalent teine_engine.sh)
└── utils.sh                   # Fonctions utilitaires (équivalent ollamh.sh)
```

### 1. utils.sh - Fonctions utilitaires

**Fonctions principales** :
```bash
# Réseau
check_port()              # Vérifie si un port est accessible
wait_for_service()        # Attend qu'un service soit disponible
ensure_network()          # Crée le réseau Podman si nécessaire
kill_processes_on_port()  # Nettoie les processus sur un port

# Podman
check_pod_exists()        # Vérifie l'existence d'un pod
get_pod_status()          # Récupère le statut d'un pod
get_pod_name()            # Génère le nom d'un pod

# Environnement
load_env()                # Charge les variables .env
validate_env_vars()       # Valide les variables requises
```

### 2. pod-engine.sh - Moteur de gestion

**Commandes** :
```bash
./scripts/pod-engine.sh start <service>     # Démarrer un pod
./scripts/pod-engine.sh stop <service>      # Arrêter un pod
./scripts/pod-engine.sh restart <service>   # Redémarrer un pod
./scripts/pod-engine.sh status <service>    # Statut d'un pod
./scripts/pod-engine.sh health <service>    # Vérification santé
./scripts/pod-engine.sh logs <service>      # Logs d'un pod
./scripts/pod-engine.sh clean <service>     # Nettoyage complet
```

**Workflow de démarrage** :
1. Charger la configuration (`pod.yml`)
2. Vérifier les prérequis (Podman, réseau, volumes)
3. Nettoyer les anciens pods/conteneurs
4. Substituer les variables d'environnement dans `pod.yml`
5. Démarrer le pod avec `podman play kube`
6. Attendre la disponibilité du service (health check)
7. Afficher les informations de connexion

### 3. symfony-orchestrator.sh - Orchestrateur principal

**Commandes groupées** :

```bash
# Démarrage
./scripts/symfony-orchestrator.sh start          # Services essentiels
./scripts/symfony-orchestrator.sh start all      # Tous les services

# Services individuels
./scripts/symfony-orchestrator.sh mariadb        # Démarrer MariaDB
./scripts/symfony-orchestrator.sh redis          # Démarrer Redis
./scripts/symfony-orchestrator.sh symfony        # Démarrer Symfony
./scripts/symfony-orchestrator.sh node           # Démarrer Node

# Arrêt
./scripts/symfony-orchestrator.sh stop mariadb   # Arrêter un service
./scripts/symfony-orchestrator.sh stop all       # Arrêter tous

# Statut
./scripts/symfony-orchestrator.sh status         # Statut essentiels
./scripts/symfony-orchestrator.sh status all     # Statut tous

# Nettoyage
./scripts/symfony-orchestrator.sh clean all      # Nettoyer tous
```

**Ordre de démarrage** :
1. **MariaDB** (base de données)
2. **Redis** (cache)
3. **Symfony Web** (application)
4. **Node** (assets)
5. Services optionnels (Qdrant, FastAPI, phpMyAdmin)

---

## 📦 Déploiement

### Prérequis

```bash
# Installer Podman
sudo apt-get update
sudo apt-get install -y podman podman-compose

# Vérifier l'installation
podman --version
```

### Configuration initiale

#### 1. Créer le fichier `.env.podman`

```bash
# smfn_73_multi/.env.podman

# Project Configuration
PROJECT_NAME=symfony-multi
SYMFONY_ROOT=/home/sam/Bureau/dev/production/smfn_73_multi
PODS_DIR=${SYMFONY_ROOT}/pods
HOST=localhost

# Service Ports (69XX range)
SYMFONY_PORT=6900
MARIADB_PORT=6901
REDIS_PORT=6902
NODE_PORT=6903
QDRANT_PORT=6904
FASTAPI_PORT=6905
PHPMYADMIN_PORT=6906

# Database Configuration
DB_ROOT_PASSWORD=root_password
DB_USER=symfony_user
DB_PASSWORD=symfony_password
DB_SLNS_NAME=slns_db
DB_NSDM_NAME=nsdm_db

# Symfony Configuration
APP_ENV=dev
APP_SECRET=your_secret_key_here
```

#### 2. Créer la structure des répertoires

```bash
cd /home/sam/Bureau/dev/production/smfn_73_multi

# Créer les répertoires de pods
mkdir -p pods/{symfony-web,mariadb,redis,node,qdrant,fastapi,composer,phpmyadmin}/{data,config,init,logs}

# Créer les répertoires de scripts
mkdir -p scripts

# Permissions
chmod -R 755 pods/
chmod +x scripts/*.sh
```

#### 3. Créer le réseau Podman

```bash
# Créer le réseau symfony-network
podman network create symfony-network

# Vérifier
podman network ls
```

### Démarrage de l'environnement

#### Option 1 : Avec l'orchestrateur (recommandé)

```bash
# Charger les variables d'environnement
source .env.podman

# Démarrer les services essentiels
./scripts/symfony-orchestrator.sh start

# Vérifier le statut
./scripts/symfony-orchestrator.sh status
```

#### Option 2 : Manuellement (pod par pod)

```bash
# 1. Démarrer MariaDB
./scripts/pod-engine.sh start mariadb

# 2. Démarrer Redis
./scripts/pod-engine.sh start redis

# 3. Démarrer Symfony
./scripts/pod-engine.sh start symfony-web

# 4. Démarrer Node
./scripts/pod-engine.sh start node
```

### Vérification du déploiement

```bash
# Vérifier tous les pods
podman pod ps

# Vérifier les conteneurs
podman ps

# Tester les services
curl http://localhost:6900  # Symfony
curl http://localhost:6906  # phpMyAdmin
```

### Accès aux services

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Symfony** | http://localhost:6900 | - |
| **phpMyAdmin** | http://localhost:6906 | root / `${DB_ROOT_PASSWORD}` |
| **Node/Vite** | http://localhost:6903 | - |
| **Qdrant Dashboard** | http://localhost:6904/dashboard | - |
| **FastAPI Docs** | http://localhost:6905/docs | - |

---

## 🛠️ Commandes utiles

### Gestion des pods

```bash
# Lister tous les pods
podman pod ps

# Démarrer/Arrêter un pod
podman pod start symfony-mariadb-pod
podman pod stop symfony-mariadb-pod

# Supprimer un pod
podman pod rm -f symfony-mariadb-pod
```

### Logs et debugging

```bash
# Logs d'un conteneur
podman logs symfony-web

# Logs en temps réel
podman logs -f symfony-web

# Accéder au shell
podman exec -it symfony-web bash

# Exécuter une commande Symfony
podman exec symfony-web php bin/console cache:clear
```

### Nettoyage

```bash
# Arrêter tous les pods
podman pod stop --all

# Supprimer tous les pods
podman pod rm --all

# Nettoyage complet
podman system prune -a --volumes
```

---

## 🔧 Maintenance

### Sauvegarde des données

```bash
# Sauvegarder MariaDB
podman exec symfony-mariadb mysqldump -u root -p${DB_ROOT_PASSWORD} --all-databases > backup.sql

# Sauvegarder les volumes
tar -czf pods-backup.tar.gz pods/*/data
```

### Restauration

```bash
# Restaurer MariaDB
podman exec -i symfony-mariadb mysql -u root -p${DB_ROOT_PASSWORD} < backup.sql
```

---

## 🐛 Troubleshooting

### Pod ne démarre pas

```bash
# Vérifier les logs
podman logs symfony-web

# Inspecter le pod
podman pod inspect symfony-mariadb-pod
```

### Port déjà utilisé

```bash
# Identifier le processus
lsof -i :6900

# Nettoyer
./scripts/pod-engine.sh clean symfony-web
```

### Problème de réseau

```bash
# Recréer le réseau
podman network rm symfony-network
podman network create symfony-network

# Redémarrer
./scripts/symfony-orchestrator.sh stop all
./scripts/symfony-orchestrator.sh start
```

---

## 📚 Références

- [Documentation Podman](https://docs.podman.io/)
- [Podman Play Kube](https://docs.podman.io/en/latest/markdown/podman-play-kube.1.html)
- [Architecture dagda-lite](../dagda-lite/docs/ARCHITECTURE.md)

---

**Prochaines étapes** :
1. Créer les fichiers `pod.yml` pour chaque service
2. Implémenter les scripts d'orchestration
3. Tester le déploiement complet
