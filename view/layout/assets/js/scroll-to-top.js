// Scroll to Top Button
(function() {
  'use strict';
  
  // Tạo button element
  function createScrollButton() {
    const btn = document.createElement('button');
    btn.id = 'scroll-to-top';
    btn.className = 'scroll-to-top-btn';
    btn.innerHTML = '<i class="fa fa-chevron-up" aria-hidden="true"></i>';
    btn.setAttribute('aria-label', 'Scroll to top');
    btn.title = 'Về đầu trang';
    document.body.appendChild(btn);
    return btn;
  }
  
  // Khởi tạo
  function init() {
    const scrollBtn = createScrollButton();
    
    // Hiện/ẩn button khi scroll
    window.addEventListener('scroll', function() {
      const scrollPos = window.pageYOffset || document.documentElement.scrollTop;
      
      if (scrollPos > 300) {
        scrollBtn.classList.add('show');
      } else {
        scrollBtn.classList.remove('show');
      }
    });
    
    // Click để scroll lên
    scrollBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Smooth scroll về top (dùng native JS thay vì jQuery)
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }
  
  // Chạy khi DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
