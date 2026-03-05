<?php
/**
 * Test Rewrite Rules
 * Truy cập: http://your-site.com/wp-content/plugins/booking-plugin/test-rewrite.php
 */

require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Bạn không có quyền truy cập!');
}

echo "<h1>🔍 Kiểm Tra Rewrite Rules</h1>";
echo "<hr>";

// Get all rewrite rules
global $wp_rewrite;
$rules = get_option('rewrite_rules');

echo "<h2>📋 Tất Cả Rewrite Rules:</h2>";
echo "<pre>";
print_r($rules);
echo "</pre>";

echo "<hr>";

// Check if our rule exists
$our_rule_exists = false;
if (is_array($rules)) {
    foreach ($rules as $pattern => $rewrite) {
        if (strpos($pattern, 'nhan-don-hang') !== false) {
            $our_rule_exists = true;
            echo "<h2 style='color: green;'>✅ Rule 'nhan-don-hang' TÌM THẤY:</h2>";
            echo "<p><strong>Pattern:</strong> $pattern</p>";
            echo "<p><strong>Rewrite:</strong> $rewrite</p>";
            break;
        }
    }
}

if (!$our_rule_exists) {
    echo "<h2 style='color: red;'>❌ Rule 'nhan-don-hang' KHÔNG TÌM THẤY!</h2>";
    echo "<p>Cần flush rewrite rules.</p>";
    
    echo "<h3>🔧 Thử Flush Rewrite Rules:</h3>";
    flush_rewrite_rules();
    echo "<p style='color: green;'>✅ Đã flush rewrite rules. Vui lòng <a href=''>reload trang này</a> để kiểm tra lại.</p>";
}

echo "<hr>";
echo "<h2>🧪 Test URL:</h2>";
echo "<p>Thử truy cập: <a href='" . home_url('/nhan-don-hang/?booking=1&token=test') . "' target='_blank'>" . home_url('/nhan-don-hang/?booking=1&token=test') . "</a></p>";

echo "<hr>";
echo "<h2>📝 Query Vars:</h2>";
global $wp;
echo "<pre>";
print_r($wp->public_query_vars);
echo "</pre>";

// Check if accept_booking is registered
if (in_array('accept_booking', $wp->public_query_vars)) {
    echo "<p style='color: green;'>✅ Query var 'accept_booking' đã được đăng ký</p>";
} else {
    echo "<p style='color: red;'>❌ Query var 'accept_booking' CHƯA được đăng ký</p>";
}
