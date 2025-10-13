# Stack Technique - smfn73-multi

## Choix techniques et raisons

### Gestionnaire de paquets : npm (pas Yarn)

**Raison** : Yarn 4 avec Plug'n'Play (PnP) est incompatible avec Webpack Encore.
- Webpack Encore ne détecte pas correctement les loaders avec Yarn PnP
- npm avec node_modules classique fonctionne sans configuration supplémentaire
- Simplicité et compatibilité maximale

### TypeScript

**Configuration** : `tsconfig.json`
- `noEmit` désactivé pour permettre la compilation par ts-loader
- `module: "ESNext"` pour la compatibilité avec Webpack
- `target: "ES2020"` pour un bon équilibre compatibilité/fonctionnalités

**Fichiers** :
- ✅ `assets/**/*.ts` - Tous les fichiers TypeScript
- ❌ `assets/**/*.js` - Éviter les doublons .js/.ts

### Tailwind CSS

**Configuration** : `tailwind.config.js`
- `content: ["./assets/**/*.{js,ts}", "./templates/**/*.html.twig"]`
- **Important** : Inclure `*.ts` pour que Tailwind scanne les fichiers TypeScript
- PostCSS activé via `.enablePostCssLoader()` dans webpack.config.js

**Fichiers** :
- `assets/styles/app.css` - Point d'entrée avec directives Tailwind
- `postcss.config.js` - Configuration PostCSS avec Tailwind et Autoprefixer

### Webpack Encore

**Configuration** : `webpack.config.js`
- `.enableTypeScriptLoader()` - Support TypeScript
- `.enablePostCssLoader()` - Support Tailwind CSS via PostCSS
- Entry point : `assets/app.ts` (pas .js)

### Docker

**Images** :
- `php:8.2-fpm-alpine` - PHP léger et rapide
- `node:20-alpine` - Node.js LTS pour les assets
- `mariadb:10.11` - Base de données
- `nginx:alpine` - Serveur web

**Volumes** :
- Code source monté en volume pour le développement
- node_modules dans le conteneur (pas sur l'hôte)

## Commandes importantes

### Installation initiale
```bash
./install.sh
# ou
make install
```

### Développement
```bash
make dev              # Démarre avec watch des assets
make build            # Build production
make watch            # Watch uniquement
```

### Debugging

Si les styles ne s'affichent pas :
1. Vérifier que `tailwind.config.js` inclut `*.ts`
2. Rebuild : `make build`
3. Vérifier que `public/build/app.css` existe et contient du CSS

Si TypeScript ne compile pas :
1. Vérifier `tsconfig.json` : `noEmit` doit être absent ou false
2. Vérifier que les fichiers `.js` doublons sont supprimés
3. Rebuild : `make build`

Si npm/yarn pose problème :
1. Supprimer `node_modules`, `.yarn`, `yarn.lock`
2. Utiliser uniquement npm : `docker compose run --rm node npm install`

## Dépendances critiques

### Production
- `lucide` - Icônes

### Développement
- `@symfony/webpack-encore` - Build des assets
- `typescript` + `ts-loader` - Support TypeScript
- `tailwindcss` + `postcss` + `autoprefixer` - Styles
- `postcss-loader` - Intégration PostCSS avec Webpack
- `webpack` + `webpack-cli` - Bundler

## Notes importantes

1. **Toujours utiliser npm**, jamais yarn dans ce projet
2. **Toujours builder via Docker** : `docker compose run --rm node npm run build`
3. **Ne pas commiter** : `node_modules/`, `.yarn/`, `yarn.lock`
4. **Commiter** : `package.json`, `package-lock.json`
