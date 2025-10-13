#!/bin/bash
# scripts/build-php-image.sh
# Build de l'image PHP personnalisée pour Podman

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
DOCKERFILE_PATH="${PROJECT_ROOT}/pods/php/Dockerfile"
IMAGE_NAME="localhost/symfony-php:8.3-fpm"

echo "🐘 Build de l'image PHP personnalisée"
echo "======================================"
echo ""

# Vérifier que Podman est installé
if ! command -v podman &> /dev/null; then
    echo "❌ Podman n'est pas installé"
    echo "   Installez-le avec: sudo dnf install podman"
    exit 1
fi

# Vérifier que le Dockerfile existe
if [ ! -f "$DOCKERFILE_PATH" ]; then
    echo "❌ Dockerfile non trouvé: $DOCKERFILE_PATH"
    exit 1
fi

echo "📦 Construction de l'image..."
echo "   Dockerfile: $DOCKERFILE_PATH"
echo "   Image: $IMAGE_NAME"
echo ""

# Build de l'image
podman build \
    -t "$IMAGE_NAME" \
    -f "$DOCKERFILE_PATH" \
    "${PROJECT_ROOT}/pods/php/"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Image PHP buildée avec succès !"
    echo ""
    echo "Image disponible: $IMAGE_NAME"
    echo ""
    echo "Pour vérifier:"
    echo "  podman images | grep symfony-php"
else
    echo ""
    echo "❌ Échec du build de l'image"
    exit 1
fi
