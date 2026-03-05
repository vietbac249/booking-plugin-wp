/**
 * Admin JavaScript
 */

jQuery(document).ready(function($) {
    // Thickbox for modals
    if (typeof tb_show === 'undefined') {
        // Fallback if thickbox not loaded
        console.warn('Thickbox not loaded');
    }
    
    // Confirm delete actions
    $('.delete-item').on('click', function(e) {
        if (!confirm('Bạn có chắc chắn muốn xóa?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-refresh dashboard every 5 minutes
    if ($('.booking-dashboard').length > 0) {
        setInterval(function() {
            location.reload();
        }, 300000); // 5 minutes
    }
});
