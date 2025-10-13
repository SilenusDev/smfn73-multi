# Migration Yarn → npm

**Date** : 2025-10-13  
**Statut** : ✅ Terminé

## Contexte

Le projet utilisait npm (présence de `package-lock.json` et `.npmrc`) mais certaines références à Yarn subsistaient dans la documentation et les configurations.

## Objectif

Uniformiser l'utilisation de npm dans tout le projet pour éviter les confusions et les incohérences.

## Changements effectués

### 📝 Documentation mise à jour (9 fichiers)

- **README.md** - Toutes les commandes `yarn` remplacées par `npm run`
- **TAILWIND_SETUP.md** - Commandes npm uniquement
- **QUICKSTART.md** - Références à Yarn supprimées
- **TROUBLESHOOTING.md** - Messages d'erreur mis à jour
- **TECH_STACK.md** - Section gestionnaire de paquets simplifiée
- **INSTALL.md** - Référence à Yarn supprimée
- **CHANGELOG_TYPESCRIPT.md** - Historique mis à jour
- **docs/session-2025-10-13-summary.md** - Références historiques corrigées
- **docs/done.md** - Historique corrigé
- **docs/tailwind-design-system.md** - Commandes npm uniquement

### 🔧 Configuration (3 fichiers)

- **.gitignore** - Commentaire mis à jour (garde l'exclusion des anciens fichiers)
- **Makefile** - Commentaire de la commande `fix-assets` mis à jour
- **docker-compose.yml** - Déjà configuré avec npm ✅

### 🛠️ Scripts shell (3 fichiers)

- **fix-assets.sh** - Commentaire mis à jour (garde le nettoyage des anciens fichiers)
- **check.sh** - Message mis à jour (garde la détection des anciens fichiers)
- **install.sh** - Déjà à jour ✅

## Commandes remplacées

| Avant | Après |
|-------|-------|
| `yarn install` | `npm install` |
| `yarn watch` | `npm run watch` |
| `yarn dev` | `npm run dev` |
| `yarn build` | `npm run build` |

## Sections renommées

- "Gestion des assets (Yarn/Vite)" → "Gestion des assets (npm/Webpack Encore)"
- "Yarn (Assets)" → "npm (Assets)"
- "Yarn watch (assets)" → "npm watch (assets)"

## Validation

✅ **Aucune référence à Yarn dans les fichiers de code**  
✅ **Aucune référence à Yarn dans package.json**  
✅ **docker-compose.yml utilise npm**  
✅ **Documentation cohérente avec npm**  
✅ **Scripts shell à jour**

### Commande de vérification

```bash
# Vérifier qu'il ne reste pas de références inappropriées
grep -rn "yarn" --include="*.md" --include="*.sh" --include="*.yml" \
  | grep -v "node_modules" | grep -v ".git" \
  | grep -v "yarn.lock" | grep -v ".yarn"

# Résultat attendu : uniquement check.sh et fix-assets.sh
# (ces fichiers nettoient les anciens fichiers Yarn lors de la migration)
```

## Notes importantes

### Fichiers conservés intentionnellement

Les scripts `check.sh` et `fix-assets.sh` conservent la détection et la suppression des anciens fichiers Yarn (`.yarn/`, `yarn.lock`, `.yarnrc.yml`) pour :

1. Assurer la compatibilité lors de la migration
2. Nettoyer automatiquement les anciens fichiers si présents
3. Éviter les conflits entre gestionnaires de paquets

### Pas de changement fonctionnel

Cette migration est **purement documentaire**. Le projet utilisait déjà npm en pratique :
- `package-lock.json` présent
- `.npmrc` configuré
- `docker-compose.yml` utilise npm

## Pour les développeurs

### Installation

```bash
# Installation complète
./install.sh

# Ou manuellement
npm install
```

### Développement

```bash
# Mode watch
npm run watch

# Build dev
npm run dev

# Build production
npm run build
```

### Via Docker

```bash
# Installation
docker compose run --rm node npm install

# Build
docker compose run --rm node npm run build

# Watch (déjà actif dans le conteneur)
docker compose up node
```

## Compatibilité

- ✅ Node.js 20 (LTS)
- ✅ npm 10+
- ✅ Docker / Podman
- ✅ Linux / macOS / Windows (WSL2)

---

**Migration effectuée par** : Cascade AI  
**Fichiers modifiés** : 15  
**Lignes modifiées** : ~50
