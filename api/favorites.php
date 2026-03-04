<?php
require_once '../config.php';
require_once '../db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'list') {
    $favorites = getUserFavorites($userId);
    echo json_encode(['success' => true, 'favorites' => $favorites]);
    exit;
}

if ($action === 'add') {
    $placeId = sanitize($_POST['place_id'] ?? '');
    if (empty($placeId)) {
        echo json_encode(['success' => false, 'message' => 'Place ID is required.']);
        exit;
    }

    $result = addFavorite($userId, $placeId);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Added to favorites.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'This place is already in your favorites.']);
    }
    exit;
}

if ($action === 'remove') {
    $placeId = sanitize($_POST['place_id'] ?? '');
    if (empty($placeId)) {
        echo json_encode(['success' => false, 'message' => 'Place ID is required.']);
        exit;
    }

    removeFavorite($userId, $placeId);
    echo json_encode(['success' => true, 'message' => 'Removed from favorites.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
