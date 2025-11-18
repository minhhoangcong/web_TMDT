/**
 * 🔥 Toast Notification System
 * Usage: 
 *   Toast.success('Đã thêm vào giỏ hàng!')
 *   Toast.error('Có lỗi xảy ra!')
 *   Toast.warning('Vui lòng đăng nhập')
 *   Toast.info('Thông tin đã được cập nhật')
 */

const Toast = {
    container: null,
    
    // Initialize toast container
    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    },
    
    // Show toast notification
    show(message, type = 'success', duration = 3000) {
        this.init();
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        // Icon based on type
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        
        // Titles based on type
        const titles = {
            success: 'Thành công',
            error: 'Lỗi',
            warning: 'Cảnh báo',
            info: 'Thông báo'
        };
        
        toast.innerHTML = `
            <div class="toast-icon">${icons[type]}</div>
            <div class="toast-content">
                <div class="toast-title">${titles[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="Toast.close(this.parentElement)">×</button>
            <div class="toast-progress" style="animation: shrinkWidth ${duration}ms linear"></div>
        `;
        
        // Add to container
        this.container.appendChild(toast);
        
        // Auto remove after duration
        setTimeout(() => {
            this.close(toast);
        }, duration);
        
        // Remove on click close button
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.close(toast);
        });
        
        return toast;
    },
    
    // Close toast with animation
    close(toast) {
        if (!toast || !toast.parentElement) return;
        
        toast.classList.add('hiding');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    },
    
    // Shorthand methods
    success(message, duration) {
        return this.show(message, 'success', duration);
    },
    
    error(message, duration) {
        return this.show(message, 'error', duration);
    },
    
    warning(message, duration) {
        return this.show(message, 'warning', duration);
    },
    
    info(message, duration) {
        return this.show(message, 'info', duration);
    }
};

// Make it globally available
window.Toast = Toast;
