<?php
// Expect $orders list for current user
function status_badge_class($st){
  $st = trim(mb_strtolower($st));
  if ($st === mb_strtolower('Đã thanh toán')) return 'badge paid';
  if ($st === mb_strtolower('Chưa thanh toán')) return 'badge pending';
  if ($st === mb_strtolower('Đang giao')) return 'badge shipping';
  if ($st === mb_strtolower('Đã giao')) return 'badge delivered';
  return 'badge';
}
?>
<div class="app-fixed">
  <ul class="app-fixed-menu">
    <li class="app-fixed-list"><a href="index.php" class="app-fixed-link"><i class="fa fa-home" aria-hidden="true"></i></a></li>
    <li class="app-fixed-list"><a href="index.php?pg=account" class="app-fixed-link"><i class="fa fa-user-circle" aria-hidden="true"></i></a></li>
    <div class="selected-option-bg"></div>
  </ul>
</div>
<div class="link-mobile">
  <a href="index.php">Trang chủ</a>
  <i class="fa fa-chevron-right" aria-hidden="true"></i>
  <a href="#">Đơn hàng</a>
</div>
<section class="checkout" style="padding-top: 12px;">
  <div class="container">
    <div class="checkout-center">
      <div class="checkout-center-icon"><i class="fa fa-list" aria-hidden="true"></i></div>
      <div class="checkout-center-text">Đơn hàng của tôi</div>
      <p>Xem tất cả đơn hàng và trạng thái mới nhất.</p>
    </div>

    <div class="order" style="margin-top: 8px;">
      <div class="order-title">Danh sách đơn hàng</div>
      <div class="list-product__box">
        <div style="overflow-x:auto;">
          <table class="cart-table" style="min-width:760px;">
            <thead>
              <tr>
                <th>Mã đơn</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (is_array($orders) && count($orders)>0) { foreach($orders as $o) { ?>
                <tr>
                  <td><strong><?=htmlspecialchars($o['ma_donhang'])?></strong></td>
                  <td><?=htmlspecialchars($o['ngaylap'])?></td>
                  <td><?=number_format($o['tongtien'],0,'',',')?>đ</td>
                  <td><span class="<?=status_badge_class($o['trangthai'])?>"><?=htmlspecialchars($o['trangthai'])?></span></td>
                  <td>
                    <a class="btn btn-outline" href="index.php?pg=order_detail&id=<?=$o['id']?>"><i class="fa fa-eye" aria-hidden="true"></i> Xem</a>
                  </td>
                </tr>
              <?php }} else { ?>
                <tr><td colspan="5" style="text-align:center; padding:16px;">Chưa có đơn hàng nào.</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
