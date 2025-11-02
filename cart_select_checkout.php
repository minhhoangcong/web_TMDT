<?php
session_start();

// Ensure cart exists
if (!isset($_SESSION['giohang']) || !is_array($_SESSION['giohang'])) {
    header('Location: index.php?pg=cart');
    exit;
}

// Collect selected indices
$selected = isset($_POST['selected']) ? $_POST['selected'] : [];
if (!is_array($selected) || count($selected) === 0) {
    // Nothing selected; go back to cart
    header('Location: index.php?pg=cart');
    exit;
}

// Build product_checkout from selected indices
$_SESSION['product_checkout'] = [];
foreach ($selected as $idx) {
    $i = intval($idx);
    if (isset($_SESSION['giohang'][$i])) {
        $_SESSION['product_checkout'][] = $_SESSION['giohang'][$i];
    }
}

// Redirect to checkout page; checkout should prefer product_checkout if present
header('Location: index.php?pg=checkout');
exit;
