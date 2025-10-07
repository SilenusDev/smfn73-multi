# 🚀 Architecture Multisite SIMPLIFIÉE
## Symfony 7.3 - 2 Sites (Silenus & Insidiome) - 2 Bases Séparées - 2 Entity Managers

---

## 🎯 PRINCIPE : Simplicité + Sécurité Maximale

### ✨ Les 3 Piliers

- **🔐 2 Bases de Données** : `multisite_silenus` et `multisite_insidiome` physiquement séparées
- **🏢 2 Entity Managers** : Sélection automatique selon le site détecté
- **🚀 1 Repo Git** : Déploiement classique simple

---

## 📁 Structure du Projet

```
/multisite-symfony/
├── /src/
│   ├── /Entity/              # COMMUNES (sans site_id !)
│   │   ├── User.php
│   │   ├── BlogPost.php
│   │   ├── Category.php
│   │   └── Comment.php
│   ├── /Controller/
│   │   ├── /Silenus/
│   │   └── /Insidiome/
│   ├── /Service/
│   │   ├── SiteResolver.php
│   │   ├── DatabaseManager.php
│   │   └── MailerManager.php
│   └── /Repository/
├── /templates/
│   ├── /common/
│   ├── /silenus/
│   └── /insidiome/
├── /assets/
│   ├── /common/
│   ├── /silenus/
│   └── /insidiome/
└── /config/
    ├── /sites/
    │   ├── silenus.yaml
    │   └── insidiome.yaml
    └── /packages/
        └── doctrine.yaml
```

---

## 🗄️ Deux Bases de Données Séparées

### Configuration

| Site | Base de données | Entity Manager |
|------|-----------------|----------------|
| **Silenus** | `multisite_silenus` | `silenus` |
| **Insidiome** | `multisite_insidiome` | `insidiome` |

### Tables (identiques dans chaque base)

- `user` : id, email, password
- `blog_post` : id, title, content, user_id
- `category` : id, name
- `comment` : id, content, post_id, user_id

**🔐 Isolation totale : Les données de Silenus ne sont PAS dans la même base qu'Insidiome !**

---

## 🏗️ Phase 1 : Configuration Apache & Domaines

**Durée : 2 heures**

### 1.1 - Configuration /etc/hosts

```bash
# Linux/Mac : /etc/hosts
# Windows : C:\Windows\System32\drivers\etc\hosts

127.0.0.1    silenus.local
127.0.0.1    insidiome.local
```

### 1.2 - VirtualHosts Apache

```apache
# /etc/apache2/sites-available/silenus.conf
<VirtualHost *:80>
    ServerName silenus.local
    DocumentRoot /var/www/multisite-symfony/public
    
    <Directory /var/www/multisite-symfony/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# /etc/apache2/sites-available/insidiome.conf
<VirtualHost *:80>
    ServerName insidiome.local
    DocumentRoot /var/www/multisite-symfony/public
    
    <Directory /var/www/multisite-symfony/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

```bash
sudo a2ensite silenus insidiome
sudo systemctl restart apache2
```

### 1.3 - Tester l'accès

```bash
# Doit afficher la page Symfony par défaut
curl http://silenus.local
curl http://insidiome.local
```

---

## 🗄️ Phase 2 : Création des 2 Bases MariaDB

**Durée : 1 heure**

### 2.1 - Créer les bases

```sql
CREATE DATABASE multisite_silenus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE multisite_insidiome CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Permissions
GRANT ALL PRIVILEGES ON multisite_silenus.* TO 'your_user'@'localhost';
GRANT ALL PRIVILEGES ON multisite_insidiome.* TO 'your_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2.2 - Variables d'environnement

```env
# .env
DATABASE_SILENUS_URL="mysql://your_user:password@127.0.0.1:3306/multisite_silenus"
DATABASE_INSIDIOME_URL="mysql://your_user:password@127.0.0.1:3306/multisite_insidiome"

# Mailer OAuth2 par site
SILENUS_MAILER_DSN="gmail+oauth2://username:token@default"
INSIDIOME_MAILER_DSN="gmail+oauth2://username:token@default"
```

