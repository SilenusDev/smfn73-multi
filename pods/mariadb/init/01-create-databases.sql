-- pods/mariadb/init/01-create-databases.sql
-- Script d'initialisation des bases de données multi-sites

-- Création de la base SLNS
CREATE DATABASE IF NOT EXISTS `slns_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Création de la base NSDM
CREATE DATABASE IF NOT EXISTS `nsdm_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Permissions pour l'utilisateur Symfony
GRANT ALL PRIVILEGES ON `slns_db`.* TO 'symfony'@'%';
GRANT ALL PRIVILEGES ON `nsdm_db`.* TO 'symfony'@'%';

-- Appliquer les permissions
FLUSH PRIVILEGES;

-- Logs
SELECT 'Bases de données créées: slns_db, nsdm_db' AS Status;
