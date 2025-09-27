# PROJET - Multisite Symfony 7.3

> **Style** : Document sobre et épuré pour la gestion de projet

## Vue d'ensemble

**Objectif** : Architecture multisite Symfony avec 2 sites indépendants  
**Framework** : Symfony 7.3  
**Sites** : silenus + insidiome  

## Architecture technique

### Sites
- **silenus** : silenus.local (dev) → domaine production
- **insidiome** : insidiome.local (dev) → domaine production

### Bases de données
- `silenus_db` : données site silenus
- `insidiome_db` : données site insidiome
- `dagda_db` : données site dagda
- Isolation complète des données

### Structure projet
```
/src/
├── /Entity/Common/     # Entités réutilisables
├── /Entity/Silenus/    # Entités spécifiques silenus
├── /Entity/Insidiome/  # Entités spécifiques insidiome
└── /Service/           # Services communs

/templates/
├── /common/            # Templates de base
├── /silenus/           # Templates silenus
└── /insidiome/         # Templates insidiome
```

## Fonctionnalités communes
- Blog (posts, catégories, commentaires)
- Authentification par site
- Mailer OAuth2 par site
- Interface admin commune

## Avantages architecture
- Isolation complète des données
- Code réutilisable entre sites  
- Sécurité maximale
- Facilité d'ajout de nouveaux sites

---
*Dernière mise à jour : 2025-09-17*
