#!/bin/bash
# scripts/pod-engine.sh
# Moteur de gestion des pods Symfony Multi-sites
# Inspiré de dagda-lite/dagda/awen-core/teine_engine.sh

SCRIPT_NAME="pod-engine"

# Configuration des chemins
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Chargement des variables d'environnement
if [ -z "$SYMFONY_ROOT" ]; then
    echo "[$SCRIPT_NAME][error] Variable SYMFONY_ROOT non définie" >&2
    exit 1
fi

if [ ! -f "${SYMFONY_ROOT}/.env.podman" ]; then
    echo "[$SCRIPT_NAME][warning] Fichier .env.podman non trouvé dans ${SYMFONY_ROOT}" >&2
fi

# Charger les utilitaires
source "${SCRIPT_DIR}/utils.sh" || { echo "[$SCRIPT_NAME][error] utils.sh non trouvé"; exit 1; }

# =============================================================================
# FONCTIONS UTILITAIRES
# =============================================================================

# Lire la configuration d'un service depuis son pod.yml
read_service_config() {
    local service_dir=$1
    local config_file="$service_dir/pod.yml"
    
    if [ ! -f "$config_file" ]; then
        echo "[$SCRIPT_NAME][error] Fichier de configuration non trouvé: $config_file"
        return 1
    fi
    
    echo "$config_file"
}

# Afficher un résumé d'opération
show_summary() {
    local service_name=$1
    local port=$2
    local operation=$3
    local message=$4
    
    echo
    echo "[$SCRIPT_NAME][step] Résumé de l'opération '$operation' pour $service_name:"
    if [ -n "$port" ]; then
        echo "[$SCRIPT_NAME][info]   Service: $service_name (port $port)"
    else
        echo "[$SCRIPT_NAME][info]   Service: $service_name"
    fi
    echo "[$SCRIPT_NAME][info]   Résultat: $message"
    echo
}

# =============================================================================
# ACTIONS PRINCIPALES DU MOTEUR
# =============================================================================

# Démarrage d'un service (start)
pod_start() {
    local service_dir="$1"
    local service_name="$(basename "$service_dir")"
    local pod_name=$(get_pod_name "$service_name")
    
    echo "[$SCRIPT_NAME][step] Démarrage du service $service_name..."
    
    # Charger la configuration
    local config_file=$(read_service_config "$service_dir")
    if [ $? -ne 0 ]; then
        return 1
    fi
    
    # Vérifier les prérequis
    if ! check_prerequisites "$service_name"; then
        return 1
    fi
    
    # Déterminer le port depuis les variables d'environnement
    local port_var=$(echo "${service_name}_PORT" | tr '[:lower:]' '[:upper:]' | tr '-' '_')
    local port="${!port_var}"
    
    # Nettoyer les processus existants sur le port si spécifié
    if [ -n "$port" ]; then
        kill_processes_on_port "$port" "$service_name"
    fi
    
    # Nettoyer les conteneurs/pods existants
    echo "[$SCRIPT_NAME][step] Nettoyage des anciens pods..."
    podman pod stop "$pod_name" 2>/dev/null || true
    podman pod rm -f "$pod_name" 2>/dev/null || true
    
    # Nettoyer les conteneurs orphelins
    local containers=$(podman ps -aq --filter name="$pod_name" 2>/dev/null || true)
    if [ -n "$containers" ]; then
        echo "[$SCRIPT_NAME][info] Nettoyage des conteneurs orphelins..."
        for container in $containers; do
            podman stop "$container" 2>/dev/null || true
            podman rm -f "$container" 2>/dev/null || true
        done
    fi
    
    # S'assurer que les répertoires existent
    ensure_directories "$service_name"
    
    # Créer le réseau si nécessaire
    local network=$(ensure_network)
    
    # Démarrer le pod avec Kubernetes YAML
    echo "[$SCRIPT_NAME][step] Démarrage du pod avec configuration..."
    cd "$service_dir"
    
    # Substituer les variables d'environnement dans pod.yml
    local temp_pod_file=$(mktemp)
    if command -v envsubst &> /dev/null; then
        # Exporter toutes les variables du .env pour envsubst
        set -a
        source "${SYMFONY_ROOT}/.env.podman" 2>/dev/null || true
        set +a
        envsubst < "pod.yml" > "$temp_pod_file"
    else
        # Fallback : utiliser eval pour substituer les variables
        eval "cat <<EOF
$(cat pod.yml)
EOF
" > "$temp_pod_file"
    fi
    
    if ! podman play kube "$temp_pod_file"; then
        echo "[$SCRIPT_NAME][error] Échec du démarrage du service $service_name"
        show_container_logs "$pod_name" 30
        rm -f "$temp_pod_file"
        return 1
    fi
    
    # Nettoyer le fichier temporaire
    rm -f "$temp_pod_file"
    
    echo "[$SCRIPT_NAME][success] Service $service_name démarré avec succès"
    
    # Vérifier que le pod est en cours d'exécution
    echo "[$SCRIPT_NAME][step] Vérification de l'état du service..."
    sleep 3
    
    local pod_status=$(get_pod_status "$pod_name")
    if [ "$pod_status" = "NotFound" ]; then
        echo "[$SCRIPT_NAME][error] Service $service_name non trouvé après démarrage"
        return 1
    fi
    
    echo "[$SCRIPT_NAME][info] État du service: $pod_status"
    
    # Attendre que le port soit disponible si spécifié
    if [ -n "$port" ]; then
        if ! wait_for_service "$service_name" "$port" "${HOST:-localhost}" 30 2; then
            echo "[$SCRIPT_NAME][warning] Le port $port n'est pas encore accessible pour $service_name"
            echo "[$SCRIPT_NAME][info] Le service peut nécessiter plus de temps pour démarrer"
        fi
    fi
    
    # Afficher les informations du pod
    echo "[$SCRIPT_NAME][step] Informations du service démarré..."
    podman pod ps --filter name="$pod_name" --format "table {{.Name}}\t{{.Status}}\t{{.Created}}\t{{.NumberOfContainers}}"
    
    # Résumé final
    show_summary "$service_name" "$port" "start" "Service démarré avec $(get_pod_containers "$pod_name") conteneur(s)"
    
    echo "[$SCRIPT_NAME][success] Démarrage du service $service_name terminé avec succès !"
}

