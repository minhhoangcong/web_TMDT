// Recently Viewed Products - Lưu lịch sử xem sản phẩm
(function() {
  'use strict';
  
  const STORAGE_KEY = 'uthshop_recently_viewed';
  const MAX_ITEMS = 8;

  // Lấy danh sách từ localStorage
  function getRecentlyViewed() {
    try {
      const data = localStorage.getItem(STORAGE_KEY);
      return data ? JSON.parse(data) : [];
    } catch (e) {
      console.warn('Cannot read recently viewed:', e);
      return [];
    }
  }

  // Lưu sản phẩm vào localStorage
  function saveRecentlyViewed(items) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    } catch (e) {
      console.warn('Cannot save recently viewed:', e);
    }
  }

  // Thêm sản phẩm mới
  function addProduct(product) {
    let items = getRecentlyViewed();
    
    // Xóa sản phẩm cũ nếu đã tồn tại (để đưa lên đầu)
    items = items.filter(item => item.id !== product.id);
    
    // Thêm vào đầu danh sách
    items.unshift(product);
    
    // Giới hạn số lượng
    if (items.length > MAX_ITEMS) {
      items = items.slice(0, MAX_ITEMS);
    }
    
    saveRecentlyViewed(items);
  }

  // Lưu sản phẩm khi vào trang detail
  function trackProductView() {
    // Kiểm tra xem có đang ở trang detail không
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('pg');
    const productId = urlParams.get('id');
    
    if (page === 'detail' && productId) {
      // Lấy thông tin sản phẩm từ DOM
      const titleEl = document.querySelector('.detail-title');
      const priceEl = document.querySelector('.detail-price');
      const imgEl = document.querySelector('.detail-img');
      
      if (titleEl && priceEl && imgEl) {
        // Lấy CHỈ giá đầu tiên (bỏ giá cũ gạch ngang)
        let priceText = priceEl.childNodes[0].textContent.trim();
        
        const product = {
          id: parseInt(productId),
          name: titleEl.textContent.trim(),
          price: priceText,
          img: imgEl.src,
          timestamp: Date.now()
        };
        
        addProduct(product);
      }
    }
  }

  // Render danh sách sản phẩm đã xem
  function renderRecentlyViewed() {
    const container = document.getElementById('recently-viewed-list');
    if (!container) return;
    
    const items = getRecentlyViewed();
    
    if (items.length === 0) {
      // Ẩn section nếu không có sản phẩm
      const section = document.querySelector('.recently-viewed-section');
      if (section) section.style.display = 'none';
      return;
    }
    
    // Tạo HTML cho từng sản phẩm
    const html = items.map(item => `
      <div class="recently-viewed-item">
        <a href="index.php?pg=detail&id=${item.id}" class="recently-viewed-link">
          <div class="recently-viewed-image">
            <img src="${item.img}" alt="${item.name}" onerror="this.src='view/layout/assets/images/product-1.png'" />
          </div>
          <div class="recently-viewed-content">
            <div class="recently-viewed-title">${item.name}</div>
            <div class="recently-viewed-price">${item.price}</div>
          </div>
        </a>
      </div>
    `).join('');
    
    container.innerHTML = html;
    
    // Hiện section
    const section = document.querySelector('.recently-viewed-section');
    if (section) section.style.display = 'block';
  }

  // Khởi tạo khi DOM ready
  function init() {
    // Track sản phẩm hiện tại (nếu đang ở trang detail)
    trackProductView();
    
    // Render danh sách (cho tất cả các trang)
    renderRecentlyViewed();
  }

  // Chạy khi DOM đã sẵn sàng
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
