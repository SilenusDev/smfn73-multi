#!/bin/bash
# scripts/check-podman.sh
# Vérification de l'installation Podman

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "🔍 Vérification de l'installation Podman"
echo "=========================================="
echo ""

ERRORS=0
WARNINGS=0

# ============================================================================
# 1. PRÉREQUIS SYSTÈME
# ============================================================================

echo "📋 Prérequis système"
echo "--------------------"

# Podman
if command -v podman &> /dev/null; then
    echo "✅ Podman installé ($(podman --version))"
else
    echo "❌ Podman non installé"
    ((ERRORS++))
fi

# Buildah
if command -v buildah &> /dev/null; then
    echo "✅ Buildah installé ($(buildah --version))"
else
    echo "⚠️  Buildah non installé (recommandé)"
    ((WARNINGS++))
fi

echo ""

# ============================================================================
# 2. IMAGE PHP
# ============================================================================

echo "🐘 Image PHP"
echo "------------"

if podman images | grep -q "symfony-php.*8.3-fpm"; then
    echo "✅ Image PHP buildée"
    podman images | grep "symfony-php" | head -1
else
    echo "❌ Image PHP non buildée"
    echo "   Exécutez: ./scripts/build-php-image.sh"
    ((ERRORS++))
fi

echo ""

# ============================================================================
# 3. PODS DÉMARRÉS
# ============================================================================

echo "🚀 Pods Podman"
echo "--------------"

# Vérifier les pods
PODS=(
    "symfony-mariadb-pod:MariaDB"
    "symfony-redis-pod:Redis"
    "symfony-web-pod:Web (Apache+PHP+Composer)"
    "symfony-node-pod:Node.js"
)

for pod_info in "${PODS[@]}"; do
    IFS=':' read -r pod_name pod_desc <<< "$pod_info"
    
    if podman pod exists "$pod_name" 2>/dev/null; then
        status=$(podman pod inspect "$pod_name" --format '{{.State}}')
        if [ "$status" = "Running" ]; then
            echo "✅ $pod_desc démarré"
        else
            echo "⚠️  $pod_desc existe mais n'est pas démarré (statut: $status)"
            ((WARNINGS++))
        fi
    else
        echo "❌ $pod_desc non démarré"
        ((ERRORS++))
    fi
done

echo ""

# ============================================================================
# 4. PORTS
# ============================================================================

echo "🔌 Ports"
echo "--------"

PORTS=(
    "6900:Apache"
    "6909:MariaDB"
    "6910:Redis"
    "6904:Node"
)

for port_info in "${PORTS[@]}"; do
    IFS=':' read -r port service <<< "$port_info"
    
    if ss -tuln | grep -q ":$port "; then
        echo "✅ Port $port ($service) en écoute"
    else
        echo "⚠️  Port $port ($service) non en écoute"
        ((WARNINGS++))
    fi
done

echo ""

# ============================================================================
# 5. CONFIGURATION /etc/hosts
# ============================================================================

echo "🌐 Configuration multi-sites"
echo "----------------------------"

DOMAINS=("silenus.local" "insidiome.local")

for domain in "${DOMAINS[@]}"; do
    if grep -q "$domain" /etc/hosts 2>/dev/null; then
        echo "✅ $domain configuré dans /etc/hosts"
    else
        echo "❌ $domain manquant dans /etc/hosts"
        echo "   Exécutez: ./scripts/setup-hosts.sh"
        ((ERRORS++))
    fi
done

echo ""

# ============================================================================
# 6. FICHIERS DE CONFIGURATION
# ============================================================================

echo "📝 Fichiers de configuration"
echo "----------------------------"

# .env.podman
if [ -f "$PROJECT_ROOT/.env.podman" ]; then
    echo "✅ Fichier .env.podman présent"
else
    echo "❌ Fichier .env.podman manquant"
    ((ERRORS++))
fi

# .env
if [ -f "$PROJECT_ROOT/.env" ]; then
    echo "✅ Fichier .env présent"
else
    echo "⚠️  Fichier .env manquant"
    ((WARNINGS++))
fi

echo ""

# ============================================================================
# 7. DÉPENDANCES
# ============================================================================

echo "📦 Dépendances"
echo "--------------"

# Vendor (Composer)
if [ -d "$PROJECT_ROOT/vendor" ]; then
    echo "✅ Dépendances PHP installées (vendor/)"
else
    echo "❌ Dépendances PHP manquantes"
    echo "   Exécutez: podman exec symfony-web-pod-symfony-composer composer install"
    ((ERRORS++))
fi

