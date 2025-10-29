<?php
session_start();
require_once __DIR__ . '/model/connectdb.php';
require_once __DIR__ . '/model/wishlist.php';

header('Content-Type: application/json');

if (!isset($_SESSION['iduser']) || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
    echo json_encode(['success' => true, 'count' => 0]);
    exit;
}

$userId = intval($_SESSION['iduser']);
$count = wishlist_count($userId);

echo json_encode(['success' => true, 'count' => (int)$count]);
