# Dépannage - Problèmes courants

## 🎨 Les styles CSS ne s'affichent pas

### Symptômes
- La page s'affiche sans styles
- Fond blanc au lieu du thème dark
- Pas de couleurs Tailwind

### Solution rapide
```bash
make fix-assets
```

Puis rafraîchir le navigateur avec **Ctrl+Shift+R**

### Diagnostic détaillé
```bash
make check
```

### Causes possibles

#### 1. Fichiers Yarn résiduels
Le projet utilise npm, pas yarn. Si des fichiers yarn existent :
```bash
rm -rf .yarn yarn.lock .yarnrc.yml
make fix-assets
```

#### 2. Fichiers .js doublons
Des fichiers `app.js` et `bootstrap.js` peuvent entrer en conflit avec les `.ts` :
```bash
rm -f assets/app.js assets/bootstrap.js
make build
```

#### 3. Cache navigateur
```bash
# Dans le navigateur : Ctrl+Shift+R
# Ou vider le cache manuellement
```

#### 4. Cache Symfony
```bash
make cache-clear
```

#### 5. Conteneur node pas redémarré
```bash
docker compose restart node
docker compose logs node
```

---

## 🔴 Erreur "Module not found: lucide"

### Symptômes
```
Module build failed: Module not found:
"./assets/app.js" contains a reference to the file "lucide".
```

### Solution
```bash
make fix-assets
```

### Cause
Le conteneur node utilise encore yarn au lieu de npm, ou les dépendances ne sont pas installées.

---

## 🔴 Erreur "Module not found: ./assets/app.js"

### Symptômes
```
Module build failed: Module not found:
"undefined" contains a reference to the file "./assets/app.js".
```

### Solution
```bash
rm -f assets/app.js assets/bootstrap.js
make fix-assets
```

### Cause
Webpack cherche `app.js` mais le projet utilise `app.ts`. Les anciens fichiers `.js` doivent être supprimés.

---

## 🔴 Erreur TypeScript "no output"

### Symptômes
```
Error: TypeScript emitted no output for /app/assets/app.ts.
```

### Solution
Vérifier `tsconfig.json` - `"noEmit"` doit être absent ou `false` :
```json
{
  "compilerOptions": {
    // "noEmit": true,  ❌ À SUPPRIMER
  }
}
```

Puis :
```bash
make build
```

---

## 🔴 Les conteneurs ne démarrent pas

### Vérifier l'état
```bash
docker compose ps
docker compose logs
```

### Redémarrer
```bash
docker compose down
docker compose up -d
```

### Reset complet
```bash
make clean
./install.sh
make start
```

---

## 🔴 Erreur de permissions

### Symptômes
```
Permission denied
```

### Solution
```bash
sudo chown -R $USER:$USER .
```

---

## 🔴 Port déjà utilisé

### Symptômes
```
Bind for 0.0.0.0:8000 failed: port is already allocated
```

### Solution
Modifier les ports dans `.env` :
```bash
WEB_PORT=8001
DB_PORT_EXTERNAL=3307
PHPMYADMIN_PORT=8082
```

Puis :
```bash
docker compose down
docker compose up -d
```

---

## 🔴 Base de données inaccessible

### Vérifier
```bash
docker compose ps db
docker compose logs db
```

### Recréer
```bash
make db-reset
```

---

## 📋 Commandes de diagnostic

```bash
make check              # Vérification complète
make logs               # Voir tous les logs
docker compose ps       # État des conteneurs
docker compose logs node    # Logs du build assets
docker compose logs web     # Logs Symfony
docker compose logs db      # Logs base de données
```

---

## 🆘 Reset complet du projet

Si rien ne fonctionne :

```bash
# 1. Tout arrêter et nettoyer
docker compose down -v
rm -rf node_modules vendor var/cache/* var/log/*
rm -rf .yarn yarn.lock .yarnrc.yml
rm -f assets/app.js assets/bootstrap.js

# 2. Réinstaller
./install.sh

# 3. Démarrer
make start

# 4. Vérifier
make check
```

---

## 📞 Aide supplémentaire

- **Documentation** : [QUICKSTART.md](QUICKSTART.md)
- **Stack technique** : [TECH_STACK.md](TECH_STACK.md)
- **Installation** : [INSTALL.md](INSTALL.md)
- **Changelog** : [CHANGELOG_TYPESCRIPT.md](CHANGELOG_TYPESCRIPT.md)