# node_modules
if [ -d "$PROJECT_ROOT/node_modules" ] || [ -d "$PROJECT_ROOT/pods/node/data/node_modules" ]; then
    echo "✅ Dépendances Node installées"
else
    echo "❌ Dépendances Node manquantes"
    echo "   Exécutez: podman exec symfony-node-pod-symfony-node npm install"
    ((ERRORS++))
fi

echo ""

# ============================================================================
# 8. ASSETS
# ============================================================================

echo "🎨 Assets"
echo "---------"

if [ -f "$PROJECT_ROOT/public/build/app.css" ]; then
    echo "✅ Assets buildés"
    SIZE=$(wc -c < "$PROJECT_ROOT/public/build/app.css")
    if [ $SIZE -gt 1000 ]; then
        echo "   CSS généré : ${SIZE} octets (OK)"
    else
        echo "   ⚠️  CSS trop petit : ${SIZE} octets (Tailwind non compilé ?)"
        ((WARNINGS++))
    fi
else
    echo "❌ Assets non buildés"
    echo "   Exécutez: podman exec symfony-node-pod-symfony-node npm run build"
    ((ERRORS++))
fi

echo ""

# ============================================================================
# 9. CONFIGURATION TYPESCRIPT/TAILWIND
# ============================================================================

echo "⚙️  Configuration"
echo "----------------"

# TypeScript
if [ -f "$PROJECT_ROOT/tsconfig.json" ]; then
    if grep -q '"noEmit": true' "$PROJECT_ROOT/tsconfig.json"; then
        echo "⚠️  tsconfig.json contient 'noEmit: true' (peut causer des problèmes)"
        ((WARNINGS++))
    else
        echo "✅ Configuration TypeScript OK"
    fi
fi

# Tailwind
if [ -f "$PROJECT_ROOT/tailwind.config.js" ]; then
    if grep -q ',ts}' "$PROJECT_ROOT/tailwind.config.js"; then
        echo "✅ Tailwind configuré pour TypeScript"
    else
        echo "⚠️  Tailwind ne scanne pas les fichiers .ts"
        ((WARNINGS++))
    fi
fi

# Vérifier les anciens fichiers (ne doivent pas exister)
if [ -d "$PROJECT_ROOT/.yarn" ] || [ -f "$PROJECT_ROOT/yarn.lock" ]; then
    echo "⚠️  Anciens fichiers Yarn détectés"
    echo "   Exécutez: ./scripts/fix-assets-podman.sh"
    ((WARNINGS++))
else
    echo "✅ npm utilisé (pas de fichiers Yarn)"
fi

# Vérifier les doublons JS/TS
if [ -f "$PROJECT_ROOT/assets/app.js" ] || [ -f "$PROJECT_ROOT/assets/bootstrap.js" ]; then
    echo "⚠️  Fichiers .js doublons détectés"
    echo "   Exécutez: ./scripts/fix-assets-podman.sh"
    ((WARNINGS++))
else
    echo "✅ Pas de fichiers .js doublons"
fi

echo ""

# ============================================================================
# 10. TESTS D'ACCÈS
# ============================================================================

echo "🌍 Tests d'accès"
echo "----------------"

# Test HTTP
if command -v curl &> /dev/null; then
    # Test silenus.local
    if curl -s -o /dev/null -w "%{http_code}" http://silenus.local:6900 | grep -q "200\|301\|302"; then
        echo "✅ silenus.local:6900 accessible"
    else
        echo "⚠️  silenus.local:6900 non accessible"
        ((WARNINGS++))
    fi
    
    # Test insidiome.local
    if curl -s -o /dev/null -w "%{http_code}" http://insidiome.local:6900 | grep -q "200\|301\|302"; then
        echo "✅ insidiome.local:6900 accessible"
    else
        echo "⚠️  insidiome.local:6900 non accessible"
        ((WARNINGS++))
    fi
else
    echo "⚠️  curl non installé, impossible de tester l'accès HTTP"
    ((WARNINGS++))
fi

echo ""

# ============================================================================
# RÉSUMÉ
# ============================================================================

echo "════════════════════════════════════════════════════════════"
if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo "✅ Installation parfaite ! Aucun problème détecté."
elif [ $ERRORS -eq 0 ]; then
    echo "⚠️  Installation OK avec $WARNINGS avertissement(s)"
else
    echo "❌ Installation incomplète : $ERRORS erreur(s), $WARNINGS avertissement(s)"
fi
echo "════════════════════════════════════════════════════════════"
echo ""

# Code de sortie
if [ $ERRORS -gt 0 ]; then
    exit 1
else
    exit 0
fi
