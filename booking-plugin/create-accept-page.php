<?php
/**
 * Tạo Page "Nhận Đơn Hàng" Tự Động
 * Chạy file này một lần để tạo page
 * Truy cập: http://your-site.com/wp-content/plugins/booking-plugin/create-accept-page.php
 */

require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Bạn không có quyền truy cập!');
}

echo "<h1>📄 Tạo Page Nhận Đơn Hàng</h1>";
echo "<hr>";

// Check if page already exists
$existing_page = get_page_by_path('nhan-don-hang');

if ($existing_page) {
    echo "<p style='color: orange;'>⚠️ Page 'nhan-don-hang' đã tồn tại (ID: {$existing_page->ID})</p>";
    echo "<p>URL: <a href='" . get_permalink($existing_page->ID) . "' target='_blank'>" . get_permalink($existing_page->ID) . "</a></p>";
    
    echo "<h3>Bạn muốn:</h3>";
    echo "<p><a href='?recreate=1' class='button'>🔄 Tạo Lại Page</a></p>";
    echo "<p><a href='" . admin_url('post.php?post=' . $existing_page->ID . '&action=edit') . "' class='button'>✏️ Sửa Page</a></p>";
    
    if (isset($_GET['recreate'])) {
        wp_delete_post($existing_page->ID, true);
        echo "<p style='color: green;'>✅ Đã xóa page cũ. <a href='create-accept-page.php'>Reload để tạo mới</a></p>";
    }
} else {
    // Create new page
    $page_data = array(
        'post_title'    => 'Nhận Đơn Hàng',
        'post_name'     => 'nhan-don-hang',
        'post_content'  => '[booking_accept_page]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => get_current_user_id(),
        'comment_status' => 'closed',
        'ping_status'   => 'closed'
    );
    
    $page_id = wp_insert_post($page_data);
    
    if ($page_id) {
        echo "<p style='color: green;'>✅ Đã tạo page thành công!</p>";
        echo "<p><strong>Page ID:</strong> $page_id</p>";
        echo "<p><strong>URL:</strong> <a href='" . get_permalink($page_id) . "' target='_blank'>" . get_permalink($page_id) . "</a></p>";
        
        // Set page template to full width if available
        update_post_meta($page_id, '_wp_page_template', 'page-templates/full-width.php');
        
        echo "<hr>";
        echo "<h3>🧪 Test URL:</h3>";
        echo "<p>Thử truy cập: <a href='" . get_permalink($page_id) . "?booking=1&token=test' target='_blank'>" . get_permalink($page_id) . "?booking=1&token=test</a></p>";
        
        echo "<hr>";
        echo "<h3>📝 Bước Tiếp Theo:</h3>";
        echo "<ol>";
        echo "<li>✅ Page đã được tạo với shortcode [booking_accept_page]</li>";
        echo "<li>Plugin sẽ tự động xử lý shortcode này</li>";
        echo "<li>Thử gửi đơn hàng và click link nhận đơn từ Telegram</li>";
        echo "</ol>";
        
        echo "<p><a href='" . admin_url('post.php?post=' . $page_id . '&action=edit') . "' class='button button-primary'>✏️ Sửa Page</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Không thể tạo page. Vui lòng thử lại.</p>";
    }
}

echo "<hr>";
echo "<p><a href='" . admin_url('admin.php?page=booking-orders') . "'>← Quay lại Quản Lý Đơn Hàng</a></p>";

echo "<style>
body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
.button { display: inline-block; padding: 10px 20px; background: #0073aa; color: #fff; text-decoration: none; border-radius: 3px; margin: 5px; }
.button:hover { background: #005177; }
.button-primary { background: #00a0d2; }
</style>";
