<?php
if (!defined('ABSPATH')) exit;
global $wpdb;

$contracts = $wpdb->get_results("
    SELECT c.*, d.full_name, d.phone 
    FROM {$wpdb->prefix}contracts c
    LEFT JOIN {$wpdb->prefix}drivers d ON c.driver_id = d.id
    ORDER BY c.created_at DESC
");
?>
<div class="wrap">
    <h1>Quản Lý Hợp Đồng</h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Mã HĐ</th>
                <th>Tài Xế</th>
                <th>SĐT</th>
                <th>Ngày Bắt Đầu</th>
                <th>Ngày Kết Thúc</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($contracts): ?>
                <?php foreach ($contracts as $contract): ?>
                    <tr>
                        <td><?php echo esc_html($contract->contract_code); ?></td>
                        <td><?php echo esc_html($contract->full_name); ?></td>
                        <td><?php echo esc_html($contract->phone); ?></td>
                        <td><?php echo esc_html($contract->start_date); ?></td>
                        <td><?php echo esc_html($contract->end_date); ?></td>
                        <td><?php echo esc_html($contract->status); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Chưa có hợp đồng</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
