<?php
// Wishlist data access functions

function wishlist_ensure_table() {
    // Create table if not exists to avoid site-wide fatal errors when header counts are rendered
    $sql = "CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE,
        UNIQUE KEY uq_user_product (user_id, product_id)
    )";
    try { pdo_execute($sql); } catch (Exception $e) { /* ignore */ }
}

function wishlist_add($userId, $productId) {
    wishlist_ensure_table();
    // Prevent duplicate rows
    $sql = "INSERT INTO wishlist (user_id, product_id) 
            SELECT ?, ? FROM DUAL 
            WHERE NOT EXISTS (
                SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?
            )";
    pdo_execute($sql, $userId, $productId, $userId, $productId);
}

function wishlist_remove($userId, $productId) {
    wishlist_ensure_table();
    $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    pdo_execute($sql, $userId, $productId);
}

function wishlist_exists($userId, $productId) {
    wishlist_ensure_table();
    try {
        $sql = "SELECT COUNT(*) AS cnt FROM wishlist WHERE user_id = ? AND product_id = ?";
        $row = pdo_query_one($sql, $userId, $productId);
        return $row ? intval($row['cnt']) > 0 : false;
    } catch (Exception $e) {
        return false;
    }
}

function wishlist_count($userId) {
    wishlist_ensure_table();
    try {
        $sql = "SELECT COUNT(*) AS cnt FROM wishlist WHERE user_id = ?";
        $row = pdo_query_one($sql, $userId);
        return $row ? intval($row['cnt']) : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function wishlist_get_for_user($userId) {
    wishlist_ensure_table();
    // return product IDs and basic product data for rendering
    $sql = "SELECT w.product_id, p.name, p.price, p.priceold
            FROM wishlist w
            JOIN product p ON p.id = w.product_id
            WHERE w.user_id = ?
            ORDER BY w.created_at DESC";
    try { return pdo_query($sql, $userId); } catch (Exception $e) { return []; }
}
