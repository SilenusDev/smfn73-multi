# TODO - Tâches à réaliser

> **Style** : Document sobre et épuré pour la gestion de projet

## Phase 1 - Configuration de base
- [ ] Sauvegarde du projet actuel
- [ ] Configuration domaines locaux (silenus.local, insidiome.local)
- [ ] Restructuration des dossiers selon l'architecture multisite

## Phase 2 - Système de détection
- [ ] Service SiteResolver
- [ ] EventSubscriber SiteInitializer

## Phase 3 - Bases de données
- [ ] Création bases multisite_silenus et multisite_insidiome
- [ ] Configuration Doctrine multiple
- [ ] Service DatabaseManager

## Phase 4 - Entités blog
- [ ] Entités communes (BlogPost, BlogCategory, BlogComment, User)
- [ ] Traits réutilisables
- [ ] Migrations par site

## Phase 5 - Système Mailer
- [ ] Configuration OAuth2 par site
- [ ] Service MailerManager contextuel
- [ ] Templates email par site

## Phase 6 - Authentification
- [ ] Configuration Security contextuelle
- [ ] UserProvider personnalisé
- [ ] Interface admin commune

## Phase 7 - Templates et SCSS
- [ ] Organisation Twig hiérarchique
- [ ] Service TemplateResolver
- [ ] Webpack Encore multisite

## Phase 8 - Controllers et Routing
- [ ] BaseController contextuel
- [ ] Controllers Blog réutilisables
- [ ] Routing conditionnel

## Phase 9 - Tests et Optimisation
- [ ] Tests automatisés
- [ ] Sécurité avancée
- [ ] Cache et performance

## Phase 10 - Déploiement
- [ ] Configuration production
- [ ] Scripts de déploiement
- [ ] Documentation ajout nouveaux sites

---
*Dernière mise à jour : 2025-09-17*
