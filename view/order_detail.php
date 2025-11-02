<?php
// Expect $order and $orderItems provided by router
// Support functions used: check_img, getproductdetail, getproductdesigndetail, getcolor, getsize
?>
<div class="app-fixed">
  <ul class="app-fixed-menu">
    <li class="app-fixed-list"><a href="index.php" class="app-fixed-link"><i class="fa fa-home" aria-hidden="true"></i></a></li>
    <li class="app-fixed-list"><a href="index.php?pg=product" class="app-fixed-link"><i class="fa fa-list" aria-hidden="true"></i></a></li>
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
      <div class="checkout-center-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></div>
      <div class="checkout-center-text">Chi tiết đơn hàng</div>
      <p>
        Mã đơn: <strong><?=htmlspecialchars($order['ma_donhang'])?></strong>
        <span style="margin-left:8px;" class="<?php
          $st = trim(mb_strtolower($order['trangthai']));
          if ($st === mb_strtolower('Đã thanh toán')) echo 'badge paid';
          else if ($st === mb_strtolower('Chưa thanh toán')) echo 'badge pending';
          else if ($st === mb_strtolower('Đang giao')) echo 'badge shipping';
          else if ($st === mb_strtolower('Đã giao')) echo 'badge delivered';
          else echo 'badge';
        ?>"><?=htmlspecialchars($order['trangthai'])?></span>
      </p>
      <div class="print-hide" style="margin-top:8px;">
        <button onclick="window.print()" class="btn btn-outline"><i class="fa fa-print" aria-hidden="true"></i> In hóa đơn / Lưu PDF</button>
        <a href="index.php?pg=orders" class="btn btn-outline"><i class="fa fa-list" aria-hidden="true"></i> Tất cả đơn hàng</a>
      </div>
    </div>

    <div class="checkout-main">
      <div class="checkout-left">
        <div class="order">
          <h3 class="order-title">Thông tin đơn hàng</h3>
          <div class="order-form order-info">
            <div class="form-flex"><span>Trạng thái</span><span><strong><?=htmlspecialchars($order['trangthai'])?></strong></span></div>
            <div class="form-flex"><span>Ngày đặt</span><span><?=htmlspecialchars($order['ngaylap'])?></span></div>
            <div class="form-flex"><span>Phương thức thanh toán</span><span><?=htmlspecialchars($order['ptthanhtoan'])?></span></div>
            <div class="form-flex"><span>Giao hàng nhanh</span><span><?=($order['giaohangnhanh']? 'Có' : 'Không')?></span></div>
          </div>
        </div>
        <div class="order" style="margin-top:16px;">
          <h3 class="order-title">Thông tin khách hàng</h3>
          <div class="order-form order-info">
            <div class="form-flex"><span>Người đặt</span><span><?=htmlspecialchars($order['tendat'])?></span></div>
            <div class="form-flex"><span>Email đặt</span><span><?=htmlspecialchars($order['emaildat'])?></span></div>
            <div class="form-flex"><span>SĐT đặt</span><span><?=htmlspecialchars($order['sdtdat'])?></span></div>
            <div class="form-flex"><span>Địa chỉ đặt</span><span><?=htmlspecialchars($order['diachidat'])?></span></div>
          </div>
          <?php if (!empty($order['tennhan']) || !empty($order['emailnhan']) || !empty($order['sdtnhan']) || !empty($order['diachinhan'])) { ?>
          <div class="order-form order-info" style="margin-top:12px;">
            <div class="form-flex"><span>Người nhận</span><span><?=htmlspecialchars($order['tennhan'])?></span></div>
            <div class="form-flex"><span>Email nhận</span><span><?=htmlspecialchars($order['emailnhan'])?></span></div>
            <div class="form-flex"><span>SĐT nhận</span><span><?=htmlspecialchars($order['sdtnhan'])?></span></div>
            <div class="form-flex"><span>Địa chỉ nhận</span><span><?=htmlspecialchars($order['diachinhan'])?></span></div>
          </div>
          <?php } ?>
        </div>
      </div>

      <div class="checkout-right">
        <div class="checkout-right-box">
          <?php
            $tongsoluong = 0; $tam_tinh = 0;
            foreach ($orderItems as $item) { $tongsoluong += $item['soluong']; $tam_tinh += ($item['price']*$item['soluong']); }
            $giam_gia = max(0, $tam_tinh - (int)$order['tongtien']);
          ?>
          <div class="checkout-right-title-heading">Sản phẩm (<?=$tongsoluong?>)</div>
          <div class="checkout-right-overflow">
            <?php foreach($orderItems as $ct){
              $name = '';
              if ((int)$ct['product_design'] === 0) { $pd = getproductdetail((int)$ct['id_product']); $name = $pd ? $pd['name'] : 'Sản phẩm'; }
              else { $pd = getproductdesigndetail((int)$ct['id_product_design']); $name = $pd ? $pd['name'] : 'Thiết kế của bạn'; }
            ?>
            <div class="checkout-right-list">
              <div class="checkout-right-item">
                <div class="checkout-right-image">
                  <?=check_img($ct['img'])?>
                  <?php if ($ct['soluong']>1) { ?><div class="checkout-right-quantity"><span class="number"><?=$ct['soluong']?></span></div><?php } ?>
                </div>
                <div class="checkout-right-content">
                  <div class="checkout-right-title"><?=$name?></div>
                  <div class="checkout-right-main">
                    <div class="checkout-right-color">Màu: <?=htmlspecialchars(getcolor($ct['id_color']))?></div>
                    <div class="checkout-right-size">Size: <?=htmlspecialchars(getsize($ct['id_size']))?></div>
                  </div>
                </div>
              </div>
              <div class="checkout-right-price"><?=number_format($ct['price'],0,'',',')?>đ</div>
            </div>
            <?php } ?>
          </div>

          <div class="form-group">
            <div class="form-flex"><span> Tạm tính</span><span><?=number_format($tam_tinh,0,'',',')?>đ</span></div>
            <?php if ($giam_gia>0) { ?>
              <div class="form-flex"><span> Giảm giá</span><span>-<?=number_format($giam_gia,0,'',',')?>đ</span></div>
            <?php } ?>
            <div class="form-flex"><span> Phí vận chuyển</span><span>-</span></div>
          </div>
          <div class="form-flex mt-10">
            <span class="checkout-total">Tổng cộng</span>
            <span><?=number_format($order['tongtien'],0,'',',')?>đ</span>
          </div>
          <div class="form-flex back-flex mt-10">
            <div class="back-cart"><a href="index.php?pg=account">Về tài khoản</a></div>
            <div class="voucher-btn button-primary__primary">
              <a class="voucher-button" href="index.php">Tiếp tục mua sắm</a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
