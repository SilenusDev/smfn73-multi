# 📦 Installation Podman - Guide complet

Guide d'installation et d'utilisation du projet Symfony Multi-sites avec Podman.

---

## 🚀 Installation rapide

### Installation automatique (RECOMMANDÉ)

```bash
# Installation complète en une commande
./scripts/install-podman.sh
```

**Durée** : 5-10 minutes (selon connexion internet)

Ce script effectue automatiquement :
- ✅ Vérification des prérequis (Podman, Git, Buildah)
- ✅ Configuration des fichiers .env
- ✅ Nettoyage des anciens fichiers (Yarn, doublons JS)
- ✅ Build de l'image PHP personnalisée
- ✅ Configuration /etc/hosts (multi-sites)
- ✅ Création des répertoires de données
- ✅ Démarrage des services (MariaDB, Redis, Web, Node)
- ✅ Installation des dépendances (Composer + NPM)
- ✅ Build des assets
- ✅ Configuration des permissions

### Vérification

```bash
# Vérifier que tout est bien installé
./scripts/check-podman.sh
```

---

## 📋 Prérequis

### Système

- **OS** : Linux (Fedora, RHEL, Ubuntu, Debian)
- **Podman** : >= 4.0
- **Buildah** : >= 1.28 (recommandé)
- **Git** : Toute version récente
- **Accès sudo** : Pour configuration /etc/hosts

### Vérification

```bash
# Vérifier Podman
podman --version

# Vérifier Buildah
buildah --version

# Vérifier Git
git --version
```

### Installation de Podman (si nécessaire)

**Fedora / RHEL** :
```bash
sudo dnf install podman buildah
```

**Ubuntu / Debian** :
```bash
sudo apt update
sudo apt install podman buildah
```

---

## 🔧 Installation manuelle (avancé)

Si vous préférez installer étape par étape :

### 1. Configuration des fichiers

```bash
# Copier le fichier d'exemple
cp .env.example .env.podman

# Éditer les variables (optionnel)
nano .env.podman
```

### 2. Build de l'image PHP

```bash
./scripts/build-php-image.sh
```

**Image créée** : `localhost/symfony-php:8.3-fpm`

**Extensions incluses** :
- pdo_mysql, intl, zip, gd, opcache
- Configuration PHP optimisée

### 3. Configuration multi-sites

```bash
./scripts/setup-hosts.sh
```

**Domaines ajoutés** :
- `silenus.local` et `www.silenus.local`
- `insidiome.local` et `www.insidiome.local`

### 4. Démarrage des services

```bash
# Démarrer en mode développement
./scripts/symfony-orchestrator.sh dev
```

### 5. Installation des dépendances

```bash
# Composer
podman exec symfony-web-pod-symfony-composer composer install

# NPM
podman exec symfony-node-pod-symfony-node npm install

# Build assets
podman exec symfony-node-pod-symfony-node npm run build
```

### 6. Configuration des permissions

```bash
podman exec -w /usr/local/apache2/htdocs symfony-web-pod-symfony-php sh -c "mkdir -p var/log var/cache && chmod -R 777 var/"
```

---

## 🌐 Accès aux sites

Après installation, les sites sont accessibles via :

| Site | URL | Base de données |
|------|-----|-----------------|
| **Silenus** | http://silenus.local:6900/slns/ | `slns_db` |
| **Insidiome** | http://insidiome.local:6900/nsdm/ | `nsdm_db` |
| **Localhost** | http://localhost:6900 | `slns_db` (défaut) |

### Ports utilisés

| Port | Service | Description |
|------|---------|-------------|
| 6900 | Apache | Serveur web |
| 6909 | MariaDB | Base de données |
| 6910 | Redis | Cache |
| 6904 | Node.js | Webpack dev server |

---

## 📜 Scripts disponibles

### Scripts d'installation

#### install-podman.sh

Installation complète automatisée.

```bash
./scripts/install-podman.sh
```

#### check-podman.sh

Vérification de l'installation.

```bash
./scripts/check-podman.sh
```

**Vérifications effectuées** :
- Prérequis système
- Image PHP buildée
- Pods démarrés
- Ports en écoute
- Configuration /etc/hosts
- Dépendances installées
- Assets buildés
- Tests d'accès HTTP

#### build-php-image.sh

Build de l'image PHP personnalisée.

```bash
./scripts/build-php-image.sh
```

#### setup-hosts.sh

Configuration automatique de /etc/hosts.

```bash
./scripts/setup-hosts.sh
```

**Note** : Nécessite `sudo`

### Scripts de maintenance

#### clean-podman.sh

Nettoyage complet de l'environnement.

```bash
./scripts/clean-podman.sh
```

**Actions** (avec confirmations) :
- Arrêt et suppression des pods
- Suppression des données (optionnel)
- Suppression des dépendances (optionnel)
- Nettoyage du cache Symfony
- Suppression de l'image PHP (optionnel)

#### fix-assets-podman.sh

Correction des problèmes d'assets.

```bash
./scripts/fix-assets-podman.sh
```

**Actions** :
- Nettoyage des fichiers Yarn
- Suppression des doublons .js
- Réinstallation NPM
- Rebuild des assets
- Vidage du cache Symfony

---

## 🔧 Commandes quotidiennes

### Démarrage

```bash
# Démarrer en mode développement (avec npm watch)
./scripts/symfony-orchestrator.sh dev

# Vérifier le statut
./scripts/check-podman.sh
```

### Développement

