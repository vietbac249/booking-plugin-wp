<?php
/**
 * Test Accept Booking AJAX
 * Truy cập: http://your-site.com/wp-content/plugins/booking-plugin/test-accept-ajax.php
 */

require_once('../../../wp-load.php');

echo "<h1>🧪 Test Accept Booking AJAX</h1>";
echo "<hr>";

// Test data
$test_booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 4;
$test_token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
$test_phone = isset($_GET['phone']) ? sanitize_text_field($_GET['phone']) : '0904885057';

echo "<h2>📋 Test Parameters:</h2>";
echo "<ul>";
echo "<li><strong>Booking ID:</strong> $test_booking_id</li>";
echo "<li><strong>Token:</strong> $test_token</li>";
echo "<li><strong>Phone:</strong> $test_phone</li>";
echo "</ul>";

if (empty($test_token)) {
    echo "<p style='color: red;'>⚠️ Vui lòng cung cấp token trong URL:</p>";
    echo "<code>?booking_id=4&token=YOUR_TOKEN&phone=0904885057</code>";
    exit;
}

echo "<hr>";
echo "<h2>🔍 Kiểm Tra Booking:</h2>";

global $wpdb;

$booking = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}bookings WHERE id = %d",
    $test_booking_id
));

if (!$booking) {
    echo "<p style='color: red;'>❌ Không tìm thấy booking với ID: $test_booking_id</p>";
    exit;
}

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Value</th></tr>";
echo "<tr><td>ID</td><td>{$booking->id}</td></tr>";
echo "<tr><td>Booking Code</td><td>{$booking->booking_code}</td></tr>";
echo "<tr><td>Status</td><td><strong>{$booking->status}</strong></td></tr>";
echo "<tr><td>Assignment Type</td><td><strong>{$booking->assignment_type}</strong></td></tr>";
echo "<tr><td>Driver ID</td><td>{$booking->driver_id}</td></tr>";
echo "<tr><td>Group ID</td><td>{$booking->group_id}</td></tr>";
echo "<tr><td>Accept Token</td><td>" . substr($booking->accept_token, 0, 20) . "...</td></tr>";
echo "<tr><td>Token Expires</td><td>" . ($booking->token_expires ? date('Y-m-d H:i:s', $booking->token_expires) : 'N/A') . "</td></tr>";
echo "</table>";

echo "<hr>";
echo "<h2>🔍 Kiểm Tra Token:</h2>";

if ($booking->accept_token === $test_token) {
    echo "<p style='color: green;'>✅ Token khớp!</p>";
} else {
    echo "<p style='color: red;'>❌ Token không khớp!</p>";
    echo "<p><strong>Token trong DB:</strong> " . substr($booking->accept_token, 0, 30) . "...</p>";
    echo "<p><strong>Token test:</strong> " . substr($test_token, 0, 30) . "...</p>";
}

if ($booking->token_expires && $booking->token_expires < time()) {
    echo "<p style='color: red;'>❌ Token đã hết hạn!</p>";
} else {
    echo "<p style='color: green;'>✅ Token còn hiệu lực</p>";
}

echo "<hr>";
echo "<h2>🔍 Kiểm Tra Tài Xế:</h2>";

$driver = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}drivers WHERE phone = %s",
    $test_phone
));

if ($driver) {
    echo "<p style='color: green;'>✅ Tìm thấy tài xế!</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>ID</td><td>{$driver->id}</td></tr>";
    echo "<tr><td>Full Name</td><td>{$driver->full_name}</td></tr>";
    echo "<tr><td>Phone</td><td>{$driver->phone}</td></tr>";
    echo "<tr><td>Status</td><td><strong>{$driver->status}</strong></td></tr>";
    echo "<tr><td>Telegram Chat ID</td><td>{$driver->telegram_chat_id}</td></tr>";
    echo "<tr><td>Zalo User ID</td><td>{$driver->zalo_user_id}</td></tr>";
    echo "</table>";
    
    if ($driver->status !== 'active') {
        echo "<p style='color: red;'>⚠️ Tài xế chưa được kích hoạt!</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Không tìm thấy tài xế với SĐT: $test_phone</p>";
}

echo "<hr>";
echo "<h2>🧪 Test AJAX Call:</h2>";

if ($booking->accept_token === $test_token && $driver && $driver->status === 'active') {
    echo "<button onclick='testAccept()' style='padding: 15px 30px; background: #28a745; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;'>
        🚀 Test Accept Booking
    </button>";
    
    echo "<div id='result' style='margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; display: none;'></div>";
    
    echo "<script>
    function testAccept() {
        var resultDiv = document.getElementById('result');
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '⏳ Đang gửi request...';
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '" . admin_url('admin-ajax.php') . "', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    console.log('Response:', response);
                    
                    if (response.success) {
                        resultDiv.innerHTML = '<div style=\"background: #d4edda; color: #155724; padding: 15px; border-radius: 4px;\">' +
                            '<strong>✅ Thành công!</strong><br>' +
                            'Message: ' + response.data.message + '<br>' +
                            'Driver: ' + response.data.driver_name +
                            '</div>';
                    } else {
                        resultDiv.innerHTML = '<div style=\"background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px;\">' +
                            '<strong>❌ Lỗi!</strong><br>' +
                            'Message: ' + response.data.message +
                            '</div>';
                    }
                } catch(e) {
                    resultDiv.innerHTML = '<div style=\"background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px;\">' +
                        '<strong>❌ Parse Error!</strong><br>' +
                        'Response: ' + xhr.responseText +
                        '</div>';
                }
            } else {
                resultDiv.innerHTML = '<div style=\"background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px;\">' +
                    '<strong>❌ HTTP Error!</strong><br>' +
                    'Status: ' + xhr.status +
                    '</div>';
            }
        };
        
        xhr.onerror = function() {
            resultDiv.innerHTML = '<div style=\"background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px;\">' +
                '<strong>❌ Network Error!</strong>' +
                '</div>';
        };
        
        var data = 'action=accept_booking&booking_id=$test_booking_id&token=$test_token&driver_phone=$test_phone';
        console.log('Sending:', data);
        xhr.send(data);
    }
    </script>";
} else {
    echo "<p style='color: red;'>❌ Không thể test vì có lỗi ở trên. Vui lòng kiểm tra lại.</p>";
}

echo "<hr>";
echo "<p><a href='" . admin_url('admin.php?page=booking-orders') . "'>← Quay lại Quản Lý Đơn Hàng</a></p>";
