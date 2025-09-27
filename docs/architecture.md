# ARCHITECTURE - Technique

> **Style** : Document sobre et épuré pour la gestion de projet

## Structure du projet

```
/multisite-symfony/
├── /src/
│   ├── /Entity/Common/
│   ├── /Entity/Silenus/
│   ├── /Entity/Insidiome/
│   ├── /Entity/Dagda/
│   ├── /Controller/Common/
│   └── /Service/
├── /templates/
│   ├── /common/
│   ├── /silenus/
│   ├── /insidiome/
│   └── /dagda/
├── /assets/
│   ├── /common/
│   ├── /silenus/
│   ├── /insidiome/
│   └── /dagda/
└── /config/
    ├── sites/
    └── packages/
```

## Services principaux

### SiteResolver
- Détection automatique du site actif
- Basé sur `$_SERVER['HTTP_HOST']`
- Configuration dans `parameters.yaml`

### DatabaseManager
- Accès contextuel aux Entity Managers
- Injection du SiteResolver
- Gestion des connexions multiples

### MailerManager
- Configuration OAuth2 par site
- Transport dynamique selon contexte
- Templates email personnalisés

### TemplateResolver
- Résolution automatique des templates
- Fallback vers common si absent
- Integration controllers

## Configuration Doctrine

```yaml
doctrine:
    dbal:
        connections:
            silenus:
                url: '%env(DATABASE_SILENUS_URL)%'
                dbname: silenus_db
            insidiome:
                url: '%env(DATABASE_INSIDIOME_URL)%'
                dbname: insidiome_db
            dagda:
                url: '%env(DATABASE_DAGDA_URL)%'
                dbname: dagda_db
    orm:
        entity_managers:
            silenus:
                connection: silenus
                mappings:
                    AppSilenus:
                        type: attribute
                        dir: '%kernel.project_dir%/src/Entity/Silenus'
                        prefix: 'App\Entity\Silenus'
            insidiome:
                connection: insidiome
                mappings:
                    AppInsidiome:
                        type: attribute
                        dir: '%kernel.project_dir%/src/Entity/Insidiome'
                        prefix: 'App\Entity\Insidiome'
            dagda:
                connection: dagda
                mappings:
                    AppDagda:
                        type: attribute
                        dir: '%kernel.project_dir%/src/Entity/Dagda'
                        prefix: 'App\Entity\Dagda'
```

## Security par site

```yaml
security:
    firewalls:
        silenus_secured:
            pattern: ^/
            host: silenus.local
            provider: silenus_users
        insidiome_secured:
            pattern: ^/
            host: insidiome.local
            provider: insidiome_users
        dagda_secured:
            pattern: ^/
            host: dagda.local
            provider: dagda_users
```

## Webpack Encore

```javascript
Encore
    .addEntry('common', './assets/common/app.js')
    .addEntry('silenus', './assets/silenus/app.js')
    .addEntry('insidiome', './assets/insidiome/app.js')
    .addEntry('dagda', './assets/dagda/app.js')
```

---
*Dernière mise à jour : 2025-09-17*
