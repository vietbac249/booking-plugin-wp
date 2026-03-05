<?php
if (!defined('ABSPATH')) exit;
global $wpdb;

$reviews = $wpdb->get_results("
    SELECT r.*, d.full_name as driver_name, b.booking_code
    FROM {$wpdb->prefix}reviews r
    LEFT JOIN {$wpdb->prefix}drivers d ON r.driver_id = d.id
    LEFT JOIN {$wpdb->prefix}bookings b ON r.booking_id = b.id
    ORDER BY r.created_at DESC
");
?>
<div class="wrap">
    <h1>Đánh Giá & Feedback</h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Mã Đơn</th>
                <th>Tài Xế</th>
                <th>Đánh Giá</th>
                <th>Nhận Xét</th>
                <th>Ngày</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?php echo esc_html($review->booking_code); ?></td>
                        <td><?php echo esc_html($review->driver_name); ?></td>
                        <td><?php echo number_format($review->rating, 1); ?>⭐</td>
                        <td><?php echo esc_html($review->comment); ?></td>
                        <td><?php echo esc_html($review->created_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Chưa có đánh giá</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
