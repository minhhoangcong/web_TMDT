<?php
session_start();
header('Content-Type: application/json');

$cartCount = 0;
if (isset($_SESSION['giohang']) && is_array($_SESSION['giohang'])) {
    foreach ($_SESSION['giohang'] as $ci) {
        $cartCount += isset($ci['soluong']) ? intval($ci['soluong']) : 0;
    }
}

echo json_encode(['cartCount' => $cartCount]);