# Arrêt d'un service (stop)
pod_stop() {
    local service_dir="$1"
    local service_name="$(basename "$service_dir")"
    local pod_name=$(get_pod_name "$service_name")
    
    echo "[$SCRIPT_NAME][step] Arrêt du service $service_name..."
    
    # Vérifier si le pod existe
    local pod_status=$(get_pod_status "$pod_name")
    if [ "$pod_status" = "NotFound" ]; then
        echo "[$SCRIPT_NAME][warning] Service $service_name déjà arrêté"
        show_summary "$service_name" "" "stop" "Service déjà arrêté"
        return 0
    fi
    
    echo "[$SCRIPT_NAME][info] État actuel du service: $pod_status"
    
    # Afficher les conteneurs avant arrêt
    echo "[$SCRIPT_NAME][step] Conteneurs dans le service avant arrêt:"
    podman ps --filter pod="$pod_name" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" || true
    
    # Arrêter le pod gracieusement
    echo "[$SCRIPT_NAME][step] Arrêt gracieux du service..."
    if podman pod stop "$pod_name" 2>/dev/null; then
        echo "[$SCRIPT_NAME][success] Service $service_name arrêté avec succès"
    else
        echo "[$SCRIPT_NAME][warning] Échec de l'arrêt gracieux, forçage de l'arrêt..."
        podman pod kill "$pod_name" 2>/dev/null || true
    fi
    
    # Vérifier que le port est libéré
    local port_var=$(echo "${service_name}_PORT" | tr '[:lower:]' '[:upper:]' | tr '-' '_')
    local port="${!port_var}"
    
    if [ -n "$port" ]; then
        echo "[$SCRIPT_NAME][step] Vérification de la libération du port $port..."
        sleep 2
        
        if check_port "$port" "${HOST:-localhost}" 2; then
            echo "[$SCRIPT_NAME][warning] Le port $port est encore occupé, nettoyage des processus..."
            kill_processes_on_port "$port" "$service_name"
        else
            echo "[$SCRIPT_NAME][success] Port $port libéré"
        fi
    fi
    
    # Vérifier le statut final
    local pod_status_final=$(get_pod_status "$pod_name")
    echo "[$SCRIPT_NAME][info] État final du service: $pod_status_final"
    
    # Résumé final
    show_summary "$service_name" "$port" "stop" "Service arrêté, port libéré"
    
    echo "[$SCRIPT_NAME][success] Arrêt du service $service_name terminé avec succès !"
}

