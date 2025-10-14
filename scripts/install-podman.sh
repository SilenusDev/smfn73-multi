#!/bin/bash
# scripts/install-podman.sh
# Installation complète du projet Symfony multi-sites avec Podman

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "🚀 Installation du projet Symfony Multi-sites (Podman)"
echo "======================================================="
echo ""

# Charger les utilitaires
if [ -f "$SCRIPT_DIR/utils.sh" ]; then
    source "$SCRIPT_DIR/utils.sh"
fi

# ============================================================================
# 1. VÉRIFICATION DES PRÉREQUIS
# ============================================================================

echo "📋 Vérification des prérequis..."
echo ""

# Vérifier Podman
if ! command -v podman &> /dev/null; then
    echo "❌ Podman n'est pas installé"
    echo "   Installez-le avec: sudo dnf install podman"
    exit 1
fi
echo "  ✅ Podman installé ($(podman --version))"

# Vérifier Buildah (optionnel mais recommandé)
if command -v buildah &> /dev/null; then
    echo "  ✅ Buildah installé ($(buildah --version))"
else
    echo "  ⚠️  Buildah non installé (recommandé)"
fi

# Vérifier Git
if ! command -v git &> /dev/null; then
    echo "❌ Git n'est pas installé"
    exit 1
fi
echo "  ✅ Git installé"

echo ""

# ============================================================================
# 2. CONFIGURATION DES FICHIERS
# ============================================================================

echo "📝 Configuration des fichiers..."
echo ""

# Copier .env.podman si nécessaire
if [ ! -f "$PROJECT_ROOT/.env.podman" ]; then
    if [ -f "$PROJECT_ROOT/.env.podman.example" ]; then
        echo "  📄 Création de .env.podman depuis .env.podman.example..."
        cp "$PROJECT_ROOT/.env.podman.example" "$PROJECT_ROOT/.env.podman"
        echo "  ⚠️  N'oubliez pas de configurer .env.podman avec vos paramètres"
        echo "  ⚠️  Notamment: PROJECT_ROOT (chemin absolu du projet)"
    else
        echo "  ⚠️  Aucun fichier .env.podman.example trouvé"
    fi
else
    echo "  ✅ Fichier .env.podman présent"
fi

# Copier .env si nécessaire
if [ ! -f "$PROJECT_ROOT/.env" ]; then
    if [ -f "$PROJECT_ROOT/.env.example" ]; then
        echo "  📄 Création de .env depuis .env.example..."
        cp "$PROJECT_ROOT/.env.example" "$PROJECT_ROOT/.env"
    fi
else
    echo "  ✅ Fichier .env présent"
fi

echo ""

# ============================================================================
# 3. NETTOYAGE DES ANCIENS FICHIERS
# ============================================================================

echo "🧹 Nettoyage des anciens fichiers..."
echo ""

# Nettoyer les anciens fichiers Yarn si présents
if [ -d "$PROJECT_ROOT/.yarn" ] || [ -f "$PROJECT_ROOT/yarn.lock" ]; then
    echo "  🗑️  Suppression des fichiers Yarn..."
    rm -rf "$PROJECT_ROOT/.yarn" "$PROJECT_ROOT/yarn.lock" "$PROJECT_ROOT/.yarnrc.yml" 2>/dev/null || true
fi

# Supprimer les fichiers JS doublons si présents
if [ -f "$PROJECT_ROOT/assets/app.js" ] || [ -f "$PROJECT_ROOT/assets/bootstrap.js" ]; then
    echo "  🗑️  Suppression des doublons .js..."
    rm -f "$PROJECT_ROOT/assets/app.js" "$PROJECT_ROOT/assets/bootstrap.js" 2>/dev/null || true
fi

echo "  ✅ Nettoyage terminé"
echo ""

# ============================================================================
# 4. GÉNÉRATION DES CONFIGURATIONS PODMAN
# ============================================================================

echo "⚙️  Génération des configurations Podman..."
echo ""

# Générer les fichiers pod.yml à partir des templates
if [ -f "$SCRIPT_DIR/generate-pod-configs.sh" ]; then
    "$SCRIPT_DIR/generate-pod-configs.sh"
    if [ $? -ne 0 ]; then
        echo "  ❌ Échec de la génération des configurations"
        echo "  ⚠️  Vérifiez votre fichier .env.podman"
        exit 1
    fi
else
    echo "  ⚠️  Script generate-pod-configs.sh non trouvé"
fi

echo ""

# ============================================================================
# 5. BUILD DE L'IMAGE PHP
# ============================================================================

echo "🐘 Build de l'image PHP personnalisée..."
echo ""

# Vérifier si l'image existe déjà
if podman images | grep -q "symfony-php.*8.3-fpm"; then
    echo "  ℹ️  Image PHP déjà présente"
    read -p "  Voulez-vous la rebuilder ? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        "$SCRIPT_DIR/build-php-image.sh"
    fi
