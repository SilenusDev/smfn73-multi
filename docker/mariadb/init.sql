-- Création des 2 bases de données pour le multisite
CREATE DATABASE IF NOT EXISTS slns_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS nsdm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Attribution des droits à l'utilisateur symfony (créé automatiquement par MariaDB)
GRANT ALL PRIVILEGES ON slns_db.* TO 'symfony'@'%';
GRANT ALL PRIVILEGES ON nsdm_db.* TO 'symfony'@'%';

-- Flush des privilèges
FLUSH PRIVILEGES;

-- Message de confirmation
SELECT 'Bases slns_db et nsdm_db créées avec succès!' AS message;