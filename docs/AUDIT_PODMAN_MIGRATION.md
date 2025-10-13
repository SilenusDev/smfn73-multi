# 📊 Audit Migration Docker → Podman

**Date**: 13 octobre 2025  
**Projet**: smfn_73_multi  
**Objectif**: Installation propre et automatisée avec Podman

---

## 🔍 Analyse comparative Docker vs Podman

### Architecture Docker (ancienne)
```
docker-compose.yml
├── web (Apache + PHP)
├── db (MariaDB)
├── node (Webpack watch)
└── phpmyadmin (optionnel)
```

### Architecture Podman (nouvelle)
```
pods/
├── web/pod.yml (Apache + PHP + Composer)
├── mariadb/pod.yml
├── redis/pod.yml
├── node/pod.yml
└── fastapi/pod.yml (optionnel)
```

---

## ✅ Ce qui fonctionne actuellement

### 1. Scripts d'orchestration
- ✅ `scripts/symfony-orchestrator.sh` - Gestion des pods
- ✅ `scripts/pod-engine.sh` - Moteur de gestion
- ✅ `scripts/utils.sh` - Fonctions utilitaires

### 2. Configuration
- ✅ `.env.podman` - Variables d'environnement
- ✅ Multi-sites (silenus.local + insidiome.local)
- ✅ VirtualHosts Apache configurés
- ✅ Utilisateur non-root MariaDB (`symfony:symfony`)

### 3. Services opérationnels
- ✅ Apache + PHP-FPM (pod combiné)
- ✅ MariaDB 10.11
- ✅ Redis
- ✅ Node.js (Webpack)
- ✅ Composer intégré

---

## 📁 Scripts racine existants (Docker)

### Scripts à la racine du projet

| Script | Fonction | Dépendance Docker | À adapter |
|--------|----------|-------------------|-----------|
| `install.sh` | Installation complète | ✅ Oui | ✅ Créer `install-podman.sh` |
| `check.sh` | Vérification installation | ✅ Oui | ✅ Créer `check-podman.sh` |
| `fix-assets.sh` | Correction problèmes assets | ✅ Oui | ✅ Adapter pour Podman |
| `get-docker.sh` | Installation Docker | ❌ Non pertinent | ❌ Supprimer ou ignorer |

### Analyse détaillée

#### `install.sh` (62 lignes)
```bash
Fonctions:
1. Vérifier Docker
2. Nettoyer anciens fichiers (Yarn)
3. Copier .env
4. composer install (via docker compose)
5. npm install (via docker compose)
6. npm run build
7. Créer bases de données
8. Migrations
```
**Statut**: ❌ Incompatible Podman - À recréer

#### `check.sh` (95 lignes)
```bash
Vérifications:
1. Docker installé
2. Conteneurs web/db démarrés
3. Fichier .env présent
4. Dépendances PHP/Node installées
5. Assets buildés (taille CSS)
6. Configuration TypeScript
7. Configuration Tailwind
8. Pas de fichiers Yarn
9. Pas de doublons .js
```
**Statut**: ⚠️ Partiellement adaptable - Remplacer checks Docker par Podman

#### `fix-assets.sh` (43 lignes)
```bash
Actions:
1. Arrêter conteneur node
2. Nettoyer .yarn, yarn.lock
3. Supprimer doublons .js
4. Nettoyer node_modules
5. npm install (via docker compose)
6. npm run build
7. Redémarrer conteneur node
8. Vider cache Symfony
```
**Statut**: ⚠️ À adapter - Remplacer docker compose par podman exec

#### `get-docker.sh` (721 lignes)
Script officiel d'installation Docker - **Non pertinent pour Podman**

---

## ❌ Ce qui manque pour l'automatisation

### 1. Installation initiale (`install-podman.sh`)

#### Docker (ancien)
```bash
./install.sh
├── Vérifier Docker
├── Nettoyer anciens fichiers
├── Copier .env
├── composer install
├── npm install
├── npm run build
├── Créer bases de données
└── Migrations
```

#### Podman (à créer)
```bash
./install-podman.sh
├── Vérifier Podman
├── Vérifier image PHP personnalisée
├── Copier .env.podman
├── Configurer /etc/hosts (multi-sites)
├── Créer répertoires pods/*/data
├── Démarrer pods (mariadb, redis, web, node)
├── composer install (via pod web)
├── npm install (via pod node)
├── npm run build
├── Créer bases de données
├── Migrations
└── Permissions var/
```

### 2. Makefile adapté à Podman

#### Commandes à adapter