```bash
# Voir les logs en temps réel
podman logs -f symfony-web-pod-symfony-apache  # Apache
podman logs -f symfony-web-pod-symfony-php     # PHP-FPM
podman logs -f symfony-node-pod-symfony-node   # Node.js

# Exécuter des commandes Symfony
podman exec -w /usr/local/apache2/htdocs symfony-web-pod-symfony-php php bin/console cache:clear
podman exec -w /usr/local/apache2/htdocs symfony-web-pod-symfony-php php bin/console doctrine:migrations:migrate

# Installer des dépendances
podman exec symfony-web-pod-symfony-composer composer require <package>
podman exec symfony-node-pod-symfony-node npm install <package>

# Build des assets
podman exec symfony-node-pod-symfony-node npm run build
./scripts/symfony-orchestrator.sh build
```

### Arrêt

```bash
# Arrêt des services essentiels
./scripts/symfony-orchestrator.sh stop symfony

# Arrêt complet
./scripts/symfony-orchestrator.sh stop all
```

---

## 🐛 Dépannage

### Installation échoue

```bash
# Vérifier les prérequis
podman --version
buildah --version

# Nettoyer et réessayer
./scripts/clean-podman.sh
./scripts/install-podman.sh
```

### Sites non accessibles

```bash
# Vérifier /etc/hosts
cat /etc/hosts | grep -E "silenus|insidiome"

# Reconfigurer
./scripts/setup-hosts.sh

# Vérifier les pods
podman pod ps

# Redémarrer le pod web
podman pod stop symfony-web-pod
podman pod rm -f symfony-web-pod
cd pods/web && podman play kube pod.yml
```

### Problèmes d'assets

```bash
# Utiliser le script de correction
./scripts/fix-assets-podman.sh
```

### Base de données non accessible

```bash
# Vérifier MariaDB
podman pod ps | grep mariadb
podman logs symfony-mariadb-pod-symfony-mariadb

# Redémarrer
./scripts/symfony-orchestrator.sh stop mariadb
./scripts/symfony-orchestrator.sh mariadb
```

### Port déjà utilisé

```bash
# Nettoyer le port
lsof -ti:6900 | xargs kill -9

# Redémarrer
./scripts/symfony-orchestrator.sh stop symfony
./scripts/symfony-orchestrator.sh dev
```

### Tout réinstaller

```bash
# Nettoyage complet
./scripts/clean-podman.sh

# Réinstallation
./scripts/install-podman.sh

# Vérification
./scripts/check-podman.sh
```

---

## 📊 Commandes Podman utiles

### Gestion des pods

```bash
# Lister les pods
podman pod ps

# Inspecter un pod
podman pod inspect symfony-web-pod

# Arrêter un pod
podman pod stop symfony-web-pod

# Supprimer un pod
podman pod rm -f symfony-web-pod
```

### Gestion des conteneurs

```bash
# Lister les conteneurs
podman ps -a

# Logs d'un conteneur
podman logs symfony-web-pod-symfony-apache
podman logs -f symfony-web-pod-symfony-php  # Temps réel

# Accéder au shell
podman exec -it symfony-web-pod-symfony-php sh
podman exec -it symfony-web-pod-symfony-apache sh

# Statistiques
podman stats
```

### Gestion des images

```bash
# Lister les images
podman images

# Supprimer une image
podman rmi localhost/symfony-php:8.3-fpm

# Nettoyer les images inutilisées
podman image prune -a
```

### Nettoyage global

```bash
# Nettoyer tout (ATTENTION : destructif)
podman system prune -a --volumes
```

---

## 🔗 Documentation complémentaire

- **Usage Podman** : `../PODMAN_USAGE.md`
- **Configuration multi-sites** : `../HOSTS_CONFIGURATION.md`
- **Audit de migration** : `AUDIT_PODMAN_MIGRATION.md`
- **Scripts d'orchestration** : `../scripts/README.md`

---

## 💡 Astuces

### Alias utiles

Ajoutez dans votre `~/.bashrc` ou `~/.zshrc` :

```bash
# Alias Podman Symfony
alias sf-start='./scripts/symfony-orchestrator.sh dev'
alias sf-stop='./scripts/symfony-orchestrator.sh stop symfony'
alias sf-check='./scripts/check-podman.sh'
alias sf-logs='podman logs -f symfony-web-pod-symfony-apache'
alias sf-console='podman exec -w /usr/local/apache2/htdocs symfony-web-pod-symfony-php php bin/console'
alias sf-composer='podman exec symfony-web-pod-symfony-composer composer'
alias sf-npm='podman exec symfony-node-pod-symfony-node npm'
```

### Développement rapide

```bash
# Redémarrage rapide après modif config
podman pod restart symfony-web-pod

# Clear cache + warmup
sf-console cache:clear && sf-console cache:warmup

# Migrations + fixtures
sf-console doctrine:migrations:migrate --no-interaction
sf-console doctrine:fixtures:load --no-interaction
```

---

## ⚠️ Notes importantes

### Permissions

- Les données MariaDB appartiennent à UID 999 (mysql)
- Ne pas versionner `pods/*/data/` et `pods/*/logs/`
- Ajouter à `.gitignore` si nécessaire

### Multi-sites

- Le `SiteResolver` détecte automatiquement le site via le domaine
- Chaque site a sa propre base de données
- Les sessions sont séparées par site

### Production

Pour un déploiement en production :
- Utiliser Podman secrets pour les mots de passe
- Désactiver le mode debug (`APP_ENV=prod`)
- Utiliser `npm run build` au lieu de `watch`
- Configurer des sauvegardes automatiques
- Mettre en place un monitoring

---

**Dernière mise à jour** : 13 octobre 2025
