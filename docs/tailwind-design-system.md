# 🎨 Tailwind Design System - SMFN73 Multi-Site

## 🎯 Vue d'ensemble

Design system avec Tailwind CSS pour les deux sites avec thème dark et glassmorphisme.

---

## 🌈 Couleurs

### Silenus - Lime Primary

```css
.site-slns {
  --primary-500: #84cc16; /* Main lime */
}
```

| Nuance | Hex | Usage |
|--------|-----|-------|
| 50 | `#f7fee7` | Backgrounds très clairs |
| 100 | `#ecfccb` | Backgrounds clairs |
| 300 | `#bef264` | Hover states |
| 400 | `#a3e635` | Accents |
| **500** | **`#84cc16`** | **Primary (boutons, liens)** |
| 600 | `#65a30d` | Hover primary |
| 700 | `#4d7c0f` | Active states |
| 900 | `#365314` | Text foncé |

**Classes Tailwind** :
- `bg-slns-primary-500` - Background lime
- `text-slns-primary-500` - Texte lime
- `border-slns-primary-500` - Bordure lime
- `hover:bg-slns-primary-600` - Hover

### Insidiome - Purple Primary

```css
.site-nsdm {
  --primary-500: #a855f7; /* Main purple */
}
```

| Nuance | Hex | Usage |
|--------|-----|-------|
| 50 | `#faf5ff` | Backgrounds très clairs |
| 100 | `#f3e8ff` | Backgrounds clairs |
| 300 | `#d8b4fe` | Hover states |
| 400 | `#c084fc` | Accents |
| **500** | **`#a855f7`** | **Primary (boutons, liens)** |
| 600 | `#9333ea` | Hover primary |
| 700 | `#7e22ce` | Active states |
| 900 | `#581c87` | Text foncé |

**Classes Tailwind** :
- `bg-nsdm-primary-500` - Background purple
- `text-nsdm-primary-500` - Texte purple
- `border-nsdm-primary-500` - Bordure purple
- `hover:bg-nsdm-primary-600` - Hover

### Dark Theme - Grays

| Nuance | Hex | Usage |
|--------|-----|-------|
| 50 | `#f9fafb` | Text très clair |
| 100 | `#f3f4f6` | Text clair |
| 200 | `#e5e7eb` | Borders claires |
| 400 | `#9ca3af` | Text secondaire |
| 600 | `#4b5563` | Backgrounds élevés |
| 800 | `#1f2937` | Backgrounds cards |
| 900 | `#111827` | Backgrounds sections |
| **950** | **`#030712`** | **Background principal** |

---

## 🪟 Glassmorphism

### Classes disponibles

#### `.glass`
Card glassmorphisme standard.

```html
<div class="glass p-6 rounded-xl">
  Contenu avec effet verre
</div>
```

**Propriétés** :
- `backdrop-blur-xl` - Flou d'arrière-plan
- `bg-white/5` - Background semi-transparent
- `border border-white/10` - Bordure subtile
- `shadow-glass` - Ombre profonde

#### `.glass-sm`
Version plus subtile.

```html
<div class="glass-sm p-4 rounded-lg">
  Card légère
</div>
```

#### `.glass-lg`
Version plus prononcée.

```html
<div class="glass-lg p-8 rounded-2xl">
  Card principale
</div>
```

#### `.glass-hover`
Avec effet hover.

```html
<a href="#" class="glass-hover p-4 rounded-lg">
  Lien avec effet
</a>
```

#### `.glass-input`
Input glassmorphisme.

```html
<input type="text" class="glass-input w-full" placeholder="Email">
```

#### `.glass-btn`
Bouton glassmorphisme.

```html
<button class="glass-btn">
  Bouton verre
</button>
```

### Classes spécifiques par site

#### Silenus `.glass-primary`

```html
<div class="site-slns">
  <div class="glass-primary p-6 rounded-xl">
    Card avec accent lime
  </div>
</div>
```

#### Insidiome `.glass-primary`

