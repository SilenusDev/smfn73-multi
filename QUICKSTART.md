# 🚀 Quick Start - smfn73-multi

## Installation en 3 commandes

```bash
# 1. Installation complète
./install.sh

# 2. Vérifier l'installation
make check

# 3. Démarrer le projet
make start
```

## Accès

- **Application** : http://localhost:8080
- **phpMyAdmin** : http://localhost:8081

## Commandes essentielles

```bash
make help              # Liste toutes les commandes
make dev               # Mode développement avec watch
make build             # Build production des assets
make logs              # Voir les logs
make stop              # Arrêter les conteneurs
```

## En cas de problème

### Les styles ne s'affichent pas

```bash
# Solution rapide
make fix-assets

# Ou manuellement
make build
./scripts/symfony-orchestrator.sh stop node
./scripts/symfony-orchestrator.sh start node
```

### Erreur TypeScript

```bash
# Vérifier qu'il n'y a pas de fichiers .js doublons
rm -f assets/app.js assets/bootstrap.js
make build
```

### Problème avec npm ou erreurs de compilation

```bash
# Correction automatique
make fix-assets
```

### Reset complet

```bash
make clean
./install.sh
make start
```

## Structure du projet

```
smfn73-multi/
├── assets/
│   ├── app.ts              # Point d'entrée TypeScript
│   ├── styles/
│   │   └── app.css         # Styles Tailwind
│   └── controllers/        # Stimulus controllers
├── templates/              # Templates Twig
├── src/                    # Code PHP Symfony
├── public/
│   └── build/             # Assets compilés (généré)
├── pods/                  # Configuration Podman
├── scripts/               # Scripts d'orchestration
├── webpack.config.js      # Configuration Webpack
├── tailwind.config.js     # Configuration Tailwind
├── tsconfig.json          # Configuration TypeScript
├── install.sh             # Script d'installation
├── Makefile               # Commandes make
└── check.sh               # Script de vérification
```

## Documentation complète

- [INSTALL.md](INSTALL.md) - Guide d'installation détaillé
- [TECH_STACK.md](TECH_STACK.md) - Choix techniques et debugging
- [README.md](README.md) - Documentation complète du projet

## Développement

### Modifier les styles

1. Éditer `assets/styles/app.css` ou ajouter des classes Tailwind dans les templates
2. Le watch rebuild automatiquement : `make watch`
3. Ou rebuild manuellement : `make build`

### Ajouter des dépendances

```bash
# PHP
podman exec -it symfony-multi-web-container composer require [package]

# Node
podman exec -it symfony-multi-node-container npm install [package]
```

### Base de données

```bash
make db-create             # Créer la BDD
make db-migrate            # Exécuter les migrations
make db-reset              # Reset complet
```

## Tips

- ✅ Toujours utiliser **npm**
- ✅ Toujours builder via **Podman** : `make build`
- ✅ Fichiers TypeScript en **`.ts`** uniquement
- ✅ Tailwind scanne les fichiers **`.ts`** et **`.twig`**
- ✅ Utiliser les commandes **make** pour gérer les pods
- ❌ Ne pas commiter `node_modules/`