---

## ⚙️ Phase 3 : Configuration Doctrine avec 2 Entity Managers

**Durée : 2 heures - CRITIQUE**

### 3.1 - Configuration Doctrine

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        default_connection: silenus
        connections:
            silenus:
                url: '%env(resolve:DATABASE_SILENUS_URL)%'
            insidiome:
                url: '%env(resolve:DATABASE_INSIDIOME_URL)%'

    orm:
        default_entity_manager: silenus
        entity_managers:
            silenus:
                connection: silenus
                naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
                mappings:
                    App:
                        is_bundle: false
                        dir: '%kernel.project_dir%/src/Entity'
                        prefix: 'App\Entity'
                        alias: App
            
            insidiome:
                connection: insidiome
                naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
                mappings:
                    App:
                        is_bundle: false
                        dir: '%kernel.project_dir%/src/Entity'
                        prefix: 'App\Entity'
                        alias: App
```

### 3.2 - Vérifier la configuration

```bash
# Liste des Entity Managers
php bin/console debug:container --env-vars | grep DATABASE

# Doit afficher :
# DATABASE_SILENUS_URL
# DATABASE_INSIDIOME_URL
```

---

## 🔍 Phase 4 : Service SiteResolver

**Durée : 2 heures**

### 4.1 - Créer le service

```php
// src/Service/SiteResolver.php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class SiteResolver
{
    private ?string $currentSite = null;

    // Mapping domaine → site
    private const SITE_MAPPING = [
        'silenus.local' => 'silenus',
        'insidiome.local' => 'insidiome',
        // Production
        'silenus.com' => 'silenus',
        'insidiome.com' => 'insidiome',
    ];

    public function __construct(
        private RequestStack $requestStack
    ) {}

    public function getCurrentSite(): string
    {
        if ($this->currentSite !== null) {
            return $this->currentSite;
        }

        $request = $this->requestStack->getCurrentRequest();
        
        if (!$request) {
            throw new \RuntimeException('Pas de requête disponible');
        }

        $host = $request->getHost();
        
        if (!isset(self::SITE_MAPPING[$host])) {
            throw new \RuntimeException("Site inconnu pour le domaine : {$host}");
        }

        $this->currentSite = self::SITE_MAPPING[$host];
        
        return $this->currentSite;
    }

    public function isSilenus(): bool
    {
        return $this->getCurrentSite() === 'silenus';
    }

    public function isInsidiome(): bool
    {
        return $this->getCurrentSite() === 'insidiome';
    }
}
```

### 4.2 - EventSubscriber pour initialiser

```php
// src/EventSubscriber/SiteSubscriber.php
namespace App\EventSubscriber;

use App\Service\SiteResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SiteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SiteResolver $siteResolver
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // Force la détection du site
        $site = $this->siteResolver->getCurrentSite();
        
        // Log pour debug
        // dump("Site actuel : " . $site);
    }
}
```

---

## 🎯 Phase 5 : Service DatabaseManager (CŒUR DU SYSTÈME)

**Durée : 2 heures - FONDAMENTAL**

### 5.1 - Créer le DatabaseManager

```php
// src/Service/DatabaseManager.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class DatabaseManager
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private SiteResolver $siteResolver
    ) {}

    /**
     * Retourne l'Entity Manager correspondant au site actuel
     */
    public function getEntityManager(): EntityManagerInterface
    {
        $site = $this->siteResolver->getCurrentSite();
        
        return $this->doctrine->getManager($site);
    }

    /**
     * Raccourci pour obtenir un repository
     */
    public function getRepository(string $entityClass)
    {
        return $this->getEntityManager()->getRepository($entityClass);
    }

    /**
     * Persiste une entité dans la bonne base
     */
    public function persist(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * Flush dans la bonne base
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Récupère l'EM d'un site spécifique (utile pour les commandes)
     */
    public function getEntityManagerForSite(string $site): EntityManagerInterface
    {
        return $this->doctrine->getManager($site);
    }
}
```

### 5.2 - Exemple d'utilisation

```php
// Dans n'importe quel service ou controller

