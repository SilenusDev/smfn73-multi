#!/bin/bash
# scripts/symfony-orchestrator.sh
# Script principal d'orchestration Symfony Multi-sites
# Inspiré de dagda-lite/dagda/eveil/taranis.sh

SCRIPT_NAME="symfony-orchestrator"

# Détection automatique de SYMFONY_ROOT
if [ -z "$SYMFONY_ROOT" ]; then
    SCRIPT_PATH="$(readlink -f "${BASH_SOURCE[0]}")"
    DETECTED_SYMFONY_ROOT="$(dirname "$(dirname "$SCRIPT_PATH")")"
    
    if [ -f "${DETECTED_SYMFONY_ROOT}/composer.json" ] && [ -d "${DETECTED_SYMFONY_ROOT}/pods" ]; then
        export SYMFONY_ROOT="$DETECTED_SYMFONY_ROOT"
        echo "[$SCRIPT_NAME][info] SYMFONY_ROOT détecté: ${SYMFONY_ROOT}"
    else
        echo "[$SCRIPT_NAME][error] Impossible de détecter SYMFONY_ROOT" >&2
        exit 1
    fi
fi

echo "[$SCRIPT_NAME][start] Démarrage de l'orchestrateur Symfony Multi-sites"

# Charger les utilitaires
UTILS_SCRIPT="${SYMFONY_ROOT}/scripts/utils.sh"
POD_ENGINE_SCRIPT="${SYMFONY_ROOT}/scripts/pod-engine.sh"

if [ ! -f "$UTILS_SCRIPT" ]; then
    echo "[$SCRIPT_NAME][error] utils.sh non trouvé: ${UTILS_SCRIPT}" >&2
    exit 1
fi

source "${UTILS_SCRIPT}"

# Charger les variables d'environnement
load_env 2>/dev/null || true

