# Scripts d'orchestration Podman - Symfony Multi-sites

Scripts inspirés de l'architecture **dagda-lite** pour la gestion des pods Podman.

## 📁 Structure

```
scripts/
├── utils.sh                    # Fonctions utilitaires (comme ollamh.sh)
├── pod-engine.sh               # Moteur de gestion pods (comme teine_engine.sh)
├── symfony-orchestrator.sh     # Orchestrateur principal (comme taranis.sh)
│
├── install-podman.sh           # Installation complète automatisée
├── check-podman.sh             # Vérification de l'installation
├── build-php-image.sh          # Build de l'image PHP personnalisée
├── setup-hosts.sh              # Configuration /etc/hosts multi-sites
├── clean-podman.sh             # Nettoyage complet de l'environnement
├── fix-assets-podman.sh        # Correction des problèmes d'assets
│
└── README.md                   # Cette documentation
```

## 📚 Documentation

Pour l'utilisation des scripts d'installation et de maintenance, consultez :
- **Installation** : `../docs/INSTALLATION_PODMAN.md`
- **Usage Podman** : `../PODMAN_USAGE.md`
- **Configuration** : `../HOSTS_CONFIGURATION.md`

## 🔧 Scripts d'orchestration (dagda-lite)

### utils.sh - Fonctions utilitaires

**Ne pas exécuter directement**, ce script est sourcé par les autres.

**Fonctions principales** :
- `check_port()` - Vérifie si un port est accessible
- `wait_for_service()` - Attend qu'un service soit disponible
- `ensure_network()` - Crée le réseau Podman si nécessaire
- `kill_processes_on_port()` - Nettoie les processus sur un port
- `check_pod_exists()` - Vérifie l'existence d'un pod
- `get_pod_status()` - Récupère le statut d'un pod
- `load_env()` - Charge les variables .env.podman

### pod-engine.sh - Moteur de gestion des pods

Gestion individuelle des pods (équivalent de `teine_engine.sh`).

**Syntaxe** :
```bash
./scripts/pod-engine.sh <action> <service_path> [options]
```

**Actions** : `start`, `stop`, `status`, `health`, `clean`, `logs`

### symfony-orchestrator.sh - Orchestrateur principal

Orchestration globale des services (équivalent de `taranis.sh`).

**Commandes principales** :
- `start` / `symfony` - Démarrer les services essentiels
- `dev` - Mode développement (avec npm watch)
- `stop <service>` - Arrêter un ou plusieurs services
- `status <service>` - Consulter le statut
- `clean <service>` - Nettoyage complet
- `build` - Build des assets en production

**Services disponibles** :
- `apache`, `php`, `mariadb`, `redis`, `node`
- `qdrant`, `fastapi`, `phpmyadmin`, `composer`

## 🔗 Références

- Architecture dagda-lite : `/home/sam/Bureau/dev/production/dagda-lite`
- Scripts source :
  - `dagda-lite/dagda/awens-utils/ollamh.sh` → `utils.sh`
  - `dagda-lite/dagda/awen-core/teine_engine.sh` → `pod-engine.sh`
  - `dagda-lite/dagda/eveil/taranis.sh` → `symfony-orchestrator.sh`
