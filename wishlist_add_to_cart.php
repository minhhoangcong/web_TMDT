<?php
session_start();
require_once __DIR__ . '/model/connectdb.php';
require_once __DIR__ . '/model/detail.php';
require_once __DIR__ . '/model/cart.php';
require_once __DIR__ . '/model/product.php';

header('Content-Type: application/json');

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'invalid_product']);
    exit;
}

try {
    // Prepare default selection: first color image and first size
    $spdetail = getproductdetail($productId);
    if (!$spdetail) {
        echo json_encode(['success' => false, 'error' => 'product_not_found']);
        exit;
    }
    $images = getlistimgcolor($productId);
    if (!$images || count($images) === 0) {
        echo json_encode(['success' => false, 'error' => 'no_variant']);
        exit;
    }
    $defaultImgRow = $images[0];
    $defaultImg = $defaultImgRow['main_img'];
    $defaultColorId = $defaultImgRow['id_color'];
    $defaultColor = getcolor($defaultColorId);
    $defaultSizeRow = getlistsize()[0];
    $defaultSize = $defaultSizeRow['ma_size'];

    if (!isset($_SESSION['giohang'])) {
        $_SESSION['giohang'] = [];
    }
    $sp = [
        'id' => $productId,
        'img' => $defaultImg,
        'name' => $spdetail['name'],
        'price' => $spdetail['price'],
        'color' => $defaultColor,
        'size' => $defaultSize,
        'soluong' => 1,
        'product_design' => 0,
        'id_product_design' => 1,
    ];

    // Merge if same line exists
    $merged = false;
    foreach ($_SESSION['giohang'] as $i => $item) {
        if (
            $item['id'] == $sp['id'] &&
            $item['img'] == $sp['img'] &&
            $item['name'] == $sp['name'] &&
            $item['price'] == $sp['price'] &&
            $item['color'] == $sp['color'] &&
            $item['size'] == $sp['size']
        ) {
            $_SESSION['giohang'][$i]['soluong'] += $sp['soluong'];
            $merged = true;
            break;
        }
    }
    if (!$merged) {
        $_SESSION['giohang'][] = $sp;
    }

    // Calculate updated cart count (sum of quantities)
    $cartCount = 0;
    if (isset($_SESSION['giohang']) && is_array($_SESSION['giohang'])) {
        foreach ($_SESSION['giohang'] as $ci) {
            $cartCount += isset($ci['soluong']) ? intval($ci['soluong']) : 0;
        }
    }

    echo json_encode(['success' => true, 'cartCount' => $cartCount]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'server_error']);
}
