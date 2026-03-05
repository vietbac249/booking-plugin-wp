<?php
/**
 * Update Database - Thêm các cột cần thiết cho tính năng gán đơn hàng
 * 
 * Chạy file này một lần để cập nhật database
 * Truy cập: http://your-site.com/wp-content/plugins/booking-plugin/update-database-assignment-fix.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Bạn không có quyền truy cập!');
}

global $wpdb;

echo "<h1>🔧 Cập Nhật Database - Fix Gán Đơn Hàng</h1>";
echo "<hr>";

// Thêm các cột vào bảng bookings
$table_bookings = $wpdb->prefix . 'bookings';

$columns_to_add = [
    'assigned_at' => "ALTER TABLE {$table_bookings} ADD COLUMN assigned_at timestamp NULL AFTER driver_accepted_at",
    'assigned_by' => "ALTER TABLE {$table_bookings} ADD COLUMN assigned_by bigint(20) NULL AFTER assigned_at",
    'assignment_type' => "ALTER TABLE {$table_bookings} ADD COLUMN assignment_type varchar(20) NULL COMMENT 'direct, group' AFTER assigned_by",
    'group_id' => "ALTER TABLE {$table_bookings} ADD COLUMN group_id bigint(20) NULL AFTER assignment_type",
    'accept_token' => "ALTER TABLE {$table_bookings} ADD COLUMN accept_token varchar(64) NULL AFTER group_id",
    'token_expires' => "ALTER TABLE {$table_bookings} ADD COLUMN token_expires bigint(20) NULL AFTER accept_token"
];

echo "<h2>📋 Thêm cột vào bảng bookings</h2>";

foreach ($columns_to_add as $column => $sql) {
    // Check if column exists
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_bookings} LIKE '{$column}'");
    
    if (empty($column_exists)) {
        echo "<p>➕ Thêm cột <strong>{$column}</strong>... ";
        $result = $wpdb->query($sql);
        
        if ($result !== false) {
            echo "<span style='color: green;'>✅ Thành công</span></p>";
        } else {
            echo "<span style='color: red;'>❌ Lỗi: " . $wpdb->last_error . "</span></p>";
        }
    } else {
        echo "<p>⏭️ Cột <strong>{$column}</strong> đã tồn tại</p>";
    }
}

// Kiểm tra bảng booking_assignment_logs
$table_logs = $wpdb->prefix . 'booking_assignment_logs';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_logs}'");

if (!$table_exists) {
    echo "<h2>📋 Tạo bảng booking_assignment_logs</h2>";
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE {$table_logs} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        booking_id bigint(20) NOT NULL,
        driver_id bigint(20) NULL,
        group_id bigint(20) NULL,
        assignment_type varchar(20) NOT NULL COMMENT 'direct, group',
        assigned_by bigint(20) NOT NULL,
        status varchar(20) NOT NULL COMMENT 'assigned, accepted, rejected',
        accepted_at timestamp NULL,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY booking_id (booking_id),
        KEY driver_id (driver_id),
        KEY group_id (group_id)
    ) {$charset_collate};";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    echo "<p><span style='color: green;'>✅ Đã tạo bảng booking_assignment_logs</span></p>";
} else {
    echo "<h2>📋 Bảng booking_assignment_logs</h2>";
    echo "<p>⏭️ Bảng đã tồn tại</p>";
}

// Kiểm tra bảng booking_notification_groups
$table_groups = $wpdb->prefix . 'booking_notification_groups';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_groups}'");

if (!$table_exists) {
    echo "<h2>📋 Tạo bảng booking_notification_groups</h2>";
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE {$table_groups} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        type varchar(20) NOT NULL COMMENT 'telegram, zalo',
        chat_id varchar(100) NULL COMMENT 'Telegram chat ID',
        bot_token varchar(255) NULL COMMENT 'Telegram bot token',
        group_id varchar(100) NULL COMMENT 'Zalo group ID',
        access_token varchar(255) NULL COMMENT 'Zalo access token',
        is_active tinyint(1) DEFAULT 1,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY type (type),
        KEY is_active (is_active)
    ) {$charset_collate};";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    echo "<p><span style='color: green;'>✅ Đã tạo bảng booking_notification_groups</span></p>";
} else {
    echo "<h2>📋 Bảng booking_notification_groups</h2>";
    echo "<p>⏭️ Bảng đã tồn tại</p>";
}

// Thêm cột telegram_chat_id và zalo_user_id vào bảng drivers nếu chưa có
$table_drivers = $wpdb->prefix . 'drivers';

$driver_columns = [
    'telegram_chat_id' => "ALTER TABLE {$table_drivers} ADD COLUMN telegram_chat_id varchar(100) NULL AFTER ekyc_photo",
    'zalo_user_id' => "ALTER TABLE {$table_drivers} ADD COLUMN zalo_user_id varchar(100) NULL AFTER telegram_chat_id"
];

echo "<h2>📋 Thêm cột vào bảng drivers</h2>";

foreach ($driver_columns as $column => $sql) {
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_drivers} LIKE '{$column}'");
    
    if (empty($column_exists)) {
        echo "<p>➕ Thêm cột <strong>{$column}</strong>... ";
        $result = $wpdb->query($sql);
        
        if ($result !== false) {
            echo "<span style='color: green;'>✅ Thành công</span></p>";
        } else {
            echo "<span style='color: red;'>❌ Lỗi: " . $wpdb->last_error . "</span></p>";
        }
    } else {
        echo "<p>⏭️ Cột <strong>{$column}</strong> đã tồn tại</p>";
    }
}

echo "<hr>";
echo "<h2 style='color: green;'>✅ Hoàn tất cập nhật database!</h2>";
echo "<p><strong>Bây giờ bạn có thể:</strong></p>";
echo "<ul>";
echo "<li>✅ Gán đơn hàng cho tài xế</li>";
echo "<li>✅ Gán đơn hàng cho group</li>";
echo "<li>✅ Tài xế nhận/từ chối đơn hàng</li>";
echo "<li>✅ Theo dõi lịch sử gán đơn</li>";
echo "</ul>";
echo "<p><a href='" . admin_url('admin.php?page=booking-orders') . "'>← Quay lại Quản Lý Đơn Hàng</a></p>";
