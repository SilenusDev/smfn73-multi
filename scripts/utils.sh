#!/bin/bash
# scripts/utils.sh
# Fonctions utilitaires pour la gestion des pods Symfony Multi-sites
# Inspiré de dagda-lite/dagda/awens-utils/ollamh.sh

SCRIPT_NAME="utils"

# =============================================================================
# DÉTECTION AUTOMATIQUE DU RÉPERTOIRE RACINE
# =============================================================================

find_project_root() {
    local start_dir="${1:-$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)}"
    local current_dir="$(cd "$start_dir" && pwd)"
    local max_depth=10
    local depth=0
    
    while [ $depth -lt $max_depth ]; do
        # Chercher les marqueurs du projet Symfony
        if [ -f "$current_dir/composer.json" ] && \
           [ -d "$current_dir/pods" ] && \
           [ -d "$current_dir/scripts" ]; then
            echo "$current_dir"
            return 0
        fi
        
        # Remonter d'un niveau
        local parent_dir="$(dirname "$current_dir")"
        if [ "$parent_dir" = "$current_dir" ]; then
            break
        fi
        current_dir="$parent_dir"
        depth=$((depth + 1))
    done
    
    return 1
}

# =============================================================================
# INITIALISATION DES CHEMINS GLOBAUX
# =============================================================================

# Détection automatique du répertoire racine si SYMFONY_ROOT n'est pas défini
if [ -z "$SYMFONY_ROOT" ]; then
    PROJECT_DIR=$(find_project_root)
    if [ $? -eq 0 ]; then
        export SYMFONY_ROOT="$PROJECT_DIR"
        echo "[$SCRIPT_NAME][info] SYMFONY_ROOT détecté automatiquement: $SYMFONY_ROOT"
    else
        echo "[$SCRIPT_NAME][error] Impossible de détecter le répertoire racine" >&2
        echo "[$SCRIPT_NAME][error] Définissez SYMFONY_ROOT manuellement" >&2
        return 1 2>/dev/null || exit 1
    fi
else
    PROJECT_DIR="$SYMFONY_ROOT"
fi

# Vérifier que le répertoire racine existe
if [ ! -d "$PROJECT_DIR" ]; then
    echo "[$SCRIPT_NAME][error] Répertoire racine non trouvé: $PROJECT_DIR" >&2
    return 1 2>/dev/null || exit 1
fi

# =============================================================================
# DÉFINITION DES CHEMINS PRINCIPAUX
# =============================================================================

if [ -n "$PROJECT_DIR" ]; then
    # Chemins principaux
    PODS_DIR="$PROJECT_DIR/pods"
    SCRIPTS_DIR="$PROJECT_DIR/scripts"
    DOCS_DIR="$PROJECT_DIR/docs"
    
    # Export des variables
    export PROJECT_DIR PODS_DIR SCRIPTS_DIR DOCS_DIR
fi

# =============================================================================
# GESTION ENVIRONNEMENT
# =============================================================================

load_env() {
    local env_file="$PROJECT_DIR/.env.podman"
    
    if [ -f "$env_file" ]; then
        set -a
        source "$env_file"
        set +a
        echo "[$SCRIPT_NAME][debug] Variables d'environnement chargées depuis $env_file"
        return 0
    else
        echo "[$SCRIPT_NAME][warning] Fichier .env.podman non trouvé: $env_file"
        echo "[$SCRIPT_NAME][info] Utilisation des valeurs par défaut"
        return 1
    fi
}