# État d'un service (status)
pod_status() {
    local service_dir="$1"
    local service_name="$(basename "$service_dir")"
    local detailed="$2"
    local pod_name=$(get_pod_name "$service_name")
    
    echo "[$SCRIPT_NAME][step] Consultation de l'état du service $service_name..."
    
    # Obtenir le statut du pod
    local pod_status=$(get_pod_status "$pod_name")
    
    if [ "$pod_status" = "NotFound" ]; then
        echo "[$SCRIPT_NAME][error] Service $service_name non trouvé"
        return 1
    fi
    
    # Affichage basique du statut
    echo "[$SCRIPT_NAME][info] État du service: $pod_status"
    
    # Obtenir le nombre de conteneurs
    local container_count=$(get_pod_containers "$pod_name")
    echo "[$SCRIPT_NAME][info] Nombre de conteneurs: $container_count"
    
    # Affichage détaillé si demandé
    if [ "$detailed" = "--detailed" ]; then
        echo
        echo "[$SCRIPT_NAME][step] Informations détaillées du service..."
        
        # Informations générales du pod
        podman pod ps --filter name="$pod_name" --format "table {{.Name}}\t{{.Status}}\t{{.Created}}\t{{.NumberOfContainers}}\t{{.Id}}"
        
        echo
        echo "[$SCRIPT_NAME][step] Conteneurs dans le service..."
        podman ps --filter pod="$pod_name" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}\t{{.Created}}"
        
        # Logs récents si le pod est en cours d'exécution
        if [[ "$pod_status" == *"Running"* ]]; then
            echo
            echo "[$SCRIPT_NAME][step] Logs récents (5 dernières lignes)..."
            show_container_logs "$pod_name" 5
        fi
    fi
    
    # Déterminer le code de sortie basé sur le statut
    if [[ "$pod_status" == *"Running"* ]]; then
        echo "[$SCRIPT_NAME][success] Service $service_name fonctionne correctement"
        return 0
    else
        echo "[$SCRIPT_NAME][warning] Service $service_name dans un état: $pod_status"
        return 1
    fi
}

# Santé d'un service (health)
pod_health() {
    local service_dir="$1"
    local service_name="$(basename "$service_dir")"
    local pod_name=$(get_pod_name "$service_name")
    
    echo "[$SCRIPT_NAME][step] Vérification de la santé du service $service_name..."
    
    # Vérifier d'abord si le pod existe et est en cours d'exécution
    local pod_status=$(get_pod_status "$pod_name")
    if [ "$pod_status" = "NotFound" ]; then
        echo "[$SCRIPT_NAME][error] Service $service_name non trouvé"
        return 1
    fi
    
    echo "[$SCRIPT_NAME][info] État du service: $pod_status"
    
    if [[ "$pod_status" != *"Running"* ]]; then
        echo "[$SCRIPT_NAME][error] Service $service_name n'est pas démarré (état: $pod_status)"
        return 2
    fi
    
    # Obtenir le port depuis les variables d'environnement
    local port_var=$(echo "${service_name}_PORT" | tr '[:lower:]' '[:upper:]' | tr '-' '_')
    local port="${!port_var}"
    
    if [ -z "$port" ]; then
        echo "[$SCRIPT_NAME][warning] Port non défini pour $service_name"
        echo "[$SCRIPT_NAME][success] Service $service_name en cours d'exécution (pas de vérification de port)"
        return 0
    fi
    
    # Test: Vérification du port
    echo "[$SCRIPT_NAME][step] Vérification du port $port..."
    if check_port "$port" "${HOST:-localhost}" 5; then
        echo "[$SCRIPT_NAME][success] Port $port accessible"
        echo "[$SCRIPT_NAME][success] Service $service_name en bonne santé !"
        return 0
    else
        echo "[$SCRIPT_NAME][error] Port $port non accessible"
        echo "[$SCRIPT_NAME][step] Affichage des logs pour diagnostic..."
        show_container_logs "$pod_name" 10
        return 4
    fi
}

