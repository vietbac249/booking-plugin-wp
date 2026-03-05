<?php
if (!defined('ABSPATH')) exit;
global $wpdb;

// Lấy thống kê
$stats = array();

// 1. Thống kê đơn hàng
$stats['total_bookings'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings");
$stats['pending_bookings'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE status = 'pending'");
$stats['completed_bookings'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE status = 'completed'");
$stats['cancelled_bookings'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE status = 'cancelled'");
$stats['today_bookings'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE DATE(created_at) = CURDATE()");

// 2. Thống kê doanh thu
$stats['total_revenue'] = $wpdb->get_var("SELECT SUM(price) FROM {$wpdb->prefix}bookings WHERE status = 'completed'");
$stats['today_revenue'] = $wpdb->get_var("SELECT SUM(price) FROM {$wpdb->prefix}bookings WHERE status = 'completed' AND DATE(created_at) = CURDATE()");
$stats['month_revenue'] = $wpdb->get_var("SELECT SUM(price) FROM {$wpdb->prefix}bookings WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");

// 3. Thống kê tài xế
$stats['total_drivers'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}drivers");
$stats['active_drivers'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}drivers WHERE status = 'active'");
$stats['pending_drivers'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}drivers WHERE status = 'pending'");
$stats['verified_drivers'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}drivers WHERE status = 'verified'");

// 4. Thống kê hợp đồng
$stats['total_contracts'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}contracts");
$stats['active_contracts'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}contracts WHERE status = 'active'");
$stats['expired_contracts'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}contracts WHERE status = 'expired' OR end_date < CURDATE()");

// 5. Thống kê đánh giá
$stats['total_reviews'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}reviews");
$stats['avg_rating'] = $wpdb->get_var("SELECT AVG(rating) FROM {$wpdb->prefix}reviews");

// 6. Top 5 tài xế xuất sắc
$top_drivers = $wpdb->get_results("
    SELECT d.*, 
           COUNT(b.id) as total_trips,
           AVG(r.rating) as avg_rating
    FROM {$wpdb->prefix}drivers d
    LEFT JOIN {$wpdb->prefix}bookings b ON d.id = b.driver_id AND b.status = 'completed'
    LEFT JOIN {$wpdb->prefix}reviews r ON d.id = r.driver_id
    WHERE d.status = 'active'
    GROUP BY d.id
    ORDER BY d.points DESC, avg_rating DESC
    LIMIT 5
");

// 7. Đơn hàng gần đây
$recent_bookings = $wpdb->get_results("
    SELECT b.*, d.full_name as driver_name
    FROM {$wpdb->prefix}bookings b
    LEFT JOIN {$wpdb->prefix}drivers d ON b.driver_id = d.id
    ORDER BY b.created_at DESC
    LIMIT 10
");

// 8. Thống kê theo loại xe
$car_type_stats = $wpdb->get_results("
    SELECT car_type, COUNT(*) as count, SUM(price) as revenue
    FROM {$wpdb->prefix}bookings
    WHERE status = 'completed'
    GROUP BY car_type
    ORDER BY count DESC
");

// 9. Thống kê theo tháng (6 tháng gần nhất)
$monthly_stats = $wpdb->get_results("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as bookings,
        SUM(price) as revenue
    FROM {$wpdb->prefix}bookings
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month DESC
");

?>

<div class="wrap">
    <h1>📊 Dashboard - Tổng Quan Hệ Thống</h1>
    
    <!-- Thống kê tổng quan -->
    <div class="dashboard-stats">
        <div class="stat-row">
            <!-- Đơn hàng -->
            <div class="stat-card stat-bookings">
                <div class="stat-icon">🚗</div>
                <div class="stat-content">
                    <h3>Đơn Hàng</h3>
                    <div class="stat-number"><?php echo number_format($stats['total_bookings']); ?></div>
                    <div class="stat-details">
                        <span class="stat-label">Hôm nay:</span>
                        <span class="stat-value"><?php echo $stats['today_bookings']; ?></span>
                    </div>
                    <div class="stat-breakdown">
                        <span class="pending">Chờ: <?php echo $stats['pending_bookings']; ?></span>
                        <span class="completed">Hoàn thành: <?php echo $stats['completed_bookings']; ?></span>
                        <span class="cancelled">Hủy: <?php echo $stats['cancelled_bookings']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Doanh thu -->
            <div class="stat-card stat-revenue">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h3>Doanh Thu</h3>
                    <div class="stat-number"><?php echo number_format($stats['total_revenue'] ?: 0); ?>đ</div>
                    <div class="stat-details">
                        <span class="stat-label">Hôm nay:</span>
                        <span class="stat-value"><?php echo number_format($stats['today_revenue'] ?: 0); ?>đ</span>
                    </div>
                    <div class="stat-details">
                        <span class="stat-label">Tháng này:</span>
                        <span class="stat-value"><?php echo number_format($stats['month_revenue'] ?: 0); ?>đ</span>
                    </div>
                </div>
            </div>

            <!-- Tài xế -->
            <div class="stat-card stat-drivers">
                <div class="stat-icon">👨‍✈️</div>
                <div class="stat-content">
                    <h3>Tài Xế</h3>
                    <div class="stat-number"><?php echo number_format($stats['total_drivers']); ?></div>
                    <div class="stat-breakdown">
                        <span class="active">Hoạt động: <?php echo $stats['active_drivers']; ?></span>
                        <span class="pending">Chờ duyệt: <?php echo $stats['pending_drivers']; ?></span>
                        <span class="verified">Đã xác minh: <?php echo $stats['verified_drivers']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Hợp đồng -->
            <div class="stat-card stat-contracts">
                <div class="stat-icon">📄</div>
                <div class="stat-content">
                    <h3>Hợp Đồng</h3>
                    <div class="stat-number"><?php echo number_format($stats['total_contracts']); ?></div>
                    <div class="stat-breakdown">
                        <span class="active">Còn hiệu lực: <?php echo $stats['active_contracts']; ?></span>
                        <span class="expired">Hết hạn: <?php echo $stats['expired_contracts']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Đánh giá -->
            <div class="stat-card stat-reviews">
                <div class="stat-icon">⭐</div>
                <div class="stat-content">
                    <h3>Đánh Giá</h3>
                    <div class="stat-number"><?php echo number_format($stats['total_reviews']); ?></div>
                    <div class="stat-details">
                        <span class="stat-label">Trung bình:</span>
                        <span class="stat-value rating-value">
                            <?php echo number_format($stats['avg_rating'] ?: 0, 1); ?> ⭐
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ và bảng -->
    <div class="dashboard-content">
        <div class="dashboard-row">
            <!-- Biểu đồ doanh thu 6 tháng -->
            <div class="dashboard-box chart-box">
                <h2>📈 Doanh Thu 6 Tháng Gần Nhất</h2>
                <canvas id="revenueChart"></canvas>
            </div>

            <!-- Thống kê theo loại xe -->
            <div class="dashboard-box">
                <h2>🚙 Thống Kê Theo Loại Xe</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Loại Xe</th>
                            <th>Số Chuyến</th>
                            <th>Doanh Thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($car_type_stats)): ?>
                            <?php foreach ($car_type_stats as $car): ?>
                                <tr>
                                    <td><strong><?php echo esc_html($car->car_type); ?></strong></td>
                                    <td><?php echo number_format($car->count); ?> chuyến</td>
                                    <td><?php echo number_format($car->revenue); ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">Chưa có dữ liệu</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard-row">
            <!-- Top 5 tài xế xuất sắc -->
            <div class="dashboard-box">
                <h2>🏆 Top 5 Tài Xế Xuất Sắc</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Hạng</th>
                            <th>Tài Xế</th>
                            <th>Điểm</th>
                            <th>Chuyến</th>
                            <th>Đánh Giá</th>
                            <th>Hạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($top_drivers)): ?>
                            <?php 
                            $rank = 1;
                            foreach ($top_drivers as $driver): 
                                $rank_icon = $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : ''));
                            ?>
                                <tr>
                                    <td><?php echo $rank_icon . ' #' . $rank; ?></td>
                                    <td>
                                        <strong><?php echo esc_html($driver->full_name); ?></strong><br>
                                        <small><?php echo esc_html($driver->phone); ?></small>
                                    </td>
                                    <td><strong><?php echo number_format($driver->points); ?></strong> điểm</td>
                                    <td><?php echo number_format($driver->total_trips ?: 0); ?> chuyến</td>
                                    <td><?php echo number_format($driver->avg_rating ?: 0, 1); ?> ⭐</td>
                                    <td>
                                        <span class="rank-badge rank-<?php echo esc_attr($driver->rank); ?>">
                                            <?php 
                                            $ranks = array('new' => 'Mới', 'bronze' => 'Đồng', 'silver' => 'Bạc', 'gold' => 'Vàng', 'platinum' => 'Bạch Kim');
                                            echo $ranks[$driver->rank] ?? 'Mới';
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                            $rank++;
                            endforeach; 
                            ?>
                        <?php else: ?>
                            <tr><td colspan="6">Chưa có tài xế nào</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Đơn hàng gần đây -->
            <div class="dashboard-box">
                <h2>📋 Đơn Hàng Gần Đây</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Tuyến</th>
                            <th>Giá</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_bookings)): ?>
                            <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td><strong><?php echo esc_html($booking->booking_code); ?></strong></td>
                                    <td>
                                        <?php echo esc_html($booking->customer_name); ?><br>
                                        <small><?php echo esc_html($booking->customer_phone); ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo esc_html(substr($booking->from_location, 0, 30)); ?> →<br>
                                            <?php echo esc_html(substr($booking->to_location, 0, 30)); ?>
                                        </small>
                                    </td>
                                    <td><?php echo number_format($booking->price); ?>đ</td>
                                    <td>
                                        <span class="status-badge status-<?php echo esc_attr($booking->status); ?>">
                                            <?php 
                                            $statuses = array(
                                                'pending' => 'Chờ xử lý',
                                                'confirmed' => 'Đã xác nhận',
                                                'assigned' => 'Đã phân xe',
                                                'in_progress' => 'Đang thực hiện',
                                                'completed' => 'Hoàn thành',
                                                'cancelled' => 'Đã hủy'
                                            );
                                            echo $statuses[$booking->status] ?? $booking->status;
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">Chưa có đơn hàng nào</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Biểu đồ doanh thu
    var ctx = document.getElementById('revenueChart');
    if (ctx) {
        var monthlyData = <?php echo json_encode(array_reverse($monthly_stats)); ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(function(item) { return item.month; }),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: monthlyData.map(function(item) { return item.revenue; }),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Số đơn hàng',
                    data: monthlyData.map(function(item) { return item.bookings; }),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    }
});
</script>

<style>
.dashboard-stats {
    margin: 20px 0;
}

.stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    gap: 15px;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-icon {
    font-size: 48px;
    line-height: 1;
}

.stat-content {
    flex: 1;
}

.stat-content h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #2271b1;
    margin-bottom: 10px;
}

