<?php
session_start();
include_once "model/connectdb.php";
include_once "model/user.php";

// Debug script to check login issues
if (isset($_POST['debug_user']) && isset($_POST['debug_pass'])) {
    $user = $_POST['debug_user'];
    $pass = $_POST['debug_pass'];
    
    echo "<h3>Debug kết quả đăng nhập</h3>";
    echo "<p><strong>Username:</strong> " . htmlspecialchars($user) . "</p>";
    echo "<p><strong>Password length:</strong> " . strlen($pass) . "</p>";
    
    // Check if user exists
    $sql = "SELECT * FROM users WHERE user=?";
    $row = pdo_query_one($sql, $user);
    
    if (!is_array($row)) {
        echo "<p style='color:red'>❌ User không tồn tại trong database</p>";
    } else {
        echo "<p style='color:green'>✅ User tồn tại</p>";
        echo "<p><strong>ID:</strong> " . $row['id'] . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
        echo "<p><strong>Role:</strong> " . $row['role'] . "</p>";
        echo "<p><strong>Kích hoạt:</strong> " . ($row['kichhoat'] ?? 'null') . "</p>";
        
        $stored = isset($row['pass']) ? $row['pass'] : '';
        echo "<p><strong>Stored password length:</strong> " . strlen($stored) . "</p>";
        
        // Check if password is hashed
        $info = password_get_info($stored);
        if ($info['algo'] !== 0) {
            echo "<p style='color:blue'>🔒 Password đã được băm (algo: " . $info['algoName'] . ")</p>";
            $verify = password_verify($pass, $stored);
            echo "<p><strong>Password verify:</strong> " . ($verify ? '✅ Đúng' : '❌ Sai') . "</p>";
        } else {
            echo "<p style='color:orange'>⚠️ Password chưa băm (plaintext)</p>";
            $match = ($pass === $stored);
            echo "<p><strong>Plaintext match:</strong> " . ($match ? '✅ Đúng' : '❌ Sai') . "</p>";
            if ($match) {
                echo "<p style='color:green'>🔄 Sẽ tự động băm sau khi đăng nhập thành công</p>";
            }
        }
        
        // Test getlogin function
        $loginResult = getlogin($user, $pass);
        echo "<p><strong>getlogin() result:</strong> " . (is_array($loginResult) ? '✅ Thành công' : '❌ Thất bại (' . $loginResult . ')') . "</p>";
        
        // Test getrole function
        $roleResult = getrole($user, $pass);
        echo "<p><strong>getrole() result:</strong> " . $roleResult . "</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Login</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        form { background: #f5f5f5; padding: 20px; border-radius: 5px; max-width: 400px; }
        input { width: 100%; padding: 8px; margin: 5px 0; box-sizing: border-box; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; }
    </style>
</head>
<body>
    <h2>Debug Login Tool</h2>
    <form method="post">
        <label>Username:</label>
        <input type="text" name="debug_user" value="<?= htmlspecialchars($_POST['debug_user'] ?? '') ?>" required>
        
        <label>Password:</label>
        <input type="password" name="debug_pass" required>
        
        <button type="submit">Kiểm tra đăng nhập</button>
    </form>
    
    <p><small>Tool này giúp debug vấn đề đăng nhập bằng cách kiểm tra trực tiếp database và các hàm xác thực.</small></p>
</body>
</html>