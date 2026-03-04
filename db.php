<?php

require_once 'config.php';

function getDB() {
    static $pdo = null;

    if ($pdo === null) {
        global $db_host, $db_port, $db_name, $db_user, $db_pass;
        
        if (!extension_loaded('pdo_pgsql')) {
            die("PDO PostgreSQL extension is not loaded. Loaded extensions: " . implode(', ', get_loaded_extensions()));
        }
        
        try {
            $pdo = new PDO(
                "pgsql:host=$db_host;port=$db_port;dbname=$db_name;sslmode=require",
                $db_user,
                $db_pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}

function findUserByEmail($email) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function findUserById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createUser($name, $email, $password) {
    $db = getDB();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    return $stmt->execute([$name, $email, $hash]);
}

function updateUser($id, $name, $email) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    return $stmt->execute([$name, $email, $id]);
}

function updateUserPassword($id, $newPassword) {
    $db = getDB();
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    return $stmt->execute([$hash, $id]);
}

function getAllUsers() {
    $db = getDB();
    $stmt = $db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function updateUserRole($id, $role) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
    return $stmt->execute([$role, $id]);
}

function deleteUser($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

// Places functions
function getAllPlaces() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM places ORDER BY name ASC");
    return $stmt->fetchAll();
}

function getPlaceById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM places WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createPlace($data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO places (id, name, name_ar, city, city_ar, category, category_ar, lat, lng, year_established, era, era_ar, image, description, description_ar) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['id'], $data['name'], $data['name_ar'] ?? null,
        $data['city'] ?? null, $data['city_ar'] ?? null,
        $data['category'] ?? null, $data['category_ar'] ?? null,
        $data['lat'] ?? null, $data['lng'] ?? null,
        $data['year_established'] ?? null, $data['era'] ?? null, $data['era_ar'] ?? null,
        $data['image'] ?? null, $data['description'] ?? null, $data['description_ar'] ?? null
    ]);
}

function updatePlace($id, $data) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE places SET name=?, name_ar=?, city=?, city_ar=?, category=?, category_ar=?, lat=?, lng=?, year_established=?, era=?, era_ar=?, image=?, description=?, description_ar=? WHERE id=?");
    return $stmt->execute([
        $data['name'], $data['name_ar'] ?? null,
        $data['city'] ?? null, $data['city_ar'] ?? null,
        $data['category'] ?? null, $data['category_ar'] ?? null,
        $data['lat'] ?? null, $data['lng'] ?? null,
        $data['year_established'] ?? null, $data['era'] ?? null, $data['era_ar'] ?? null,
        $data['image'] ?? null, $data['description'] ?? null, $data['description_ar'] ?? null,
        $id
    ]);
}

function deletePlace($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM places WHERE id = ?");
    return $stmt->execute([$id]);
}

function searchPlaces($search = null, $category = null) {
    $db = getDB();
    $where = [];
    $params = [];

    if ($category && $category !== 'all') {
        $where[] = "LOWER(category) = LOWER(?)";
        $params[] = $category;
    }

    if ($search) {
        $where[] = "(LOWER(name) LIKE ? OR LOWER(city) LIKE ? OR LOWER(description) LIKE ? OR LOWER(name_ar) LIKE ?)";
        $s = '%' . strtolower($search) . '%';
        $params = array_merge($params, [$s, $s, $s, $s]);
    }

    $sql = "SELECT * FROM places";
    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY name ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Favorites
function getUserFavorites($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT place_id FROM favorites WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function addFavorite($userId, $placeId) {
    $db = getDB();
    $check = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND place_id = ?");
    $check->execute([$userId, $placeId]);
    if ($check->fetch()) {
        return false;
    }
    $stmt = $db->prepare("INSERT INTO favorites (user_id, place_id) VALUES (?, ?)");
    return $stmt->execute([$userId, $placeId]);
}

function removeFavorite($userId, $placeId) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND place_id = ?");
    return $stmt->execute([$userId, $placeId]);
}
