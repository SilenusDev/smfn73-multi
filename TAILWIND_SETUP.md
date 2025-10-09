# 🎨 Installation Tailwind CSS - Instructions

## ✅ Ce qui a été fait

1. ✅ **Configuration Tailwind** - `tailwind.config.js` créé
2. ✅ **Configuration PostCSS** - `postcss.config.js` créé
3. ✅ **Dépendances ajoutées** - `package.json` mis à jour
4. ✅ **Webpack configuré** - PostCSS activé
5. ✅ **CSS créé** - `assets/styles/app.css` avec Tailwind
6. ✅ **Templates mis à jour** - Glassmorphisme implémenté
7. ✅ **Documentation** - `docs/tailwind-design-system.md`

---

## 🚀 Étapes à suivre

### 1. Installer les dépendances

```bash
# Dans le conteneur Node ou en local
yarn install

# ou avec npm
npm install
```

**Dépendances installées** :
- `tailwindcss@^3.4.1`
- `postcss@^8.4.47`
- `postcss-loader@^8.1.1`
- `autoprefixer@^10.4.20`

### 2. Builder les assets

```bash
# Mode watch (recommandé pour le développement)
yarn watch

# ou build unique
yarn dev

# ou build production
yarn build
```

### 3. Vérifier le résultat

Ouvrir dans le navigateur :
- **Insidiome** : http://localhost:8000/nsdm/
- **Silenus** : http://localhost:8000/slns/

---

## 🎨 Thèmes implémentés

### Insidiome (Purple)
- **Couleur primary** : Purple `#a855f7`
- **Classe body** : `site-nsdm`
- **Gradient** : Dark avec accents purple
- **Glassmorphisme** : Activé

### Silenus (Lime)
- **Couleur primary** : Lime `#84cc16`
- **Classe body** : `site-slns`
- **Gradient** : Dark avec accents lime
- **Glassmorphisme** : Activé

---

## 📁 Fichiers modifiés/créés

### Configuration
- ✅ `tailwind.config.js` - Config Tailwind
- ✅ `postcss.config.js` - Config PostCSS
- ✅ `package.json` - Dépendances
- ✅ `webpack.config.js` - PostCSS loader

### Assets
- ✅ `assets/styles/app.css` - Styles Tailwind

### Templates
- ✅ `templates/insidiome/front/base.html.twig`
- ✅ `templates/silenus/front/base.html.twig`

### Documentation
- ✅ `docs/tailwind-design-system.md`

---

## 🎯 Classes CSS disponibles

### Glassmorphisme
- `.glass` - Card standard
- `.glass-sm` - Card subtile
- `.glass-lg` - Card prononcée
- `.glass-hover` - Avec effet hover
- `.glass-input` - Input glassmorphisme
- `.glass-btn` - Bouton glassmorphisme
- `.glass-primary` - Card avec accent (lime/purple)

### Boutons
- `.btn-primary` - Bouton primary (lime pour Silenus, purple pour Insidiome)

### Animations
- `.animate-fade-in` - Apparition en fondu
- `.animate-slide-up` - Glissement vers le haut

### Couleurs
- `bg-slns-primary-500` - Background lime (Silenus)
- `text-slns-primary-500` - Texte lime (Silenus)
- `bg-nsdm-primary-500` - Background purple (Insidiome)
- `text-nsdm-primary-500` - Texte purple (Insidiome)
- `bg-dark-950` - Background très sombre
- `text-gray-100` - Texte clair

---

## 🔧 Dépannage

### Les styles ne s'appliquent pas

```bash
# Vérifier que le build est terminé
yarn watch

# Vider le cache Symfony
php bin/console cache:clear

# Redémarrer le serveur
```

### Erreur "Unknown at rule @tailwind"

C'est normal ! Ces warnings apparaissent dans l'IDE mais disparaissent après le build. PostCSS transforme ces directives en CSS standard.

### Classes Tailwind non reconnues

```bash
# Vérifier que tailwind.config.js pointe vers les bons fichiers
# Content doit inclure :
# - "./assets/**/*.js"
# - "./templates/**/*.html.twig"
```

### Build très lent

```bash
# En production, Tailwind purge le CSS inutilisé
# Le fichier final fait ~10-50 KB au lieu de 3.5 MB
yarn build
```

---

## 📚 Documentation

- **Design System** : [`docs/tailwind-design-system.md`](docs/tailwind-design-system.md)
- **Tailwind Docs** : https://tailwindcss.com/docs
- **Webpack Encore** : https://symfony.com/doc/current/frontend.html

---

## 🎨 Prochaines étapes

### Améliorer les pages existantes

Les templates de base sont mis à jour, mais les pages enfants utilisent encore du HTML inline. Pour les mettre à jour :

1. **Remplacer les styles inline** par des classes Tailwind
2. **Utiliser les composants glassmorphisme**
3. **Ajouter des animations**

### Exemple de mise à jour d'une page

**Avant** :
```html
<h2 style="color: #f5576c; margin-bottom: 20px;">
  Titre
</h2>
```

**Après** :
```html
<h2 class="text-3xl font-bold text-primary mb-6">
  Titre
</h2>
```

---

## ✅ Checklist

- [ ] Exécuter `yarn install`
- [ ] Exécuter `yarn watch`
- [ ] Vérifier Insidiome (purple)
- [ ] Vérifier Silenus (lime)
- [ ] Tester le responsive
- [ ] Mettre à jour les pages enfants (optionnel)

---

*Bon développement ! 🚀*
