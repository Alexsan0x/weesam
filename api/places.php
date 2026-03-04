<?php
require_once '../config.php';
require_once '../db.php';

header('Content-Type: application/json');

$category = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;

$places = searchPlaces($search, $category);

if ($places === false) {
    echo json_encode(['success' => false, 'message' => 'Could not load places data.']);
    exit;
}

echo json_encode(['success' => true, 'places' => array_values($places)]);
