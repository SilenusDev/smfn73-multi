# Changelog - Migration TypeScript + Tailwind

## Date : 2025-10-13

### Problème initial
- Perte des styles après mise en place de TypeScript
- Conflit entre TypeScript, Tailwind et Webpack
- Yarn 4 incompatible avec Webpack Encore

### Solutions appliquées

#### 1. Configuration Tailwind
- ✅ Ajout de `*.ts` dans `tailwind.config.js` content
- Avant : `"./assets/**/*.js"`
- Après : `"./assets/**/*.{js,ts}"`

#### 2. Configuration TypeScript
- ✅ Suppression de `"noEmit": true` dans `tsconfig.json`
- Permet à ts-loader de compiler correctement

#### 3. Migration Yarn → npm
- ✅ Suppression de Yarn 4 (incompatible avec Webpack Encore PnP)
- ✅ Utilisation de npm avec node_modules classique
- ✅ Mise à jour de `docker-compose.yml` pour utiliser npm
- ✅ Suppression de `packageManager: "yarn@4.10.3"` dans package.json

#### 4. Nettoyage des fichiers
- ✅ Suppression des doublons `.js`/`.ts` :
  - `assets/app.js` (doublon de app.ts)
  - `assets/bootstrap.js` (doublon de bootstrap.ts)
- ✅ Ajout des fichiers Yarn au `.gitignore`

#### 5. Configuration Webpack
- ✅ `.enablePostCssLoader()` pour Tailwind
- ✅ `.enableTypeScriptLoader()` pour TypeScript
- Entry point : `assets/app.ts`

### Fichiers créés pour automatisation

#### Scripts d'installation
- `install.sh` - Installation automatique complète
- `check.sh` - Vérification de l'installation
- `fix-assets.sh` - Correction automatique des problèmes d'assets
- `Makefile` - Commandes simplifiées (make install, make build, etc.)

#### Documentation
- `QUICKSTART.md` - Guide de démarrage rapide
- `INSTALL.md` - Guide d'installation détaillé
- `TECH_STACK.md` - Documentation technique et debugging
- `CHANGELOG_TYPESCRIPT.md` - Ce fichier

#### Configuration
- `.npmrc` - Configuration npm
- `.gitignore` - Ajout des fichiers Yarn à ignorer
- `package.json` - Suppression de la référence à Yarn

### Commandes disponibles

```bash
# Installation
./install.sh              # Installation complète
make install              # Idem

# Développement
make start                # Démarrer les conteneurs
make dev                  # Mode dev avec watch
make watch                # Watch uniquement
make build                # Build production

# Vérification et correction
make check                # Vérifier l'installation
make fix-assets           # Corriger les problèmes d'assets
make logs                 # Voir les logs

# Base de données
make db-create            # Créer la BDD
make db-migrate           # Migrations
make db-reset             # Reset

# Nettoyage
make stop                 # Arrêter
make clean                # Nettoyage complet
```

### Stack technique final

- **Backend** : Symfony 7.3 + PHP 8.2
- **Frontend** : TypeScript + Tailwind CSS
- **Build** : Webpack Encore
- **Package Manager** : npm (Node 20)
- **Conteneurisation** : Docker avec node:20-alpine

### Points importants

1. ✅ Toujours utiliser **npm** (jamais yarn)
2. ✅ Toujours builder via **Docker** : `make build`
3. ✅ Fichiers TypeScript en **`.ts`** uniquement
4. ✅ Tailwind scanne **`.ts`** et **`.twig`**
5. ❌ Ne pas commiter `node_modules/`, `.yarn/`, `yarn.lock`

### Tests effectués

- ✅ Build réussi via Docker : `docker compose run --rm node npm run build`
- ✅ CSS généré : `public/build/app.css` (19.7 KiB)
- ✅ JS généré : `public/build/app.js`
- ✅ TypeScript compilé sans erreur
- ✅ Tailwind CSS fonctionnel

### Prochaines étapes

Pour un nouveau développeur :
1. Cloner le projet
2. Lancer `./install.sh`
3. Lancer `make start`
4. Accéder à http://localhost:8000

Pour développer :
1. Lancer `make dev` (watch automatique)
2. Modifier les fichiers `.ts` ou `.twig`
3. Le navigateur se rafraîchit automatiquement
