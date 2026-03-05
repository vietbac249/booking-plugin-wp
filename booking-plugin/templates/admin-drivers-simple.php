<?php
/**
 * Admin Drivers Management Template - Simple Version for Testing
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Test query
try {
    $drivers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}drivers ORDER BY created_at DESC LIMIT 10");
    $error = $wpdb->last_error;
} catch (Exception $e) {
    $error = $e->getMessage();
}

?>

<div class="wrap">
    <h1>Quản Lý Tài Xế - Test Version</h1>
    
    <?php if ($error): ?>
        <div class="notice notice-error">
            <p>Database Error: <?php echo esc_html($error); ?></p>
        </div>
    <?php endif; ?>
    
    <p>Total drivers found: <?php echo count($drivers); ?></p>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ Tên</th>
                <th>SĐT</th>
                <th>Loại Xe</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($drivers)): ?>
                <?php foreach ($drivers as $driver): ?>
                    <tr>
                        <td><?php echo esc_html($driver->id); ?></td>
                        <td><?php echo esc_html($driver->full_name); ?></td>
                        <td><?php echo esc_html($driver->phone); ?></td>
                        <td><?php echo esc_html($driver->car_type); ?></td>
                        <td><?php echo esc_html($driver->status); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Chưa có tài xế nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <h2>Debug Info</h2>
    <pre><?php print_r($drivers); ?></pre>
</div>
