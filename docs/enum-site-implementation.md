# Implémentation SiteEnum - Résumé

## ✅ Ce qui a été fait

### 1. Création de l'énumération SiteEnum

**Fichier** : `src/Enum/SiteEnum.php`

```php
enum SiteEnum: string
{
    case SILENUS = 'silenus';
    case INSIDIOME = 'insidiome';
}
```

**Méthodes utiles** :
- `getCode()` → 'slns' ou 'nsdm'
- `getDisplayName()` → 'Silenus' ou 'Insidiome'
- `getDomain()` → 'silenus.local' ou 'insidiome.local'
- `getPrimaryColor()` → Couleur du thème
- `getRoutePrefix()` → '/slns' ou '/nsdm'
- `fromCode(string)` → Créer depuis code
- `fromName(string)` → Créer depuis nom
- `all()` → Liste tous les sites

### 2. Modification de l'entité Site

**Avant** :
```php
#[ORM\Column(length: 20, unique: true)]
private ?string $name = null;
```

**Après** :
```php
#[ORM\Column(length: 20, unique: true, enumType: SiteEnum::class)]
private ?SiteEnum $name = null;
```

**Nouvelles méthodes** :
- `getCode()` → Délègue à l'enum
- `getDisplayName()` → Délègue à l'enum
- `getRoutePrefix()` → Délègue à l'enum

### 3. Mise à jour du SiteManager

Le service utilise maintenant `SiteEnum` :

```php
$siteEnum = SiteEnum::fromName($siteName);
$site->setName($siteEnum);
$site->setDomain($siteEnum->getDomain());
```

### 4. Migration de la base de données

**Migration** : `Version20251013094100.php`

```sql
ALTER TABLE site MODIFY name ENUM('silenus', 'insidiome') NOT NULL
```

✅ Appliquée sur les 2 bases (slns_db, nsdm_db)

## 🎯 Avantages de cette architecture

### Type Safety
```php
// ❌ Avant - Erreur possible
$site->setName('silenuss'); // Typo non détectée

// ✅ Maintenant - Erreur de compilation
$site->setName(SiteEnum::SILENUS); // Type-safe
```

### Centralisation
```php
// Toutes les infos du site en un seul endroit
$site = SiteEnum::SILENUS;
$code = $site->getCode(); // 'slns'
$domain = $site->getDomain(); // 'silenus.local'
$color = $site->getPrimaryColor(); // '#84cc16'
```

### Facilité d'ajout d'un nouveau site
```php
// 1. Ajouter dans l'enum
enum SiteEnum: string
{
    case SILENUS = 'silenus';
    case INSIDIOME = 'insidiome';
    case DAGDA = 'dagda'; // ← Nouveau site
}

// 2. Ajouter les méthodes match
public function getCode(): string
{
    return match($this) {
        self::SILENUS => 'slns',
        self::INSIDIOME => 'nsdm',
        self::DAGDA => 'dgda', // ← Nouveau
    };
}

// 3. Modifier la migration
ALTER TABLE site MODIFY name ENUM('silenus', 'insidiome', 'dagda')
```

## 📝 Utilisation dans le code

### Dans les controllers
```php
use App\Enum\SiteEnum;

// Vérifier le site actuel
$currentSite = $this->siteResolver->getCurrentSite();
if ($currentSite === 'silenus') {
    // Logique Silenus
}

// Ou avec l'entité
$siteEntity = $this->siteManager->getCurrentSiteEntity();
if ($siteEntity->getName() === SiteEnum::SILENUS) {
    // Type-safe
}
```

### Dans les templates
```twig
{# Via l'entité Site #}
{% set site = app.request.attributes.get('site_entity') %}
{{ site.displayName }} {# Silenus ou Insidiome #}
{{ site.code }} {# slns ou nsdm #}

{# Pour les classes CSS #}
<body class="site-{{ site.code }}">
```

### Dans les services
```php
public function doSomething(Site $site): void
{
    $action = match($site->getName()) {
        SiteEnum::SILENUS => $this->handleSilenus(),
        SiteEnum::INSIDIOME => $this->handleInsidiome(),
    };
}
```

## 🔄 Prochaines étapes

Maintenant que l'enum est en place, on peut créer l'entité BlogPost commune :

```php
namespace App\Entity\Common;

use App\Entity\Site;
use App\Enum\SiteEnum;

#[ORM\Entity]
class BlogPost
{
    #[ORM\ManyToOne(targetEntity: Site::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;
    
    // Le site est de type SiteEnum via la relation
    public function getSiteName(): ?SiteEnum
    {
        return $this->site?->getName();
    }
}
```

## ✅ Tests de validation

```bash
# Vérifier les données
docker compose exec db mysql -uroot -proot -e "SELECT * FROM site" slns_db
docker compose exec db mysql -uroot -proot -e "SELECT * FROM site" nsdm_db

# Résultat attendu :
# slns_db : id=1, name='silenus', domain='silenus.local'
# nsdm_db : id=1, name='insidiome', domain='insidiome.local'
```

## 📚 Documentation

- **Enum** : `src/Enum/SiteEnum.php`
- **Entité** : `src/Entity/Site.php`
- **Service** : `src/Service/SiteManager.php`
- **Migration** : `migrations/Version20251013094100.php`
