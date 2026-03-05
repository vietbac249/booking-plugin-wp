<?php
// Script cap nhat database - Them cot from_location va to_location
// Chay file nay 1 lan de cap nhat database

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Ban khong co quyen truy cap!');
}

global $wpdb;

$table_name = $wpdb->prefix . 'custom_pricing';

// Kiem tra xem bang co ton tai khong
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

if (!$table_exists) {
    die('Bang custom_pricing chua ton tai. Vui long kich hoat lai plugin.');
}

// Kiem tra xem cot from_location da ton tai chua
$column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'from_location'");

if (empty($column_exists)) {
    // Them cot from_location
    $wpdb->query("ALTER TABLE $table_name ADD COLUMN from_location VARCHAR(255) AFTER id");
    echo "OK - Da them cot from_location<br>";
} else {
    echo "INFO - Cot from_location da ton tai<br>";
}

// Kiem tra xem cot to_location da ton tai chua
$column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'to_location'");

if (empty($column_exists)) {
    // Them cot to_location
    $wpdb->query("ALTER TABLE $table_name ADD COLUMN to_location VARCHAR(255) AFTER from_location");
    echo "OK - Da them cot to_location<br>";
} else {
    echo "INFO - Cot to_location da ton tai<br>";
}

// Them index
$wpdb->query("ALTER TABLE $table_name ADD INDEX idx_from_location (from_location)");
$wpdb->query("ALTER TABLE $table_name ADD INDEX idx_to_location (to_location)");
echo "OK - Da them index<br>";

// Hien thi cau truc bang
echo "<br><strong>Cau truc bang hien tai:</strong><br>";
$columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
echo "<pre>";
foreach ($columns as $column) {
    echo $column->Field . " - " . $column->Type . "\n";
}
echo "</pre>";

// Hien thi du lieu
echo "<br><strong>Du lieu trong bang:</strong><br>";
$data = $wpdb->get_results("SELECT * FROM $table_name");
echo "<pre>";
print_r($data);
echo "</pre>";

echo "<br><strong>HOAN THANH! Ban co the xoa file nay.</strong>";
?>
