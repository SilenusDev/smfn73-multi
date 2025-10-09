# 📊 Guide SEO & Meta Tags

## 🎯 Vue d'ensemble

Chaque site (Silenus et Insidiome) dispose d'une structure complète de meta tags SEO dans son template de base.

## 🏗️ Structure des templates de base

### Insidiome (`templates/insidiome/front/base.html.twig`)
- **Domaine** : insidiome.com
- **Auteur** : Him of INSIDIOME
- **Locale** : fr_FR
- **Site Name** : INSIDIOME

### Silenus (`templates/silenus/front/base.html.twig`)
- **Domaine** : silenus.fr
- **Auteur** : Team Silenus
- **Locale** : fr_FR
- **Site Name** : Silenus

## 📝 Blocks disponibles

### Meta Tags SEO de base

| Block | Description | Valeur par défaut |
|-------|-------------|-------------------|
| `lang` | Langue de la page | `fr` |
| `title` | Titre de la page | `INSIDIOME` / `Silenus` |
| `description` | Description de la page | `Plateforme moderne construite avec Symfony 7.3` |
| `author` | Auteur de la page | `Him of INSIDIOME` / `Team Silenus` |
| `keywords` | Mots-clés SEO | `insidiome, symfony, multisite` |
| `canonical` | URL canonique | URL actuelle |
| `robots` | Directives robots | `index,follow` |

### Open Graph (Réseaux sociaux)

| Block | Description | Valeur par défaut |
|-------|-------------|-------------------|
| `og_title` | Titre OG | Hérite de `title` |
| `og_description` | Description OG | Hérite de `description` |
| `og_image` | Image OG (1200x630) | `/build/img/gen/og-default.jpg` |
| `og_url` | URL OG | URL actuelle |
| `og_type` | Type de contenu | `website` |
| `og_locale` | Locale OG | `fr_FR` |

### Types OG disponibles
- `website` - Site web (par défaut)
- `article` - Article de blog
- `book` - Livre
- `profile` - Profil utilisateur
- `video` - Vidéo
- `music` - Musique
- `product` - Produit
- `event` - Événement

### Directives Robots

| Valeur | Description |
|--------|-------------|
| `index,follow` | Indexer et crawler (par défaut) |
| `noindex,follow` | Ne pas indexer mais crawler |
| `noindex,nofollow` | Ne pas indexer ni crawler |

## 🎨 Exemple d'utilisation

### Page d'accueil avec SEO complet

```twig
{% extends 'insidiome/front/base.html.twig' %}

{# SEO Meta Tags #}
{% block title %}Accueil - INSIDIOME{% endblock %}
{% block description %}Bienvenue sur INSIDIOME, votre plateforme dédiée construite avec Symfony 7.3. Découvrez nos fonctionnalités et créez votre compte.{% endblock %}
{% block keywords %}insidiome, accueil, plateforme, symfony, authentification, multisite{% endblock %}

{# Open Graph #}
{% block og_title %}Accueil - INSIDIOME{% endblock %}
{% block og_description %}Bienvenue sur INSIDIOME, votre plateforme dédiée construite avec Symfony 7.3.{% endblock %}
{% block og_type %}website{% endblock %}

{# Robots #}
{% block robots %}index,follow{% endblock %}

{% block body %}
    <h1>Bienvenue sur INSIDIOME</h1>
{% endblock %}
```

### Article de blog

```twig
{% extends 'insidiome/front/base.html.twig' %}

{# SEO Meta Tags #}
{% block title %}{{ article.title }} - INSIDIOME{% endblock %}
{% block description %}{{ article.excerpt }}{% endblock %}
{% block keywords %}{{ article.tags|join(', ') }}{% endblock %}
{% block canonical %}{{ url('article_show', {slug: article.slug}) }}{% endblock %}

{# Open Graph #}
{% block og_title %}{{ article.title }}{% endblock %}
{% block og_description %}{{ article.excerpt }}{% endblock %}
{% block og_image %}{{ article.featuredImage }}{% endblock %}
{% block og_type %}article{% endblock %}

{# Robots #}
{% block robots %}index,follow{% endblock %}

{% block body %}
    <article>
        <h1>{{ article.title }}</h1>
        <p>{{ article.content }}</p>
    </article>
{% endblock %}
```

### Page privée (non indexée)

```twig
{% extends 'insidiome/front/base.html.twig' %}

{# SEO Meta Tags #}
{% block title %}Mon Profil - INSIDIOME{% endblock %}
{% block description %}Page de profil utilisateur{% endblock %}

{# Robots - Ne pas indexer #}
{% block robots %}noindex,nofollow{% endblock %}

{% block body %}
    <h1>Mon Profil</h1>
{% endblock %}
```

