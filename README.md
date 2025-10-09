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
```bash
# Vérifier les logs
docker-compose logs

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