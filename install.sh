#!/bin/bash
set -e

echo "🚀 Installation du projet smfn73-multi"
echo "======================================"

# Vérifier que Docker est disponible
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé"
    exit 1
fi

# Nettoyer les anciens fichiers Yarn si présents
echo "🧹 Nettoyage des anciens fichiers..."
rm -rf node_modules .yarn yarn.lock .yarnrc.yml 2>/dev/null || true

# Supprimer les fichiers JS doublons si présents
echo "🧹 Suppression des doublons .js..."
rm -f assets/app.js assets/bootstrap.js 2>/dev/null || true

# Copier le fichier .env si nécessaire
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        echo "📝 Création du fichier .env..."
        cp .env.example .env
    else
        echo "⚠️  Aucun fichier .env.example trouvé"
    fi
fi

# Installer les dépendances PHP via Docker
echo "📦 Installation des dépendances PHP..."
docker compose run --rm web composer install --no-interaction

# Installer les dépendances Node via Docker
echo "📦 Installation des dépendances Node..."
docker compose run --rm node npm install

# Build des assets
echo "🔨 Build des assets..."
docker compose run --rm node npm run build

# Redémarrer le conteneur node pour le watch
echo "🔄 Redémarrage du conteneur node..."
docker compose up -d node

# Créer la base de données
echo "🗄️  Configuration de la base de données..."
docker compose up -d db
sleep 5
docker compose exec -T web php bin/console doctrine:database:create --if-not-exists || true
docker compose exec -T web php bin/console doctrine:migrations:migrate --no-interaction || true

echo ""
echo "✅ Installation terminée !"
echo ""
echo "Pour démarrer le projet :"
echo "  docker compose up -d"
echo ""
echo "Pour rebuilder les assets en mode watch :"
echo "  docker compose up node"