### Page de connexion

```twig
{% extends 'insidiome/front/base.html.twig' %}

{# SEO Meta Tags #}
{% block title %}Connexion - INSIDIOME{% endblock %}
{% block description %}Connectez-vous à votre compte INSIDIOME{% endblock %}
{% block keywords %}connexion, login, insidiome{% endblock %}

{# Robots - Ne pas indexer mais crawler #}
{% block robots %}noindex,follow{% endblock %}

{% block body %}
    <h1>Connexion</h1>
{% endblock %}
```

## 🔧 Configuration des variables d'environnement

Les URLs et domaines sont configurés dans `.env` :

```env
# Silenus (silenus.fr)
SITE_SLNS_DOMAIN=silenus.fr
SITE_SLNS_URL=https://www.silenus.fr
SITE_SLNS_NAME=Silenus
SITE_SLNS_AUTHOR=Team Silenus
SITE_SLNS_LOCALE=fr_FR
SITE_SLNS_LOGO=https://www.silenus.fr/build/img/gen/logo.ico
SITE_SLNS_DEFAULT_IMAGE=https://www.silenus.fr/build/img/gen/og-default.jpg

# Insidiome (insidiome.com)
SITE_NSDM_DOMAIN=insidiome.com
SITE_NSDM_URL=https://www.insidiome.com
SITE_NSDM_NAME=INSIDIOME
SITE_NSDM_AUTHOR=Him of INSIDIOME
SITE_NSDM_LOCALE=fr_FR
SITE_NSDM_LOGO=https://www.insidiome.com/build/img/gen/logo.ico
SITE_NSDM_DEFAULT_IMAGE=https://www.insidiome.com/build/img/gen/og-default.jpg
```

## 📁 Fichiers requis

Pour que le SEO fonctionne correctement, créez ces fichiers dans `/public` :

### 1. robots.txt
```txt
User-agent: *
Allow: /

Sitemap: https://www.insidiome.com/sitemap.xml
```

### 2. sitemap.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://www.insidiome.com/</loc>
        <lastmod>2025-10-09</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
</urlset>
```

### 3. Images Open Graph
- **Logo** : `/public/build/img/gen/logo.ico` (favicon)
- **Image OG par défaut** : `/public/build/img/gen/og-default.jpg` (1200x630 pixels, format paysage)

## ✅ Checklist SEO

### Pour chaque nouvelle page

- [ ] Définir un `title` unique et descriptif (50-60 caractères)
- [ ] Écrire une `description` pertinente (150-160 caractères)
- [ ] Ajouter des `keywords` pertinents (5-10 mots-clés)
- [ ] Définir la directive `robots` appropriée
- [ ] Configurer les meta tags Open Graph
- [ ] Ajouter une image OG si pertinent (1200x630)
- [ ] Définir le bon `og_type`

### Pages publiques
- `robots`: `index,follow`
- `og_type`: `website` ou `article`

### Pages privées/authentification
- `robots`: `noindex,nofollow`
- Pas besoin d'Open Graph

### Pages de contenu
- `robots`: `index,follow`
- `og_type`: `article`
- Image OG personnalisée recommandée

## 🔍 Validation

### Outils de test
- **Google Rich Results Test** : https://search.google.com/test/rich-results
- **Facebook Sharing Debugger** : https://developers.facebook.com/tools/debug/
- **Twitter Card Validator** : https://cards-dev.twitter.com/validator
- **LinkedIn Post Inspector** : https://www.linkedin.com/post-inspector/

### Vérification locale
```bash
# Voir le HTML généré
curl http://localhost:8000/nsdm/ | grep -E "(meta|title|og:)"
```

## 📊 Bonnes pratiques

1. **Title** : 50-60 caractères, incluez le nom du site
2. **Description** : 150-160 caractères, call-to-action
3. **Keywords** : 5-10 mots-clés pertinents, séparés par des virgules
4. **OG Image** : 1200x630 pixels, format JPEG, paysage
5. **Canonical** : Toujours définir pour éviter le duplicate content
6. **Robots** : `noindex` pour pages privées, admin, login

## 🚀 Prochaines étapes

- [ ] Créer les images OG par défaut pour chaque site
- [ ] Générer les sitemaps dynamiques
- [ ] Ajouter les meta tags Twitter Card
- [ ] Implémenter Schema.org (JSON-LD)
- [ ] Créer un service Twig pour centraliser les meta tags

---

*Dernière mise à jour : 2025-10-09*