```html
<div class="site-nsdm">
  <div class="glass-primary p-6 rounded-xl">
    Card avec accent purple
  </div>
</div>
```

---

## 🔘 Boutons

### Bouton Primary

#### Silenus (Lime)

```html
<div class="site-slns">
  <button class="btn-primary">
    Bouton Lime
  </button>
</div>
```

**Rendu** :
- Background : Lime `#84cc16`
- Text : Dark `#030712`
- Hover : Lime foncé `#65a30d`
- Shadow : Lime glow
- Scale : 1.05 au hover

#### Insidiome (Purple)

```html
<div class="site-nsdm">
  <button class="btn-primary">
    Bouton Purple
  </button>
</div>
```

**Rendu** :
- Background : Purple `#a855f7`
- Text : White
- Hover : Purple foncé `#9333ea`
- Shadow : Purple glow
- Scale : 1.05 au hover

### Bouton Glassmorphisme

```html
<button class="glass-btn">
  Bouton Verre
</button>
```

---

## 📝 Formulaires

### Input Standard

```html
<input 
  type="text" 
  class="glass-input w-full" 
  placeholder="Votre email"
>
```

### Input avec label

```html
<div class="space-y-2">
  <label class="block text-gray-300 font-medium">
    Email
  </label>
  <input 
    type="email" 
    class="glass-input w-full" 
    placeholder="vous@exemple.com"
  >
</div>
```

### Textarea

```html
<textarea 
  class="glass-input w-full min-h-[120px]" 
  placeholder="Votre message"
></textarea>
```

### Checkbox

```html
<label class="flex items-center gap-3 cursor-pointer">
  <input 
    type="checkbox" 
    class="w-5 h-5 rounded border-white/20 bg-white/5 text-nsdm-primary-500 focus:ring-nsdm-primary-500"
  >
  <span class="text-gray-300">J'accepte les conditions</span>
</label>
```

---

## 🎴 Cards

### Card Standard

```html
<div class="glass-lg rounded-2xl p-8">
  <h3 class="text-2xl font-bold text-primary mb-4">
    Titre de la card
  </h3>
  <p class="text-gray-300">
    Contenu de la card avec glassmorphisme.
  </p>
</div>
```

### Card avec hover

```html
<div class="glass-hover rounded-xl p-6 cursor-pointer">
  <h4 class="font-semibold text-gray-100 mb-2">
    Card interactive
  </h4>
  <p class="text-gray-400 text-sm">
    Effet au survol
  </p>
</div>
```

### Card avec icône

```html
<div class="glass-lg rounded-2xl p-6">
  <div class="flex items-start gap-4">
    <div class="p-3 bg-nsdm-primary-500/20 rounded-lg">
      <svg class="w-6 h-6 text-nsdm-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
      </svg>
    </div>
    <div>
      <h4 class="font-semibold text-gray-100 mb-1">
        Fonctionnalité
      </h4>
      <p class="text-gray-400 text-sm">
        Description de la fonctionnalité
      </p>
    </div>
  </div>
</div>
```

---

## 🎭 Animations

### Fade In

```html
<div class="animate-fade-in">
  Apparition en fondu
</div>
```

### Slide Up

```html
<div class="animate-slide-up">
  Glissement vers le haut
</div>
```

### Combinaison

```html
<div class="glass-lg rounded-2xl p-8 animate-fade-in">
  Card animée
</div>
```

---

## 🎨 Gradients

### Gradient de texte (Primary)

#### Silenus

```html
<h1 class="bg-gradient-to-r from-slns-primary-400 to-slns-primary-600 bg-clip-text text-transparent">
  Titre Lime
</h1>
```

#### Insidiome

```html
<h1 class="bg-gradient-to-r from-nsdm-primary-400 to-nsdm-primary-600 bg-clip-text text-transparent">
  Titre Purple
</h1>
```

### Gradient de background

```html
<div class="bg-gradient-to-br from-dark-950 via-dark-900 to-dark-950">
  Background avec gradient
</div>
```

---

## 📱 Responsive

### Breakpoints Tailwind

