<?php
session_start();
require_once __DIR__ . '/model/connectdb.php';
require_once __DIR__ . '/model/wishlist.php';

header('Content-Type: application/json');

if (!isset($_SESSION['iduser']) || !isset($_SESSION['role']) || $_SESSION['role'] != 0) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

$userId = intval($_SESSION['iduser']);
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'invalid_product']);
    exit;
}

try {
    wishlist_add($userId, $productId);
    $count = wishlist_count($userId);
    echo json_encode(['success' => true, 'count' => $count]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'db_error']);
}