# Nettoyage d'un service (clean)
pod_clean() {
    local service_dir="$1"
    local service_name="$(basename "$service_dir")"
    local pod_name=$(get_pod_name "$service_name")
    
    echo "[$SCRIPT_NAME][step] Nettoyage complet du service $service_name..."
    
    # Obtenir le port
    local port_var=$(echo "${service_name}_PORT" | tr '[:lower:]' '[:upper:]' | tr '-' '_')
    local port="${!port_var}"
    
    # Nettoyer les processus sur le port si spécifié
    if [ -n "$port" ]; then
        kill_processes_on_port "$port" "$service_name"
    fi
    
    # Vérifier si le pod existe
    local pod_status=$(get_pod_status "$pod_name")
    if [ "$pod_status" = "NotFound" ]; then
        echo "[$SCRIPT_NAME][info] Service $service_name non trouvé, nettoyage des conteneurs orphelins..."
    else
        echo "[$SCRIPT_NAME][info] État actuel du service: $pod_status"
        
        # Afficher les conteneurs avant nettoyage
        echo "[$SCRIPT_NAME][step] Conteneurs dans le service avant nettoyage:"
        podman ps --filter pod="$pod_name" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" || true
    fi
    
    # Arrêter le pod s'il existe
    if [ "$pod_status" != "NotFound" ]; then
        echo "[$SCRIPT_NAME][step] Arrêt du service..."
        podman pod stop "$pod_name" 2>/dev/null || true
        podman pod kill "$pod_name" 2>/dev/null || true
    fi
    
    # Nettoyer tous les conteneurs associés au pod
    echo "[$SCRIPT_NAME][step] Nettoyage des conteneurs..."
    local containers=$(podman ps -aq --filter name="$pod_name" 2>/dev/null || true)
    if [ -n "$containers" ]; then
        echo "[$SCRIPT_NAME][info] Conteneurs trouvés: $(echo $containers | wc -w)"
        for container in $containers; do
            podman stop "$container" 2>/dev/null || true
            podman rm -f "$container" 2>/dev/null || true
        done
        echo "[$SCRIPT_NAME][success] Conteneurs nettoyés"
    fi
    
    # Supprimer le pod
    if [ "$pod_status" != "NotFound" ]; then
        echo "[$SCRIPT_NAME][step] Suppression du service..."
        if podman pod rm -f "$pod_name" 2>/dev/null; then
            echo "[$SCRIPT_NAME][success] Service $service_name supprimé"
        fi
    fi
    
    # Vérification finale du port
    if [ -n "$port" ]; then
        echo "[$SCRIPT_NAME][step] Vérification finale du port $port..."
        sleep 2
        
        if check_port "$port" "${HOST:-localhost}" 2; then
            echo "[$SCRIPT_NAME][warning] Le port $port est encore occupé après nettoyage"
            kill_processes_on_port "$port" "$service_name"
        else
            echo "[$SCRIPT_NAME][success] Port $port définitivement libéré"
        fi
    fi
    
    # Vérification finale
    local pod_status_final=$(get_pod_status "$pod_name")
    if [ "$pod_status_final" = "NotFound" ]; then
        echo "[$SCRIPT_NAME][success] Service $service_name complètement nettoyé"
    else
        echo "[$SCRIPT_NAME][warning] Service $service_name encore présent: $pod_status_final"
    fi
    
    # Résumé final
    show_summary "$service_name" "$port" "clean" "Service et conteneurs supprimés, port libéré"
    
    echo "[$SCRIPT_NAME][success] Nettoyage complet du service $service_name terminé !"
}

# Logs d'un service (logs)
pod_logs() {
    local service_dir="$1"
    local service_name="$(basename "$service_dir")"
    local lines="${2:-20}"
    local pod_name=$(get_pod_name "$service_name")
    
    echo "[$SCRIPT_NAME][step] Consultation des logs du service $service_name..."
    
    show_container_logs "$pod_name" "$lines"
}

# =============================================================================
# FONCTION PRINCIPALE DU MOTEUR
# =============================================================================

# Point d'entrée principal
main() {
    local action="$1"
    local service_path="$2"
    local extra_args="${@:3}"
    
    if [ -z "$action" ] || [ -z "$service_path" ]; then
        echo "[$SCRIPT_NAME][error] Usage: $0 <action> <service_path> [args...]"
        echo "[$SCRIPT_NAME][error] Actions: start, stop, status, health, clean, logs"
        echo "[$SCRIPT_NAME][error] Exemple: $0 start /path/to/pods/mariadb"
        echo "[$SCRIPT_NAME][error] Exemple: $0 status /path/to/pods/mariadb --detailed"
        return 1
    fi
    
    if [ ! -d "$service_path" ]; then
        echo "[$SCRIPT_NAME][error] Répertoire de service non trouvé: $service_path"
        return 1
    fi
    
    case "$action" in
        "start")
            pod_start "$service_path"
            ;;
        "stop")
            pod_stop "$service_path"
            ;;
        "status")
            pod_status "$service_path" $extra_args
            ;;
        "health")
            pod_health "$service_path"
            ;;
        "clean")
            pod_clean "$service_path"
            ;;
        "logs")
            pod_logs "$service_path" $extra_args
            ;;
        *)
            echo "[$SCRIPT_NAME][error] Action non reconnue: $action"
            echo "[$SCRIPT_NAME][error] Actions disponibles: start, stop, status, health, clean, logs"
            return 1
            ;;
    esac
}

# Si le script est exécuté directement (pas sourcé)
if [ "${BASH_SOURCE[0]}" == "${0}" ]; then
    main "$@"
fi
