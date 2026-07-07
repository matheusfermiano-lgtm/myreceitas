-- Schema SQL para Site de Receitas
-- Banco de dados com todas as tabelas
/*
DROP DATABASE myreceitas_db;
Somente em casos estremamente necessarios, nunca rodar quando estiver com raiva
*/

CREATE DATABASE IF NOT EXISTS myreceitas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE myreceitas_db;

-- 1. Tabela de Usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    photo VARCHAR(255) DEFAULT 'default_user.png',
    is_active BOOLEAN DEFAULT TRUE,
    login_attempts INT DEFAULT 0,
    last_login DATETIME,
    reset_token VARCHAR(255),
    reset_token_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Atualização da tabela de Usuários

-- 2. Tabela de Chefes
CREATE TABLE chef (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    description TEXT,
    phone VARCHAR(20),
    address TEXT,
    professional_experience TEXT,
    services_offered TEXT,
    region_operation VARCHAR(255),
    photo VARCHAR(255) DEFAULT 'default_chef.png',


    is_active BOOLEAN DEFAULT TRUE,
    login_attempts INT DEFAULT 0,
    last_login DATETIME,
    reset_token VARCHAR(255),
    reset_token_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabela de Restaurantes
CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    location_map_link VARCHAR(500),
    description TEXT,
    photo VARCHAR(255) DEFAULT 'default_restaurant.png',
    opening_hours VARCHAR(255),
    services_offered TEXT,
    menu_description TEXT,



    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabela de Receitas
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    ingredients TEXT NOT NULL,
    description TEXT,
    preparation_time INT,
    category VARCHAR(50),
    price DECIMAL(10, 2) DEFAULT 0.00,
    is_public BOOLEAN DEFAULT TRUE,
    
    user_id INT NULL,
    chef_id INT NULL,
    restaurant_id INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME,

    CONSTRAINT fk_recipe_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_recipe_chef FOREIGN KEY (chef_id) REFERENCES chef(id) ON DELETE CASCADE,
    CONSTRAINT fk_recipe_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabela de Favoritos
CREATE TABLE user_favorites (
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, recipe_id),
    CONSTRAINT fk_fav_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_fav_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabela de Vínculo: Chefes e Restaurantes
CREATE TABLE restaurant_chefs (
    restaurant_id INT NOT NULL,
    chef_id INT NOT NULL,
    role VARCHAR(100) DEFAULT 'Chef Principal',
    hired_at DATE,
    PRIMARY KEY (restaurant_id, chef_id),
    CONSTRAINT fk_rel_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    CONSTRAINT fk_rel_chef FOREIGN KEY (chef_id) REFERENCES chef(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Avaliações de Restaurantes
CREATE TABLE restaurant_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rev_rest FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    CONSTRAINT fk_rev_user_rest FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Avaliações de Chefes
CREATE TABLE chef_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chef_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rev_chef FOREIGN KEY (chef_id) REFERENCES chef(id) ON DELETE CASCADE,
    CONSTRAINT fk_rev_user_chef FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Avaliações de Receitas
CREATE TABLE recipe_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rev_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    CONSTRAINT fk_rev_user_rec FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    restaurant_id INT NULL,
    chef_id INT NULL,
    booking_date DATETIME NOT NULL,
    service_type VARCHAR(100), -- Ex: "Almoço em casa" ou "Reserva de mesa"
    status ENUM('pendente', 'confirmado', 'cancelado') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_book_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_book_rest FOREIGN KEY (restaurant_id) REFERENCES restaurants(id),
    CONSTRAINT fk_book_chef FOREIGN KEY (chef_id) REFERENCES chef(id)
) ENGINE=InnoDB;

CREATE TABLE recipe_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recipe_id INT NOT NULL,
    CONSTRAINT fk_like_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_like_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE(user_id, recipe_id) -- Impede curtir a mesma receita duas vezes
);



SELECT * FROM recipes;
SELECT * FROM users;
SELECT * FROM restaurants;
SELECT * FROM chef;

/*
SELECT r.name, AVG(rv.rating) as media_estrelas
FROM restaurants r
JOIN restaurant_reviews rv ON r.id = rv.restaurant_id
GROUP BY r.id
ORDER BY media_estrelas DESC
LIMIT 10; -- Pega os 10 melhores
*/