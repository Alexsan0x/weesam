<?php
require_once 'config.php';

try {
    $pdo = new PDO(
        "pgsql:host=$db_host;port=$db_port;dbname=$db_name;sslmode=require",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to database.\n";

    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'user'");
        echo "Users table updated with role column.\n";
    } catch (PDOException $e) {
        echo "Role column note: " . $e->getMessage() . "\n";
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS places (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        name_ar VARCHAR(200),
        city VARCHAR(100),
        city_ar VARCHAR(100),
        category VARCHAR(50),
        category_ar VARCHAR(50),
        lat DOUBLE PRECISION,
        lng DOUBLE PRECISION,
        year_established INT,
        era VARCHAR(100),
        era_ar VARCHAR(100),
        image TEXT,
        description TEXT,
        description_ar TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Places table created.\n";

    $jsonPath = __DIR__ . '/data/places.json';
    $places = json_decode(file_get_contents($jsonPath), true);

    if (!$places) {
        echo "Could not read places.json\n";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO places (id, name, name_ar, city, city_ar, category, category_ar, lat, lng, year_established, era, era_ar, image, description, description_ar) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT (id) DO UPDATE SET 
            name = EXCLUDED.name, name_ar = EXCLUDED.name_ar, city = EXCLUDED.city, 
            city_ar = EXCLUDED.city_ar, category = EXCLUDED.category, category_ar = EXCLUDED.category_ar,
            lat = EXCLUDED.lat, lng = EXCLUDED.lng, year_established = EXCLUDED.year_established,
            era = EXCLUDED.era, era_ar = EXCLUDED.era_ar, image = EXCLUDED.image, 
            description = EXCLUDED.description, description_ar = EXCLUDED.description_ar");

    foreach ($places as $p) {
        $stmt->execute([
            $p['id'],
            $p['name'],
            $p['name_ar'] ?? null,
            $p['city'] ?? null,
            $p['city_ar'] ?? null,
            $p['category'] ?? null,
            $p['category_ar'] ?? null,
            $p['lat'] ?? null,
            $p['lng'] ?? null,
            $p['year_established'] ?? null,
            $p['era'] ?? null,
            $p['era_ar'] ?? null,
            $p['image'] ?? null,
            $p['description'] ?? null,
            $p['description_ar'] ?? null,
        ]);
        echo "  Inserted/Updated: " . $p['name'] . "\n";
    }

    echo "\nPlaces migrated to database successfully!\n";

    $adminCheck = $pdo->prepare("SELECT id FROM users WHERE role = 'admin'");
    $adminCheck->execute();
    if (!$adminCheck->fetch()) {
        $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')")
            ->execute(['Admin', 'admin@dalili.jo', $adminHash]);
        echo "Default admin created: admin@dalili.jo / admin123\n";
    } else {
        echo "Admin user already exists.\n";
    }

    echo "\nMigration complete!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
