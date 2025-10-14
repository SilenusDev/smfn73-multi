# Stack Technique - smfn73-multi

## Choix techniques et raisons

### Gestionnaire de paquets : npm

**Raison** : npm avec node_modules classique fonctionne parfaitement avec Webpack Encore.
- Compatibilité maximale avec tous les loaders
- Configuration simple et standard
- Pas de complexité supplémentaire

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

### Podman

**Pods et Images** :
- `php:8.3-apache` - PHP avec Apache intégré
- `node:20-alpine` - Node.js LTS pour les assets
- `mariadb:10.11` - Base de données
- `redis:alpine` - Cache Redis
- `phpmyadmin` - Interface de gestion BDD

**Architecture** :
- Pods Podman natifs (pas de compose)
- Scripts d'orchestration personnalisés
- Volumes montés pour le développement

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

Si npm pose problème :
1. Supprimer `node_modules`
2. Réinstaller : `make npm-install`

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

1. **Toujours utiliser npm**
2. **Toujours builder via Podman** : `make build`
3. **Utiliser les commandes make** pour gérer les pods
4. **Ne pas commiter** : `node_modules/`
5. **Commiter** : `package.json`, `package-lock.json`
