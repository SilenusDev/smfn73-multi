# Configuration des hôtes locaux pour multi-sites

## 🌐 Domaines locaux

Le projet utilise deux sites distincts :
- **Silenus** : `silenus.local`
- **Insidiome** : `insidiome.local`

## 📝 Configuration du fichier /etc/hosts

### Ajouter les entrées suivantes :

```bash
sudo nano /etc/hosts
```

Ajoutez ces lignes :

```
127.0.0.1   silenus.local www.silenus.local
127.0.0.1   insidiome.local www.insidiome.local
```

### Commande rapide :

```bash
echo "127.0.0.1   silenus.local www.silenus.local" | sudo tee -a /etc/hosts
echo "127.0.0.1   insidiome.local www.insidiome.local" | sudo tee -a /etc/hosts
```

## 🔄 Redémarrer Apache

Après avoir modifié `/etc/hosts`, redémarrez le pod Apache :

```bash
./scripts/symfony-orchestrator.sh stop apache
./scripts/symfony-orchestrator.sh apache
```

## 🌐 Accès aux sites

Une fois configuré, vous pouvez accéder aux sites via :

| Site | URL | Base de données |
|------|-----|-----------------|
| **Silenus** | http://silenus.local:6900 | `slns_db` |
| **Insidiome** | http://insidiome.local:6900 | `nsdm_db` |
| **Localhost** | http://localhost:6900 | `slns_db` (par défaut) |

## 🔍 Vérification

### Tester la résolution DNS :

```bash
ping silenus.local
ping insidiome.local
```

### Tester les sites :

```bash
curl -I http://silenus.local:6900
curl -I http://insidiome.local:6900
```

### Voir les logs Apache :

```bash
# Logs Silenus
podman exec symfony-apache-pod-symfony-apache tail -f /usr/local/apache2/logs/silenus_access.log

# Logs Insidiome
podman exec symfony-apache-pod-symfony-apache tail -f /usr/local/apache2/logs/insidiome_access.log
```

## 🐛 Dépannage

### Si les domaines ne fonctionnent pas :

1. Vérifier que `/etc/hosts` est bien modifié :
   ```bash
   cat /etc/hosts | grep -E "silenus|insidiome"
   ```

2. Vider le cache DNS :
   ```bash
   sudo systemd-resolve --flush-caches
   ```

3. Redémarrer Apache :
   ```bash
   ./scripts/symfony-orchestrator.sh stop apache
   ./scripts/symfony-orchestrator.sh apache
   ```

4. Vérifier la configuration Apache :
   ```bash
   podman exec symfony-apache-pod-symfony-apache httpd -t
   ```

## 📋 Résolution de sites Symfony

Le `SiteResolver` détecte automatiquement le site via :
1. **Domaine** : `silenus.local` → Silenus, `insidiome.local` → Insidiome
2. **Path** (fallback) : `/slns/*` → Silenus, `/nsdm/*` → Insidiome
3. **Défaut** : Silenus si aucune correspondance
