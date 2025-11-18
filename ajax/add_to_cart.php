<?php
session_start();
header('Content-Type: application/json');

// Helper functions
if (!function_exists('csrf_validate')) {
    function csrf_validate($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

// CSRF protection
// Avoid null-coalescing to satisfy older linters
$token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
if (!csrf_validate($token)) {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ (CSRF)']);
    exit();
}

// Initialize cart
if (isset($_SESSION['product_checkout']) && !isset($_SESSION['giohang'])) {
    $_SESSION['giohang'] = [];
    $_SESSION['giohang'] = $_SESSION['product_checkout'];
    unset($_SESSION['product_checkout']);
} else {
    if (isset($_SESSION['product_checkout']) && isset($_SESSION['giohang']) && count($_SESSION['product_checkout']) > 0) {
        unset($_SESSION['giohang']);
        $_SESSION['giohang'] = [];
        $_SESSION['giohang'] = $_SESSION['product_checkout'];
        unset($_SESSION['product_checkout']);
    } else {
        if (isset($_SESSION['product_checkout']) && isset($_SESSION['giohang']) && count($_SESSION['product_checkout']) == 0) {
            unset($_SESSION['giohang']);
            unset($_SESSION['product_checkout']);
        }
    }
}

if (!isset($_SESSION['giohang'])) {
    $_SESSION['giohang'] = [];
}

// Add to cart
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $color = $_POST['color'] ?? '';
    $size = $_POST['size'] ?? '';
    $img = $_POST['img'] ?? '';
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $soluong = isset($_POST['soluong']) ? (int)$_POST['soluong'] : 1;
    
    $sp = [
        "id" => $id,
        "img" => $img,
        "name" => $name,
        "price" => $price,
        "color" => $color,
        "size" => $size,
        "soluong" => $soluong,
        "product_design" => 0,
        "id_product_design" => 1
    ];
    
    $i = 0;
    $kt = true;
    
    // Check if product already exists in cart
    foreach ($_SESSION['giohang'] as $item) {
        if ($sp['id'] == $item['id'] && 
            $sp['img'] == $item['img'] && 
            $sp['name'] == $item['name'] && 
            $sp['price'] == $item['price'] && 
            $sp['color'] == $item['color'] && 
            $sp['size'] == $item['size']) {
            $_SESSION['giohang'][$i]['soluong'] += $sp['soluong'];
            $kt = false;
            break;
        }
        $i++;
    }
    
    if ($kt == true) {
        array_unshift($_SESSION['giohang'], $sp);
    }
    
    // Return success with cart count
    $cartCount = 0;
    foreach ($_SESSION['giohang'] as $item) {
        $cartCount += $item['soluong'];
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã thêm vào giỏ hàng!',
        'cartCount' => $cartCount,
        'productName' => $name
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin sản phẩm']);
}
?>
