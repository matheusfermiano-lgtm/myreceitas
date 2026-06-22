-- Schema SQL para Sistema Petshop
-- Banco de dados com todas as tabelas

CREATE DATABASE IF NOT EXISTS myreceitas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myreceitas_db;

-- Tabela de Usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'chief', 'client') DEFAULT 'client',
    active BOOLEAN DEFAULT true,
    login_attempts INT DEFAULT 0,
    last_login DATETIME,
    reset_token VARCHAR(255),
    reset_token_expiry DATETIME,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Receitas
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_recipe VARCHAR(150) NOT NULL,
    ingredients VARCHAR(150) NOT NULL,
    description VARCHAR(1000) NOT NULL,
    preparation_time TIME NOT NULL,
    type_recipe VARCHAR(25) NOT NULL,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'chief', 'client') DEFAULT 'client',
    active BOOLEAN DEFAULT true,
    login_attempts INT DEFAULT 0,
    last_login DATETIME,
    reset_token VARCHAR(255),
    reset_token_expiry DATETIME,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
																													



