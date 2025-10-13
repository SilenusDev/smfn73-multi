#!/bin/bash

echo "🔍 Vérification de l'installation"
echo "=================================="
echo ""

# Vérifier Docker
if command -v docker &> /dev/null; then
    echo "✅ Docker installé"
else
    echo "❌ Docker non installé"
fi

# Vérifier les conteneurs
if docker compose ps | grep -q "symfony_web"; then
    echo "✅ Conteneur web démarré"
else
    echo "⚠️  Conteneur web non démarré"
fi

if docker compose ps | grep -q "symfony_db"; then
    echo "✅ Conteneur db démarré"
else
    echo "⚠️  Conteneur db non démarré"
fi

# Vérifier les fichiers
if [ -f ".env" ]; then
    echo "✅ Fichier .env présent"
else
    echo "❌ Fichier .env manquant"
fi

if [ -d "vendor" ]; then
    echo "✅ Dépendances PHP installées"
else
    echo "❌ Dépendances PHP manquantes"
fi

if [ -d "node_modules" ]; then
    echo "✅ Dépendances Node installées"
else
    echo "❌ Dépendances Node manquantes"
fi

if [ -f "public/build/app.css" ]; then
    echo "✅ Assets buildés"
    SIZE=$(wc -c < public/build/app.css)
    if [ $SIZE -gt 1000 ]; then
        echo "   CSS généré : ${SIZE} octets (OK)"
    else
        echo "   ⚠️  CSS trop petit : ${SIZE} octets (Tailwind non compilé ?)"
    fi
else
    echo "❌ Assets non buildés"
fi

# Vérifier TypeScript
if [ -f "tsconfig.json" ]; then
    if grep -q '"noEmit": true' tsconfig.json; then
        echo "⚠️  tsconfig.json contient 'noEmit: true' (peut causer des problèmes)"
    else
        echo "✅ Configuration TypeScript OK"
    fi
fi

# Vérifier Tailwind
if [ -f "tailwind.config.js" ]; then
    if grep -q ',ts}' tailwind.config.js; then
        echo "✅ Tailwind configuré pour TypeScript"
    else
        echo "⚠️  Tailwind ne scanne pas les fichiers .ts"
    fi
fi

# Vérifier les fichiers Yarn (ne doivent pas exister)
if [ -d ".yarn" ] || [ -f "yarn.lock" ]; then
    echo "⚠️  Fichiers Yarn détectés (utiliser npm uniquement)"
    echo "   💡 Exécuter: make fix-assets"
else
    echo "✅ Pas de fichiers Yarn (npm utilisé)"
fi

# Vérifier les doublons JS/TS
if [ -f "assets/app.js" ] || [ -f "assets/bootstrap.js" ]; then
    echo "⚠️  Fichiers .js doublons détectés"
    echo "   💡 Exécuter: make fix-assets"
else
    echo "✅ Pas de fichiers .js doublons"
fi

echo ""
echo "=================================="
echo "Vérification terminée"