| Commande Docker | Équivalent Podman | Statut |
|----------------|-------------------|--------|
| `make install` | Installation complète | ❌ À créer |
| `make start` | `./scripts/symfony-orchestrator.sh start symfony` | ⚠️ Partiel |
| `make stop` | `./scripts/symfony-orchestrator.sh stop symfony` | ✅ OK |
| `make dev` | `./scripts/symfony-orchestrator.sh dev` | ✅ OK |
| `make build` | `./scripts/symfony-orchestrator.sh build` | ✅ OK |
| `make watch` | Pod node en mode watch | ⚠️ À tester |
| `make clean` | Nettoyage pods + données | ❌ À créer |
| `make logs` | Logs des pods | ❌ À créer |
| `make composer-install` | Via pod composer | ❌ À créer |
| `make npm-install` | Via pod node | ❌ À créer |
| `make db-create` | Via pod web | ❌ À créer |
| `make db-migrate` | Via pod web | ❌ À créer |
| `make db-reset` | Via pod web | ❌ À créer |
| `make cache-clear` | Via pod web | ❌ À créer |
| `make check` | Vérification Podman | ❌ À adapter |

### 3. Script de vérification (`check.sh`)

#### À vérifier pour Podman
- ✅ Podman installé
- ❌ Image PHP personnalisée buildée
- ❌ Pods démarrés (web, mariadb, redis, node)
- ❌ Ports disponibles (6900, 6909, 6910, 6904)
- ❌ /etc/hosts configuré (silenus.local, insidiome.local)
- ✅ Fichier .env.podman présent
- ✅ Dépendances PHP/Node installées
- ✅ Assets buildés
- ❌ Bases de données créées
- ❌ Permissions var/ correctes

### 4. Configuration /etc/hosts

#### Automatisation nécessaire
```bash
# Vérifier si les entrées existent
# Ajouter si manquantes :
127.0.0.1   silenus.local www.silenus.local
127.0.0.1   insidiome.local www.insidiome.local
```

### 5. Image PHP personnalisée

#### Docker (ancien)
```dockerfile
docker/php/Dockerfile
├── PHP 8.3 + extensions
├── Composer
└── Configuration Apache
```

#### Podman (actuel)
- ⚠️ Utilise `localhost/symfony-php:8.3-fpm`
- ❌ Pas de Dockerfile trouvé
- ❌ Pas de script de build

**Action requise**: Créer `pods/php/Dockerfile` et script de build

### 6. Gestion des données persistantes

#### Répertoires à créer automatiquement
```
pods/
├── mariadb/data/          # Données MariaDB
├── mariadb/logs/          # Logs MariaDB
├── php/data/var/          # Cache/logs Symfony
├── php/logs/              # Logs PHP
├── apache/logs/           # Logs Apache
├── node/data/node_modules/# node_modules persistant
└── redis/data/            # Données Redis
```

#### Permissions
- MariaDB: UID 999 (mysql)
- PHP: UID 82 (www-data)
- Apache: UID 82 (www-data)

### 7. Gestion des secrets

#### Actuellement
- ⚠️ Mots de passe en clair dans `pods/*/pod.yml`
- ✅ Variables dans `.env.podman`

#### Recommandation
- Option A: Garder simple (dev uniquement)
- Option B: Utiliser Podman secrets (production)

---

## 📝 Scripts à créer

### 1. `install-podman.sh` (PRIORITAIRE)
```bash
#!/bin/bash
# Installation complète du projet avec Podman

Étapes:
1. Vérifier prérequis (Podman, buildah)
2. Copier .env.podman si nécessaire
3. Builder l'image PHP personnalisée
4. Configurer /etc/hosts (avec sudo)
5. Créer répertoires de données
6. Démarrer les pods
7. Installer dépendances (composer + npm)
8. Build assets
9. Créer bases de données
10. Migrations
11. Fixer permissions var/
```

### 2. `Makefile.podman` (PRIORITAIRE)
```makefile
# Makefile adapté pour Podman
# Wrapper autour de symfony-orchestrator.sh

Commandes:
- install: Installation complète
- start: Démarrer tous les pods
- stop: Arrêter tous les pods
- dev: Mode développement (avec watch)
- build: Build assets production
- clean: Nettoyer tout
- logs: Afficher logs
- composer-*: Commandes Composer
- npm-*: Commandes NPM
- db-*: Commandes base de données
- check: Vérification installation
```

### 3. `check-podman.sh` (PRIORITAIRE)
```bash
#!/bin/bash
# Vérification de l'installation Podman

Vérifications:
1. Podman installé
2. Image PHP buildée
3. Pods démarrés
4. Ports disponibles
5. /etc/hosts configuré
6. Fichiers .env présents
7. Dépendances installées
8. Assets buildés
9. Bases de données créées
10. Permissions correctes
```

