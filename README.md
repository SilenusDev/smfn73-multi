# 🚀 Projet Multisite Symfony 7.3

Architecture multisite avec 2 sites (Silenus & Insidiome) et 2 bases de données séparées.

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

- **Application Symfony**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081 (user: `symfony`, pass: `symfony`)
- **Base de données**: localhost:3306

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

## 🚨 Dépannage

### Les services ne démarrent pas

```bash
# Vérifier les logs
docker-compose logs

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

*Dernière mise à jour: 2025-10-07*