case "$1" in
    start|symfony|dev)
        MODE="dev"
        if [ "$2" = "--prod" ] || [ "$2" = "prod" ]; then
            MODE="prod"
        fi
        
        echo "[$SCRIPT_NAME][start] 🚀 DÉMARRAGE DES SERVICES ESSENTIELS SYMFONY (mode: $MODE)"
        echo "[$SCRIPT_NAME][info] =========================================="
        
        echo "[$SCRIPT_NAME][step] 🗄️  Démarrage de MariaDB..."
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/mariadb"
        mariadb_status=$?
        
        echo "[$SCRIPT_NAME][step] 🔴 Démarrage de Redis..."
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/redis"
        redis_status=$?
        
        echo "[$SCRIPT_NAME][step] 🐘 Démarrage de PHP-FPM..."
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/php"
        php_status=$?
        
        echo "[$SCRIPT_NAME][step] 🌐 Démarrage d'Apache..."
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/apache"
        apache_status=$?
        
        if [ "$MODE" = "prod" ]; then
            echo "[$SCRIPT_NAME][step] 📦 Build des assets en mode production..."
            podman run --rm -v "${SYMFONY_ROOT}:/app:z" -w /app docker.io/library/node:20-alpine sh -c "npm install && npm run build"
            node_status=$?
            echo "[$SCRIPT_NAME][info] Assets buildés pour la production"
        else
            echo "[$SCRIPT_NAME][step] 📦 Démarrage de Node.js (watch mode)..."
            "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/node"
            node_status=$?
        fi
        
        echo "[$SCRIPT_NAME][info] =========================================="
        echo "[$SCRIPT_NAME][step] 📊 VÉRIFICATION DES SERVICES ESSENTIELS"
        
        if [ $mariadb_status -eq 0 ] && [ $redis_status -eq 0 ] && [ $php_status -eq 0 ] && [ $apache_status -eq 0 ] && [ $node_status -eq 0 ]; then
            echo "[$SCRIPT_NAME][success] ✅ Services essentiels SYMFONY démarrés avec succès"
            echo "[$SCRIPT_NAME][info] 🗄️  MariaDB    : http://${HOST}:${MARIADB_PORT}/"
            echo "[$SCRIPT_NAME][info] 🔴 Redis      : ${HOST}:${REDIS_PORT}"
            echo "[$SCRIPT_NAME][info] 🐘 PHP-FPM    : ${HOST}:${PHP_PORT}"
            echo "[$SCRIPT_NAME][info] 🌐 Apache     : http://${HOST}:${APACHE_PORT}/"
            echo "[$SCRIPT_NAME][info] 📦 Node/Vite  : http://${HOST}:${NODE_PORT}/"
            echo "[$SCRIPT_NAME][info] =========================================="
            echo "[$SCRIPT_NAME][success] 🎉 SYMFONY MULTI-SITES OPÉRATIONNEL"
            echo "[$SCRIPT_NAME][info] =========================================="
        else
            echo "[$SCRIPT_NAME][error] ❌ Échec du démarrage de certains services essentiels"
            echo "[$SCRIPT_NAME][info] 🗄️  MariaDB : $([ $mariadb_status -eq 0 ] && echo "✅" || echo "❌")"
            echo "[$SCRIPT_NAME][info] 🔴 Redis   : $([ $redis_status -eq 0 ] && echo "✅" || echo "❌")"
            echo "[$SCRIPT_NAME][info] 🐘 PHP-FPM : $([ $php_status -eq 0 ] && echo "✅" || echo "❌")"
            echo "[$SCRIPT_NAME][info] 🌐 Apache  : $([ $apache_status -eq 0 ] && echo "✅" || echo "❌")"
            echo "[$SCRIPT_NAME][info] 📦 Node    : $([ $node_status -eq 0 ] && echo "✅" || echo "❌")"
            exit 1
        fi
        ;;
    
    # Commande build (assets production)
    build)
        echo "[$SCRIPT_NAME][info] 🔨 BUILD DES ASSETS EN MODE PRODUCTION"
        echo "[$SCRIPT_NAME][step] Compilation des assets avec Webpack Encore..."
        
        # Utiliser le même montage de node_modules que le pod Node
        NODE_MODULES_PATH="${SYMFONY_ROOT}/pods/node/data/node_modules"
        
        podman run --rm \
            -v "${SYMFONY_ROOT}:/app:z" \
            -v "${NODE_MODULES_PATH}:/app/node_modules:z" \
            -w /app \
            docker.io/library/node:20-alpine \
            sh -c "npm install && npm run build"
        
        if [ $? -eq 0 ]; then
            echo "[$SCRIPT_NAME][success] ✅ Assets buildés avec succès"
            echo "[$SCRIPT_NAME][info] Fichiers générés dans public/build/"
        else
            echo "[$SCRIPT_NAME][error] ❌ Échec du build des assets"
            exit 1
        fi
        ;;
    
    # Commandes par pod individuel
    apache)
        echo "[$SCRIPT_NAME][info] Démarrage d'Apache"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/apache"
        ;;
    php)
        echo "[$SCRIPT_NAME][info] Démarrage de PHP-FPM"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/php"
        ;;
    mariadb)
        echo "[$SCRIPT_NAME][info] Démarrage de MariaDB"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/mariadb"
        ;;
    redis)
        echo "[$SCRIPT_NAME][info] Démarrage de Redis"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/redis"
        ;;
    node)
        echo "[$SCRIPT_NAME][info] Démarrage de Node.js"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/node"
        ;;
    qdrant)
        echo "[$SCRIPT_NAME][info] Démarrage de Qdrant"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/qdrant"
        ;;
    fastapi)
        echo "[$SCRIPT_NAME][info] Démarrage de FastAPI"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/fastapi"
        ;;
    phpmyadmin)
        echo "[$SCRIPT_NAME][info] Démarrage de phpMyAdmin"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/phpmyadmin"
        ;;
    composer)
        echo "[$SCRIPT_NAME][info] Démarrage de Composer"
        "${POD_ENGINE_SCRIPT}" start "${PODS_DIR}/composer"
        ;;
    
    # Commandes d'arrêt par pod
    stop)
        case "$2" in
            apache)
                echo "[$SCRIPT_NAME][info] Arrêt d'Apache"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/apache"
                ;;
            php)
                echo "[$SCRIPT_NAME][info] Arrêt de PHP-FPM"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/php"
                ;;
            mariadb)
                echo "[$SCRIPT_NAME][info] Arrêt de MariaDB"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/mariadb"
                ;;
            redis)
                echo "[$SCRIPT_NAME][info] Arrêt de Redis"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/redis"
                ;;
            node)
                echo "[$SCRIPT_NAME][info] Arrêt de Node.js"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/node"
                ;;
            qdrant)
                echo "[$SCRIPT_NAME][info] Arrêt de Qdrant"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/qdrant"
                ;;
            fastapi)
                echo "[$SCRIPT_NAME][info] Arrêt de FastAPI"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/fastapi"
                ;;
            phpmyadmin)
                echo "[$SCRIPT_NAME][info] Arrêt de phpMyAdmin"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/phpmyadmin"
                ;;
            composer)
                echo "[$SCRIPT_NAME][info] Arrêt de Composer"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/composer"
                ;;
            symfony)
                echo "[$SCRIPT_NAME][info] Arrêt des services essentiels SYMFONY"
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/node" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/apache" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/php" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/redis" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/mariadb" 2>/dev/null || true
                echo "[$SCRIPT_NAME][success] Services essentiels SYMFONY arrêtés"
                ;;
            all)
                echo "[$SCRIPT_NAME][info] Arrêt de tous les services Symfony Multi-sites"
                echo "[$SCRIPT_NAME][step] Arrêt des services optionnels..."
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/phpmyadmin" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/fastapi" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/qdrant" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/composer" 2>/dev/null || true
                echo "[$SCRIPT_NAME][step] Arrêt des services essentiels..."
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/node" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/apache" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/php" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/redis" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" stop "${PODS_DIR}/mariadb" 2>/dev/null || true
                echo "[$SCRIPT_NAME][success] Tous les services Symfony Multi-sites arrêtés"
                ;;
            *)
                echo "[$SCRIPT_NAME][error] Usage: $0 stop {apache|php|mariadb|redis|node|qdrant|fastapi|phpmyadmin|composer|symfony|all}"
                exit 1
                ;;
        esac
        ;;
    
    # Commande de nettoyage complet
    clean)
        case "$2" in
            symfony|"")
                echo "[$SCRIPT_NAME][info] Nettoyage complet des services essentiels SYMFONY"
                echo "[$SCRIPT_NAME][step] Nettoyage de Node.js..."
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/node" 2>/dev/null || true
                echo "[$SCRIPT_NAME][step] Nettoyage d'Apache..."
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/apache" 2>/dev/null || true
                echo "[$SCRIPT_NAME][step] Nettoyage de PHP-FPM..."
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/php" 2>/dev/null || true
                echo "[$SCRIPT_NAME][step] Nettoyage de Redis..."
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/redis" 2>/dev/null || true
                echo "[$SCRIPT_NAME][step] Nettoyage de MariaDB..."
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/mariadb" 2>/dev/null || true
                echo "[$SCRIPT_NAME][success] Services essentiels SYMFONY nettoyés"
                ;;
            all)
                echo "[$SCRIPT_NAME][info] Nettoyage complet de tous les services Symfony Multi-sites"
                echo "[$SCRIPT_NAME][step] Nettoyage des services optionnels..."
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/phpmyadmin" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/fastapi" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/qdrant" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/composer" 2>/dev/null || true
                echo "[$SCRIPT_NAME][step] Nettoyage des services essentiels..."
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/node" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/apache" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/php" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/redis" 2>/dev/null || true
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/mariadb" 2>/dev/null || true
                echo "[$SCRIPT_NAME][success] Tous les services Symfony Multi-sites nettoyés"
                ;;
            apache|php|mariadb|redis|node|qdrant|fastapi|phpmyadmin|composer)
                echo "[$SCRIPT_NAME][info] Nettoyage de $2"
                "${POD_ENGINE_SCRIPT}" clean "${PODS_DIR}/$2"
                ;;
            *)
                echo "[$SCRIPT_NAME][error] Usage: $0 clean {symfony|apache|php|mariadb|redis|node|qdrant|fastapi|phpmyadmin|composer|all|''}"
                exit 1
                ;;
        esac
        ;;
    
    # Commandes de statut par pod
    status)
        case "$2" in
            apache|php|mariadb|redis|node|qdrant|fastapi|phpmyadmin|composer)
                echo "[$SCRIPT_NAME][info] Statut de $2"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/$2"
                ;;
            symfony)
                echo "[$SCRIPT_NAME][info] Statut des services essentiels SYMFONY"
                echo "[$SCRIPT_NAME][step] MariaDB:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/mariadb" 2>/dev/null || echo "[$SCRIPT_NAME][warning] MariaDB non disponible"
                echo "[$SCRIPT_NAME][step] Redis:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/redis" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Redis non disponible"
                echo "[$SCRIPT_NAME][step] PHP-FPM:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/php" 2>/dev/null || echo "[$SCRIPT_NAME][warning] PHP-FPM non disponible"
                echo "[$SCRIPT_NAME][step] Apache:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/apache" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Apache non disponible"
                echo "[$SCRIPT_NAME][step] Node.js:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/node" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Node.js non disponible"
                ;;
            all|"")
                echo "[$SCRIPT_NAME][info] Statut global de tous les services Symfony Multi-sites"
                echo "[$SCRIPT_NAME][step] === SERVICES ESSENTIELS (SYMFONY) ==="
                echo "[$SCRIPT_NAME][step] MariaDB:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/mariadb" 2>/dev/null || echo "[$SCRIPT_NAME][warning] MariaDB non disponible"
                echo "[$SCRIPT_NAME][step] Redis:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/redis" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Redis non disponible"
                echo "[$SCRIPT_NAME][step] PHP-FPM:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/php" 2>/dev/null || echo "[$SCRIPT_NAME][warning] PHP-FPM non disponible"
                echo "[$SCRIPT_NAME][step] Apache:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/apache" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Apache non disponible"
                echo "[$SCRIPT_NAME][step] Node.js:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/node" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Node.js non disponible"
                echo "[$SCRIPT_NAME][step] === SERVICES OPTIONNELS ==="
                echo "[$SCRIPT_NAME][step] Qdrant:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/qdrant" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Qdrant non disponible"
                echo "[$SCRIPT_NAME][step] FastAPI:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/fastapi" 2>/dev/null || echo "[$SCRIPT_NAME][warning] FastAPI non disponible"
                echo "[$SCRIPT_NAME][step] phpMyAdmin:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/phpmyadmin" 2>/dev/null || echo "[$SCRIPT_NAME][warning] phpMyAdmin non disponible"
                echo "[$SCRIPT_NAME][step] Composer:"
                "${POD_ENGINE_SCRIPT}" status "${PODS_DIR}/composer" 2>/dev/null || echo "[$SCRIPT_NAME][warning] Composer non disponible"
                ;;
            *)
                echo "[$SCRIPT_NAME][error] Usage: $0 status {apache|php|mariadb|redis|node|qdrant|fastapi|phpmyadmin|composer|symfony|all|''}"
                exit 1
                ;;
        esac
        ;;
    
    *)
        echo "[$SCRIPT_NAME][help] Usage: $0 {COMMAND} [OPTIONS]"
        echo "[$SCRIPT_NAME][help]"
        echo "[$SCRIPT_NAME][help] === DÉMARRAGE RAPIDE ==="
        echo "[$SCRIPT_NAME][help]   dev                      - Démarrer en mode développement (avec watch)"
        echo "[$SCRIPT_NAME][help]   start                    - Alias de 'dev'"
        echo "[$SCRIPT_NAME][help]   start --prod             - Démarrer en mode production (build assets)"
        echo "[$SCRIPT_NAME][help]   build                    - Builder les assets pour la production"
        echo "[$SCRIPT_NAME][help]"
        echo "[$SCRIPT_NAME][help] === SERVICES ESSENTIELS ==="
        echo "[$SCRIPT_NAME][help]   symfony                  - Démarrer les 5 services essentiels (mariadb, redis, php, apache, node)"
        echo "[$SCRIPT_NAME][help]   stop symfony             - Arrêter les services essentiels"
        echo "[$SCRIPT_NAME][help]   status symfony           - Statut des services essentiels"
        echo "[$SCRIPT_NAME][help]"
        echo "[$SCRIPT_NAME][help] === SERVICES INDIVIDUELS ==="
        echo "[$SCRIPT_NAME][help]   apache                   - Démarrer Apache (serveur web)"
        echo "[$SCRIPT_NAME][help]   php                      - Démarrer PHP-FPM (runtime PHP)"
        echo "[$SCRIPT_NAME][help]   mariadb                  - Démarrer MariaDB (base de données)"
        echo "[$SCRIPT_NAME][help]   redis                    - Démarrer Redis (cache)"
        echo "[$SCRIPT_NAME][help]   node                     - Démarrer Node.js (assets)"
        echo "[$SCRIPT_NAME][help]   qdrant                   - Démarrer Qdrant (vector DB)"
        echo "[$SCRIPT_NAME][help]   fastapi                  - Démarrer FastAPI (API Python)"
        echo "[$SCRIPT_NAME][help]   phpmyadmin               - Démarrer phpMyAdmin (interface DB)"
        echo "[$SCRIPT_NAME][help]   composer                 - Démarrer Composer (utilitaire)"
        echo "[$SCRIPT_NAME][help]"
        echo "[$SCRIPT_NAME][help] === COMMANDES GLOBALES ==="
        echo "[$SCRIPT_NAME][help]   stop {service|all}       - Arrêter un service ou tous"
        echo "[$SCRIPT_NAME][help]   status {service|all}     - Statut d'un service ou tous"
        echo "[$SCRIPT_NAME][help]   clean {service|all}      - Nettoyer un service ou tous"
        echo "[$SCRIPT_NAME][help]"
        echo "[$SCRIPT_NAME][help] === EXEMPLES ==="
        echo "[$SCRIPT_NAME][help]   $0 dev                   # Démarrer en mode développement (défaut)"
        echo "[$SCRIPT_NAME][help]   $0 start --prod          # Démarrer en mode production"
        echo "[$SCRIPT_NAME][help]   $0 build                 # Builder les assets pour la production"
        echo "[$SCRIPT_NAME][help]   $0 symfony               # Démarrer les services essentiels"
        echo "[$SCRIPT_NAME][help]   $0 qdrant                # Démarrer Qdrant individuellement"
        echo "[$SCRIPT_NAME][help]   $0 status all            # Voir le statut de tous les services"
        echo "[$SCRIPT_NAME][help]   $0 stop apache           # Arrêter Apache"
        echo "[$SCRIPT_NAME][help]   $0 clean all             # Nettoyer tous les services"
        echo "[$SCRIPT_NAME][help]"
        echo "[$SCRIPT_NAME][help] === DÉMARRAGE INDIVIDUEL AVEC POD-ENGINE ==="
        echo "[$SCRIPT_NAME][help]   ${POD_ENGINE_SCRIPT} start ${PODS_DIR}/apache"
        echo "[$SCRIPT_NAME][help]   ${POD_ENGINE_SCRIPT} start ${PODS_DIR}/php"
        echo "[$SCRIPT_NAME][help]   ${POD_ENGINE_SCRIPT} start ${PODS_DIR}/mariadb"
        echo "[$SCRIPT_NAME][help]   ${POD_ENGINE_SCRIPT} start ${PODS_DIR}/redis"
        echo "[$SCRIPT_NAME][help]   ${POD_ENGINE_SCRIPT} start ${PODS_DIR}/node"
        ;;
esac
