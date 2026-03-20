# 📘 GUIDE DE VISUALISATION — ActuSénégal
## Projet Backend PHP/MySQL — ESP Dakar

---

## 🗂 STRUCTURE DU PROJET LIVRÉ

```
actualite/
├── config/
│   ├── db.php              ← Connexion PDO (à configurer)
│   └── db.exemple.php      ← Version GitHub sans mot de passe
├── includes/
│   ├── entete.php          ← session_start() + <head>
│   ├── menu.php            ← Navigation dynamique
│   └── pied.php            ← Footer + </body></html>
├── css/
│   └── style.css           ← Tout le design
├── js/
│   └── validation.js       ← Validation JS tous formulaires
├── articles/
│   ├── ajouter.php
│   ├── modifier.php
│   └── supprimer.php
├── categories/
│   ├── liste.php
│   ├── ajouter.php
│   ├── modifier.php
│   └── supprimer.php
├── utilisateurs/
│   ├── liste.php
│   ├── ajouter.php
│   ├── modifier.php
│   └── supprimer.php
├── uploads/                ← Images uploadées
├── accueil.php
├── article.php
├── categorie.php
├── connexion.php
├── deconnexion.php
├── actualite_db.sql        ← Script SQL complet
└── .gitignore
```

---

## 🪟 INSTALLATION SUR WINDOWS (WAMP)

### Étape 1 — Installer WAMP

1. Télécharger **WAMP Server** sur → https://www.wampserver.com
2. Installer avec les options par défaut (tout cocher si demandé)
3. Lancer WAMP → icône en bas à droite de l'écran
4. Attendre que l'icône devienne **VERTE** ✅
   - Rouge = problème de port (souvent Skype ou IIS qui bloque le port 80)
   - Si rouge : clic droit sur l'icône → "Use a port other than 80" → choisir 8080

### Étape 2 — Placer le projet

```
Copier le dossier  actualite/
vers               C:\wamp64\www\actualite\
```

Résultat attendu :
```
C:\wamp64\www\actualite\accueil.php   ← doit exister
C:\wamp64\www\actualite\config\db.php
...
```

### Étape 3 — Créer la base de données

1. Ouvrir le navigateur → http://localhost/phpmyadmin
2. Connexion : login `root`, mot de passe **vide** (par défaut WAMP)
3. Cliquer **"SQL"** dans la barre du haut
4. Coller le contenu du fichier `actualite_db.sql`
5. Cliquer **"Exécuter"**
6. Vérifier que la base `actualite_db` apparaît dans la liste à gauche

### Étape 4 — Configurer la connexion

Ouvrir `C:\wamp64\www\actualite\config\db.php` dans un éditeur :

```php
$host     = 'localhost';
$dbname   = 'actualite_db';
$username = 'root';
$password = '';          // Vide par défaut sur WAMP
```

> ⚠️ Si vous avez défini un mot de passe MySQL, mettez-le ici.

### Étape 5 — Tester

Ouvrir le navigateur → **http://localhost/actualite/accueil.php**

---

## 🐧 INSTALLATION SUR KALI LINUX (LAMP)

### Étape 1 — Installer Apache, PHP et MySQL

Ouvrir un terminal et taper :

```bash
sudo apt update

# Installer Apache
sudo apt install apache2 -y

# Installer PHP et les extensions nécessaires
sudo apt install php php-mysql php-mbstring php-gd php-fileinfo -y

# Installer MySQL (MariaDB)
sudo apt install mariadb-server -y

# Démarrer les services
sudo systemctl start apache2
sudo systemctl start mariadb

# Les activer au démarrage (optionnel)
sudo systemctl enable apache2
sudo systemctl enable mariadb
```

### Étape 2 — Sécuriser MySQL

```bash
sudo mysql_secure_installation
```

Répondre aux questions :
- Mot de passe root actuel → **Entrée** (vide)
- Définir un nouveau mot de passe root → **oui**, choisir ex: `root123`
- Supprimer utilisateurs anonymes → **oui**
- Interdire connexion root à distance → **oui**
- Supprimer la base test → **oui**
- Recharger les privilèges → **oui**

> 📝 Notez ce mot de passe, vous en aurez besoin dans `config/db.php`

### Étape 3 — Placer le projet

```bash
# Copier le projet dans le répertoire web Apache
sudo cp -r /chemin/vers/actualite /var/www/html/actualite

# Donner les bonnes permissions
sudo chown -R www-data:www-data /var/www/html/actualite
sudo chmod -R 755 /var/www/html/actualite
sudo chmod -R 775 /var/www/html/actualite/uploads
```

> Si vous avez téléchargé le ZIP, décompressez-le d'abord :
> ```bash
> unzip actualite.zip -d /var/www/html/
> ```

### Étape 4 — Créer la base de données

```bash
# Se connecter à MySQL
sudo mysql -u root -p
# (saisir le mot de passe défini à l'étape 2)
```

Dans le prompt MySQL :
```sql
source /var/www/html/actualite/actualite_db.sql
exit;
```

OU depuis le terminal sans entrer dans MySQL :
```bash
sudo mysql -u root -p actualite_db < /var/www/html/actualite/actualite_db.sql
```