### 4. `pods/php/Dockerfile` (PRIORITAIRE)
```dockerfile
# Image PHP personnalisée pour Podman
FROM php:8.3-fpm-alpine

Extensions:
- pdo_mysql
- intl
- opcache
- zip
- redis

Outils:
- Composer
- Symfony CLI (optionnel)
```

### 5. `build-php-image.sh` (PRIORITAIRE)
```bash
#!/bin/bash
# Build de l'image PHP personnalisée

podman build -t localhost/symfony-php:8.3-fpm pods/php/
```

### 6. `clean-podman.sh`
```bash
#!/bin/bash
# Nettoyage complet

Actions:
1. Arrêter tous les pods
2. Supprimer les pods
3. Supprimer les données (optionnel)
4. Nettoyer node_modules, vendor, var/
```

### 7. `setup-hosts.sh`
```bash
#!/bin/bash
# Configuration automatique de /etc/hosts

Actions:
1. Vérifier si entrées existent
2. Ajouter si manquantes (avec sudo)
3. Vérifier résolution DNS
```

### 8. `fix-assets-podman.sh`
```bash
#!/bin/bash
# Correction des problèmes d'assets (version Podman)

Actions:
1. Arrêter pod node
2. Nettoyer .yarn, yarn.lock
3. Supprimer doublons .js
4. Nettoyer node_modules
5. npm install (via pod node)
6. npm run build
7. Redémarrer pod node
8. Vider cache Symfony (via pod web)
```

---

## 🎯 Plan d'action par priorité

### Phase 1: Scripts essentiels (URGENT)
1. ✅ `pods/php/Dockerfile` - Image PHP
2. ✅ `build-php-image.sh` - Build image
3. ✅ `install-podman.sh` - Installation complète
4. ✅ `Makefile.podman` - Commandes simplifiées

### Phase 2: Vérification et maintenance
5. ✅ `check-podman.sh` - Vérification
6. ✅ `setup-hosts.sh` - Configuration /etc/hosts
7. ✅ `clean-podman.sh` - Nettoyage
8. ✅ `fix-assets-podman.sh` - Correction assets

### Phase 3: Optimisations
9. ⚠️ Améliorer `symfony-orchestrator.sh`
10. ⚠️ Ajouter commandes manquantes au Makefile
11. ⚠️ Documentation complète

### Phase 4: Nettoyage (optionnel)
12. ⚠️ Supprimer/archiver scripts Docker (`install.sh`, `check.sh`, `fix-assets.sh`)
13. ⚠️ Supprimer `get-docker.sh`
14. ⚠️ Supprimer `docker-compose.yml` (ou renommer en `.docker-compose.yml.backup`)

---

## 📋 Checklist d'installation propre

### Prérequis
- [ ] Podman installé (`podman --version`)
- [ ] Buildah installé (`buildah --version`)
- [ ] Git installé
- [ ] Accès sudo (pour /etc/hosts)

### Installation
- [ ] Cloner le projet
- [ ] Copier `.env.example` → `.env.podman`
- [ ] Éditer `.env.podman` (mots de passe)
- [ ] Builder l'image PHP
- [ ] Configurer /etc/hosts
- [ ] Lancer `./install-podman.sh`

### Vérification
- [ ] Pods démarrés (`podman pod ps`)
- [ ] Sites accessibles (silenus.local:6900, insidiome.local:6900)
- [ ] Bases de données créées
- [ ] Assets compilés
- [ ] Pas d'erreurs dans les logs

---

## 🔧 Commandes équivalentes

| Action | Docker | Podman |
|--------|--------|--------|
| Démarrer | `docker compose up -d` | `./scripts/symfony-orchestrator.sh dev` |
| Arrêter | `docker compose down` | `./scripts/symfony-orchestrator.sh stop symfony` |
| Logs | `docker compose logs -f web` | `podman logs -f symfony-web-pod-symfony-apache` |
| Exec | `docker compose exec web bash` | `podman exec -it symfony-web-pod-symfony-php sh` |
| Composer | `docker compose run --rm web composer install` | `podman exec symfony-web-pod-symfony-composer composer install` |
| Console | `docker compose exec web php bin/console` | `podman exec -w /usr/local/apache2/htdocs symfony-web-pod-symfony-php php bin/console` |

---

## 🚨 Points d'attention

### 1. Permissions
- Les données MariaDB appartiennent à UID 999
- Impossible de faire `git add` sur `pods/mariadb/data/`
- **Solution**: Ajouter à `.gitignore`

