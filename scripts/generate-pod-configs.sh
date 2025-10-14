#!/bin/bash
# scripts/generate-pod-configs.sh
# Génère les fichiers pod.yml à partir des templates et des variables d'environnement
# Garantit la portabilité du projet entre environnements

SCRIPT_NAME="generate-pod-configs"

# =============================================================================
# DÉTECTION DU RÉPERTOIRE RACINE
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

echo "[$SCRIPT_NAME][info] Répertoire du projet: $PROJECT_ROOT"

# =============================================================================
# CHARGEMENT DES VARIABLES D'ENVIRONNEMENT
# =============================================================================

ENV_FILE="$PROJECT_ROOT/.env.podman"

if [ ! -f "$ENV_FILE" ]; then
    echo "[$SCRIPT_NAME][error] Fichier .env.podman non trouvé"
    echo "[$SCRIPT_NAME][info] Créez-le à partir de .env.podman.example:"
    echo "[$SCRIPT_NAME][info]   cp .env.podman.example .env.podman"
    echo "[$SCRIPT_NAME][info]   # Puis éditez .env.podman avec vos valeurs"
    exit 1
fi

# Charger les variables et les exporter
set -a
source "$ENV_FILE"
set +a

# Exporter explicitement toutes les variables pour envsubst
export PROJECT_ROOT PROJECT_NAME HOST TZ
export APACHE_PORT MARIADB_PORT REDIS_PORT NODE_PORT PHPMYADMIN_PORT PHP_PORT
export DB_USER DB_PASSWORD DB_ROOT_PASSWORD DB_SLNS_NAME DB_NSDM_NAME
export APP_ENV APP_SECRET
export DATABASE_SLNS_URL DATABASE_NSDM_URL REDIS_URL
export IMAGE_PHP IMAGE_APACHE IMAGE_MARIADB IMAGE_REDIS IMAGE_NODE IMAGE_COMPOSER IMAGE_PHPMYADMIN

echo "[$SCRIPT_NAME][success] Variables d'environnement chargées et exportées"

# =============================================================================
# VALIDATION DES VARIABLES REQUISES
# =============================================================================

REQUIRED_VARS=(
    "PROJECT_ROOT"
    "PROJECT_NAME"
    "HOST"
    "APACHE_PORT"
    "MARIADB_PORT"
    "REDIS_PORT"
    "NODE_PORT"
    "DB_USER"
    "DB_PASSWORD"
)

MISSING_VARS=()
for var in "${REQUIRED_VARS[@]}"; do
    if [ -z "${!var}" ]; then
        MISSING_VARS+=("$var")
    fi
done

if [ ${#MISSING_VARS[@]} -gt 0 ]; then
    echo "[$SCRIPT_NAME][error] Variables manquantes dans .env.podman:"
    for var in "${MISSING_VARS[@]}"; do
        echo "[$SCRIPT_NAME][error]   - $var"
    done
    exit 1
fi

# =============================================================================
# FONCTION DE GÉNÉRATION
# =============================================================================

generate_pod_config() {
    local service=$1
    local template_file="$PROJECT_ROOT/pods/$service/pod.yml.template"
    local output_file="$PROJECT_ROOT/pods/$service/pod.yml"
    
    if [ ! -f "$template_file" ]; then
        echo "[$SCRIPT_NAME][warning] Template non trouvé: $template_file"
        return 1
    fi
    
    echo "[$SCRIPT_NAME][info] Génération de $service/pod.yml..."
    
    # Remplacer les variables dans le template
    envsubst < "$template_file" > "$output_file"
    
    if [ $? -eq 0 ]; then
        echo "[$SCRIPT_NAME][success] ✅ $service/pod.yml généré"
        return 0
    else
        echo "[$SCRIPT_NAME][error] ❌ Échec de la génération de $service/pod.yml"
        return 1
    fi
}

# =============================================================================
# GÉNÉRATION DE TOUS LES PODS
# =============================================================================

echo "[$SCRIPT_NAME][step] =========================================="
echo "[$SCRIPT_NAME][step] GÉNÉRATION DES CONFIGURATIONS PODMAN"
echo "[$SCRIPT_NAME][step] =========================================="
echo

SERVICES=(
    "mariadb"
    "redis"
    "web"
    "node"
    "phpmyadmin"
)

SUCCESS_COUNT=0
FAIL_COUNT=0

for service in "${SERVICES[@]}"; do
    if generate_pod_config "$service"; then
        ((SUCCESS_COUNT++))
    else
        ((FAIL_COUNT++))
    fi
done

echo
echo "[$SCRIPT_NAME][step] =========================================="
echo "[$SCRIPT_NAME][step] RÉSUMÉ"
echo "[$SCRIPT_NAME][step] =========================================="
echo "[$SCRIPT_NAME][info] ✅ Succès: $SUCCESS_COUNT"
echo "[$SCRIPT_NAME][info] ❌ Échecs: $FAIL_COUNT"
echo

if [ $FAIL_COUNT -eq 0 ]; then
    echo "[$SCRIPT_NAME][success] Toutes les configurations ont été générées avec succès"
    echo "[$SCRIPT_NAME][info] Vous pouvez maintenant démarrer les pods avec:"
    echo "[$SCRIPT_NAME][info]   ./scripts/symfony-orchestrator.sh dev"
    exit 0
else
    echo "[$SCRIPT_NAME][error] Certaines configurations n'ont pas pu être générées"
    exit 1
fi