> Si la base n'existe pas encore, le script SQL la crée automatiquement
> grâce à la ligne `CREATE DATABASE IF NOT EXISTS actualite_db;`

### Étape 5 — Configurer la connexion

```bash
sudo nano /var/www/html/actualite/config/db.php
```

Modifier :
```php
$username = 'root';
$password = 'root123';  // Le mot de passe que vous avez choisi
```

Sauvegarder : `Ctrl+O` → Entrée → `Ctrl+X`

### Étape 6 — Vérifier Apache

```bash
# Tester la configuration Apache
sudo apache2ctl configtest

# Redémarrer Apache
sudo systemctl restart apache2
```

### Étape 7 — Tester

Ouvrir Firefox sur Kali → **http://localhost/actualite/accueil.php**

---

## 🔑 COMPTES DE TEST (insérés par le script SQL)

| Profil | Login | Mot de passe | Accès |
|--------|-------|--------------|-------|
| Admin  | `admin` | `password` | Tout (articles + catégories + utilisateurs) |
| Éditeur | `editeur` | `password` | Articles + catégories uniquement |

> ⚠️ Ces comptes sont pour les tests uniquement. **Changez les mots de passe** avant toute mise en ligne !

---

## 🌐 PAGES DISPONIBLES

| URL | Accès |
|-----|-------|
| `http://localhost/actualite/accueil.php` | Public |
| `http://localhost/actualite/connexion.php` | Public |
| `http://localhost/actualite/article.php?id=1` | Public |
| `http://localhost/actualite/categorie.php?id=1` | Public |
| `http://localhost/actualite/articles/ajouter.php` | Éditeur + Admin |
| `http://localhost/actualite/categories/liste.php` | Éditeur + Admin |
| `http://localhost/actualite/utilisateurs/liste.php` | Admin uniquement |

---

## ❗ PROBLÈMES FRÉQUENTS ET SOLUTIONS

### ❌ "Erreur de connexion à la base de données"
- Vérifier que MySQL/MariaDB est démarré
- Vérifier le mot de passe dans `config/db.php`
- Vérifier que la base `actualite_db` existe dans phpMyAdmin

### ❌ Page blanche ou erreur 500
```bash
# Sur Kali, voir les logs d'erreur Apache :
sudo tail -f /var/log/apache2/error.log

# Activer l'affichage des erreurs PHP (pour le dev uniquement) :
sudo nano /etc/php/*/apache2/php.ini
# Chercher "display_errors" et mettre "On"
sudo systemctl restart apache2
```

### ❌ "Cannot send session cookie – headers already sent"
→ Il y a un espace ou caractère avant `<?php` dans `entete.php`.
   Ouvrir le fichier et s'assurer qu'il commence **exactement** par `<?php`

### ❌ Images non affichées après upload (Kali)
```bash
sudo chmod 775 /var/www/html/actualite/uploads
sudo chown www-data:www-data /var/www/html/actualite/uploads
```

### ❌ Port 80 déjà utilisé (Windows)
→ Changer le port WAMP : clic droit sur l'icône WAMP → "Apache" → "httpd.conf"
   Changer `Listen 80` en `Listen 8080`
→ Accéder via : http://localhost:8080/actualite/accueil.php

### ❌ "Access denied for user root" (Kali)
```bash
sudo mysql -u root
ALTER USER 'root'@'localhost' IDENTIFIED BY 'votre_nouveau_mdp';
FLUSH PRIVILEGES;
EXIT;
```

---

## ✅ CHECKLIST DE VÉRIFICATION FINALE

- [ ] La page d'accueil affiche les 5 articles de test avec la grille
- [ ] Les boutons Précédent/Suivant et numéros de page fonctionnent
- [ ] Cliquer sur un titre ouvre la page de détail
- [ ] Les liens de catégories filtrent les articles
- [ ] La connexion avec `admin` / `password` fonctionne
- [ ] Après connexion, le menu affiche le prénom et le rôle
- [ ] Un éditeur peut ajouter / modifier / supprimer un article
- [ ] Un admin voit le menu "Utilisateurs"
- [ ] Un éditeur **ne voit pas** le menu "Utilisateurs"
- [ ] Accéder à `utilisateurs/liste.php` sans être admin redirige
- [ ] La déconnexion fonctionne et redirige vers l'accueil
- [ ] La validation JavaScript bloque les formulaires vides
- [ ] Les messages d'erreur PHP s'affichent si JS désactivé

---

## 📚 RÉCAPITULATIF DES TECHNOLOGIES UTILISÉES

| Technologie | Usage | Exigence |
|-------------|-------|----------|
| PHP 8.x | Toute la logique serveur | ✅ Obligatoire |
| PDO + prepare() | Toutes les requêtes SQL | ✅ Obligatoire |
| Sessions PHP | Authentification | ✅ Obligatoire |
| htmlspecialchars() | Protection XSS | ✅ Obligatoire |
| password_hash/verify | Sécurité mots de passe | ✅ Obligatoire |
| JavaScript ES6 | Validation formulaires | ✅ Obligatoire |
| MySQL / MariaDB | Base de données | ✅ Obligatoire |
| CSS3 + Variables | Design moderne | ✅ Inclus |
| Upload d'image | Bonus | ✅ Inclus |
| Pagination numérotée | Bonus | ✅ Inclus |
