# Phase 7 - Entités Métier - Plan révisé (Architecture commune)

## 🎯 Principe architectural

**Une seule entité commune** avec relation ManyToOne vers Site pour l'isolation des données.

```
BlogPost (commune)
  ├── site_id (ManyToOne → Site)
  └── Stockée dans la base du site correspondant
```

## Étape 1 : Entité BlogPost commune (1h)

### 1.1 Créer l'entité commune
```bash
src/Entity/Common/BlogPost.php
```

**Propriétés** :
- `id` (int, auto)
- `site` (ManyToOne → Site, nullable: false) ← **CLÉ D'ISOLATION**
- `author` (ManyToOne → User)
- `title` (string, 255)
- `slug` (string, 255)
- `content` (text)
- `excerpt` (text, nullable)
- `status` (string: draft, published, archived)
- `publishedAt` (datetime, nullable)
- `viewCount` (int, default 0)

**Traits à utiliser** :
- `TimeTrait` (createdAt, updatedAt)
- `SeoTrait` (slug, description, tags, image)

**Index unique** :
```php
#[UniqueConstraint(name: 'UNIQ_SITE_SLUG', columns: ['site_id', 'slug'])]
```
→ Un slug peut exister sur les 2 sites, mais unique par site

### 1.2 Configuration Doctrine

L'entité sera mappée dans **les 2 Entity Managers** :

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        entity_managers:
            silenus:
                mappings:
                    Common:
                        type: attribute
                        dir: '%kernel.project_dir%/src/Entity/Common'
                        prefix: 'App\Entity\Common'
                    
            insidiome:
                mappings:
                    Common:
                        type: attribute
                        dir: '%kernel.project_dir%/src/Entity/Common'
                        prefix: 'App\Entity\Common'
```

### 1.3 Générer les migrations

```bash
# Migration pour Silenus
docker compose exec web php bin/console doctrine:migrations:diff --em=silenus

# Migration pour Insidiome
docker compose exec web php bin/console doctrine:migrations:diff --em=insidiome

