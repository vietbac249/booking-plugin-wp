<?php
if (!defined('ABSPATH')) exit;
global $wpdb;

$drivers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}drivers ORDER BY points DESC LIMIT 20");
?>
<div class="wrap">
    <h1>Bảng Xếp Hạng</h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Hạng</th>
                <th>Tài Xế</th>
                <th>Điểm</th>
                <th>Đơn Hoàn Thành</th>
                <th>Đánh Giá</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($drivers): ?>
                <?php $rank = 1; foreach ($drivers as $driver): ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo esc_html($driver->full_name); ?></td>
                        <td><?php echo number_format($driver->points); ?></td>
                        <td><?php echo number_format($driver->completed_trips); ?></td>
                        <td><?php echo number_format($driver->rating, 1); ?>⭐</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Chưa có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
