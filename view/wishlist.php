<?php
  if (!isset($_SESSION)) session_start();
  $isLogin = (isset($_SESSION['loginuser']) && isset($_SESSION['role']) && $_SESSION['role']==0 && isset($_SESSION['iduser']));
?>

<section class="page-header">
  <div class="container">
    <h1 style="margin: 24px 0">Yêu thích</h1>
  </div>
</section>

<section class="wishlist-content wishlist-page">
  <div class="container">
    <?php if (!$isLogin): ?>
      <p>Vui lòng <a href="index.php?pg=login">đăng nhập</a> để sử dụng mục Yêu thích.</p>
    <?php else: ?>
      <?php 
        $items = wishlist_get_for_user($_SESSION['iduser']);
      ?>
      <?php if (!$items || count($items)===0): ?>
        <p>Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.</p>
      <?php else: ?>
        <div class="product-list wishlist-grid">
          <?php foreach ($items as $it): 
            $pid = (int)$it['product_id'];
            $pname = $it['name'];
            $price = $it['price'];
            $img = getimg_product_main($pid);
            $linkdetail='index.php?pg=detail&id='.$pid;
          ?>
            <div class="product-item">
              <div class="product-images">
                <a href="<?=$linkdetail?>">
                  <?=check_img($img['main_img'])?>
                </a>
                <button class="wishlist-toggle active" data-product-id="<?=$pid?>" title="Bỏ yêu thích">
                  <i class="fa fa-heart" aria-hidden="true"></i>
                </button>
              </div>
              <div class="product-title"><?=$pname?></div>
              <div class="product-price">
                <?=number_format($price,0,'',',')?>đ
              </div>
              <div class="wishlist-actions">
                <button class="btn btn-primary wishlist-addtocart" data-product-id="<?=$pid?>">
                  <i class="fa fa-cart-plus" aria-hidden="true"></i>
                  Thêm vào giỏ hàng
                </button>
                <button class="btn btn-outline btn-remove wishlist-toggle active" data-product-id="<?=$pid?>" title="Bỏ yêu thích">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                  Xoá
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
