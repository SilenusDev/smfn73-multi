# TODO - Tâches à faire

> **Style** : Document sobre et épuré pour la gestion de projet
> **Dernière mise à jour** : 2025-10-07 12:53

## ✅ Phase 1 - Infrastructure (TERMINÉ)
- [x] Infrastructure Docker opérationnelle
- [x] Services web, db, node, phpmyadmin
- [x] Configuration .env avec variables

## ✅ Phase 2 - Architecture Multisite (TERMINÉ)
- [x] Service SiteResolver créé
- [x] EventSubscriber SiteSubscriber créé
- [x] Service DatabaseManager créé
- [x] Service SiteManager créé

## ✅ Phase 3 - Bases de données (TERMINÉ)
- [x] Création bases slns_db et nsdm_db
- [x] Configuration Doctrine avec 2 Entity Managers
- [x] Migrations générées et exécutées
- [x] Entités User et Site créées

## ✅ Phase 4 - Controllers et Routes (TERMINÉ)
- [x] Controllers Silenus (/slns/)
- [x] Controllers Insidiome (/ndsm/)
- [x] HomeController pour les 2 sites
- [x] SecurityController pour les 2 sites
- [x] RegistrationController pour les 2 sites

## ✅ Phase 5 - Templates (TERMINÉ)
- [x] Templates Silenus (thème violet)
- [x] Templates Insidiome (thème rose)
- [x] Templates home, security, registration
- [x] Navigation contextuelle
- [x] Design responsive

## 🔄 Phase 6 - Authentification (EN COURS)
- [ ] Configuration Security.yaml avec firewalls par site
- [ ] UserProvider contextuel
- [ ] Tests de connexion/inscription
- [ ] Gestion des rôles par site

## ⏳ Phase 7 - Entités Métier (À FAIRE)
- [ ] Entité BlogPost
- [ ] Entité Category
- [ ] Entité Comment
- [ ] Entité Page
- [ ] Migrations pour les nouvelles entités

## ⏳ Phase 8 - Système Mailer (À FAIRE)
- [ ] Configuration Mailer par site
- [ ] Service MailerManager contextuel
- [ ] Templates email par site
- [ ] Configuration OAuth2 (optionnel)

## ⏳ Phase 9 - Assets et Frontend (À FAIRE)
- [ ] Structure assets par site (common, silenus, insidiome)
- [ ] Configuration Webpack Encore multisite
- [ ] SCSS par site
- [ ] JavaScript par site

## ⏳ Phase 10 - Administration (À FAIRE)
- [ ] Interface admin commune ou séparée
- [ ] CRUD pour les entités
- [ ] Gestion des utilisateurs
- [ ] Dashboard par site

## ⏳ Phase 11 - Tests et Optimisation (À FAIRE)
- [ ] Tests unitaires pour les services
- [ ] Tests fonctionnels pour les controllers
- [ ] Tests d'isolation des bases de données
- [ ] Optimisation des requêtes
- [ ] Configuration du cache

## ⏳ Phase 12 - Déploiement (À FAIRE)
- [ ] Configuration production
- [ ] Scripts de déploiement
- [ ] Documentation ajout nouveaux sites
- [ ] Configuration domaines production
- [ ] SSL/HTTPS

---
*Dernière mise à jour : 2025-10-07 12:53*
