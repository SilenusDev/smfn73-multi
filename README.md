<<<<<<< HEAD
# SMFN73 Multi-Site Symfony 7.3

Projet multi-site Symfony 7.3 avec deux bases de données séparées (SLNS et NSDM).

## Prérequis

- Docker ou Podman
- Docker Compose ou Podman Compose
- Git

## Installation

### 1. Cloner le projet
```bash
git clone <repository-url>
cd smfn73-multi
```

### 2. Configuration de l'environnement
```bash
# Copier le fichier d'exemple
cp .env.example .env

# Éditer le fichier .env avec vos paramètres
# Les valeurs par défaut fonctionnent pour un environnement de développement local
```

### 3. Lancer les conteneurs
```bash
# Avec Docker Compose
docker-compose up -d

# Ou avec Podman Compose
podman-compose up -d
```

### 4. Installer les dépendances
```bash
# Installer les dépendances PHP (si nécessaire)
docker exec -it symfony_web composer install

# Les dépendances Node.js sont installées automatiquement au démarrage
```

### 5. Créer les bases de données
```bash
# Les bases de données sont créées automatiquement via le script init.sql
# Vérifier que les deux bases existent : slns_db et nsdm_db
```

## URLs d'accès

### Applications principales
- **Site SLNS** : http://localhost:8080 (ou le port défini dans `WEB_PORT`)
- **Site NSDM** : http://localhost:8080 (ou le port défini dans `WEB_PORT`)

### Outils de développement
- **phpMyAdmin** : http://localhost:8081 (ou le port défini dans `PHPMYADMIN_PORT`)
- **Vite Dev Server** : http://localhost:5173 (ou le port défini dans `NODE_PORT`)

### Identifiants par défaut
- **Base de données** : 
  - Utilisateur : `symfony`
  - Mot de passe : `symfony`
  - Root password : `root`

## Commandes utiles

### Gestion des assets (Yarn/Vite)

#### Mode watch (développement)
```bash
docker exec -it symfony_node yarn watch
```

#### Build développement
```bash
docker exec -it symfony_node yarn dev
```

#### Build production
```bash
docker exec -it symfony_node yarn build
```

### Gestion des conteneurs

#### Arrêter les conteneurs
```bash
docker-compose down
# ou
podman-compose down
```

#### Voir les logs
```bash
docker-compose logs -f
# ou
podman-compose logs -f
```

#### Redémarrer un service
```bash
docker-compose restart web
# ou
podman-compose restart web
```

## Structure du projet

```
smfn73-multi/
├── assets/              # Assets frontend (JS, CSS)
├── config/              # Configuration Symfony
├── docker/              # Configuration Docker/Podman
│   ├── php/            # Dockerfile PHP/Apache
│   └── mariadb/        # Scripts SQL d'initialisation
├── migrations/          # Migrations de base de données
├── public/              # Point d'entrée web
├── src/                 # Code source PHP
├── templates/           # Templates Twig
├── docker-compose.yml   # Configuration Docker Compose
├── podman-compose.yml   # Configuration Podman Compose
└── .env                 # Variables d'environnement
```

## Dépannage

### Les conteneurs ne démarrent pas
=======
# 🚀 Projet Multisite Symfony 7.3

Architecture multisite avec 2 sites (Silenus & Insidiome) et 2 bases de données séparées.

## 🔗 Liens Rapides

