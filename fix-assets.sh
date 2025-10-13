#!/bin/bash
set -e

echo "🔧 Correction des problèmes d'assets"
echo "====================================="

# Arrêter le conteneur node
echo "⏹️  Arrêt du conteneur node..."
docker compose stop node

# Nettoyer les fichiers Yarn
echo "🧹 Suppression des fichiers Yarn..."
rm -rf .yarn yarn.lock .yarnrc.yml

# Supprimer les doublons JS
echo "🧹 Suppression des doublons .js..."
rm -f assets/app.js assets/bootstrap.js

# Nettoyer node_modules
echo "🧹 Nettoyage de node_modules..."
rm -rf node_modules

# Réinstaller avec npm
echo "📦 Réinstallation avec npm..."
docker compose run --rm node npm install

# Rebuild les assets
echo "🔨 Build des assets..."
docker compose run --rm node npm run build

# Redémarrer le conteneur node
echo "🔄 Redémarrage du conteneur node..."
docker compose up -d node

# Vider le cache Symfony
echo "🗑️  Vidage du cache Symfony..."
docker compose exec -T web php bin/console cache:clear

echo ""
echo "✅ Assets corrigés !"
echo ""
echo "Rafraîchis ton navigateur avec Ctrl+Shift+R"
