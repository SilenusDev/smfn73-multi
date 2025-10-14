# SMFN73 Multi-Site Symfony 7.3

Projet multi-site Symfony 7.3 avec deux bases de données séparées (SLNS et NSDM).

## 🚀 Installation rapide

```bash
# Installation automatique en une commande
./install.sh

# Démarrer le projet
make start
```

📖 **Guide de démarrage** : voir [QUICKSTART.md](QUICKSTART.md)  
📚 **Documentation complète** : voir [INSTALL.md](INSTALL.md)  
🛠️ **Stack technique** : voir [TECH_STACK.md](TECH_STACK.md)  
🆘 **Dépannage** : voir [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## Prérequis

- **Podman** (recommandé) ou Docker
- Git
- Ports disponibles: 8080, 3306, 8081, 5173

## Installation manuelle

### 1. Cloner le projet
```bash
git clone <repository-url>
cd smfn73-multi
```

### 2. Configuration de l'environnement
```bash
# Copier le fichier d'exemple Podman
cp .env.podman.example .env.podman

# Éditer le fichier .env.podman avec vos paramètres si nécessaire
# Les valeurs par défaut fonctionnent pour un environnement de développement local
```

### 3. Lancer les pods
```bash
# Démarrer tous les services
make start

# Ou directement avec le script
./scripts/symfony-orchestrator.sh start
```

### 4. Vérifier l'installation
```bash
# Voir le statut des pods
make status

# Ou
podman pod ps
```

## URLs d'accès

### Applications principales
- **Site SLNS** : http://localhost:8080/slns/
- **Site NSDM** : http://localhost:8080/nsdm/

### Outils de développement
- **phpMyAdmin** : http://localhost:8081
- **Vite Dev Server** : http://localhost:5173

### Identifiants par défaut
- **Base de données** : 
  - Utilisateur : `symfony`
  - Mot de passe : `symfony`
  - Root password : `root`

## Commandes utiles (Makefile)

### Gestion des pods

```bash
make start          # Démarre tous les pods en mode développement
make dev            # Alias de start
make prod           # Démarre en mode production (build assets)
make stop           # Arrête tous les pods
make status         # Affiche le statut de tous les pods
make clean          # Nettoie les pods et fichiers temporaires
```

### Gestion des assets (npm/Webpack Encore)

```bash
make build          # Build les assets en production
make watch          # Lance le watch des assets (mode dev)
```

### Commandes Symfony

```bash
make composer-install  # Installe les dépendances PHP
make npm-install       # Installe les dépendances Node
make db-create         # Crée la base de données
make db-migrate        # Exécute les migrations
make db-reset          # Reset la base de données
make cache-clear       # Vide le cache Symfony
```

### Services individuels

```bash
make mariadb        # Démarre MariaDB
make redis          # Démarre Redis
make web            # Démarre le pod Web (Apache + PHP)
make node           # Démarre Node.js
make phpmyadmin     # Démarre phpMyAdmin
```

## Structure du projet

```
smfn73-multi/
├── assets/              # Assets frontend (JS, CSS)
├── config/              # Configuration Symfony
├── pods/                # Configuration des pods Podman
│   ├── mariadb/        # Pod MariaDB
│   ├── redis/          # Pod Redis
│   ├── web/            # Pod Web (Apache + PHP)
│   ├── node/           # Pod Node.js
│   └── phpmyadmin/     # Pod phpMyAdmin
├── scripts/             # Scripts d'orchestration
│   ├── symfony-orchestrator.sh  # Script principal
│   ├── pod-engine.sh            # Moteur de gestion des pods
│   └── utils.sh                 # Utilitaires
├── migrations/          # Migrations de base de données
├── public/              # Point d'entrée web
├── src/                 # Code source PHP
├── templates/           # Templates Twig
├── Makefile             # Commandes make
└── .env.podman          # Variables d'environnement Podman
```

---

## 🔗 Liens Rapides

### Silenus (Thème Violet 🌙)
- [Accueil](http://localhost:8080/slns/) • [Inscription](http://localhost:8080/slns/register) • [Connexion](http://localhost:8080/slns/login)

### Insidiome (Thème Rose 🔥)
- [Accueil](http://localhost:8080/nsdm/) • [Inscription](http://localhost:8080/nsdm/register) • [Connexion](http://localhost:8080/nsdm/login)

### Administration
- [phpMyAdmin](http://localhost:8081) (symfony / symfony)

---

## 🏗️ Architecture

Architecture multisite avec 2 sites (Silenus & Insidiome) et 2 bases de données séparées.

### Accès aux services

#### 🌐 Sites Web
- **Silenus (Accueil)**: http://localhost:8080/slns/
- **Silenus (Inscription)**: http://localhost:8080/slns/register
- **Silenus (Connexion)**: http://localhost:8080/slns/login
- **Silenus (À propos)**: http://localhost:8080/slns/about

- **Insidiome (Accueil)**: http://localhost:8080/nsdm/
- **Insidiome (Inscription)**: http://localhost:8080/nsdm/register
- **Insidiome (Connexion)**: http://localhost:8080/nsdm/login
- **Insidiome (À propos)**: http://localhost:8080/nsdm/about

#### 🗄️ Base de données
- **phpMyAdmin**: http://localhost:8081
  - User: `symfony`
  - Password: `symfony`
  - Base Silenus: `slns_db`
  - Base Insidiome: `nsdm_db`

#### 🔌 Accès direct
- **MariaDB**: localhost:3306
- **Vite/Node (assets)**: localhost:5173

### Services Podman (Pods)

| Pod | Image | Port | Description |
|-----|-------|------|-------------|
| **symfony-multi-web-pod** | php:8.3-apache | 8080 | Application Symfony |
| **symfony-multi-mariadb-pod** | mariadb:10.11 | 3306 | Base de données |
| **symfony-multi-node-pod** | node:20-alpine | 5173 | npm watch (assets) |
| **symfony-multi-redis-pod** | redis:alpine | 6379 | Cache Redis |
| **symfony-multi-phpmyadmin-pod** | phpmyadmin | 8081 | Interface BDD |

### Bases de Données

| Base | Site | Entity Manager |
|------|------|----------------|
| `slns_db` | Silenus | `silenus` |
| `nsdm_db` | Insidiome | `insidiome` |

**Isolation totale**: Chaque site a sa propre base de données.

### Services Core

- **SiteResolver** - Détecte automatiquement le site selon le domaine
- **DatabaseManager** - Sélectionne la bonne base de données
- **SiteManager** - Gère les entités Site locales

---

## 🎯 Commandes Podman

### Gestion des pods

```bash
# Démarrer tous les pods
make start

# Arrêter tous les pods
make stop

# Voir le statut
make status

# Nettoyer
make clean
```

### Commandes Symfony

```bash
# Installer les dépendances Composer
make composer-install

# Créer les bases de données
make db-create

# Exécuter les migrations
make db-migrate

# Vider le cache
make cache-clear
```

### Doctrine & Migrations (commandes avancées)

```bash
# Générer une migration pour Silenus
podman exec -it symfony-multi-web-container php bin/console doctrine:migrations:diff --em=silenus

# Générer une migration pour Insidiome
podman exec -it symfony-multi-web-container php bin/console doctrine:migrations:diff --em=insidiome

# Exécuter les migrations Silenus
podman exec -it symfony-multi-web-container php bin/console doctrine:migrations:migrate --em=silenus --no-interaction

# Exécuter les migrations Insidiome
podman exec -it symfony-multi-web-container php bin/console doctrine:migrations:migrate --em=insidiome --no-interaction
```

### Commandes Custom

```bash
# Initialiser les entités Site dans les 2 bases
podman exec -it symfony-multi-web-container php bin/console app:init-sites

# Vider le cache
podman exec -it symfony-multi-web-container php bin/console cache:clear
```

### npm (Assets)

```bash
# Installer les dépendances
make npm-install

# Build production
make build

# Watch (mode développement)
make watch
```

---

## 📚 Documentation

Documentation complète dans le dossier `/docs`:

- **[projet.md](docs/projet.md)** - Architecture détaillée du multisite
- **[roadmap.md](docs/roadmap.md)** - Planning de développement
- **[todo.md](docs/todo.md)** - Tâches à faire
- **[done.md](docs/done.md)** - Historique des réalisations
- **[architecture.md](docs/architecture.md)** - Schémas techniques

---

## 🔧 Configuration

### Variables d'environnement (.env)

```env
# Symfony
APP_ENV=dev
APP_SECRET=your_secret_key

# Base de données
DB_HOST=db
DB_USER=symfony
DB_PASSWORD=symfony
DATABASE_SLNS_URL="mysql://symfony:symfony@db:3306/slns_db"
DATABASE_NSDM_URL="mysql://symfony:symfony@db:3306/nsdm_db"

# Ports
WEB_PORT=8080
DB_PORT_EXTERNAL=3306
PHPMYADMIN_PORT=8081
NODE_PORT=5173
```

---

## 🎨 Structure du Projet

```
/smfn73-multi/
├── /src/
│   ├── /Entity/          # Entités communes (User, Site)
│   ├── /Service/         # Services (SiteResolver, DatabaseManager, SiteManager)
│   ├── /Controller/      # Controllers
│   ├── /Command/         # Commandes console
│   └── /EventSubscriber/ # Event subscribers
├── /config/
│   └── /packages/        # Configuration Doctrine, Messenger, etc.
├── /templates/           # Templates Twig
├── /assets/              # Assets frontend
├── /migrations/          # Migrations Doctrine
├── /docs/                # Documentation
└── docker-compose.yml    # Configuration Docker
```

---

## 🧪 Tests Rapides

### Vérifier que tout fonctionne

```bash
# 1. Vérifier les pods
make status
# ou
podman pod ps

# 2. Vérifier les routes
podman exec -it symfony-multi-web-container php bin/console debug:router | grep -E "(slns|nsdm)"

# 3. Vérifier les bases de données
podman exec -it symfony-multi-mariadb-container mysql -usymfony -psymfony -e "SHOW DATABASES;"

# 4. Voir les tables de chaque base
podman exec -it symfony-multi-mariadb-container mysql -usymfony -psymfony slns_db -e "SHOW TABLES;"
podman exec -it symfony-multi-mariadb-container mysql -usymfony -psymfony nsdm_db -e "SHOW TABLES;"
```

### Test d'inscription

1. **Silenus** : http://localhost:8080/slns/register
   - Créer un compte (ex: `test@silenus.com`)
   - Se connecter

2. **Insidiome** : http://localhost:8080/nsdm/register
   - Créer un compte (ex: `test@insidiome.com`)
   - Se connecter

3. **Vérifier l'isolation** :
   ```bash
   # Les users doivent être dans des bases séparées
   podman exec -it symfony-multi-mariadb-container mysql -usymfony -psymfony slns_db -e "SELECT id, email FROM user;"
   podman exec -it symfony-multi-mariadb-container mysql -usymfony -psymfony nsdm_db -e "SELECT id, email FROM user;"
   ```

---

## 🚨 Dépannage

### Les pods ne démarrent pas

```bash
# Vérifier les logs
make logs
# ou
podman pod ps
podman logs symfony-multi-web-pod

# Vérifier que les ports ne sont pas déjà utilisés
ss -tulpn | grep -E "8080|8081|3306|5173"
```

### Erreur de connexion à la base de données

```bash
# Vérifier que le pod MariaDB est démarré
podman pod ps | grep mariadb

# Se connecter au conteneur pour vérifier
podman exec -it symfony-multi-mariadb-container mysql -u symfony -psymfony
```

### Les assets ne se compilent pas

```bash
# Redémarrer le pod Node
./scripts/symfony-orchestrator.sh stop node
./scripts/symfony-orchestrator.sh start node

# Vérifier les logs
podman logs symfony-multi-node-container
```

### Rebuild complet

```bash
# Nettoyer et redémarrer
make clean
make start
```

---

## 📝 Notes

- **Isolation des données**: Les 2 sites ne partagent AUCUNE donnée
- **Code mutualisé**: Les entités et services sont partagés (DRY)
- **Détection automatique**: Le site est détecté selon le domaine (localhost → silenus par défaut)

---

## 🤖 SEO & AI Optimization

### SEO traditionnel
- ✅ Meta tags complets (title, description, keywords, canonical)
- ✅ Open Graph pour réseaux sociaux
- ✅ Directives robots (index/noindex)
- ✅ Sitemap.xml et robots.txt

### AI SEO (ChatGPT, Claude, Bard, etc.)
- ✅ Schema.org / JSON-LD pour structured data
- ✅ Meta tags spécifiques IA (chatgpt, ai-content-declaration)
- ✅ robots.txt avancé avec crawlers IA (GPTBot, anthropic-ai, etc.)
- ✅ ai.txt pour directives IA spécifiques

### Documentation
- **Guide SEO** : [`docs/seo-meta-tags.md`](docs/seo-meta-tags.md)
- **Guide AI SEO** : [`docs/ai-seo-guide.md`](docs/ai-seo-guide.md)
- **Fichiers** : `/public/robots.txt`, `/public/ai.txt`

### Crawlers IA supportés
- OpenAI (ChatGPT) - `GPTBot`
- Anthropic (Claude) - `anthropic-ai`
- Google AI (Bard/Gemini) - `Google-Extended`
- Perplexity AI - `PerplexityBot`
- Apple Intelligence - `Applebot-Extended`
- Common Crawl - `CCBot`

---

*Dernière mise à jour: 2025-10-09*
