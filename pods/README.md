# 📦 Configurations Podman - Système de Templates

## ⚠️ IMPORTANT : Ne modifiez PAS les fichiers `pod.yml` directement !

Les fichiers `pod.yml` sont **générés automatiquement** à partir des templates `.pod.yml.template`.

## 📁 Structure

Chaque service a deux fichiers :

```
service/
├── pod.yml.template    # ✅ Template (versionné, à modifier)
└── pod.yml             # ❌ Généré automatiquement (ne pas modifier)
```

## 🔧 Modification d'une configuration

### ❌ Mauvaise méthode
```bash
# NE FAITES PAS ÇA !
nano pods/mariadb/pod.yml  # ❌ Sera écrasé à la prochaine génération
```

### ✅ Bonne méthode
```bash
# 1. Modifier le template
nano pods/mariadb/pod.yml.template

# 2. Régénérer les configurations
./scripts/generate-pod-configs.sh

# 3. Redémarrer le service
./scripts/symfony-orchestrator.sh mariadb
```

## 🔄 Génération des configurations

### Automatique
Les configurations sont générées automatiquement lors de :
- L'installation : `./scripts/install-podman.sh`
- Le démarrage : `./scripts/symfony-orchestrator.sh dev`

### Manuelle
```bash
./scripts/generate-pod-configs.sh
```

## 📝 Variables disponibles dans les templates

Toutes les variables définies dans `.env.podman` sont disponibles :

```yaml
# Exemple dans pod.yml.template
metadata:
  name: ${PROJECT_NAME}-mariadb-pod  # Remplacé par "symfony-multi-mariadb-pod"

volumes:
  - name: data
    hostPath:
      path: ${PROJECT_ROOT}/pods/mariadb/data  # Remplacé par le chemin absolu
```

## 🎯 Services disponibles

| Service | Template | Description |
|---------|----------|-------------|
| `mariadb` | `mariadb/pod.yml.template` | Base de données MariaDB 10.11 |
| `redis` | `redis/pod.yml.template` | Cache Redis |
| `web` | `web/pod.yml.template` | Apache + PHP-FPM + Composer |
| `node` | `node/pod.yml.template` | Node.js 20 (build assets) |
| `phpmyadmin` | `phpmyadmin/pod.yml.template` | Interface PHPMyAdmin |

## 📚 Documentation

- [Portabilité du projet](../docs/PORTABILITY.md)
- [Installation Podman](../docs/INSTALLATION_PODMAN.md)
- [Utilisation Podman](../PODMAN_USAGE.md)
