<?php
session_start();
require_once __DIR__ . '/model/connectdb.php';
require_once __DIR__ . '/model/donhang.php';

$orderId = isset($_GET['order']) ? intval($_GET['order']) : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order']) ? intval($_POST['order']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($orderId > 0) {
    if ($action === 'success') {
            // Mark order as paid
            update_trangthai_donhang($orderId, 'Đã thanh toán');
            // Optional: set session to show success modal
            $_SESSION['donhang'] = getdonhangtoid($orderId);
            $_SESSION['mail'] = 1;
      unset($_SESSION['pending_payment_order_id']);
            header('Location: index.php?pg=checkout');
            exit();
        } elseif ($action === 'fail') {
            // Keep as "Chưa thanh toán" and go back to checkout
            $_SESSION['payment_error'] = 'Thanh toán thất bại. Vui lòng thử lại hoặc chọn phương thức khác.';
      unset($_SESSION['pending_payment_order_id']);
            header('Location: index.php?pg=checkout');
            exit();
        }
    }
}
if ($orderId <= 0) {
    // Try from session
    if (isset($_SESSION['pending_payment_order_id'])) {
        $orderId = intval($_SESSION['pending_payment_order_id']);
    }
}
$order = $orderId > 0 ? getdonhangtoid($orderId) : null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giả lập thanh toán</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f7f7f7; margin:0; padding:20px; }
    .wrap { max-width: 720px; margin: 0 auto; background:#fff; border-radius:8px; padding:24px; box-shadow:0 2px 10px rgba(0,0,0,.06);} 
    h1 { font-size:20px; margin:0 0 12px; }
    .meta { color:#666; font-size:14px; margin-bottom:16px; }
    .amount { font-size:18px; margin:12px 0; }
    .row { display:flex; gap:12px; margin-top:16px; }
    form { display:inline-block; }
    button { padding:10px 16px; border:0; border-radius:6px; cursor:pointer; }
    .success { background:#2e7d32; color:#fff; }
    .fail { background:#c62828; color:#fff; }
    .back { background:#607d8b; color:#fff; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Giả lập cổng thanh toán</h1>
    <?php if ($order) { ?>
      <div class="meta">Mã đơn: <strong><?php echo htmlspecialchars($order['ma_donhang']); ?></strong></div>
      <div class="meta">Trạng thái hiện tại: <strong><?php echo htmlspecialchars($order['trangthai']); ?></strong></div>
      <div class="amount">Số tiền: <strong><?php echo number_format($order['tongtien'], 0, '', ','); ?>đ</strong></div>
      <div class="row">
        <form method="post">
          <input type="hidden" name="order" value="<?php echo $orderId; ?>" />
          <input type="hidden" name="action" value="success" />
          <button class="success" type="submit">Thanh toán thành công</button>
        </form>
        <form method="post">
          <input type="hidden" name="order" value="<?php echo $orderId; ?>" />
          <input type="hidden" name="action" value="fail" />
          <button class="fail" type="submit">Thanh toán thất bại</button>
        </form>
        <form action="index.php?pg=checkout" method="get">
          <button class="back" type="submit">Quay lại thanh toán</button>
        </form>
      </div>
    <?php } else { ?>
      <p>Không tìm thấy đơn hàng để thanh toán.</p>
      <a href="index.php?pg=checkout">Quay lại</a>
    <?php } ?>
  </div>
</body>
</html>