.stat-details {
    margin: 5px 0;
    font-size: 13px;
}

.stat-label {
    color: #666;
}

.stat-value {
    font-weight: bold;
    color: #333;
}

.stat-breakdown {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 10px;
    font-size: 12px;
}

.stat-breakdown span {
    padding: 3px 8px;
    border-radius: 4px;
    background: #f0f0f0;
}

.stat-breakdown .pending {
    background: #fff3cd;
    color: #856404;
}

.stat-breakdown .completed,
.stat-breakdown .active {
    background: #d4edda;
    color: #155724;
}

.stat-breakdown .cancelled,
.stat-breakdown .expired {
    background: #f8d7da;
    color: #721c24;
}

.stat-breakdown .verified {
    background: #d1ecf1;
    color: #0c5460;
}

.dashboard-content {
    margin-top: 30px;
}

.dashboard-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.dashboard-box {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dashboard-box h2 {
    margin-top: 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.chart-box {
    grid-column: span 2;
}

.chart-box canvas {
    max-height: 300px;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-confirmed,
.status-assigned {
    background: #d1ecf1;
    color: #0c5460;
}

.status-in_progress {
    background: #cce5ff;
    color: #004085;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.rank-badge {
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.rank-new {
    background: #e0e0e0;
    color: #666;
}

.rank-bronze {
    background: #cd7f32;
    color: #fff;
}

.rank-silver {
    background: #c0c0c0;
    color: #333;
}

.rank-gold {
    background: #ffd700;
    color: #333;
}

.rank-platinum {
    background: #e5e4e2;
    color: #333;
}

.rating-value {
    color: #f39c12;
    font-weight: bold;
}

@media (max-width: 768px) {
    .stat-row {
        grid-template-columns: 1fr;
    }
    
    .dashboard-row {
        grid-template-columns: 1fr;
    }
    
    .chart-box {
        grid-column: span 1;
    }
}
</style>
