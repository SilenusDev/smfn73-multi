# 🚀 Architecture Multisite - Référence Technique

> **Projet** : Symfony 7.3 - 2 Sites (Silenus & Insidiome)  
> **Dernière mise à jour** : 2025-10-07

---

## 🎯 Principe

### Architecture
- **2 Bases de données** : `slns_db` et `nsdm_db` (isolation totale)
- **2 Entity Managers** : Sélection automatique selon le path
- **1 Codebase** : Code mutualisé, données isolées

### Détection du site
```
Par path (localhost):
  /slns/* → Silenus (base: slns_db)
  /ndsm/* → Insidiome (base: nsdm_db)

Par domaine (production):
  silenus.local → Silenus
  insidiome.local → Insidiome
```

---

## 📁 Structure

```
/src/
├── /Controller/Slns/       # Controllers Silenus
├── /Controller/Ndsm/       # Controllers Insidiome
├── /Entity/                # Entités communes (User, Site)
├── /Service/               # SiteResolver, DatabaseManager, SiteManager
├── /Security/              # UserProvider contextuel
└── /Command/               # Commandes console

/templates/
├── /silenus/front/         # Templates Silenus (thème violet)
└── /insidiome/front/       # Templates Insidiome (thème rose)

/config/packages/
├── doctrine.yaml           # 2 Entity Managers
└── security.yaml           # 2 Firewalls
```

---

## 🗄️ Bases de Données

| Site | Base | Entity Manager | Routes |
|------|------|----------------|--------|
| **Silenus** | `slns_db` | `silenus` | `/slns/*` |
| **Insidiome** | `nsdm_db` | `insidiome` | `/ndsm/*` |

### Tables (identiques dans chaque base)
- `site` - Marqueur local (id=1)
- `user` - Utilisateurs avec authentification
- `messenger_messages` - Queue de messages
- `doctrine_migration_versions` - Historique migrations

---

## ⚙️ Services Core

### SiteResolver
Détecte le site courant.

```php
$site = $siteResolver->getCurrentSite(); // 'silenus' ou 'insidiome'
$isSilenus = $siteResolver->isSilenus();
$siteName = $siteResolver->getSiteName(); // 'Silenus' ou 'Insidiome'
```

### DatabaseManager
Sélectionne automatiquement la bonne base.

```php
// Utilise automatiquement slns_db ou nsdm_db selon le site
$users = $dbManager->getRepository(User::class)->findAll();
$dbManager->persist($entity);
$dbManager->flush();

// Pour les commandes (accès direct à une base)
$em = $dbManager->getEntityManagerForSite('silenus');
```

### SiteManager
Gère les entités Site locales.

```php
$site = $siteManager->getCurrentSiteEntity(); // Site id=1 de la base courante
$exists = $siteManager->siteEntityExists();
```

---

## 🔐 Authentification

### Configuration
- **2 Firewalls** : `slns` (pattern: `^/slns`) et `ndsm` (pattern: `^/ndsm`)
- **UserProvider contextuel** : Charge les users depuis la bonne base via DatabaseManager
- **Routes séparées** : `/slns/login`, `/ndsm/login`, `/slns/register`, `/ndsm/register`

### Utilisation dans les controllers
```php
// Vérifier si l'utilisateur est connecté
if ($this->getUser()) {
    // User connecté (depuis slns_db ou nsdm_db selon le site)
    $email = $this->getUser()->getEmail();
}

// Redirection après login
return $this->redirectToRoute('slns_home'); // ou 'ndsm_home'
```

---

## 🚀 Commandes

### Migrations
```bash
# Générer une migration pour Silenus
docker-compose exec web php bin/console doctrine:migrations:diff --em=silenus

# Générer une migration pour Insidiome
docker-compose exec web php bin/console doctrine:migrations:diff --em=insidiome

# Exécuter les migrations
docker-compose exec web php bin/console doctrine:migrations:migrate --em=silenus --no-interaction
docker-compose exec web php bin/console doctrine:migrations:migrate --em=insidiome --no-interaction
```

### Initialisation
```bash
# Initialiser les entités Site dans les 2 bases
docker-compose exec web php bin/console app:init-sites
```

### Cache
```bash
# Vider le cache
docker-compose exec web php bin/console cache:clear
```

---

## 🎨 Créer un nouveau Controller

### Pour Silenus
```php
// src/Controller/Slns/MonController.php
namespace App\Controller\Slns;

use App\Service\SiteResolver;
use App\Service\DatabaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/slns')]
class MonController extends AbstractController
{
    public function __construct(
        private SiteResolver $siteResolver,
        private DatabaseManager $dbManager
    ) {}

    #[Route('/ma-route', name: 'slns_ma_route')]
    public function index()
    {
        // Vérification optionnelle
        if (!$this->siteResolver->isSilenus()) {
            throw $this->createNotFoundException();
        }

        // Utiliser DatabaseManager pour accéder aux données
        $data = $this->dbManager->getRepository(Entity::class)->findAll();

        return $this->render('silenus/front/mon-template.html.twig', [
            'data' => $data
        ]);
    }
}
```

### Pour Insidiome
Même structure, remplacer :
- Namespace : `App\Controller\Ndsm`
- Route prefix : `#[Route('/ndsm')]`
- Vérification : `isInsidiome()`
- Template : `insidiome/front/...`

---

## 📝 Créer une nouvelle Entité

```php
// src/Entity/MaNouvelle Entité.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ma_table')]
class MaNouvelleEntite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation avec Site (optionnel mais recommandé)
    #[ORM\ManyToOne(targetEntity: Site::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    // Autres propriétés...
}
```

**Important** : Après création, générer et exécuter les migrations pour les 2 bases.

---

## 🔍 Débogage

### Vérifier les routes
```bash
docker-compose exec web php bin/console debug:router | grep slns
docker-compose exec web php bin/console debug:router | grep ndsm
```

### Vérifier les bases de données
```bash
# Lister les tables de Silenus
docker-compose exec db mysql -usymfony -psymfony slns_db -e "SHOW TABLES;"

# Lister les tables d'Insidiome
docker-compose exec db mysql -usymfony -psymfony nsdm_db -e "SHOW TABLES;"

# Voir les users de chaque base
docker-compose exec db mysql -usymfony -psymfony slns_db -e "SELECT * FROM user;"
docker-compose exec db mysql -usymfony -psymfony nsdm_db -e "SELECT * FROM user;"
```

### Vérifier les services
```bash
# Lister les services disponibles
docker-compose exec web php bin/console debug:container | grep Site
```

---

## 🚨 Points d'Attention

### Isolation des données
- ✅ **Chaque base est totalement isolée**
- ✅ **Impossible d'accéder aux données de l'autre site**
- ✅ **Les users de Silenus ne peuvent pas se connecter sur Insidiome**

### Entité Site
- Chaque base a **sa propre table `site`** avec **1 seule entrée** (id=1)
- Cette entité sert de **marqueur local**, pas de lien inter-bases
- Toujours associer les nouvelles entités au Site courant

### Détection du site
- En **localhost** : Détection par **path** (`/slns/` ou `/ndsm/`)
- En **production** : Détection par **domaine** (`silenus.local` ou `insidiome.local`)
- Fallback : Silenus par défaut

---

## 📚 Ressources

- **README.md** : Guide de démarrage et commandes Docker
- **docs/done.md** : Historique des réalisations
- **docs/todo.md** : Tâches à venir
- **docs/roadmap.md** : Planning de développement

---

*Document épuré - Contient uniquement les informations essentielles*
