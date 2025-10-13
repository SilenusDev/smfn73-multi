# Installation du projet smfn73-multi

## Prérequis

- Docker
- Docker Compose

## Installation rapide

```bash
# Installation complète en une commande
./install.sh
```

Ou avec Make :

```bash
make install
```

## Démarrage

```bash
# Démarrer le projet
make start

# Ou avec docker compose
docker compose up -d
```

Le projet sera accessible sur :
- **Application** : http://localhost:8000
- **phpMyAdmin** : http://localhost:8081

## Développement

### Mode watch (rebuild automatique des assets)

```bash
make watch
```

### Commandes utiles

```bash
make help              # Affiche toutes les commandes disponibles
make build             # Build les assets en production
make dev               # Démarre en mode développement avec watch
make logs              # Affiche les logs
make stop              # Arrête les conteneurs
make clean             # Nettoie tout (conteneurs, volumes, cache)
```

### Commandes Composer

```bash
make composer-install  # Installe les dépendances PHP
docker compose run --rm web composer require [package]
```

### Commandes npm

```bash
make npm-install       # Installe les dépendances Node
docker compose run --rm node npm install [package]
```

### Base de données

```bash
make db-create         # Crée la base de données
make db-migrate        # Exécute les migrations
make db-reset          # Reset complet de la BDD
```

## Structure du projet

- **TypeScript** : Tous les fichiers JS sont en `.ts`
- **Tailwind CSS** : Configuration dans `tailwind.config.js`
- **Webpack Encore** : Configuration dans `webpack.config.js`
- **npm** : Gestionnaire de paquets

## Problèmes courants

### Les styles ne s'affichent pas

```bash
# Rebuild les assets
make build
```

### Erreur de permissions

```bash
# Ajuster les permissions
sudo chown -R $USER:$USER .
```

### Conteneurs qui ne démarrent pas

```bash
# Nettoyer et redémarrer
make clean
make install
make start
```

## Configuration

Le fichier `.env` contient toutes les variables d'environnement.
Copiez `.env.example` vers `.env` et ajustez selon vos besoins.

Variables importantes :
- `DB_HOST` : Hôte de la base de données
- `DB_PORT` : Port de la base de données
- `DB_NAME` : Nom de la base de données
- `DB_USER` : Utilisateur de la base de données
- `DB_PASSWORD` : Mot de passe de la base de données