validate_env_vars() {
    local required_vars=(
        "PROJECT_NAME"
        "HOST"
        "APACHE_PORT"
        "PHP_PORT"
        "MARIADB_PORT"
        "REDIS_PORT"
        "NODE_PORT"
        "DB_USER"
        "DB_PASSWORD"
    )
    
    local missing_vars=()
    
    for var in "${required_vars[@]}"; do
        if [ -z "${!var}" ]; then
            missing_vars+=("$var")
        fi
    done
    
    if [ ${#missing_vars[@]} -gt 0 ]; then
        echo "[$SCRIPT_NAME][error] Variables d'environnement manquantes:"
        for var in "${missing_vars[@]}"; do
            echo "[$SCRIPT_NAME][error]   - $var"
        done
        echo "[$SCRIPT_NAME][error] Vérifiez votre fichier .env.podman"
        return 1
    fi
    
    echo "[$SCRIPT_NAME][debug] Toutes les variables d'environnement requises sont présentes"
    return 0
}

# =============================================================================
# FONCTIONS VÉRIFICATION RÉSEAU
# =============================================================================

check_port() {
    local port=$1
    local host=${2:-${HOST:-localhost}}
    local timeout=${3:-5}
    
    echo "[$SCRIPT_NAME][debug] Vérification du port $port sur $host (timeout: ${timeout}s)"
    
    if timeout "$timeout" bash -c "echo >/dev/tcp/$host/$port" 2>/dev/null; then
        return 0
    else
        return 1
    fi
}

check_http_health() {
    local url=$1
    local timeout=${2:-10}
    local expected_status=${3:-200}
    
    echo "[$SCRIPT_NAME][debug] Vérification HTTP: $url (timeout: ${timeout}s)"
    
    if ! command -v curl &> /dev/null; then
        echo "[$SCRIPT_NAME][warning] curl non installé, impossible de vérifier HTTP"
        return 1
    fi
    
    local status_code
    status_code=$(curl -s -o /dev/null -w "%{http_code}" --max-time "$timeout" "$url" 2>/dev/null || echo "000")
    
    if [ "$status_code" = "$expected_status" ]; then
        return 0
    else
        echo "[$SCRIPT_NAME][debug] Status code reçu: $status_code (attendu: $expected_status)"
        return 1
    fi
}

# =============================================================================
# FONCTIONS PODMAN
# =============================================================================

check_podman() {
    if ! command -v podman &> /dev/null; then
        return 1
    fi
    
    if podman --version &> /dev/null; then
        return 0
    else
        return 1
    fi
}

check_pod_exists() {
    local pod_name=$1
    podman pod exists "$pod_name" 2>/dev/null
}

check_container_exists() {
    local container_name=$1
    podman container exists "$container_name" 2>/dev/null
}

check_container_running() {
    local container_name=$1
    [ "$(podman ps -q -f name="$container_name")" != "" ]
}

get_pod_status() {
    local pod_name=$1
    if ! check_pod_exists "$pod_name"; then
        echo "NotFound"
        return 1
    fi
    
    local status=$(podman pod ps --filter name="$pod_name" --format "{{.Status}}" 2>/dev/null | head -n1)
    echo "${status:-Unknown}"
}

get_pod_containers() {
    local pod_name=$1
    if ! check_pod_exists "$pod_name"; then
        echo "0"
        return 1
    fi
    
    podman pod ps --filter name="$pod_name" --format "{{.NumberOfContainers}}" 2>/dev/null | head -n1 || echo "0"
}

stop_container() {
    local container_name=$1
    local timeout=${2:-10}
    
    if check_container_exists "$container_name"; then
        if check_container_running "$container_name"; then
            echo "[$SCRIPT_NAME][info] Arrêt du conteneur $container_name..."
            podman stop --time "$timeout" "$container_name" || true
        fi
        echo "[$SCRIPT_NAME][info] Suppression du conteneur $container_name..."
        podman rm "$container_name" || true
    fi
}

stop_pod() {
    local pod_name=$1
    local timeout=${2:-10}
    
    if check_pod_exists "$pod_name"; then
        echo "[$SCRIPT_NAME][info] Arrêt du pod $pod_name..."
        podman pod stop --time "$timeout" "$pod_name" || true
        echo "[$SCRIPT_NAME][info] Suppression du pod $pod_name..."
        podman pod rm "$pod_name" || true
    fi
}

# =============================================================================
# FONCTIONS ATTENTE
# =============================================================================

wait_for_service() {
    local service_name=$1
    local port=$2
    local host=${3:-${HOST:-localhost}}
    local max_attempts=${4:-30}
    local sleep_interval=${5:-2}
    
    echo "[$SCRIPT_NAME][info] Attente de $service_name sur $host:$port..."
    
    local attempt=1
    while [ $attempt -le $max_attempts ]; do
        if check_port "$port" "$host" 2; then
            echo "[$SCRIPT_NAME][success] $service_name est disponible !"
            return 0
        fi
        
        echo "[$SCRIPT_NAME][debug] Tentative $attempt/$max_attempts - $service_name non disponible, attente ${sleep_interval}s..."
        sleep "$sleep_interval"
        ((attempt++))
    done
    
    echo "[$SCRIPT_NAME][error] $service_name n'est pas disponible après $((max_attempts * sleep_interval))s"
    return 1
}

wait_for_http_service() {
    local service_name=$1
    local url=$2
    local max_attempts=${3:-30}
    local sleep_interval=${4:-2}
    local expected_status=${5:-200}
    
    echo "[$SCRIPT_NAME][info] Attente de $service_name sur $url..."
    
    local attempt=1
    while [ $attempt -le $max_attempts ]; do
        if check_http_health "$url" 5 "$expected_status"; then
            echo "[$SCRIPT_NAME][success] $service_name est disponible !"
            return 0
        fi
        
        echo "[$SCRIPT_NAME][debug] Tentative $attempt/$max_attempts - $service_name non disponible, attente ${sleep_interval}s..."
        sleep "$sleep_interval"
        ((attempt++))
    done
    
    echo "[$SCRIPT_NAME][error] $service_name n'est pas disponible après $((max_attempts * sleep_interval))s"
    return 1
}

kill_processes_on_port() {
    local port=$1
    local service_name=${2:-"Service"}
    
    if ! command -v lsof &> /dev/null; then
        echo "[$SCRIPT_NAME][warning] lsof non installé, impossible de nettoyer le port $port"
        return 1
    fi
    
    local pids
    pids=$(lsof -ti:$port 2>/dev/null || true)
    
    if [ -n "$pids" ]; then
        echo "[$SCRIPT_NAME][warning] Processus trouvés sur le port $port pour $service_name"
        echo "$pids" | xargs -r kill -9 2>/dev/null || true
        echo "[$SCRIPT_NAME][info] Processus nettoyés sur le port $port"
    fi
}

# =============================================================================
# FONCTIONS SYMFONY MULTI
# =============================================================================

check_prerequisites() {
    local service_name=${1:-"Service"}
    
    echo "[$SCRIPT_NAME][step] Vérification des prérequis pour $service_name..."
    
    if ! load_env; then
        echo "[$SCRIPT_NAME][warning] Impossible de charger les variables d'environnement"
    fi
    
    if ! check_podman; then
        echo "[$SCRIPT_NAME][error] Podman requis pour $service_name"
        return 1
    fi
    
    echo "[$SCRIPT_NAME][success] Prérequis OK pour $service_name"
    return 0
}

get_pod_name() {
    local service=$1
    echo "symfony-${service}-pod"
}

get_container_name() {
    local service=$1
    echo "symfony-${service}"
}

ensure_directories() {
    local service=$1
    local service_dir="$PODS_DIR/$service"
    
    mkdir -p "$service_dir/data"
    mkdir -p "$service_dir/config"
    mkdir -p "$service_dir/init"
    mkdir -p "$service_dir/logs"
}

show_service_info() {
    local service_name=$1
    local port=$2
    local container_name=$3
    
    echo
    echo "[$SCRIPT_NAME][step] Informations pour $service_name:"
    echo "[$SCRIPT_NAME][info]   • URL: http://${HOST:-localhost}:${port}"
    echo "[$SCRIPT_NAME][info]   • Logs: podman logs $container_name"
    echo "[$SCRIPT_NAME][info]   • Shell: podman exec -it $container_name sh"
    echo
}

ensure_network() {
    local network_name="${PROJECT_NAME:-symfony-multi}-network"
    
    if ! podman network exists "$network_name" 2>/dev/null; then
        echo "[$SCRIPT_NAME][info] Création du réseau $network_name..."
        if podman network create "$network_name"; then
            echo "[$SCRIPT_NAME][success] Réseau $network_name créé"
        else
            echo "[$SCRIPT_NAME][error] Impossible de créer le réseau $network_name"
            return 1
        fi
    fi
    
    echo "$network_name"
}

show_container_logs() {
    local pod_name=$1
    local lines=${2:-20}
    
    local containers=$(podman ps -q --filter pod="$pod_name" 2>/dev/null || true)
    if [ -n "$containers" ]; then
        for container in $containers; do
            local container_name=$(podman inspect --format '{{.Name}}' "$container" 2>/dev/null || echo "inconnu")
            echo "[$SCRIPT_NAME][info] Logs de $container_name (${lines} dernières lignes):"
            podman logs --tail "$lines" "$container" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Logs non disponibles"
            echo
        done
    else
        echo "[$SCRIPT_NAME][warning] Aucun conteneur trouvé dans le pod $pod_name"
    fi
}

# Charger les variables d'environnement au démarrage
load_env 2>/dev/null || true
