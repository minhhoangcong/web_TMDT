<?php
/**
 * Script dọn dẹp dữ liệu cũ cho UTH Shop
 * Giữ lại: sản phẩm, danh mục, ảnh thiết kế, cấu trúc DB
 * Xóa: tài khoản cũ, đơn hàng cũ, comment cũ, thống kê cũ
 */

session_start();
include_once "model/connectdb.php";

if (!isset($_POST['confirm_cleanup'])) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dọn dẹp dữ liệu cũ - UTH Shop</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { background: #dc3545; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .button:hover { background: #c82333; }
        ul { line-height: 1.6; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧹 Dọn dẹp dữ liệu cũ cho UTH Shop</h2>
        
        <div class="warning">
            <h3>⚠️ CẢNH BÁO: Hành động này không thể hoàn tác!</h3>
            <p>Script sẽ xóa vĩnh viễn dữ liệu cũ không liên quan đến UTH Shop.</p>
        </div>

        <div class="info">
            <h3>📋 Dữ liệu sẽ bị XÓA:</h3>
            <ul>
                <li>❌ Tài khoản admin/user cũ (email chủ cũ)</li>
                <li>❌ Đơn hàng và lịch sử mua hàng cũ</li>
                <li>❌ Bình luận và đánh giá cũ</li>
                <li>❌ Giỏ hàng và wishlist cũ</li>
                <li>❌ Thống kê và báo cáo cũ</li>
                <li>❌ Session và token cũ</li>
            </ul>
        </div>

        <div class="info">
            <h3>✅ Dữ liệu sẽ được GIỮ LẠI:</h3>
            <ul>
                <li>✅ Tất cả sản phẩm quần áo</li>
                <li>✅ Danh mục sản phẩm</li>
                <li>✅ Ảnh sản phẩm và ảnh thiết kế</li>
                <li>✅ Voucher và mã giảm giá</li>
                <li>✅ Cấu trúc database</li>
                <li>✅ Cài đặt màu sắc, size</li>
                <li>✅ Banner và tin tức (có thể chỉnh sửa sau)</li>
            </ul>
        </div>

        <div class="info">
            <h3>🔧 Sau khi dọn dẹp sẽ tạo:</h3>
            <ul>
                <li>👤 Admin mới: <strong>uth_admin</strong> / <strong>admin123</strong></li>
                <li>📧 Email admin: <strong>admin@uthshop.com</strong></li>
                <li>🛒 Hệ thống sạch sẽ sẵn sàng cho UTH Shop</li>
            </ul>
        </div>

        <form method="post" onsubmit="return confirm('⚠️ BẠN CHẮC CHẮN MUỐN XÓA DỮ LIỆU CŨ? Hành động này không thể hoàn tác!')">
            <input type="hidden" name="confirm_cleanup" value="1">
            <button type="submit" class="button">🧹 BẮT ĐẦU DỌN DẸP</button>
        </form>

        <p><small>💡 <strong>Khuyến nghị:</strong> Backup database trước khi chạy script này!</small></p>
    </div>
</body>
</html>
<?php
    exit;
}

// Thực hiện dọn dẹp
echo "<h2>🧹 Đang dọn dẹp dữ liệu cũ...</h2>";

try {
    $conn = pdo_get_connection();
    $conn->beginTransaction();

    // 1. Xóa tài khoản cũ (giữ lại cấu trúc)
    echo "<p>❌ Xóa tài khoản cũ...</p>";
    pdo_execute("DELETE FROM users WHERE role IN (0, 1)");

    // 2. Xóa đơn hàng cũ
    echo "<p>❌ Xóa đơn hàng cũ...</p>";
    pdo_execute("DELETE FROM donhang");
    pdo_execute("DELETE FROM cart");

    // 3. Xóa bình luận cũ  
    echo "<p>❌ Xóa bình luận cũ...</p>";
    pdo_execute("DELETE FROM comment");

    // 4. Xóa wishlist cũ
    echo "<p>❌ Xóa wishlist cũ...</p>";
    pdo_execute("DELETE FROM wishlist");

    // 5. Xóa thống kê cũ (nếu có)
    echo "<p>❌ Xóa dữ liệu thống kê cũ...</p>";
    $tables_to_check = ['thongke', 'analytics', 'logs'];
    foreach ($tables_to_check as $table) {
        try {
            pdo_execute("DELETE FROM $table");
        } catch (Exception $e) {
            // Bảng không tồn tại, bỏ qua
        }
    }

    // 6. Reset auto increment
    echo "<p>🔄 Reset ID counters...</p>";
    pdo_execute("ALTER TABLE users AUTO_INCREMENT = 1");
    pdo_execute("ALTER TABLE donhang AUTO_INCREMENT = 1");
    pdo_execute("ALTER TABLE cart AUTO_INCREMENT = 1");
    pdo_execute("ALTER TABLE comment AUTO_INCREMENT = 1");

    // 7. Tạo admin mới cho UTH Shop
    echo "<p>✅ Tạo admin UTH Shop mới...</p>";
    pdo_execute("INSERT INTO users (user, pass, name, email, sdt, gioitinh, ngaysinh, diachi, role, img, kichhoat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        'uth_admin', 'admin123', 'UTH Shop Admin', 'admin@uthshop.com', '0123456789', 0, '1990-01-01', 'UTH University', 1, '', 1);

    // 8. Tạo user demo (tùy chọn)
    echo "<p>✅ Tạo user demo...</p>";
    pdo_execute("INSERT INTO users (user, pass, name, email, sdt, gioitinh, ngaysinh, diachi, role, img, kichhoat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        'demo_user', '123456', 'Demo User', 'user@uthshop.com', '0987654321', 0, '1995-01-01', 'UTH University', 0, '', 1);

    $conn->commit();
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🎉 Dọn dẹp thành công!</h3>";
    echo "<p><strong>Tài khoản admin mới:</strong></p>";
    echo "<ul>";
    echo "<li>👤 Username: <strong>uth_admin</strong></li>";
    echo "<li>🔑 Password: <strong>admin123</strong></li>";
    echo "<li>📧 Email: <strong>admin@uthshop.com</strong></li>";
    echo "</ul>";
    echo "<p><strong>Tài khoản user demo:</strong></p>";
    echo "<ul>";
    echo "<li>👤 Username: <strong>demo_user</strong></li>";
    echo "<li>🔑 Password: <strong>123456</strong></li>";
    echo "</ul>";
    echo "<p>✅ Dữ liệu sản phẩm, danh mục, ảnh được giữ nguyên</p>";
    echo "<p>🏪 UTH Shop đã sẵn sàng sử dụng!</p>";
    echo "</div>";

    echo "<p><a href='index.php?pg=login' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔑 Đăng nhập ngay</a></p>";

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px;'>";
    echo "<h3>❌ Lỗi trong quá trình dọn dẹp:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

function pdo_get_connection(){
    $dburl = "mysql:host=localhost;dbname=zstyle;charset=utf8";
    $username = 'root';
    $password = '';
    $conn = new PDO($dburl, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

function pdo_execute($sql, ...$args){
    $conn = pdo_get_connection();
    $stmt = $conn->prepare($sql);
    $stmt->execute($args);
    return $stmt;
}
?>