# Exécuter
make db-migrate
```

## Étape 2 : Repository commune (30min)

### 2.1 Créer le repository
```bash
src/Repository/BlogPostRepository.php
```

**Méthodes importantes** :
```php
public function findBySite(Site $site): array
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.site = :site')
        ->setParameter('site', $site)
        ->orderBy('b.publishedAt', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findPublishedBySite(Site $site): array
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.site = :site')
        ->andWhere('b.status = :status')
        ->andWhere('b.publishedAt <= :now')
        ->setParameter('site', $site)
        ->setParameter('status', 'published')
        ->setParameter('now', new \DateTime())
        ->orderBy('b.publishedAt', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findBySlugAndSite(string $slug, Site $site): ?BlogPost
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.slug = :slug')
        ->andWhere('b.site = :site')
        ->setParameter('slug', $slug)
        ->setParameter('site', $site)
        ->getOneOrNullResult();
}
```

## Étape 3 : Service BlogPostManager (1h)

### 3.1 Créer le service
```bash
src/Service/BlogPostManager.php
```

**Responsabilités** :
- Utiliser le bon Entity Manager selon le site
- Filtrer automatiquement par site
- Gérer la création/modification/suppression

```php
class BlogPostManager
{
    public function __construct(
        private DatabaseManager $databaseManager,
        private SiteResolver $siteResolver
    ) {}

    public function findAll(): array
    {
        $site = $this->siteResolver->getCurrentSite();
        $em = $this->databaseManager->getEntityManager();
        
        return $em->getRepository(BlogPost::class)
            ->findBySite($site);
    }

    public function findPublished(): array
    {
        $site = $this->siteResolver->getCurrentSite();
        $em = $this->databaseManager->getEntityManager();
        
        return $em->getRepository(BlogPost::class)
            ->findPublishedBySite($site);
    }

    public function create(BlogPost $post): void
    {
        $site = $this->siteResolver->getCurrentSite();
        $post->setSite($site);
        
        $em = $this->databaseManager->getEntityManager();
        $em->persist($post);
        $em->flush();
    }
}
```

## Étape 4 : Controllers (2h)

### 4.1 BlogController (affichage public)

**UN SEUL controller** qui s'adapte au site :

```bash
src/Controller/Common/BlogController.php
```

**Routes** :
```php
#[Route('/{_site}/blog', name: 'blog_index')]
#[Route('/{_site}/blog/{slug}', name: 'blog_show')]
```

Le paramètre `{_site}` sera automatiquement `slns` ou `nsdm`.

### 4.2 AdminBlogController (gestion)

```bash
src/Controller/Common/Admin/BlogController.php
```

**Routes** :
```php
#[Route('/{_site}/admin/blog', name: 'admin_blog_index')]
#[Route('/{_site}/admin/blog/new', name: 'admin_blog_new')]
#[Route('/{_site}/admin/blog/{id}/edit', name: 'admin_blog_edit')]
```

## Étape 5 : Templates (2h)

### 5.1 Templates communs avec variables

```bash
templates/common/blog/index.html.twig
templates/common/blog/show.html.twig
templates/common/admin/blog/index.html.twig
templates/common/admin/blog/form.html.twig
```

**Utiliser les variables de site** :
```twig
{% extends site_code == 'slns' ? 'silenus/front/base.html.twig' : 'insidiome/front/base.html.twig' %}
```

Ou créer des templates spécifiques qui étendent les communs :

```bash
templates/silenus/front/blog/index.html.twig
  {% extends 'common/blog/index.html.twig' %}

templates/insidiome/front/blog/index.html.twig
  {% extends 'common/blog/index.html.twig' %}
```

## Avantages de cette architecture

✅ **Une seule entité** - Pas de duplication de code
✅ **Isolation garantie** - Via la relation site_id
✅ **Migrations simplifiées** - Une seule définition
✅ **Repository commune** - Méthodes réutilisables
✅ **Service centralisé** - BlogPostManager gère tout
✅ **Évolutif** - Facile d'ajouter un 3ème site

## Structure finale

```
src/
├── Entity/
│   └── Common/
│       ├── BlogPost.php          ← UNE SEULE entité
│       ├── Category.php          ← À venir
│       └── Comment.php           ← À venir
├── Repository/
│   └── BlogPostRepository.php    ← UN SEUL repository
├── Service/
│   └── BlogPostManager.php       ← UN SEUL service
└── Controller/
    └── Common/
        ├── BlogController.php    ← UN SEUL controller public
        └── Admin/
            └── BlogController.php ← UN SEUL controller admin
```

## Ordre d'exécution

1. ✅ Créer `src/Entity/Common/BlogPost.php`
2. ✅ Configurer les mappings Doctrine
3. ✅ Générer les migrations (2x, une par base)
4. ✅ Exécuter les migrations
5. ✅ Créer `BlogPostRepository.php`
6. ✅ Créer `BlogPostManager.php`
7. ✅ Créer `BlogController.php` (public)
8. ✅ Créer templates communs
9. ✅ Tester sur les 2 sites
10. ⏳ Créer `AdminBlogController.php`
11. ⏳ Créer formulaire
12. ⏳ Créer templates admin

## Commandes

```bash
# Vérifier la configuration Doctrine
docker compose exec web php bin/console doctrine:mapping:info

# Générer les migrations
docker compose exec web php bin/console make:migration --em=silenus
docker compose exec web php bin/console make:migration --em=insidiome

# Exécuter
docker compose exec web php bin/console doctrine:migrations:migrate --em=silenus --no-interaction
docker compose exec web php bin/console doctrine:migrations:migrate --em=insidiome --no-interaction
```

## Notes importantes

⚠️ **Toujours filtrer par site** dans les requêtes
⚠️ **Toujours définir le site** lors de la création
⚠️ **Utiliser BlogPostManager** au lieu d'accéder directement au repository
⚠️ **Tester l'isolation** : un article créé sur Silenus ne doit pas apparaître sur Insidiome
