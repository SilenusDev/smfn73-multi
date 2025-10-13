# Phase 7 - Entités Métier - Plan d'action

## Objectif
Créer un système de blog fonctionnel pour les deux sites avec isolation des données.

## Étape 1 : Entité BlogPost (2-3h)

### 1.1 Créer l'entité
```bash
# Pour chaque site
src/Entity/Slns/BlogPost.php
src/Entity/Ndsm/BlogPost.php
```

**Propriétés** :
- `id` (int, auto)
- `title` (string, 255)
- `slug` (string, 255, unique)
- `content` (text)
- `excerpt` (text, nullable)
- `author` (ManyToOne → User)
- `site` (ManyToOne → Site)
- `status` (string: draft, published, archived)
- `publishedAt` (datetime, nullable)
- `createdAt` (datetime)
- `updatedAt` (datetime)
- `viewCount` (int, default 0)

**Utiliser les Traits existants** :
- `TimeTrait` (createdAt, updatedAt)
- `TitleTrait` (title)
- `ContentTrait` (content)
- `SeoTrait` (slug, description, tags, image)
- `StatusTrait` (status)

### 1.2 Générer les migrations
```bash
make db-migrate
```

### 1.3 Créer les repositories
```bash
src/Repository/Slns/BlogPostRepository.php
src/Repository/Ndsm/BlogPostRepository.php
```

## Étape 2 : Controllers CRUD (3-4h)

### 2.1 BlogController pour affichage public
```bash
src/Controller/Slns/BlogController.php
src/Controller/Ndsm/BlogController.php
```

**Routes** :
- `/slns/blog` - Liste des articles
- `/slns/blog/{slug}` - Détail d'un article
- `/ndsm/blog` - Liste des articles
- `/ndsm/blog/{slug}` - Détail d'un article

### 2.2 AdminBlogController pour gestion
```bash
src/Controller/Slns/Admin/BlogController.php
src/Controller/Ndsm/Admin/BlogController.php
```

**Routes** :
- `/slns/admin/blog` - Liste admin
- `/slns/admin/blog/new` - Créer
- `/slns/admin/blog/{id}/edit` - Éditer
- `/slns/admin/blog/{id}/delete` - Supprimer

## Étape 3 : Templates (2-3h)

### 3.1 Templates publics
```bash
templates/silenus/front/blog/index.html.twig
templates/silenus/front/blog/show.html.twig
templates/insidiome/front/blog/index.html.twig
templates/insidiome/front/blog/show.html.twig
```

### 3.2 Templates admin
```bash
templates/silenus/admin/blog/index.html.twig
templates/silenus/admin/blog/form.html.twig
templates/insidiome/admin/blog/index.html.twig
templates/insidiome/admin/blog/form.html.twig
```

## Étape 4 : Formulaires (1-2h)

```bash
src/Form/BlogPostType.php
```

**Champs** :
- title (TextType)
- slug (TextType, auto-généré)
- content (TextareaType ou CKEditor)
- excerpt (TextareaType)
- status (ChoiceType: draft, published)
- publishedAt (DateTimeType)

## Étape 5 : Tests (1-2h)

```bash
tests/Entity/BlogPostTest.php
tests/Controller/BlogControllerTest.php
```

## Estimation totale : 9-14h

## Ordre d'exécution recommandé

1. ✅ **Créer l'entité BlogPost** (30min)
2. ✅ **Générer et exécuter les migrations** (15min)
3. ✅ **Créer BlogController public** (1h)
4. ✅ **Créer templates publics** (1h)
5. ✅ **Tester affichage public** (30min)
6. ⏳ **Créer AdminBlogController** (1h30)
7. ⏳ **Créer formulaire BlogPostType** (1h)
8. ⏳ **Créer templates admin** (1h30)
9. ⏳ **Tester CRUD complet** (1h)
10. ⏳ **Ajouter pagination** (30min)
11. ⏳ **Ajouter recherche** (30min)

## Commandes utiles

```bash
# Créer l'entité
php bin/console make:entity

# Générer migration
php bin/console make:migration

# Exécuter migration
php bin/console doctrine:migrations:migrate

# Créer controller
php bin/console make:controller

# Créer formulaire
php bin/console make:form
```

## Après BlogPost

Une fois BlogPost fonctionnel, on pourra ajouter :
- **Category** (catégories de blog)
- **Comment** (commentaires sur les articles)
- **Tag** (tags pour les articles)
- **Page** (pages statiques)

## Notes importantes

- ✅ Toujours vérifier le site actuel via `SiteResolver`
- ✅ Utiliser le bon Entity Manager (slns ou ndsm)
- ✅ Isoler les données par site
- ✅ Tester sur les 2 sites
- ✅ Utiliser les traits existants pour éviter la duplication
