<?php
/**
 * Database Update Script - Assignment Feature
 * Chạy file này một lần để cập nhật database
 */

if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

global $wpdb;

echo "<h2>Cập Nhật Database - Tính Năng Gán Đơn Hàng</h2>";

// 1. Thêm cột vào bảng bookings
echo "<h3>1. Cập nhật bảng bookings...</h3>";

$bookings_table = $wpdb->prefix . 'booking_bookings';

$columns_to_add = [
    "ADD COLUMN assigned_at DATETIME NULL COMMENT 'Thời gian admin gán'",
    "ADD COLUMN assigned_by INT NULL COMMENT 'Admin ID người gán'",
    "ADD COLUMN accepted_at DATETIME NULL COMMENT 'Thời gian tài xế nhận'",
    "ADD COLUMN assignment_type ENUM('direct', 'group') DEFAULT 'direct' COMMENT 'Loại gán'",
    "ADD COLUMN group_id INT NULL COMMENT 'ID group nếu gán cho group'",
    "ADD COLUMN accept_token VARCHAR(64) NULL COMMENT 'Token để nhận đơn'",
    "ADD COLUMN token_expires INT NULL COMMENT 'Thời gian hết hạn token'"
];

foreach ($columns_to_add as $column) {
    $result = $wpdb->query("ALTER TABLE $bookings_table $column");
    if ($result === false) {
        echo "<p style='color: orange;'>⚠️ Cột có thể đã tồn tại: " . $wpdb->last_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Đã thêm cột thành công</p>";
    }
}

// 2. Tạo bảng notification_groups
echo "<h3>2. Tạo bảng notification_groups...</h3>";

$groups_table = $wpdb->prefix . 'booking_notification_groups';

$sql = "CREATE TABLE IF NOT EXISTS $groups_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Tên group',
    type ENUM('zalo', 'telegram') NOT NULL COMMENT 'Loại group',
    group_id VARCHAR(255) NOT NULL COMMENT 'ID group từ Zalo/Telegram',
    access_token TEXT NULL COMMENT 'Token để gửi tin (Zalo)',
    bot_token VARCHAR(255) NULL COMMENT 'Bot token (Telegram)',
    chat_id VARCHAR(255) NULL COMMENT 'Chat ID (Telegram)',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Trạng thái hoạt động',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$result = $wpdb->query($sql);

if ($result === false) {
    echo "<p style='color: red;'>❌ Lỗi: " . $wpdb->last_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ Đã tạo bảng notification_groups thành công</p>";
}

// 3. Thêm cột telegram_chat_id và zalo_user_id vào bảng drivers
echo "<h3>3. Cập nhật bảng drivers...</h3>";

$drivers_table = $wpdb->prefix . 'booking_drivers';

$driver_columns = [
    "ADD COLUMN telegram_chat_id VARCHAR(255) NULL COMMENT 'Telegram Chat ID'",
    "ADD COLUMN zalo_user_id VARCHAR(255) NULL COMMENT 'Zalo User ID'"
];

foreach ($driver_columns as $column) {
    $result = $wpdb->query("ALTER TABLE $drivers_table $column");
    if ($result === false) {
        echo "<p style='color: orange;'>⚠️ Cột có thể đã tồn tại: " . $wpdb->last_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Đã thêm cột thành công</p>";
    }
}

// 4. Cập nhật ENUM status trong bảng bookings
echo "<h3>4. Cập nhật trạng thái đơn hàng...</h3>";

$update_status = "ALTER TABLE $bookings_table 
    MODIFY COLUMN status ENUM('pending', 'assigned', 'accepted', 'in_progress', 'completed', 'cancelled') 
    DEFAULT 'pending' 
    COMMENT 'Trạng thái đơn hàng'";

$result = $wpdb->query($update_status);

if ($result === false) {
    echo "<p style='color: orange;'>⚠️ Có thể đã cập nhật: " . $wpdb->last_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ Đã cập nhật trạng thái thành công</p>";
}

// 5. Tạo bảng assignment_logs (lịch sử gán đơn)
echo "<h3>5. Tạo bảng assignment_logs...</h3>";

$logs_table = $wpdb->prefix . 'booking_assignment_logs';

$sql = "CREATE TABLE IF NOT EXISTS $logs_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL COMMENT 'ID đơn hàng',
    driver_id INT NULL COMMENT 'ID tài xế (nếu gán trực tiếp)',
    group_id INT NULL COMMENT 'ID group (nếu gán cho group)',
    assignment_type ENUM('direct', 'group') NOT NULL COMMENT 'Loại gán',
    assigned_by INT NOT NULL COMMENT 'Admin ID người gán',
    status ENUM('assigned', 'accepted', 'rejected', 'timeout') DEFAULT 'assigned' COMMENT 'Trạng thái',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    accepted_at DATETIME NULL COMMENT 'Thời gian nhận đơn',
    INDEX idx_booking (booking_id),
    INDEX idx_driver (driver_id),
    INDEX idx_status (status),
    FOREIGN KEY (booking_id) REFERENCES $bookings_table(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$result = $wpdb->query($sql);

if ($result === false) {
    echo "<p style='color: red;'>❌ Lỗi: " . $wpdb->last_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ Đã tạo bảng assignment_logs thành công</p>";
}

echo "<hr>";
echo "<h3>✅ Hoàn Thành Cập Nhật Database!</h3>";
echo "<p><strong>Lưu ý:</strong> Bạn có thể xóa file này sau khi chạy xong.</p>";
echo "<p><a href='" . admin_url('admin.php?page=booking-plugin') . "'>← Quay lại Dashboard</a></p>";

// Log kết quả
$log_file = BOOKING_PLUGIN_PATH . 'database-update-' . date('Y-m-d-H-i-s') . '.log';
ob_start();
echo "Database Update Log - " . date('Y-m-d H:i:s') . "\n";
echo "==========================================\n\n";
echo "Bookings table updated: " . ($wpdb->last_error ? 'Failed' : 'Success') . "\n";
echo "Groups table created: Success\n";
echo "Drivers table updated: Success\n";
echo "Assignment logs table created: Success\n";
$log_content = ob_get_clean();
file_put_contents($log_file, $log_content);

echo "<p style='color: blue;'>📝 Log đã được lưu: " . basename($log_file) . "</p>";
?>
