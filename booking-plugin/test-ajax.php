<?php
/**
 * Test AJAX Handler
 * Kiểm tra xem AJAX search_drivers có hoạt động không
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Bạn không có quyền truy cập!');
}

echo "<h2>🧪 Test AJAX Handler: search_drivers</h2>";

// Simulate AJAX request
$_POST['action'] = 'search_drivers';
$_POST['search'] = '19 B1 27726'; // Biển số xe
$_POST['nonce'] = wp_create_nonce('booking_nonce');

echo "<h3>📤 Request Data:</h3>";
echo "<pre>";
print_r([
    'action' => $_POST['action'],
    'search' => $_POST['search'],
    'nonce' => $_POST['nonce']
]);
echo "</pre>";

// Call the AJAX handler directly
global $wpdb;

echo "<h3>🔍 Direct Database Query:</h3>";

$search = '19 B1 27726';
$drivers = $wpdb->get_results($wpdb->prepare(
    "SELECT id, full_name, phone, car_type, car_plate, rating, status, telegram_chat_id, zalo_user_id
    FROM {$wpdb->prefix}drivers
    WHERE status = 'active' 
    AND (full_name LIKE %s OR phone LIKE %s OR car_plate LIKE %s)
    ORDER BY rating DESC, full_name ASC
    LIMIT 10",
    '%' . $wpdb->esc_like($search) . '%',
    '%' . $wpdb->esc_like($search) . '%',
    '%' . $wpdb->esc_like($search) . '%'
));

echo "<p><strong>SQL Query:</strong></p>";
echo "<pre>";
echo $wpdb->last_query;
echo "</pre>";

echo "<p><strong>Results:</strong></p>";
if ($drivers) {
    echo "<p style='color: green;'>✅ Tìm thấy " . count($drivers) . " tài xế</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Họ Tên</th><th>SĐT</th><th>Loại Xe</th><th>Biển Số</th><th>Status</th><th>Rating</th>";
    echo "</tr>";
    
    foreach ($drivers as $driver) {
        echo "<tr>";
        echo "<td>{$driver->id}</td>";
        echo "<td>{$driver->full_name}</td>";
        echo "<td>{$driver->phone}</td>";
        echo "<td>{$driver->car_type}</td>";
        echo "<td><strong>{$driver->car_plate}</strong></td>";
        echo "<td style='color: green;'>{$driver->status}</td>";
        echo "<td>⭐ {$driver->rating}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Format response như AJAX
    echo "<h3>📥 AJAX Response (JSON):</h3>";
    $results = [];
    foreach ($drivers as $driver) {
        $has_telegram = !empty($driver->telegram_chat_id);
        $has_zalo = !empty($driver->zalo_user_id);
        
        $results[] = [
            'id' => $driver->id,
            'name' => $driver->full_name,
            'phone' => $driver->phone,
            'car_type' => $driver->car_type,
            'car_plate' => $driver->car_plate,
            'rating' => number_format($driver->rating, 1),
            'has_telegram' => $has_telegram,
            'has_zalo' => $has_zalo,
            'label' => sprintf(
                '%s - %s (%s) - ⭐ %s %s',
                $driver->full_name,
                $driver->phone,
                $driver->car_plate,
                number_format($driver->rating, 1),
                ($has_telegram ? '📱' : '') . ($has_zalo ? '💬' : '')
            )
        ];
    }
    
    echo "<pre>";
    echo json_encode(['success' => true, 'data' => ['drivers' => $results]], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "</pre>";
    
} else {
    echo "<p style='color: red;'>❌ Không tìm thấy tài xế nào</p>";
    
    // Check if any drivers exist
    $total_drivers = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}drivers");
    $active_drivers = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}drivers WHERE status = 'active'");
    
    echo "<p><strong>Thống kê:</strong></p>";
    echo "<ul>";
    echo "<li>Tổng số tài xế: <strong>{$total_drivers}</strong></li>";
    echo "<li>Tài xế active: <strong>{$active_drivers}</strong></li>";
    echo "</ul>";
    
    if ($active_drivers == 0) {
        echo "<p style='color: orange;'>⚠️ <strong>Không có tài xế nào có status = 'active'!</strong></p>";
        echo "<p>Vui lòng kích hoạt tài xế tại: <a href='debug-drivers.php'>debug-drivers.php</a></p>";
    }
}

echo "<hr>";
echo "<h3>🔧 Test AJAX Endpoint</h3>";
echo "<p>Thử gọi AJAX endpoint trực tiếp:</p>";
echo "<pre>";
echo "URL: " . admin_url('admin-ajax.php') . "\n";
echo "Method: POST\n";
echo "Data:\n";
echo "  action: search_drivers\n";
echo "  nonce: " . wp_create_nonce('booking_nonce') . "\n";
echo "  search: 19 B1 27726\n";
echo "</pre>";

echo "<button onclick='testAjax()' style='padding: 10px 20px; background: #2271b1; color: white; border: none; border-radius: 4px; cursor: pointer;'>🧪 Test AJAX</button>";
echo "<div id='ajax-result' style='margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 4px; display: none;'></div>";

?>

<script>
function testAjax() {
    const resultDiv = document.getElementById('ajax-result');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '⏳ Đang gửi request...';
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'search_drivers',
            nonce: '<?php echo wp_create_nonce("booking_nonce"); ?>',
            search: '19 B1 27726'
        })
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.innerHTML = '<h4>✅ Response:</h4><pre>' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        resultDiv.innerHTML = '<h4>❌ Error:</h4><pre>' + error + '</pre>';
    });
}
</script>

<hr>
<p><a href="<?php echo admin_url('admin.php?page=booking-orders'); ?>">← Quay lại Đơn Hàng</a></p>
<p><a href="debug-drivers.php">→ Debug Tài Xế</a></p>

<hr>
<p style='color: #999; font-size: 12px;'>© 2026 Nguyễn Việt Bắc. All Rights Reserved.</p>