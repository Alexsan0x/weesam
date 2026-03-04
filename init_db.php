<?php
try {
    $pdo = new PDO(
        'pgsql:host=ep-calm-lab-alag0l8u-pooler.c-3.eu-central-1.aws.neon.tech;port=5432;dbname=neondb;sslmode=require',
        'neondb_owner',
        'npg_PrOcKE2Za9Gh',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to Neon PostgreSQL!\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Users table created.\n";

    $pdo->exec("CREATE TABLE IF NOT EXISTS favorites (
        id SERIAL PRIMARY KEY,
        user_id INT NOT NULL,
        place_id VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT unique_fav UNIQUE (user_id, place_id)
    )");
    echo "Favorites table created.\n";
    echo "Database setup complete!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
