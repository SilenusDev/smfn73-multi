# 🔄 Guide de Migration - Architecture Portable

## 📋 Contexte

Ce guide explique comment migrer d'une installation existante vers la nouvelle architecture portable basée sur des templates.

## 🎯 Objectif

Passer d'une configuration avec **chemins hardcodés** à une configuration **100% portable** basée sur des variables d'environnement.

## ⚡ Migration rapide (5 minutes)

### 1. Arrêter tous les services
```bash
./scripts/symfony-orchestrator.sh stop all
```

### 2. Créer le fichier `.env.podman`
```bash
cp .env.podman.example .env.podman
```

### 3. Configurer le chemin du projet
```bash
# Éditer .env.podman
nano .env.podman

# Modifier cette ligne avec le chemin ACTUEL de votre projet
PROJECT_ROOT=/home/sam/Bureau/DEV/smfn73-multi
```

**⚠️ IMPORTANT :** Utilisez le chemin **absolu** de votre projet.

Pour obtenir le chemin absolu :
```bash
pwd
# Copier le résultat dans PROJECT_ROOT
```

### 4. Générer les nouvelles configurations
```bash
./scripts/generate-pod-configs.sh
```

Vous devriez voir :
```
[generate-pod-configs][success] ✅ mariadb/pod.yml généré
[generate-pod-configs][success] ✅ redis/pod.yml généré
[generate-pod-configs][success] ✅ web/pod.yml généré
[generate-pod-configs][success] ✅ node/pod.yml généré
[generate-pod-configs][success] ✅ phpmyadmin/pod.yml généré
```

### 5. Redémarrer les services
```bash
./scripts/symfony-orchestrator.sh dev
```

### 6. Vérifier que tout fonctionne
```bash
# Vérifier les pods
podman pod ps

# Vérifier les conteneurs
podman ps

# Tester l'accès web
curl http://localhost:6900
```

## ✅ Vérification de la migration

### Vérifier qu'il n'y a plus de chemins hardcodés
```bash
# Cette commande ne devrait retourner AUCUN résultat
grep -r "/home/sam/Bureau/dev/production" pods/*/pod.yml

# Si des résultats apparaissent, régénérez :
./scripts/generate-pod-configs.sh
```

### Vérifier que les variables sont remplacées
```bash
# Vérifier un fichier généré
cat pods/mariadb/pod.yml | grep "path:"

# Devrait afficher quelque chose comme :
# path: /home/sam/Bureau/DEV/smfn73-multi/pods/mariadb/data
# (et PAS : path: ${PROJECT_ROOT}/pods/mariadb/data)
```

## 🔧 Configuration avancée

### Personnaliser les ports

Si les ports par défaut sont déjà utilisés :

```bash
# Éditer .env.podman
nano .env.podman

# Modifier les ports
APACHE_PORT=8080      # Au lieu de 6900
MARIADB_PORT=3307     # Au lieu de 6909
REDIS_PORT=6380       # Au lieu de 6379
NODE_PORT=5174        # Au lieu de 6904
```

Puis régénérer :
```bash
./scripts/generate-pod-configs.sh
./scripts/symfony-orchestrator.sh dev
```

### Personnaliser les images Docker

```bash
# Éditer .env.podman
nano .env.podman

# Modifier les images
IMAGE_PHP=localhost/mon-php-custom:8.3
IMAGE_MARIADB=docker.io/library/mariadb:11.0
IMAGE_NODE=docker.io/library/node:21-alpine
```

## 🚨 Problèmes courants

### Erreur : "Variables manquantes"
```
[generate-pod-configs][error] Variables manquantes dans .env.podman:
[generate-pod-configs][error]   - PROJECT_ROOT
```

**Solution :**
```bash
# Vérifier que PROJECT_ROOT est défini
grep PROJECT_ROOT .env.podman

# Si vide ou absent, ajouter :
echo "PROJECT_ROOT=$(pwd)" >> .env.podman
```

### Erreur : "Template non trouvé"
```
[generate-pod-configs][warning] Template non trouvé: pods/mariadb/pod.yml.template
```

**Solution :**
```bash
# Vérifier que les templates existent
ls -la pods/*/pod.yml.template

# Si absents, ils doivent être dans le dépôt Git
git status
git pull
```

### Les services ne démarrent pas
```bash
# Vérifier les logs
podman pod ps
podman logs <nom-du-conteneur>

# Vérifier les chemins dans les configurations générées
cat pods/mariadb/pod.yml | grep "path:"

# Régénérer si nécessaire
./scripts/generate-pod-configs.sh
```

## 📦 Sauvegarde avant migration

Si vous voulez être prudent :

```bash
# Sauvegarder les anciennes configurations
mkdir -p backup-configs
cp pods/*/pod.yml backup-configs/

# Sauvegarder les données
tar -czf backup-data-$(date +%Y%m%d).tar.gz pods/*/data/

# En cas de problème, restaurer :
cp backup-configs/*.yml pods/*/
```

## 🎉 Avantages après migration

### ✅ Portabilité
```bash
# Vous pouvez maintenant déplacer le projet n'importe où
mv /home/sam/Bureau/DEV/smfn73-multi /tmp/nouveau-chemin
cd /tmp/nouveau-chemin

# Reconfigurer en 2 commandes
sed -i "s|PROJECT_ROOT=.*|PROJECT_ROOT=/tmp/nouveau-chemin|" .env.podman
./scripts/generate-pod-configs.sh

# Et c'est tout !
```

### ✅ Déploiement multi-environnements
```bash
# Développement
PROJECT_ROOT=/home/dev/smfn73-multi
APACHE_PORT=6900

# Production
PROJECT_ROOT=/var/www/smfn73-multi
APACHE_PORT=80
```

### ✅ Maintenance simplifiée
```bash
# Modifier une configuration
nano pods/mariadb/pod.yml.template

# Régénérer pour tous les environnements
./scripts/generate-pod-configs.sh
```

## 📚 Prochaines étapes

1. ✅ Lire [PORTABILITY.md](PORTABILITY.md) pour comprendre l'architecture
2. ✅ Lire [INSTALLATION_PODMAN.md](INSTALLATION_PODMAN.md) pour l'installation complète
3. ✅ Configurer votre `.env.podman` selon vos besoins
4. ✅ Tester le déploiement sur un autre chemin

## 🆘 Besoin d'aide ?

Si vous rencontrez des problèmes :

1. Vérifiez les logs : `podman logs <conteneur>`
2. Vérifiez les configurations générées : `cat pods/*/pod.yml`
3. Vérifiez les variables : `cat .env.podman`
4. Consultez [PORTABILITY.md](PORTABILITY.md) pour plus de détails

---

**✅ Migration terminée ! Votre projet est maintenant 100% portable.**
