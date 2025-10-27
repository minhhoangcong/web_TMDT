<section class="page-header">
  <div class="container">
    <h1 style="margin: 24px 0">Yêu thích</h1>
  </div>
</section>

<section class="wishlist-content">
  <div class="container">
    <?php if (isset($_SESSION['loginuser']) && isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
      <p>Chức năng Yêu thích đang được phát triển. Hiện tại bạn chưa có danh sách sản phẩm yêu thích hiển thị tại đây.</p>
      <p>Trong lúc chờ, bạn có thể thêm sản phẩm vào Giỏ hàng để lưu lại.</p>
    <?php else: ?>
      <p>Vui lòng <a href="index.php?pg=login">đăng nhập</a> để sử dụng mục Yêu thích.</p>
    <?php endif; ?>
  </div>
</section>
