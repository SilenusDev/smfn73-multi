#!/bin/bash
# scripts/fix-assets-podman.sh
# Correction des problèmes d'assets (version Podman)

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "🔧 Correction des problèmes d'assets (Podman)"
echo "=============================================="
echo ""

# ============================================================================
# 1. ARRÊT DU POD NODE
# ============================================================================

echo "⏹️  Arrêt du pod Node..."
if podman pod exists symfony-node-pod 2>/dev/null; then
    podman pod stop symfony-node-pod 2>/dev/null || true
    echo "  ✅ Pod Node arrêté"
else
    echo "  ℹ️  Pod Node non démarré"
fi

echo ""

# ============================================================================
# 2. NETTOYAGE DES ANCIENS FICHIERS
# ============================================================================

echo "🧹 Nettoyage des anciens fichiers..."
echo ""

# Nettoyer les anciens fichiers Yarn
if [ -d "$PROJECT_ROOT/.yarn" ] || [ -f "$PROJECT_ROOT/yarn.lock" ]; then
    echo "  🗑️  Suppression des fichiers Yarn..."
    rm -rf "$PROJECT_ROOT/.yarn" "$PROJECT_ROOT/yarn.lock" "$PROJECT_ROOT/.yarnrc.yml" 2>/dev/null || true
    echo "  ✅ Fichiers Yarn supprimés"
fi

# Supprimer les doublons JS
if [ -f "$PROJECT_ROOT/assets/app.js" ] || [ -f "$PROJECT_ROOT/assets/bootstrap.js" ]; then
    echo "  🗑️  Suppression des doublons .js..."
    rm -f "$PROJECT_ROOT/assets/app.js" "$PROJECT_ROOT/assets/bootstrap.js" 2>/dev/null || true
    echo "  ✅ Doublons .js supprimés"
fi

echo ""

# ============================================================================
# 3. NETTOYAGE DE NODE_MODULES
# ============================================================================

echo "🗑️  Nettoyage de node_modules..."
echo ""

read -p "Voulez-vous supprimer node_modules ? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    rm -rf "$PROJECT_ROOT/node_modules" 2>/dev/null || true
    rm -rf "$PROJECT_ROOT/pods/node/data/node_modules" 2>/dev/null || true
    echo "  ✅ node_modules supprimé"
    REINSTALL_NPM=true
else
    echo "  ℹ️  node_modules conservé"
    REINSTALL_NPM=false
fi

echo ""

# ============================================================================
# 4. REDÉMARRAGE DU POD NODE
# ============================================================================

echo "🚀 Redémarrage du pod Node..."
"$SCRIPT_DIR/symfony-orchestrator.sh" node > /dev/null 2>&1 || true
sleep 3
echo "  ✅ Pod Node redémarré"

echo ""

# ============================================================================
# 5. RÉINSTALLATION DES DÉPENDANCES
# ============================================================================

if [ "$REINSTALL_NPM" = true ]; then
    echo "📦 Réinstallation des dépendances Node..."
    echo ""
    
    podman exec symfony-node-pod-symfony-node npm install
    
    echo ""
    echo "  ✅ Dépendances Node réinstallées"
    echo ""
fi

# ============================================================================
# 6. REBUILD DES ASSETS
# ============================================================================

echo "🔨 Rebuild des assets..."
echo ""

podman exec symfony-node-pod-symfony-node npm run build

echo ""
echo "  ✅ Assets rebuildés"
echo ""

# ============================================================================
# 7. VIDAGE DU CACHE SYMFONY
# ============================================================================

echo "🗑️  Vidage du cache Symfony..."
echo ""

if podman pod exists symfony-web-pod 2>/dev/null; then
    podman exec -w /usr/local/apache2/htdocs symfony-web-pod-symfony-php php bin/console cache:clear 2>/dev/null || true
    echo "  ✅ Cache Symfony vidé"
else
    echo "  ⚠️  Pod Web non démarré, impossible de vider le cache"
fi

echo ""

# ============================================================================
# TERMINÉ
# ============================================================================

echo "════════════════════════════════════════════════════════════"
echo "✅ Assets corrigés !"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "Rafraîchissez votre navigateur avec Ctrl+Shift+R"
echo ""
echo "Pour vérifier:"
echo "  ./scripts/check-podman.sh"
echo ""
