-- 1. Création de la base de données
CREATE DATABASE IF NOT EXISTS examenphp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE examenphp;

-- 2. Création de la table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    role JSON, -- Stocke les rôles (ex: ["ROLE_USER", "ROLE_ADMIN"])
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Création de la table des recettes (créations locales)
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    ingredients JSON, -- Stocke la liste des ingrédients
    instructions TEXT,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    note TEXT NULL,
    image_url VARCHAR(255) NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Création de la table des favoris (références API externe)
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    id_api VARCHAR(50) NOT NULL, -- ID provenant de l'API externe
    titre VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;