public function __construct(
    private DatabaseManager $dbManager
) {}

public function example()
{
    // Récupère automatiquement dans la bonne base !
    $users = $this->dbManager
        ->getRepository(User::class)
        ->findAll();
    
    // Crée un user dans la bonne base
    $user = new User();
    $user->setEmail('test@example.com');
    
    $this->dbManager->persist($user);
    $this->dbManager->flush();
}
```

---

## 📦 Phase 6 : Entités Communes

**Durée : 3 heures**

### 6.1 - Entité User (SANS site_id !)

```php
// src/Entity/User.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    // Pas de site_id car isolation par base !

    // Getters / Setters...

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function eraseCredentials(): void {}
}
```

### 6.2 - Entité BlogPost

```php
// src/Entity/BlogPost.php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'blog_post')]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    private ?Category $category = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    // Getters / Setters...
}
```

### 6.3 - Entités Category et Comment

```php
// src/Entity/Category.php
#[ORM\Entity]
#[ORM\Table(name: 'category')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    // ...
}

// src/Entity/Comment.php
#[ORM\Entity]
#[ORM\Table(name: 'comment')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: BlogPost::class)]
    private ?BlogPost $post = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $author = null;

    // ...
}
```

---

## 🔄 Phase 7 : Migrations pour les 2 Bases

**Durée : 1 heure**

### 7.1 - Générer les migrations

```bash
# Migration pour Silenus
php bin/console make:migration --em=silenus

# Migration pour Insidiome
php bin/console make:migration --em=insidiome
```

### 7.2 - Exécuter les migrations

```bash
# Migrer Silenus
php bin/console doctrine:migrations:migrate --em=silenus

# Migrer Insidiome
php bin/console doctrine:migrations:migrate --em=insidiome
```

### 7.3 - Script pour migrer les 2 bases

```bash
# scripts/migrate-all.sh
#!/bin/bash
echo "🔄 Migration Silenus..."
php bin/console doctrine:migrations:migrate --em=silenus --no-interaction

echo "🔄 Migration Insidiome..."
php bin/console doctrine:migrations:migrate --em=insidiome --no-interaction

echo "✅ Migrations terminées !"
```

```bash
chmod +x scripts/migrate-all.sh
./scripts/migrate-all.sh
```

---

## 👤 Phase 8 : Authentification Contextuelle

**Durée : 3 heures**

### 8.1 - UserProvider contextuel

```php
// src/Security/UserProvider.php
namespace App\Security;

use App\Entity\User;
use App\Service\DatabaseManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserProvider implements UserProviderInterface
{
    public function __construct(
        private DatabaseManager $dbManager
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Recherche dans la base du site actuel
        $user = $this->dbManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}
```

### 8.2 - Configuration Security

```yaml
# config/packages/security.yaml
security:
    password_hashers:
        App\Entity\User:
            algorithm: auto

    providers:
        app_user_provider:
            id: App\Security\UserProvider

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
                enable_csrf: true
            logout:
                path: app_logout

    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
```

### 8.3 - LoginController

```php
// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This should never be reached');
    }
}
```

**✨ Chaque site a ses propres utilisateurs, automatiquement !**

---

## 🎨 Phase 9 : Controllers par Site

**Durée : 3 heures**

### 9.1 - BaseController commun

```php
// src/Controller/BaseController.php
namespace App\Controller;

use App\Service\DatabaseManager;
use App\Service\SiteResolver;
use App\Service\MailerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    public function __construct(
        protected DatabaseManager $dbManager,
        protected SiteResolver $siteResolver,
        protected MailerManager $mailerManager
    ) {}

    protected function getCurrentSite(): string
    {
        return $this->siteResolver->getCurrentSite();
    }
}
```

### 9.2 - Controllers Silenus

```php
// src/Controller/Silenus/BlogController.php
namespace App\Controller\Silenus;

