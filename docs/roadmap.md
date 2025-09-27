# ROADMAP - Planning développement

> **Style** : Document sobre et épuré pour la gestion de projet

## Phase 1 - Préparation (1-2 jours)
**Priorité** : MAXIMALE
- Sauvegarde projet actuel
- Configuration domaines locaux
- Restructuration dossiers

## Phase 2 - Détection de site (1 jour)
**Priorité** : HAUTE
- Service SiteResolver
- EventSubscriber SiteInitializer

## Phase 3 - Bases de données (2 jours)
**Priorité** : CRITIQUE
- Création bases multisite
- Configuration Doctrine multiple
- Service DatabaseManager

## Phase 4 - Entités blog (2-3 jours)
**Priorité** : MOYENNE
- Entités communes réutilisables
- Traits (Timestampable, Sluggable, SEO)
- Migrations par site

## Phase 5 - Mailer OAuth (1-2 jours)
**Priorité** : MOYENNE
- Configuration OAuth2 par site
- Service MailerManager contextuel
- Templates email par site

## Phase 6 - Authentification (2-3 jours)
**Priorité** : MOYENNE
- Configuration Security contextuelle
- UserProvider personnalisé
- Interface admin commune

## Phase 7 - Templates SCSS (3-4 jours)
**Priorité** : MOYENNE
- Organisation Twig hiérarchique
- Service TemplateResolver
- Webpack Encore multisite

## Phase 8 - Controllers Routing (2-3 jours)
**Priorité** : BASSE
- BaseController contextuel
- Controllers Blog réutilisables
- Routing conditionnel

## Phase 9 - Tests Optimisation (2-3 jours)
**Priorité** : BASSE
- Tests automatisés
- Sécurité avancée
- Cache et performance

## Phase 10 - Déploiement (2-3 jours)
**Priorité** : BASSE
- Configuration production
- Scripts déploiement
- Documentation nouveaux sites

## Durée totale estimée
**18-27 jours** selon complexité et ressources

---
*Dernière mise à jour : 2025-09-17*