### 2. Chemins
- Docker: `/var/www/html`
- Podman: `/usr/local/apache2/htdocs`
- **Important**: Cohérence entre Apache et PHP

### 3. Réseau
- Docker: Réseau bridge automatique
- Podman: Pods partagent localhost
- **Attention**: MariaDB accessible via `localhost:6909` (pas `db:3306`)

### 4. Multi-sites
- Nécessite configuration `/etc/hosts`
- VirtualHosts Apache configurés
- SiteResolver Symfony détecte le domaine

### 5. Image PHP
- Docker: Build automatique via `docker-compose.yml`
- Podman: Build manuel requis
- **Action**: Créer script de build

---

## 📚 Documentation à créer

1. ✅ `PODMAN_USAGE.md` - Guide d'utilisation (existe)
2. ✅ `HOSTS_CONFIGURATION.md` - Config multi-sites (existe)
3. ❌ `INSTALLATION_PODMAN.md` - Guide d'installation complet
4. ❌ `TROUBLESHOOTING_PODMAN.md` - Résolution de problèmes
5. ❌ `MIGRATION_DOCKER_TO_PODMAN.md` - Guide de migration

---

## 🎓 Recommandations

### Pour le développement
1. Utiliser `make` pour toutes les commandes
2. Garder les mots de passe simples en dev
3. Documenter chaque commande
4. Tester l'installation sur machine vierge

### Pour la production
1. Utiliser Podman secrets
2. Mots de passe forts
3. Désactiver phpMyAdmin
4. Logs centralisés
5. Monitoring des pods

### Pour la maintenance
1. Script de backup des données
2. Script de mise à jour des images
3. Versionner les configurations
4. Tests automatisés

---

## ✅ Conclusion

### Ce qui est fait
- ✅ Infrastructure Podman fonctionnelle
- ✅ Multi-sites opérationnel
- ✅ Scripts d'orchestration avancés
- ✅ Documentation de base

### Ce qui manque (CRITIQUE)
- ❌ Script d'installation automatique
- ❌ Makefile adapté à Podman
- ❌ Image PHP personnalisée
- ❌ Script de vérification
- ❌ Configuration automatique /etc/hosts

### Estimation du travail

| Phase | Tâches | Temps estimé |
|-------|--------|--------------|
| **Phase 1** | 4 scripts essentiels | 4-6h |
| **Phase 2** | 4 scripts maintenance | 3-4h |
| **Phase 3** | Optimisations | 2-3h |
| **Phase 4** | Nettoyage (optionnel) | 1h |
| **Tests** | Validation complète | 2-3h |
| **Documentation** | Guides utilisateur | 2-3h |
| **Total** | | **14-20 heures** |

### Prochaine étape
**Créer les 8 scripts de la Phase 1 et 2** pour permettre une installation automatisée complète.

---

## 📊 Récapitulatif des scripts

### Scripts à créer (8 prioritaires)

| # | Script | Priorité | Temps | Dépendances |
|---|--------|----------|-------|-------------|
| 1 | `pods/php/Dockerfile` | 🔴 Critique | 30min | - |
| 2 | `build-php-image.sh` | 🔴 Critique | 15min | Dockerfile |
| 3 | `install-podman.sh` | 🔴 Critique | 2h | build-php-image.sh |
| 4 | `Makefile.podman` | 🔴 Critique | 1h | symfony-orchestrator.sh |
| 5 | `check-podman.sh` | 🟡 Important | 1h | - |
| 6 | `setup-hosts.sh` | 🟡 Important | 30min | - |
| 7 | `clean-podman.sh` | 🟡 Important | 30min | - |
| 8 | `fix-assets-podman.sh` | 🟡 Important | 30min | - |

### Scripts existants à conserver

| Script | Statut | Action |
|--------|--------|--------|
| `scripts/symfony-orchestrator.sh` | ✅ OK | Conserver |
| `scripts/pod-engine.sh` | ✅ OK | Conserver |
| `scripts/utils.sh` | ✅ OK | Conserver |
| `scripts/generate-pod-configs.sh` | ⚠️ Non utilisé | Supprimer |

### Scripts Docker à archiver

| Script | Action recommandée |
|--------|-------------------|
| `install.sh` | Renommer en `install-docker.sh.backup` |
| `check.sh` | Renommer en `check-docker.sh.backup` |
| `fix-assets.sh` | Renommer en `fix-assets-docker.sh.backup` |
| `get-docker.sh` | Supprimer |
| `docker-compose.yml` | Renommer en `docker-compose.yml.backup` |
