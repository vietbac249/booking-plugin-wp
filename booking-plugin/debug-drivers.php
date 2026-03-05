<?php
/**
 * Debug Script - Kiểm tra tài xế
 * Chạy file này để xem danh sách tài xế và status
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Bạn không có quyền truy cập!');
}

global $wpdb;

echo "<h2>🔍 Debug: Danh Sách Tài Xế</h2>";

// Lấy tất cả tài xế
$drivers = $wpdb->get_results("
    SELECT id, full_name, phone, car_type, car_plate, status, rating, created_at
    FROM {$wpdb->prefix}drivers
    ORDER BY created_at DESC
");

if (!$drivers) {
    echo "<p style='color: red;'>❌ Không có tài xế nào trong database!</p>";
    echo "<p>Bảng: " . $wpdb->prefix . "drivers</p>";
    exit;
}

echo "<p>✅ Tìm thấy <strong>" . count($drivers) . "</strong> tài xế</p>";

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>ID</th>";
echo "<th>Họ Tên</th>";
echo "<th>SĐT</th>";
echo "<th>Loại Xe</th>";
echo "<th>Biển Số</th>";
echo "<th>Status</th>";
echo "<th>Rating</th>";
echo "<th>Ngày Tạo</th>";
echo "<th>Hành Động</th>";
echo "</tr>";

foreach ($drivers as $driver) {
    $status_color = $driver->status === 'active' ? 'green' : 'orange';
    $status_text = $driver->status === 'active' ? '✅ Active' : '⚠️ ' . ucfirst($driver->status);
    
    echo "<tr>";
    echo "<td>{$driver->id}</td>";
    echo "<td><strong>{$driver->full_name}</strong></td>";
    echo "<td>{$driver->phone}</td>";
    echo "<td>{$driver->car_type}</td>";
    echo "<td>{$driver->car_plate}</td>";
    echo "<td style='color: {$status_color};'><strong>{$status_text}</strong></td>";
    echo "<td>⭐ {$driver->rating}</td>";
    echo "<td>" . date('d/m/Y H:i', strtotime($driver->created_at)) . "</td>";
    
    if ($driver->status !== 'active') {
        echo "<td><a href='?activate={$driver->id}' style='color: green;'>Kích hoạt</a></td>";
    } else {
        echo "<td>-</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

// Xử lý kích hoạt tài xế
if (isset($_GET['activate'])) {
    $driver_id = intval($_GET['activate']);
    
    $updated = $wpdb->update(
        $wpdb->prefix . 'drivers',
        ['status' => 'active'],
        ['id' => $driver_id]
    );
    
    if ($updated) {
        echo "<script>alert('✅ Đã kích hoạt tài xế!'); window.location.href = window.location.pathname;</script>";
    }
}

echo "<hr>";
echo "<h3>📝 Hướng Dẫn</h3>";
echo "<ul>";
echo "<li>Chỉ tài xế có <strong style='color: green;'>Status = Active</strong> mới hiển thị trong autocomplete</li>";
echo "<li>Nếu tài xế đang <strong style='color: orange;'>Pending</strong>, click 'Kích hoạt' để chuyển sang Active</li>";
echo "<li>Sau khi kích hoạt, quay lại trang Đơn Hàng và thử gán lại</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='" . admin_url('admin.php?page=booking-orders') . "'>← Quay lại Đơn Hàng</a></p>";
echo "<p><a href='" . admin_url('admin.php?page=booking-drivers') . "'>→ Quản Lý Tài Xế</a></p>";

echo "<hr>";
echo "<p style='color: #999; font-size: 12px;'>© 2026 Nguyễn Việt Bắc. All Rights Reserved.</p>";
?>
