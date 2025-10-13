#!/bin/bash
# scripts/setup-hosts.sh
# Configuration automatique de /etc/hosts pour le multi-sites

set -e

echo "🌐 Configuration de /etc/hosts pour multi-sites"
echo "================================================"
echo ""

# Domaines à ajouter
DOMAINS=(
    "silenus.local www.silenus.local"
    "insidiome.local www.insidiome.local"
)

# Vérifier si les entrées existent déjà
check_hosts() {
    local domain=$1
    if grep -q "$domain" /etc/hosts 2>/dev/null; then
        return 0  # Existe
    else
        return 1  # N'existe pas
    fi
}

# Ajouter une entrée dans /etc/hosts
add_host_entry() {
    local entry=$1
    echo "127.0.0.1   $entry" | sudo tee -a /etc/hosts > /dev/null
    echo "  ✅ Ajouté: $entry"
}

# Vérifier chaque domaine
NEEDS_UPDATE=false
for domain_entry in "${DOMAINS[@]}"; do
    # Extraire le premier domaine de l'entrée
    first_domain=$(echo "$domain_entry" | awk '{print $1}')
    
    if check_hosts "$first_domain"; then
        echo "  ✓ $first_domain déjà configuré"
    else
        NEEDS_UPDATE=true
        echo "  ⚠️  $first_domain manquant"
    fi
done

echo ""

# Si des mises à jour sont nécessaires
if [ "$NEEDS_UPDATE" = true ]; then
    echo "📝 Ajout des entrées manquantes (nécessite sudo)..."
    echo ""
    
    for domain_entry in "${DOMAINS[@]}"; do
        first_domain=$(echo "$domain_entry" | awk '{print $1}')
        
        if ! check_hosts "$first_domain"; then
            add_host_entry "$domain_entry"
        fi
    done
    
    echo ""
    echo "✅ Configuration de /etc/hosts terminée !"
else
    echo "✅ Tous les domaines sont déjà configurés !"
fi

echo ""
echo "🔍 Vérification de la résolution DNS..."
echo ""

# Tester la résolution
for domain_entry in "${DOMAINS[@]}"; do
    first_domain=$(echo "$domain_entry" | awk '{print $1}')
    
    if ping -c 1 -W 1 "$first_domain" &> /dev/null; then
        echo "  ✅ $first_domain résolu correctement"
    else
        echo "  ⚠️  $first_domain ne répond pas (normal si les services ne sont pas démarrés)"
    fi
done

echo ""
echo "📋 Contenu de /etc/hosts (lignes concernées):"
echo ""
grep -E "silenus|insidiome" /etc/hosts || echo "  Aucune entrée trouvée"
echo ""