### Silenus (Thème Violet 🌙)
- [Accueil](http://localhost:8080/slns/) • [Inscription](http://localhost:8080/slns/register) • [Connexion](http://localhost:8080/slns/login)

### Insidiome (Thème Rose 🔥)
- [Accueil](http://localhost:8080/nsdm/) • [Inscription](http://localhost:8080/nsdm/register) • [Connexion](http://localhost:8080/nsdm/login)

### Administration
- [phpMyAdmin](http://localhost:8081) (symfony / symfony)

---

## 📋 Table des matières

- [Démarrage rapide](#démarrage-rapide)
- [Architecture](#architecture)
- [Commandes Docker](#commandes-docker)
- [Commandes Symfony](#commandes-symfony)
- [Documentation](#documentation)

---

## 🎯 Démarrage Rapide

### Prérequis
- Docker et Docker Compose installés
- Ports disponibles: 8080, 3306, 8081, 5173

### Lancer le projet

```bash
# Démarrer tous les services
docker-compose up -d

# Vérifier l'état des services
docker-compose ps

# Initialiser les sites (première fois uniquement)
docker-compose exec web php bin/console app:init-sites
```

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

---

## 🏗️ Architecture

### Services Docker

| Service | Image | Port | Description |
|---------|-------|------|-------------|
| **web** | php:8.3-apache | 8080 | Application Symfony |
| **db** | mariadb:10.11 | 3306 | Base de données |
| **node** | node:20-alpine | 5173 | Yarn watch (assets) |
| **phpmyadmin** | phpmyadmin/phpmyadmin | 8081 | Interface BDD |

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

## 🐳 Commandes Docker

### Gestion des services

```bash
# Démarrer
docker-compose up -d

# Arrêter
docker-compose stop

# Redémarrer
docker-compose restart

# Arrêter et supprimer
docker-compose down

# Rebuild après modification
docker-compose up -d --build
```

### Logs

```bash
# Tous les services
docker-compose logs -f

# Service spécifique
docker-compose logs -f web
docker-compose logs -f node
docker-compose logs -f db
```

---

## 🎯 Commandes Symfony

### Composer

```bash
# Installer les dépendances
docker-compose exec web composer install

# Mettre à jour
docker-compose exec web composer update
```

### Doctrine & Migrations

```bash
# Générer une migration pour Silenus
docker-compose exec web php bin/console doctrine:migrations:diff --em=silenus

# Générer une migration pour Insidiome
docker-compose exec web php bin/console doctrine:migrations:diff --em=insidiome

# Exécuter les migrations Silenus
docker-compose exec web php bin/console doctrine:migrations:migrate --em=silenus --no-interaction

# Exécuter les migrations Insidiome
docker-compose exec web php bin/console doctrine:migrations:migrate --em=insidiome --no-interaction
```

### Commandes Custom

```bash
# Initialiser les entités Site dans les 2 bases
docker-compose exec web php bin/console app:init-sites

# Vider le cache
docker-compose exec web php bin/console cache:clear
```

### Yarn (Assets)

```bash
# Installer les dépendances
docker-compose exec node yarn install

# Build production
docker-compose exec node yarn build

# Watch (déjà actif dans le conteneur)
docker-compose exec node yarn watch
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
# 1. Vérifier les services
docker-compose ps

# 2. Vérifier les routes
docker-compose exec web php bin/console debug:router | grep -E "(slns|nsdm)"

# 3. Vérifier les bases de données
docker-compose exec db mysql -usymfony -psymfony -e "SHOW DATABASES;"

# 4. Voir les tables de chaque base
docker-compose exec db mysql -usymfony -psymfony slns_db -e "SHOW TABLES;"
docker-compose exec db mysql -usymfony -psymfony nsdm_db -e "SHOW TABLES;"
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
   docker-compose exec db mysql -usymfony -psymfony slns_db -e "SELECT id, email FROM user;"
   docker-compose exec db mysql -usymfony -psymfony nsdm_db -e "SELECT id, email FROM user;"
   ```

---

## 🚨 Dépannage

### Les services ne démarrent pas

>>>>>>> cd96ac4e809eb12db41eb411fae8c4001a7d21a7
```bash
# Vérifier les logs
docker-compose logs

<<<<<<< HEAD
# Vérifier que les ports ne sont pas déjà utilisés
netstat -an | findstr "8080 8081 3306 5173"
```

### Erreur de connexion à la base de données
```bash
# Vérifier que le conteneur de base de données est démarré
docker ps | findstr symfony_db

# Se connecter au conteneur pour vérifier
docker exec -it symfony_db mysql -u symfony -psymfony
```

### Les assets ne se compilent pas
```bash
# Redémarrer le conteneur Node
docker-compose restart node

# Vérifier les logs
docker logs symfony_node
```

## Documentation

Pour plus d'informations sur l'architecture multi-site, consulter le fichier `mutli-site.html`.

## Contribution

1. Créer une branche pour votre fonctionnalité
2. Commiter vos changements
3. Pousser vers la branche
4. Créer une Pull Request
=======
# Reconstruire les images
docker-compose down
docker-compose up -d --build
```

### Erreur de connexion à la base

```bash
# Vérifier que MariaDB est démarré
docker-compose ps db

# Tester la connexion
docker-compose exec db mysql -usymfony -psymfony -e "SHOW DATABASES;"
```

### Les migrations échouent

```bash
# Vérifier l'état des migrations
docker-compose exec web php bin/console doctrine:migrations:status --em=silenus

# Forcer la synchronisation du schéma (dev uniquement)
docker-compose exec web php bin/console doctrine:schema:update --em=silenus --force
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
