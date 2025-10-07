# DONE - Tâches accomplies

> **Style** : Document sobre et épuré pour la gestion de projet

## 2025-09-17

### Configuration initiale
- [x] Analyse du fichier project.hml existant
- [x] Correction des noms de sites : site1 → silenus, site2 → insidiome
- [x] Création de la structure de documentation /docs
- [x] Mise en place du système de suivi des tâches (todo.md, done.md)

### Documentation
- [x] Création du fichier todo.md avec roadmap complète
- [x] Création du fichier done.md pour historique des tâches
- [x] Identification des 18 occurrences site1/site2 dans project.hml

### Ajout du troisième site dagda
- [x] Modification de project.hml pour intégrer dagda dans toutes les sections
- [x] Mise à jour architecture.md avec configuration Doctrine, Security et Webpack pour dagda
- [x] Mise à jour projet.md pour passer de 2 à 3 sites
- [x] Mise à jour todo.md avec tâches spécifiques à dagda
- [x] Architecture multisite étendue : silenus + insidiome + dagda

### Correction des noms de bases de données
- [x] Modification project.hml : multisite_* → *_db (plus explicite)
- [x] Mise à jour architecture.md avec nouveaux noms de bases
- [x] Mise à jour projet.md avec nouveaux noms de bases
- [x] Noms finaux : silenus_db, insidiome_db, dagda_db

### Création de la structure complète du projet
- [x] Création structure /src (Entity/Common, Entity/Silenus, Entity/Insidiome, Entity/Dagda, Controller/Common, Service)
- [x] Création structure /templates (common, silenus, insidiome, dagda)
- [x] Création structure /assets (common, silenus, insidiome, dagda)
- [x] Création structure /config/sites
- [x] Mise à jour architecture.md avec schéma complet du projet
- [x] Correction sections Security et Webpack pour inclure dagda
- [x] Architecture multisite complètement opérationnelle

---

## 2025-10-07

### Infrastructure Docker (TERMINÉ)
- [x] Migration de Podman vers Docker (problèmes de permissions résolus)
- [x] Configuration docker-compose.yml opérationnelle
- [x] Services démarrés: web (PHP 8.3 + Apache), db (MariaDB 10.11), node (Yarn), phpmyadmin
- [x] Ports configurés: 8080 (web), 3306 (db), 8081 (phpmyadmin), 5173 (node)
- [x] Volumes persistants pour MariaDB
- [x] Script init.sql pour création des 2 bases (slns_db, nsdm_db)

### Architecture Multisite (TERMINÉ)
- [x] Configuration Doctrine avec 2 connexions DBAL (silenus, insidiome)
- [x] Configuration de 2 Entity Managers opérationnels
- [x] Service SiteResolver créé - Détection automatique du site selon le domaine
- [x] Service DatabaseManager créé - Sélection automatique de la bonne base
- [x] Service SiteManager créé - Gestion des entités Site locales
- [x] EventSubscriber SiteSubscriber créé - Initialisation du site à chaque requête

### Entités et Base de Données (TERMINÉ)
- [x] Entité User créée avec relation ManyToOne vers Site
- [x] Entité Site créée (isolation par base maintenue)
- [x] Décision architecturale: Garder Site avec 1 entrée par base (id=1)
- [x] Migrations générées pour les 2 bases (Version20251007101049, Version20251007101150)
- [x] Migrations exécutées avec succès
- [x] Tables créées: site, user, messenger_messages, doctrine_migration_versions
- [x] Commande app:init-sites créée et exécutée
- [x] Entités Site initialisées dans slns_db (id=1, name='silenus') et nsdm_db (id=1, name='insidiome')

### Configuration Messenger (TERMINÉ)
- [x] Variable MESSENGER_TRANSPORT_DSN ajoutée au .env
- [x] Configuration messenger.yaml corrigée (doctrine://silenus)
- [x] Tables messenger_messages créées dans les 2 bases

### Traits et Code Réutilisable (NETTOYÉ)
- [x] Résolution conflit UrlTrait/SeoTrait (UrlTrait supprimé)
- [x] SeoTrait conservé avec slugFR/EN, descrFR/EN, urlFR/EN, tagsFR/EN, img
- [x] Traits disponibles: TimeTrait, TitleTrait, ContentTrait, SeoTrait, StatusTrait, etc.

### Assets et Frontend (PARTIELLEMENT FAIT)
- [x] Webpack Encore configuré
- [x] Yarn installé et fonctionnel
- [x] Node conteneur opérationnel avec watch
- [x] Build des assets réussi
- [ ] Structure assets par site à créer (common, silenus, insidiome)

### Nettoyage et Corrections
- [x] Suppression entités BlogPost et Category (temporaire)
- [x] Nettoyage des tables obsolètes dans les bases
- [x] Suppression des anciennes migrations
- [x] Régénération des migrations propres
- [x] Base de données propre et cohérente

### Documentation
- [x] Mise à jour done.md avec l'état actuel du projet
- [x] Allègement et épuration de projet.md (référence technique uniquement)

### Controllers par Site (TERMINÉ)
- [x] Structure /src/Controller/Slns/ créée
- [x] Structure /src/Controller/Ndsm/ créée
- [x] HomeController pour Silenus (/slns/, /slns/about)
- [x] HomeController pour Insidiome (/ndsm/, /ndsm/about)
- [x] SecurityController pour Silenus (/slns/login, /slns/logout)
- [x] SecurityController pour Insidiome (/ndsm/login, /ndsm/logout)
- [x] RegistrationController pour Silenus (/slns/register)
- [x] RegistrationController pour Insidiome (/ndsm/register)
- [x] Vérifications de site dans chaque controller
- [x] Suppression des anciens controllers génériques

### Templates par Site (TERMINÉ)
- [x] Structure /templates/silenus/front/ créée
- [x] Structure /templates/insidiome/front/ créée
- [x] Template base.html.twig pour Silenus (thème violet)
- [x] Template base.html.twig pour Insidiome (thème rose)
- [x] Templates home (index, about) pour les 2 sites
- [x] Templates security (login) pour les 2 sites
- [x] Templates registration (register) pour les 2 sites
- [x] Navigation avec liens contextuels
- [x] Affichage des messages flash
- [x] Design responsive et moderne

### Authentification (TERMINÉ)
- [x] UserProvider contextuel créé (utilise DatabaseManager)
- [x] Configuration Security.yaml avec 2 firewalls (slns, ndsm)
- [x] Routes de login/logout séparées par site
- [x] Remember me activé pour les 2 sites
- [x] CSRF protection activée
- [x] Fichier .htaccess créé pour Apache rewrite
- [x] Correction SiteResolver (détection par path ET domaine)
- [x] Tests fonctionnels: /slns/ et /ndsm/ accessibles

---

## État Actuel du Projet

### ✅ Fonctionnel
- Infrastructure Docker complète
- Architecture multisite opérationnelle
- Détection automatique du site (par path et domaine)
- Isolation des bases de données
- Migrations et entités de base (User, Site)
- Commandes d'initialisation
- Controllers séparés par site (Slns, Ndsm)
- Templates personnalisés par site
- **Authentification complète et fonctionnelle**
- **Inscription/Connexion/Déconnexion opérationnelles**

### ⏳ En Attente
- Tests de l'inscription/connexion avec création de users
- Création des entités métier (BlogPost, Category, Comment, etc.)
- Tests unitaires et fonctionnels
- Configuration Mailer (optionnel)
- Assets Webpack par site (amélioration design)
- Interface d'administration

---
*Dernière mise à jour : 2025-10-07 13:22*
