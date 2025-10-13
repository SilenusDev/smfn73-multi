#!/bin/bash
# scripts/clean-podman.sh
# Nettoyage complet de l'environnement Podman

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "🧹 Nettoyage de l'environnement Podman"
echo "======================================="
echo ""

# Demander confirmation
read -p "⚠️  Voulez-vous vraiment nettoyer l'environnement ? (y/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Annulé."
    exit 0
fi

echo ""

# ============================================================================
# 1. ARRÊT DES PODS
# ============================================================================

echo "⏹️  Arrêt des pods..."
echo ""

PODS=(
    "symfony-web-pod"
    "symfony-mariadb-pod"
    "symfony-redis-pod"
    "symfony-node-pod"
)

for pod in "${PODS[@]}"; do
    if podman pod exists "$pod" 2>/dev/null; then
        echo "  🛑 Arrêt de $pod..."
        podman pod stop "$pod" 2>/dev/null || true
        podman pod rm -f "$pod" 2>/dev/null || true
    fi
done

echo "  ✅ Pods arrêtés et supprimés"
echo ""

# ============================================================================
# 2. NETTOYAGE DES DONNÉES (OPTIONNEL)
# ============================================================================

echo "🗑️  Nettoyage des données..."
echo ""

read -p "Voulez-vous supprimer les données des pods ? (bases de données, logs, etc.) (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "  ⚠️  Suppression des données..."
    
    # Supprimer les données (nécessite sudo pour MariaDB)
    sudo rm -rf "$PROJECT_ROOT/pods/mariadb/data/"* 2>/dev/null || true
    rm -rf "$PROJECT_ROOT/pods/mariadb/logs/"* 2>/dev/null || true
    rm -rf "$PROJECT_ROOT/pods/php/data/"* 2>/dev/null || true
    rm -rf "$PROJECT_ROOT/pods/php/logs/"* 2>/dev/null || true
    rm -rf "$PROJECT_ROOT/pods/apache/logs/"* 2>/dev/null || true
    rm -rf "$PROJECT_ROOT/pods/redis/data/"* 2>/dev/null || true
    
    echo "  ✅ Données supprimées"
else
    echo "  ℹ️  Données conservées"
fi

echo ""

# ============================================================================
# 3. NETTOYAGE DES DÉPENDANCES (OPTIONNEL)
# ============================================================================

echo "📦 Nettoyage des dépendances..."
echo ""

read -p "Voulez-vous supprimer node_modules et vendor ? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "  🗑️  Suppression de node_modules..."
    rm -rf "$PROJECT_ROOT/node_modules" 2>/dev/null || true
    rm -rf "$PROJECT_ROOT/pods/node/data/node_modules" 2>/dev/null || true
    
    echo "  🗑️  Suppression de vendor..."
    rm -rf "$PROJECT_ROOT/vendor" 2>/dev/null || true
    
    echo "  ✅ Dépendances supprimées"
else
    echo "  ℹ️  Dépendances conservées"
fi

echo ""

# ============================================================================
# 4. NETTOYAGE DU CACHE SYMFONY
# ============================================================================

echo "🗑️  Nettoyage du cache Symfony..."
echo ""

if [ -d "$PROJECT_ROOT/var/cache" ]; then
    rm -rf "$PROJECT_ROOT/var/cache/"* 2>/dev/null || true
    echo "  ✅ Cache Symfony supprimé"
fi

if [ -d "$PROJECT_ROOT/var/log" ]; then
    rm -rf "$PROJECT_ROOT/var/log/"* 2>/dev/null || true
    echo "  ✅ Logs Symfony supprimés"
fi

echo ""

# ============================================================================
# 5. NETTOYAGE DES IMAGES (OPTIONNEL)
# ============================================================================

echo "🐳 Nettoyage des images..."
echo ""

read -p "Voulez-vous supprimer l'image PHP personnalisée ? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    if podman images | grep -q "symfony-php"; then
        echo "  🗑️  Suppression de l'image symfony-php..."
        podman rmi localhost/symfony-php:8.3-fpm 2>/dev/null || true
        echo "  ✅ Image supprimée"
    else
        echo "  ℹ️  Aucune image symfony-php trouvée"
    fi
else
    echo "  ℹ️  Image conservée"
fi

echo ""

# ============================================================================
# TERMINÉ
# ============================================================================

echo "════════════════════════════════════════════════════════════"
echo "✅ Nettoyage terminé !"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "Pour réinstaller le projet :"
echo "  ./scripts/install-podman.sh"
echo ""
