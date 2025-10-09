# 🤖 Guide AI SEO - Optimisation pour les Intelligences Artificielles

## 📋 Vue d'ensemble

Ce guide explique comment le site est optimisé pour être compris et indexé par les systèmes d'Intelligence Artificielle (ChatGPT, Claude, Bard, Perplexity, etc.).

---

## 🎯 Solutions implémentées

### ✅ 1. Schema.org / JSON-LD

**Emplacement** : `templates/*/front/base.html.twig`

Structured data qui permet aux IA de comprendre le contexte et la structure du contenu.

```twig
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "INSIDIOME",
    "url": "https://www.insidiome.com",
    "description": "Description du site",
    "inLanguage": "fr-FR",
    "author": {
        "@type": "Person",
        "name": "Him of INSIDIOME"
    }
}
</script>
```

**Types Schema.org disponibles** :
- `WebSite` - Site web (par défaut)
- `Article` - Article de blog
- `Organization` - Organisation
- `Person` - Profil personne
- `Product` - Produit
- `Event` - Événement

### ✅ 2. Meta tags spécifiques IA

**Emplacement** : `templates/*/front/base.html.twig`

```html
<!-- Contrôle des snippets pour les IA -->
<meta name="robots" content="max-snippet:-1, max-image-preview:large, max-video-preview:-1">

<!-- Autorisation ChatGPT -->
<meta name="chatgpt" content="index">

<!-- Déclaration du contenu -->
<meta name="ai-content-declaration" content="ai-generated=false">
```

**Valeurs possibles** :
- `max-snippet:-1` - Pas de limite de longueur pour les extraits
- `max-snippet:0` - Pas d'extraits
- `max-snippet:150` - Maximum 150 caractères
- `max-image-preview:large` - Grandes images autorisées
- `max-video-preview:-1` - Pas de limite pour les vidéos

### ✅ 3. robots.txt avancé

**Emplacement** : `/public/robots.txt`

Configuration pour tous les crawlers IA majeurs :

```txt
# OpenAI (ChatGPT)
User-agent: GPTBot
User-agent: ChatGPT-User
Allow: /

# Anthropic (Claude)
User-agent: anthropic-ai
User-agent: Claude-Web
Allow: /

# Google AI (Bard/Gemini)
User-agent: Google-Extended
Allow: /

# Perplexity AI
User-agent: PerplexityBot
Allow: /

# Apple Intelligence
User-agent: Applebot-Extended
Allow: /

# Common Crawl (utilisé par plusieurs IA)
User-agent: CCBot
Allow: /
```

### ✅ 4. ai.txt (nouveau standard)

**Emplacement** : `/public/ai.txt`

Fichier spécifique pour définir comment les IA peuvent utiliser le contenu :

```txt
User-agent: *
Allow: /

Content-Type: human-generated
Training: allowed
Summarization: allowed
Analysis: allowed
```

---

## 🔧 Configuration par page

### Page standard (indexable)

```twig
{% extends 'insidiome/front/base.html.twig' %}

{% block schema_type %}WebPage{% endblock %}
{% block robots %}index,follow{% endblock %}
```

### Article de blog

```twig
{% extends 'insidiome/front/base.html.twig' %}

{% block schema_type %}Article{% endblock %}
{% block robots %}index,follow{% endblock %}

{# Dans le template de base, ajouter : #}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ article.title }}",
    "datePublished": "{{ article.publishedAt|date('c') }}",
    "dateModified": "{{ article.updatedAt|date('c') }}",
    "author": {
        "@type": "Person",
        "name": "{{ article.author }}"
    },
    "image": "{{ article.image }}",
    "articleBody": "{{ article.content }}"
}
</script>
```

### Page privée (non indexable)

```twig
{% extends 'insidiome/front/base.html.twig' %}

{% block robots %}noindex,nofollow{% endblock %}
```

---

## 📊 Crawlers IA supportés

| Crawler | IA | User-Agent | Status |
|---------|-----|------------|--------|
| GPTBot | ChatGPT (OpenAI) | `GPTBot` | ✅ Autorisé |
| ChatGPT-User | ChatGPT Browse | `ChatGPT-User` | ✅ Autorisé |
| anthropic-ai | Claude (Anthropic) | `anthropic-ai` | ✅ Autorisé |
| Claude-Web | Claude Browse | `Claude-Web` | ✅ Autorisé |
| Google-Extended | Bard/Gemini | `Google-Extended` | ✅ Autorisé |
| PerplexityBot | Perplexity AI | `PerplexityBot` | ✅ Autorisé |
| Applebot-Extended | Apple Intelligence | `Applebot-Extended` | ✅ Autorisé |
| CCBot | Common Crawl | `CCBot` | ✅ Autorisé |
| Bytespider | ByteDance | `Bytespider` | ✅ Autorisé |
| Diffbot | Diffbot | `Diffbot` | ✅ Autorisé |
| FacebookBot | Meta AI | `FacebookBot` | ✅ Autorisé |

---

## 🚫 Bloquer un crawler IA

### Dans robots.txt

```txt
# Bloquer ChatGPT
User-agent: GPTBot
Disallow: /

# Bloquer Claude
User-agent: anthropic-ai
Disallow: /

# Bloquer Common Crawl
User-agent: CCBot
Disallow: /
```