else
    "$SCRIPT_DIR/build-php-image.sh"
fi

echo ""

# ============================================================================
# 5. CONFIGURATION /etc/hosts
# ============================================================================

echo "🌐 Configuration du multi-sites..."
echo ""

"$SCRIPT_DIR/setup-hosts.sh"

echo ""

# ============================================================================
# 6. CRÉATION DES RÉPERTOIRES DE DONNÉES
# ============================================================================

echo "📁 Création des répertoires de données..."
echo ""

# Créer les répertoires nécessaires
mkdir -p "$PROJECT_ROOT/pods/mariadb/data"
mkdir -p "$PROJECT_ROOT/pods/mariadb/logs"
mkdir -p "$PROJECT_ROOT/pods/php/data/var"
mkdir -p "$PROJECT_ROOT/pods/php/logs"
mkdir -p "$PROJECT_ROOT/pods/apache/logs"
mkdir -p "$PROJECT_ROOT/pods/node/data/node_modules"
mkdir -p "$PROJECT_ROOT/pods/redis/data"

echo "  ✅ Répertoires créés"
echo ""

# ============================================================================
# 7. DÉMARRAGE DES SERVICES
# ============================================================================

echo "🚀 Démarrage des services Podman..."
echo ""

# Démarrer MariaDB
echo "  🗄️  Démarrage de MariaDB..."
"$SCRIPT_DIR/symfony-orchestrator.sh" mariadb > /dev/null 2>&1 || true
sleep 3

# Démarrer Redis
echo "  🔴 Démarrage de Redis..."
"$SCRIPT_DIR/symfony-orchestrator.sh" redis > /dev/null 2>&1 || true
sleep 2

# Démarrer le pod Web (Apache + PHP + Composer)
echo "  🌐 Démarrage du pod Web..."
cd "$PROJECT_ROOT/pods/web" && podman play kube pod.yml > /dev/null 2>&1 || true
sleep 3

# Démarrer Node
echo "  📦 Démarrage de Node..."
"$SCRIPT_DIR/symfony-orchestrator.sh" node > /dev/null 2>&1 || true
sleep 2

echo ""
echo "  ✅ Services démarrés"
echo ""

# ============================================================================
# 8. INSTALLATION DES DÉPENDANCES
# ============================================================================

echo "📦 Installation des dépendances..."
echo ""

# Composer install
echo "  🎼 Installation des dépendances PHP (Composer)..."
podman exec symfony-web-pod-symfony-composer composer install --no-interaction --optimize-autoloader

echo ""

# NPM install
echo "  📦 Installation des dépendances Node (NPM)..."
podman exec symfony-node-pod-symfony-node npm install

echo ""

# ============================================================================
# 9. BUILD DES ASSETS
# ============================================================================

echo "🔨 Build des assets..."
echo ""

podman exec symfony-node-pod-symfony-node npm run build

echo ""

# ============================================================================
# 10. CONFIGURATION DE LA BASE DE DONNÉES
# ============================================================================

echo "🗄️  Configuration de la base de données..."
echo ""

# Attendre que MariaDB soit prêt
echo "  ⏳ Attente de MariaDB..."
sleep 5

# Les bases de données sont créées automatiquement via le script init SQL
echo "  ✅ Bases de données créées (via script d'initialisation)"

echo ""

# ============================================================================
# 11. PERMISSIONS
# ============================================================================

echo "🔐 Configuration des permissions..."
echo ""

# Créer et fixer les permissions du répertoire var/
podman exec -w /usr/local/apache2/htdocs symfony-web-pod-symfony-php sh -c "mkdir -p var/log var/cache && chmod -R 777 var/" 2>/dev/null || true

echo "  ✅ Permissions configurées"
echo ""

# ============================================================================
# TERMINÉ
# ============================================================================

echo ""
echo "════════════════════════════════════════════════════════════"
echo "✅ Installation terminée avec succès !"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "🌐 Accès aux sites:"
echo "   • Silenus    : http://silenus.local:6900/slns/"
echo "   • Insidiome  : http://insidiome.local:6900/nsdm/"
echo "   • Localhost  : http://localhost:6900"
echo ""
echo "📊 Vérifier l'installation:"
echo "   ./scripts/check-podman.sh"
echo ""
echo "🛠️  Commandes utiles:"
echo "   • Arrêter    : ./scripts/symfony-orchestrator.sh stop symfony"
echo "   • Démarrer   : ./scripts/symfony-orchestrator.sh dev"
echo "   • Logs       : podman logs -f symfony-web-pod-symfony-apache"
echo ""
echo "📚 Documentation:"
echo "   • PODMAN_USAGE.md"
echo "   • HOSTS_CONFIGURATION.md"
echo ""