| Breakpoint | Min width | Usage |
|------------|-----------|-------|
| `sm` | 640px | Mobile landscape |
| `md` | 768px | Tablet |
| `lg` | 1024px | Desktop |
| `xl` | 1280px | Large desktop |
| `2xl` | 1536px | Extra large |

### Exemples

```html
<!-- Stack mobile, row desktop -->
<div class="flex flex-col md:flex-row gap-4">
  <div class="w-full md:w-1/2">Colonne 1</div>
  <div class="w-full md:w-1/2">Colonne 2</div>
</div>

<!-- Texte responsive -->
<h1 class="text-2xl md:text-4xl lg:text-5xl">
  Titre responsive
</h1>

<!-- Padding responsive -->
<div class="p-4 md:p-8 lg:p-12">
  Contenu
</div>
```

---

## 🎯 Exemples complets

### Page de connexion

```html
<div class="site-nsdm min-h-screen bg-gradient-to-br from-dark-950 via-dark-900 to-dark-950 flex items-center justify-center p-4">
  <div class="glass-lg rounded-2xl p-8 w-full max-w-md animate-fade-in">
    <h2 class="text-3xl font-bold text-center mb-8">
      <span class="bg-gradient-to-r from-nsdm-primary-400 to-nsdm-primary-600 bg-clip-text text-transparent">
        Connexion
      </span>
    </h2>
    
    <form class="space-y-6">
      <div>
        <label class="block text-gray-300 font-medium mb-2">
          Email
        </label>
        <input 
          type="email" 
          class="glass-input w-full" 
          placeholder="vous@exemple.com"
        >
      </div>
      
      <div>
        <label class="block text-gray-300 font-medium mb-2">
          Mot de passe
        </label>
        <input 
          type="password" 
          class="glass-input w-full" 
          placeholder="••••••••"
        >
      </div>
      
      <button type="submit" class="btn-primary w-full">
        Se connecter
      </button>
    </form>
  </div>
</div>
```

### Grid de cards

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <div class="glass-hover rounded-xl p-6">
    <h3 class="text-xl font-bold text-primary mb-3">
      Feature 1
    </h3>
    <p class="text-gray-400">
      Description de la fonctionnalité
    </p>
  </div>
  
  <div class="glass-hover rounded-xl p-6">
    <h3 class="text-xl font-bold text-primary mb-3">
      Feature 2
    </h3>
    <p class="text-gray-400">
      Description de la fonctionnalité
    </p>
  </div>
  
  <div class="glass-hover rounded-xl p-6">
    <h3 class="text-xl font-bold text-primary mb-3">
      Feature 3
    </h3>
    <p class="text-gray-400">
      Description de la fonctionnalité
    </p>
  </div>
</div>
```

---

## 🚀 Installation & Build

### Installation

```bash
# Installer les dépendances
yarn install

# ou avec npm
npm install
```

### Development

```bash
# Watch mode (recommandé)
yarn watch

# Build dev
yarn dev
```

### Production

```bash
# Build optimisé avec purge CSS
yarn build
```

---

## 📝 Bonnes pratiques

### ✅ À faire

1. **Utiliser les classes utilitaires** : Préférer `class="glass-lg p-8"` plutôt que du CSS custom
2. **Respecter le thème** : Toujours wrapper dans `.site-slns` ou `.site-nsdm`
3. **Mobile-first** : Commencer par mobile puis ajouter `md:`, `lg:`
4. **Animations subtiles** : Utiliser `animate-fade-in` et `animate-slide-up`
5. **Glassmorphisme cohérent** : Utiliser les classes `.glass-*` prédéfinies

### ❌ À éviter

1. **CSS inline** : Éviter `style="..."` sauf cas exceptionnels
2. **Couleurs en dur** : Ne pas utiliser `#84cc16`, utiliser `bg-slns-primary-500`
3. **Classes trop spécifiques** : Préférer les utilitaires Tailwind
4. **Oublier le responsive** : Toujours tester mobile
5. **Trop d'animations** : Rester sobre

---

*Dernière mise à jour : 2025-10-09*