use App\Controller\BaseController;
use App\Entity\BlogPost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/silenus')]
class BlogController extends BaseController
{
    #[Route('/blog', name: 'silenus_blog')]
    public function index(): Response
    {
        // Récupère automatiquement depuis la base Silenus
        $posts = $this->dbManager
            ->getRepository(BlogPost::class)
            ->findAll();

        return $this->render('silenus/blog/index.html.twig', [
            'posts' => $posts,
            'site' => $this->getCurrentSite()
        ]);
    }

    #[Route('/blog/{slug}', name: 'silenus_blog_show')]
    public function show(string $slug): Response
    {
        $post = $this->dbManager
            ->getRepository(BlogPost::class)
            ->findOneBy(['slug' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        return $this->render('silenus/blog/show.html.twig', [
            'post' => $post
        ]);
    }
}
```

### 9.3 - Controllers Insidiome

```php
// src/Controller/Insidiome/BlogController.php
namespace App\Controller\Insidiome;

use App\Controller\BaseController;
use App\Entity\BlogPost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/insidiome')]
class BlogController extends BaseController
{
    // Même code que Silenus
    // DatabaseManager gère automatiquement la bonne base !
    
    #[Route('/blog', name: 'insidiome_blog')]
    public function index(): Response
    {
        $posts = $this->dbManager
            ->getRepository(BlogPost::class)
            ->findAll();

        return $this->render('insidiome/blog/index.html.twig', [
            'posts' => $posts,
            'site' => $this->getCurrentSite()
        ]);
    }
}
```

---

## 📧 Phase 10 : Mailer OAuth2 par Site

**Durée : 2 heures**

### 10.1 - Service MailerManager

```php
// src/Service/MailerManager.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailerManager
{
    public function __construct(
        private SiteResolver $siteResolver,
        private MailerInterface $mailer,
        private ParameterBagInterface $params
    ) {}

    public function sendEmail(string $to, string $subject, string $htmlContent): void
    {
        $site = $this->siteResolver->getCurrentSite();
        
        // Configuration email par site
        [$from, $fromName] = match($site) {
            'silenus' => ['contact@silenus.com', 'Silenus'],
            'insidiome' => ['contact@insidiome.com', 'Insidiome'],
            default => ['noreply@example.com', 'Unknown']
        };

        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->html($htmlContent);

        $this->mailer->send($email);
    }

    public function sendTemplatedEmail(string $to, string $subject, string $template, array $context = []): void
    {
        // Utiliser Twig pour rendre le template
        // Puis appeler sendEmail()
    }
}
```

### 10.2 - Configuration Mailer

```yaml
# config/packages/mailer.yaml
framework:
    mailer:
        dsn: '%env(MAILER_DSN)%'
```

**Note :** Configurez 2 comptes OAuth2 distincts et utilisez un transport factory pour sélectionner selon le site.

---

## 🎭 Phase 11 : Templates par Site

**Durée : 2 heures**

### 11.1 - Template base commun

```twig
{# templates/common/base.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{% block title %}Welcome!{% endblock %}</title>
    {% block stylesheets %}
        {{ encore_entry_link_tags('common') }}
    {% endblock %}
</head>
<body>
    {% block body %}{% endblock %}
    
    {% block javascripts %}
        {{ encore_entry_script_tags('common') }}
    {% endblock %}
</body>
</html>
```

### 11.2 - Templates Silenus

```twig
{# templates/silenus/base.html.twig #}
{% extends 'common/base.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    {{ encore_entry_link_tags('silenus') }}
{% endblock %}

{# templates/silenus/blog/index.html.twig #}
{% extends 'silenus/base.html.twig' %}

{% block title %}Blog Silenus{% endblock %}

{% block body %}
    <h1>Blog {{ site|upper }}</h1>
    
    {% for post in posts %}
        <article>
            <h2>{{ post.title }}</h2>
            <p>{{ post.content|slice(0, 200) }}...</p>
            <a href="{{ path('silenus_blog_show', {slug: post.slug}) }}">Lire plus</a>
        </article>
    {% endfor %}
{% endblock %}
```

### 11.3 - Templates Insidiome (structure identique)

---

## 🎨 Phase 12 : Assets Webpack Encore

**Durée : 2 heures**

### 12.1 - Configuration Webpack

```javascript
// webpack.config.js
const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('common', './assets/common/app.js')
    .addEntry('silenus', './assets/silenus/app.js')
    .addEntry('insidiome', './assets/insidiome/app.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
```

### 12.2 - Structure Assets

```
/assets/
├── /common/
│   ├── app.js
│   └── styles/
│       ├── _variables.scss
│       └── app.scss
├── /silenus/
│   ├── app.js
│   └── styles/
│       └── silenus.scss
└── /insidiome/
    ├── app.js
    └── styles/
        └── insidiome.scss
```

```scss
// assets/common/styles/app.scss
$primary-color: #007bff;

body {
    font-family: Arial, sans-serif;
}

// assets/silenus/styles/silenus.scss
@import '../../common/styles/variables';

$silenus-color: #ff6b6b;

body {
    background-color: lighten($silenus-color, 45%);
}

h1 {
    color: $silenus-color;
}
```

---

## ✅ Phase 13 : Tests

**Durée : 2 heures**

### 13.1 - Test du SiteResolver

```php
// tests/Service/SiteResolverTest.php
namespace App\Tests\Service;

use App\Service\SiteResolver;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SiteResolverTest extends KernelTestCase
{
    public function testDetectSilenus(): void
    {
        $request = Request::create('http://silenus.local');
        $requestStack = new RequestStack();
        $requestStack->push($request);
        
        $resolver = new SiteResolver($requestStack);
        
        $this->assertEquals('silenus', $resolver->getCurrentSite());
    }

    public function testDetectInsidiome(): void
    {
        $request = Request::create('http://insidiome.local');
        $requestStack = new RequestStack();
        $requestStack->push($request);
        
        $resolver = new SiteResolver($requestStack);
        
        $this->assertEquals('insidiome', $resolver->getCurrentSite());
    }
}
```

### 13.2 - Test isolation des données

```php
// tests/Service/DatabaseManagerTest.php
namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabaseManagerTest extends KernelTestCase
{
    public function testIsolationDesDonnees(): void
    {
        self::bootKernel();
        
        // Créer un user dans Silenus
        $emSilenus = self::getContainer()
            ->get('doctrine')
            ->getManager('silenus');
        
        $userSilenus = new User();
        $userSilenus->setEmail('silenus@example.com');
        $emSilenus->persist($userSilenus);
        $emSilenus->flush();
        
        // Vérifier qu'il n'existe PAS dans Insidiome
        $emInsidiome = self::getContainer()
            ->get('doctrine')
            ->getManager('insidiome');
        
        $userInsidiome = $emInsidiome
            ->getRepository(User::class)
            ->findOneBy(['email' => 'silenus@example.com']);
        
        $this->assertNull($userInsidiome);
    }
}
```

### 13.3 - Tests fonctionnels

```php
// tests/Controller/BlogControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testSilenusBlog(): void
    {
        $client = static::createClient();
        $client->request('GET', '/silenus/blog', [], [], [
            'HTTP_HOST' => 'silenus.local'
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'SILENUS');
    }

    public function testInsidiomeBlog(): void
    {
        $client = static::createClient();
        $client->request('GET', '/insidiome/blog', [], [], [
            'HTTP_HOST' => 'insidiome.local'
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'INSIDIOME');
    }
}
```

```bash
# Exécuter les tests
php bin/phpunit
```

---

## 📦 Phase 14 : Déploiement Production

**Durée : 2 heures**

### 14.1 - Configuration production

```env
# .env.prod
APP_ENV=prod
APP_DEBUG=0

DATABASE_SILENUS_URL="mysql://user:pass@prod-server:3306/multisite_silenus"
DATABASE_INSIDIOME_URL="mysql://user:pass@prod-server:3306/multisite_insidiome"

SILENUS_MAILER_DSN="gmail+oauth2://..."
INSIDIOME_MAILER_DSN="gmail+oauth2://..."
```

### 14.2 - Apache VirtualHosts Production

```apache
# /etc/apache2/sites-available/silenus.conf
<VirtualHost *:80>
    ServerName silenus.com
    DocumentRoot /var/www/multisite-symfony/public

    <Directory /var/www/multisite-symfony/public>
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/silenus-error.log
    CustomLog ${APACHE_LOG_DIR}/silenus-access.log combined
</VirtualHost>

# /etc/apache2/sites-available/insidiome.conf
<VirtualHost *:80>
    ServerName insidiome.com
    DocumentRoot /var/www/multisite-symfony/public

    <Directory /var/www/multisite-symfony/public>
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/insidiome-error.log
    CustomLog ${APACHE_LOG_DIR}/insidiome-access.log combined
</VirtualHost>
```

### 14.3 - Script de déploiement

```bash
#!/bin/bash
# deploy.sh

echo "🚀 Déploiement du multisite..."

# Pull du code
git pull origin main

# Installation dépendances
composer install --no-dev --optimize-autoloader

# Migrations des 2 bases
echo "📦 Migration Silenus..."
php bin/console doctrine:migrations:migrate --em=silenus --no-interaction

echo "📦 Migration Insidiome..."
php bin/console doctrine:migrations:migrate --em=insidiome --no-interaction

# Build assets
npm install
npm run build

# Clear cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Permissions
chown -R www-data:www-data var/cache var/log

echo "✅ Déploiement terminé !"
```

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## 🎉 Récapitulatif Final

### ✅ Architecture Complète

| Composant | Silenus | Insidiome |
|-----------|---------|-----------|
| **Base de données** | `multisite_silenus` | `multisite_insidiome` |
| **Entity Manager** | `silenus` | `insidiome` |
| **Domaine local** | `silenus.local` | `insidiome.local` |
| **Domaine prod** | `silenus.com` | `insidiome.com` |
| **OAuth2 Mail** | Compte 1 | Compte 2 |
| **Templates** | `/templates/silenus/` | `/templates/insidiome/` |
| **Assets** | `/assets/silenus/` | `/assets/insidiome/` |
| **Controllers** | `/Controller/Silenus/` | `/Controller/Insidiome/` |

### 🔐 Sécurité Garantie

- ✅ **Isolation physique** : 2 bases MariaDB séparées
- ✅ **Pas de mélange** : Impossible d'accéder aux données de l'autre site
- ✅ **Authentification isolée** : Users complètement séparés
- ✅ **Sessions séparées** : Isolation automatique par domaine

### 🚀 Avantages de cette Architecture

| Avantage | Bénéfice |
|----------|----------|
| **2 bases séparées** | Sécurité maximale, backup indépendants |
| **Entités communes** | Pas de duplication de code |
| **DatabaseManager** | Simplicité d'utilisation |
| **SiteResolver** | Détection automatique |
| **1 repo Git** | Déploiement simple |
| **Mailer contextuel** | Emails personnalisés par site |
| **Assets modulaires** | Design unique par site |

---

## 📅 Timeline Réaliste

### Jour 1 (8h) : Fondations
- ✅ Phase 1 : Apache + domaines (2h)
- ✅ Phase 2 : Création 2 bases (1h)
- ✅ Phase 3 : Config Doctrine 2 EM (2h)
- ✅ Phase 4 : SiteResolver (2h)
- ✅ Phase 5 : DatabaseManager (1h)

### Jour 2 (8h) : Entités et Données
- ✅ Phase 6 : Entités communes (3h)
- ✅ Phase 7 : Migrations (1h)
- ✅ Phase 8 : Authentification (3h)
- ✅ Tests isolation (1h)

### Jour 3 (8h) : Fonctionnalités
- ✅ Phase 9 : Controllers (3h)
- ✅ Phase 10 : Mailer OAuth2 (2h)
- ✅ Phase 11 : Templates (2h)
- ✅ Phase 12 : Assets Webpack (1h)

### Jour 4 (4h) : Finitions
- ✅ Phase 13 : Tests (2h)
- ✅ Phase 14 : Déploiement (2h)

**TOTAL : 3,5 jours pour un développeur junior**

---

## 🛠️ Commandes Utiles

### Développement

```bash
# Démarrer le serveur Symfony
symfony server:start

# Accéder aux sites
http://silenus.local:8000/silenus/blog
http://insidiome.local:8000/insidiome/blog

# Générer une migration pour Silenus
php bin/console make:migration --em=silenus

# Générer une migration pour Insidiome
php bin/console make:migration --em=insidiome

# Migrer toutes les bases
./scripts/migrate-all.sh

# Build assets en dev
npm run dev

# Build assets en prod
npm run build

# Clear cache
php bin/console cache:clear

# Créer un user admin pour Silenus
php bin/console app:create-admin silenus admin@silenus.com

# Créer un user admin pour Insidiome
php bin/console app:create-admin insidiome admin@insidiome.com
```

### Debug

```bash
# Lister les Entity Managers
php bin/console debug:container --parameters | grep database

# Vérifier la config Doctrine
php bin/console doctrine:mapping:info --em=silenus
php bin/console doctrine:mapping:info --em=insidiome

# Vérifier les routes
php bin/console debug:router

# Logs en temps réel
tail -f var/log/dev.log
```

---

## 💡 Astuces & Bonnes Pratiques

### 1. Commandes console avec site spécifique

```php
// src/Command/CreateAdminCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\Persistence\ManagerRegistry;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';

    public function __construct(private ManagerRegistry $doctrine)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('site', InputArgument::REQUIRED, 'Site (silenus ou insidiome)')
            ->addArgument('email', InputArgument::REQUIRED, 'Email');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $site = $input->getArgument('site');
        $email = $input->getArgument('email');
        
        // Obtenir l'EM du site
        $em = $this->doctrine->getManager($site);
        
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        // ... set password
        
        $em->persist($user);
        $em->flush();
        
        $output->writeln("✅ Admin créé pour $site");
        
        return Command::SUCCESS;
    }
}
```

### 2. Service pour switcher entre sites (utile pour admin)

```php
// src/Service/SiteSwitcher.php
namespace App\Service;

class SiteSwitcher
{
    public function __construct(
        private ManagerRegistry $doctrine
    ) {}

    public function executeForAllSites(callable $callback): array
    {
        $results = [];
        
        foreach (['silenus', 'insidiome'] as $site) {
            $em = $this->doctrine->getManager($site);
            $results[$site] = $callback($em, $site);
        }
        
        return $results;
    }
}

// Utilisation
$stats = $this->siteSwitcher->executeForAllSites(function($em, $site) {
    return $em->getRepository(User::class)->count([]);
});

// $stats = ['silenus' => 10, 'insidiome' => 15]
```

### 3. Fixtures par site

```php
// src/DataFixtures/SilenusFixtures.php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SilenusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $manager est automatiquement celui de Silenus
        // si vous exécutez : php bin/console doctrine:fixtures:load --em=silenus
        
        for ($i = 0; $i < 10; $i++) {
            $post = new BlogPost();
            $post->setTitle("Post Silenus $i");
            $manager->persist($post);
        }
        
        $manager->flush();
    }
}
```

### 4. Backup des 2 bases

```bash
#!/bin/bash
# scripts/backup-all.sh

DATE=$(date +%Y%m%d_%H%M%S)

echo "💾 Backup Silenus..."
mysqldump -u user -p multisite_silenus > backups/silenus_$DATE.sql

echo "💾 Backup Insidiome..."
mysqldump -u user -p multisite_insidiome > backups/insidiome_$DATE.sql

echo "✅ Backups terminés dans /backups/"
```

---

## 🚨 Points d'Attention

### ⚠️ À NE PAS FAIRE

❌ **Ne jamais** faire une requête croisée entre bases
```php
// MAUVAIS - Ne fonctionne pas
$userSilenus = $emSilenus->find(User::class, 1);
$postInsidiome = $emInsidiome->find(BlogPost::class, 1);
$postInsidiome->setAuthor($userSilenus); // ❌ ERREUR !
```

❌ **Ne jamais** oublier de spécifier l'EM dans les commandes
```bash
# MAUVAIS
php bin/console doctrine:migrations:migrate

# BON
php bin/console doctrine:migrations:migrate --em=silenus
```

❌ **Ne jamais** mélanger les Entity Managers
```php
// MAUVAIS
$user = $emSilenus->find(User::class, 1);
$emInsidiome->persist($user); // ❌ ERREUR !
```

### ✅ À FAIRE

✅ **Toujours** utiliser DatabaseManager dans les controllers
```php
// BON
$users = $this->dbManager->getRepository(User::class)->findAll();
```

✅ **Toujours** tester sur les 2 sites
```bash
curl http://silenus.local/blog
curl http://insidiome.local/blog
```

✅ **Toujours** vérifier l'isolation des données après modifications

---

## 🎓 Checklist de Démarrage

### ☑️ Semaine 1 : Fondations (Phases 1-8)

- [ ] Configurer Apache avec 2 VirtualHosts
- [ ] Créer les 2 bases MariaDB
- [ ] Configurer Doctrine avec 2 Entity Managers
- [ ] Créer le service SiteResolver
- [ ] Créer le service DatabaseManager
- [ ] Créer les entités (User, BlogPost, Category, Comment)
- [ ] Générer et exécuter les migrations pour les 2 bases
- [ ] Configurer l'authentification contextuelle
- [ ] Tester l'isolation des données

### ☑️ Semaine 2 : Fonctionnalités (Phases 9-14)

- [ ] Créer les controllers Silenus et Insidiome
- [ ] Configurer le mailer OAuth2
- [ ] Créer les templates par site
- [ ] Configurer Webpack Encore avec les assets
- [ ] Écrire les tests unitaires et fonctionnels
- [ ] Préparer le déploiement production
- [ ] Documenter l'architecture
- [ ] Former l'équipe

---

## 📚 Ressources Complémentaires

### Documentation Symfony

- [Multiple Entity Managers](https://symfony.com/doc/current/doctrine/multiple_entity_managers.html)
- [Symfony Security](https://symfony.com/doc/current/security.html)
- [Webpack Encore](https://symfony.com/doc/current/frontend.html)
- [Mailer Component](https://symfony.com/doc/current/mailer.html)

### Commandes de Référence

```bash
# Doctrine
php bin/console doctrine:database:create --connection=silenus
php bin/console doctrine:schema:validate --em=silenus
php bin/console doctrine:query:sql "SELECT * FROM user" --em=silenus

# Debug
php bin/console debug:container DatabaseManager
php bin/console debug:event-dispatcher
php bin/console debug:router --show-controllers

# Cache
php bin/console cache:pool:clear cache.global_clearer
php bin/console cache:pool:list

# Assets
npm run watch  # Mode développement avec hot reload
npm run build  # Mode production
```

---

## 🎯 Conclusion

### Ce que vous avez maintenant :

✅ **Architecture professionnelle** avec 2 bases séparées  
✅ **Isolation totale** des données par site  
✅ **Code réutilisable** avec entités communes  
✅ **Détection automatique** du site via domaine  
✅ **Mailer OAuth2** personnalisé par site  
✅ **Tests complets** pour garantir l'isolation  
✅ **Déploiement simple** avec 1 seul repo Git  

### Prochaines étapes possibles :

1. **Admin interface** : EasyAdmin pour gérer les 2 sites
2. **API REST** : endpoints contextuels par site
3. **Monitoring** : logs et métriques par site
4. **CDN** : assets optimisés par site
5. **Multi-langue** : i18n par site

---

**🚀 Prêt à commencer ? Lancez-vous avec la Phase 1 !**

**Besoin d'aide sur une phase spécifique ? N'hésitez pas à demander !**