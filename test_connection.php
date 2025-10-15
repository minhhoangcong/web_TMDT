<?php
// Test file để kiểm tra kết nối database
try {
    include_once "model/connectdb.php";
    $conn = pdo_get_connection();
    echo "✅ Database connection successful!";
    echo "<br>PHP Version: " . phpversion();
    echo "<br>Current Time: " . date('Y-m-d H:i:s');
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>