### Dans ai.txt

```txt
User-agent: GPTBot
Training: disallowed
Summarization: disallowed
Disallow: /
```

---

## 🎨 Exemples d'utilisation avancée

### 1. Page produit avec Schema.org

```twig
{% block schema_type %}Product{% endblock %}

{# JSON-LD personnalisé #}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ product.name }}",
    "description": "{{ product.description }}",
    "image": "{{ product.image }}",
    "offers": {
        "@type": "Offer",
        "price": "{{ product.price }}",
        "priceCurrency": "EUR",
        "availability": "https://schema.org/InStock"
    }
}
</script>
```

### 2. Page événement

```twig
{% block schema_type %}Event{% endblock %}

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "{{ event.name }}",
    "startDate": "{{ event.startDate|date('c') }}",
    "endDate": "{{ event.endDate|date('c') }}",
    "location": {
        "@type": "Place",
        "name": "{{ event.location }}",
        "address": "{{ event.address }}"
    }
}
</script>
```

### 3. Page FAQ

```twig
{% block schema_type %}FAQPage{% endblock %}

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Question 1 ?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Réponse 1"
            }
        }
    ]
}
</script>
```

---

## 🔍 Validation et test

### Outils de validation

1. **Google Rich Results Test**
   - URL : https://search.google.com/test/rich-results
   - Teste les structured data Schema.org

2. **Schema.org Validator**
   - URL : https://validator.schema.org/
   - Valide la syntaxe JSON-LD

3. **OpenAI GPTBot Checker**
   - Vérifier dans les logs serveur : `GPTBot`

4. **Test local**
   ```bash
   # Vérifier le JSON-LD généré
   curl http://localhost:8000/nsdm/ | grep -A 20 "application/ld+json"
   
   # Vérifier les meta tags IA
   curl http://localhost:8000/nsdm/ | grep -E "(chatgpt|ai-content)"
   ```

### Vérifier l'indexation par les IA

```bash
# Vérifier les accès dans les logs
tail -f /var/log/nginx/access.log | grep -E "(GPTBot|anthropic|Claude|Perplexity)"
```

---

## 📈 Bonnes pratiques

### ✅ À faire

1. **Structured Data** : Toujours ajouter du JSON-LD pertinent
2. **Descriptions claires** : Écrire des descriptions compréhensibles par les IA
3. **Contenu structuré** : Utiliser des titres H1, H2, H3 logiques
4. **Meta tags complets** : Remplir tous les meta tags SEO
5. **Contenu de qualité** : Les IA privilégient le contenu utile et bien écrit

### ❌ À éviter

1. **Contenu dupliqué** : Les IA détectent le duplicate content
2. **Keyword stuffing** : Éviter la sur-optimisation
3. **Contenu généré par IA non déclaré** : Toujours déclarer si c'est le cas
4. **Bloquer tous les crawlers IA** : Vous perdez en visibilité
5. **JSON-LD invalide** : Valider avant de déployer

---

## 🔐 Considérations légales

### RGPD et données personnelles

```txt
# Dans ai.txt
Personal-Data: protected
GDPR-Compliant: yes
```

### Droits d'auteur

```txt
# Dans ai.txt
Copyright: All rights reserved
Attribution-Required: yes
```

### Utilisation commerciale

```txt
# Dans ai.txt
Use-Cases:
  - commercial-use: contact-required
```

---

## 📊 Monitoring

### Métriques à suivre

1. **Crawl des IA** : Nombre de visites par crawler IA
2. **Erreurs** : Erreurs 404/500 pour les crawlers IA
3. **Temps de réponse** : Performance pour les crawlers
4. **Structured Data** : Validation des données structurées

### Logs à analyser

```bash
# Analyser les crawlers IA
grep -E "(GPTBot|anthropic|Claude|Perplexity)" /var/log/nginx/access.log | wc -l

# Top crawlers IA
grep -E "(GPTBot|anthropic|Claude|Perplexity)" /var/log/nginx/access.log | \
  awk '{print $1}' | sort | uniq -c | sort -rn
```

---

## 🚀 Prochaines étapes

### Améliorations futures

- [ ] Ajouter Schema.org pour les articles de blog
- [ ] Implémenter BreadcrumbList pour la navigation
- [ ] Ajouter Organization schema avec réseaux sociaux
- [ ] Créer un sitemap.xml dynamique
- [ ] Monitorer les crawls IA avec analytics
- [ ] Tester avec différents crawlers IA
- [ ] Optimiser les temps de réponse pour les crawlers

### Ressources utiles

- **Schema.org** : https://schema.org/
- **AI.txt Spec** : https://site.spawning.ai/ai-txt
- **Google AI Guidelines** : https://developers.google.com/search/docs/crawling-indexing/overview-google-crawlers
- **OpenAI GPTBot** : https://platform.openai.com/docs/gptbot
- **Anthropic Claude** : https://www.anthropic.com/

---

## 📞 Support

Pour toute question sur l'optimisation IA :
- Documentation : `/docs/ai-seo-guide.md`
- Fichiers : `/public/robots.txt`, `/public/ai.txt`
- Templates : `templates/*/front/base.html.twig`

---

*Dernière mise à jour : 2025-10-09*
*Version : 1.0*
