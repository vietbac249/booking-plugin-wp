<?php
/**
 * Kiểm tra Table Prefix
 * File này giúp xác định prefix thực tế của database
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Bạn không có quyền truy cập!');
}

global $wpdb;

echo "<h2>🔍 Kiểm Tra Database Prefix</h2>";

echo "<h3>1. WordPress Prefix:</h3>";
echo "<p><strong>\$wpdb->prefix:</strong> <code style='background: #f0f0f0; padding: 5px;'>" . $wpdb->prefix . "</code></p>";

echo "<h3>2. Kiểm Tra Bảng Thực Tế:</h3>";

// Lấy danh sách tất cả các bảng
$tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);

echo "<p>Tổng số bảng: <strong>" . count($tables) . "</strong></p>";

// Tìm các bảng liên quan đến plugin
$plugin_tables = [];
$prefixes_found = [];

foreach ($tables as $table) {
    $table_name = $table[0];
    
    // Tìm các bảng có chứa 'booking' hoặc 'driver'
    if (stripos($table_name, 'booking') !== false || stripos($table_name, 'driver') !== false) {
        $plugin_tables[] = $table_name;
        
        // Tách prefix
        if (preg_match('/^(.+?)(bookings|drivers|booking_|driver_)/', $table_name, $matches)) {
            $prefix = $matches[1];
            if (!in_array($prefix, $prefixes_found)) {
                $prefixes_found[] = $prefix;
            }
        }
    }
}

if (empty($plugin_tables)) {
    echo "<p style='color: red;'>❌ Không tìm thấy bảng nào của plugin!</p>";
    echo "<p>Có thể plugin chưa được kích hoạt hoặc database chưa được tạo.</p>";
} else {
    echo "<h4>Các bảng của plugin:</h4>";
    echo "<ul>";
    foreach ($plugin_tables as $table) {
        echo "<li><code style='background: #f0f0f0; padding: 3px;'>{$table}</code></li>";
    }
    echo "</ul>";
    
    echo "<h4>Prefix phát hiện:</h4>";
    if (!empty($prefixes_found)) {
        foreach ($prefixes_found as $prefix) {
            $is_match = ($prefix === $wpdb->prefix);
            $color = $is_match ? 'green' : 'red';
            $icon = $is_match ? '✅' : '❌';
            
            echo "<p style='color: {$color};'>{$icon} <strong><code>{$prefix}</code></strong>";
            if (!$is_match) {
                echo " (KHÔNG KHỚP với \$wpdb->prefix)";
            } else {
                echo " (Khớp với \$wpdb->prefix)";
            }
            echo "</p>";
        }
    }
}

echo "<h3>3. Test Query:</h3>";

// Test với prefix hiện tại
echo "<h4>a) Với prefix hiện tại (\$wpdb->prefix = '{$wpdb->prefix}'):</h4>";
$count_current = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}drivers");
if ($count_current !== null) {
    echo "<p style='color: green;'>✅ Tìm thấy <strong>{$count_current}</strong> tài xế</p>";
} else {
    echo "<p style='color: red;'>❌ Lỗi: " . $wpdb->last_error . "</p>";
}

// Test với các prefix phát hiện được
if (!empty($prefixes_found)) {
    foreach ($prefixes_found as $prefix) {
        if ($prefix !== $wpdb->prefix) {
            echo "<h4>b) Với prefix phát hiện ('{$prefix}'):</h4>";
            $count_detected = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}drivers");
            if ($count_detected !== null) {
                echo "<p style='color: green;'>✅ Tìm thấy <strong>{$count_detected}</strong> tài xế</p>";
            } else {
                echo "<p style='color: red;'>❌ Lỗi: " . $wpdb->last_error . "</p>";
            }
        }
    }
}

echo "<hr>";
echo "<h3>🔧 Giải Pháp:</h3>";

if (!empty($prefixes_found) && !in_array($wpdb->prefix, $prefixes_found)) {
    $correct_prefix = $prefixes_found[0];
    echo "<div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>";
    echo "<h4 style='margin-top: 0;'>⚠️ Phát hiện vấn đề!</h4>";
    echo "<p><strong>WordPress đang dùng prefix:</strong> <code>{$wpdb->prefix}</code></p>";
    echo "<p><strong>Nhưng bảng thực tế dùng prefix:</strong> <code>{$correct_prefix}</code></p>";
    echo "<p><strong>Cần làm:</strong></p>";
    echo "<ol>";
    echo "<li>Kiểm tra file <code>wp-config.php</code></li>";
    echo "<li>Tìm dòng: <code>\$table_prefix = '...';</code></li>";
    echo "<li>Đảm bảo giá trị là: <code>\$table_prefix = '{$correct_prefix}';</code></li>";
    echo "<li>Hoặc sửa code plugin để dùng prefix đúng</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<p style='color: green;'>✅ Prefix đang khớp! Không có vấn đề về prefix.</p>";
}

echo "<hr>";
echo "<p><a href='" . admin_url('admin.php?page=booking-orders') . "'>← Quay lại Đơn Hàng</a></p>";
echo "<p><a href='debug-drivers.php'>→ Debug Tài Xế</a></p>";

echo "<hr>";
echo "<p style='color: #999; font-size: 12px;'>© 2026 Nguyễn Việt Bắc. All Rights Reserved.</p>";
