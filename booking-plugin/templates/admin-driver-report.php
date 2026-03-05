<?php
/**
 * Template: Báo Cáo Tài Xế
 * Tìm kiếm và xem báo cáo chi tiết theo tài xế
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get all drivers for dropdown
global $wpdb;
$drivers_table = $wpdb->prefix . 'booking_drivers';
$drivers = $wpdb->get_results("SELECT id, full_name, phone FROM $drivers_table WHERE status = 'active' ORDER BY full_name ASC");

// Process search
$search_results = null;
$driver_name = '';
$from_date = '';
$to_date = '';
$status_filter = 'all';

if (isset($_POST['search_driver_report']) && check_admin_referer('search_driver_report_action', 'search_driver_report_nonce')) {
    $driver_id = intval($_POST['driver_id']);
    $from_date = sanitize_text_field($_POST['from_date']);
    $to_date = sanitize_text_field($_POST['to_date']);
    $status_filter = sanitize_text_field($_POST['status_filter']);
    
    // Get driver info
    $driver = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $drivers_table WHERE id = %d",
        $driver_id
    ));
    
    if ($driver) {
        $driver_name = $driver->full_name;
        
        // Build query
        $bookings_table = $wpdb->prefix . 'booking_bookings';
        $reviews_table = $wpdb->prefix . 'booking_reviews';
        
        $where_clauses = ["driver_id = %d"];
        $query_params = [$driver_id];
        
        if (!empty($from_date)) {
            $where_clauses[] = "DATE(pickup_datetime) >= %s";
            $query_params[] = $from_date;
        }
        
        if (!empty($to_date)) {
            $where_clauses[] = "DATE(pickup_datetime) <= %s";
            $query_params[] = $to_date;
        }
        
        if ($status_filter !== 'all') {
            $where_clauses[] = "status = %s";
            $query_params[] = $status_filter;
        }
        
        $where_sql = implode(' AND ', $where_clauses);
        
        // Get bookings
        $bookings = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $bookings_table WHERE $where_sql ORDER BY pickup_datetime DESC",
            $query_params
        ));
        
        // Calculate statistics
        $total_bookings = count($bookings);
        $completed_bookings = 0;
        $cancelled_bookings = 0;
        $total_revenue = 0;
        $cancellation_reasons = [];
        
        foreach ($bookings as $booking) {
            if ($booking->status === 'completed') {
                $completed_bookings++;
                $total_revenue += floatval($booking->price);
            } elseif ($booking->status === 'cancelled') {
                $cancelled_bookings++;
                if (!empty($booking->cancellation_reason)) {
                    $reason = $booking->cancellation_reason;
                    if (!isset($cancellation_reasons[$reason])) {
                        $cancellation_reasons[$reason] = 0;
                    }
                    $cancellation_reasons[$reason]++;
                }
            }
        }
        
        // Get reviews
        $reviews = $wpdb->get_results($wpdb->prepare(
            "SELECT r.*, b.pickup_location, b.dropoff_location 
             FROM $reviews_table r
             LEFT JOIN $bookings_table b ON r.booking_id = b.id
             WHERE r.driver_id = %d
             ORDER BY r.created_at DESC",
            $driver_id
        ));
        
        $avg_rating = 0;
        if (!empty($reviews)) {
            $total_rating = array_sum(array_column($reviews, 'rating'));
            $avg_rating = $total_rating / count($reviews);
        }
        
        $search_results = [
            'driver' => $driver,
            'bookings' => $bookings,
            'reviews' => $reviews,
            'stats' => [
                'total_bookings' => $total_bookings,
                'completed_bookings' => $completed_bookings,
                'cancelled_bookings' => $cancelled_bookings,
                'total_revenue' => $total_revenue,
                'avg_rating' => $avg_rating,
                'cancellation_reasons' => $cancellation_reasons
            ]
        ];
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">📊 Báo Cáo Tài Xế</h1>
    <hr class="wp-header-end">
    
    <!-- Search Form -->
    <div class="driver-report-search">
        <form method="post" action="">
            <?php wp_nonce_field('search_driver_report_action', 'search_driver_report_nonce'); ?>
            
            <div class="search-form-grid">
                <div class="form-group">
                    <label for="driver_id">Tên Tài Xế</label>
                    <select name="driver_id" id="driver_id" required class="regular-text">
                        <option value="">-- Chọn tài xế --</option>
                        <?php foreach ($drivers as $driver): ?>
                            <option value="<?php echo esc_attr($driver->id); ?>">
                                <?php echo esc_html($driver->full_name); ?> (<?php echo esc_html($driver->phone); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="from_date">Từ Ngày</label>
                    <input type="date" name="from_date" id="from_date" value="<?php echo esc_attr($from_date); ?>" class="regular-text">
                </div>
                
                <div class="form-group">
                    <label for="to_date">Đến Ngày</label>
                    <input type="date" name="to_date" id="to_date" value="<?php echo esc_attr($to_date); ?>" class="regular-text">
                </div>
                
                <div class="form-group">
                    <label for="status_filter">Trạng Thái</label>
                    <select name="status_filter" id="status_filter" class="regular-text">
                        <option value="all" <?php selected($status_filter, 'all'); ?>>Tất cả</option>
                        <option value="completed" <?php selected($status_filter, 'completed'); ?>>Hoàn thành</option>
                        <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>>Đã hủy</option>
                        <option value="pending" <?php selected($status_filter, 'pending'); ?>>Chờ xử lý</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="search_driver_report" class="button button-primary">
                    🔍 Tìm Kiếm
                </button>
            </div>
        </form>
    </div>
    
    <?php if ($search_results): ?>
        <!-- Statistics Cards -->
        <div class="report-stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($search_results['stats']['total_bookings']); ?></div>
                    <div class="stat-label">Tổng Đơn Hàng</div>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($search_results['stats']['completed_bookings']); ?></div>
                    <div class="stat-label">Hoàn Thành</div>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">❌</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($search_results['stats']['cancelled_bookings']); ?></div>
                    <div class="stat-label">Đã Hủy</div>
                </div>
            </div>
            
            <div class="stat-card primary">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($search_results['stats']['total_revenue']); ?>đ</div>
                    <div class="stat-label">Doanh Số</div>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">⭐</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($search_results['stats']['avg_rating'], 1); ?>/5</div>
                    <div class="stat-label">Đánh Giá TB</div>
                </div>
            </div>
        </div>
        
        <!-- Cancellation Reasons -->
        <?php if (!empty($search_results['stats']['cancellation_reasons'])): ?>
            <div class="report-section">
                <h2>Lý Do Hủy Đơn</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Lý Do</th>
                            <th style="width: 100px;">Số Lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results['stats']['cancellation_reasons'] as $reason => $count): ?>
                            <tr>
                                <td><?php echo esc_html($reason); ?></td>
                                <td><span class="badge badge-danger"><?php echo $count; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <!-- Reviews -->
        <?php if (!empty($search_results['reviews'])): ?>
            <div class="report-section">
                <h2>Đánh Giá Từ Khách Hàng (<?php echo count($search_results['reviews']); ?>)</h2>
                <div class="reviews-list">
                    <?php foreach ($search_results['reviews'] as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= $review->rating ? 'filled' : ''; ?>">⭐</span>
                                    <?php endfor; ?>
                                    <span class="rating-number"><?php echo $review->rating; ?>/5</span>
                                </div>
                                <div class="review-date">
                                    <?php echo date('d/m/Y H:i', strtotime($review->created_at)); ?>
                                </div>
                            </div>
                            <div class="review-route">
                                📍 <?php echo esc_html($review->pickup_location); ?> → <?php echo esc_html($review->dropoff_location); ?>
                            </div>
                            <?php if (!empty($review->comment)): ?>
                                <div class="review-comment">
                                    "<?php echo esc_html($review->comment); ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Bookings List -->
        <div class="report-section">
            <h2>Chi Tiết Đơn Hàng (<?php echo count($search_results['bookings']); ?>)</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Điểm Đi</th>
                        <th>Điểm Đến</th>
                        <th style="width: 140px;">Thời Gian</th>
                        <th style="width: 120px;">Giá</th>
                        <th style="width: 100px;">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($search_results['bookings'])): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                Không có đơn hàng nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($search_results['bookings'] as $booking): ?>
                            <tr>
                                <td>#<?php echo $booking->id; ?></td>
                                <td><?php echo esc_html($booking->pickup_location); ?></td>
                                <td><?php echo esc_html($booking->dropoff_location); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($booking->pickup_datetime)); ?></td>
                                <td><?php echo number_format($booking->price); ?>đ</td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    $status_text = '';
                                    switch ($booking->status) {
                                        case 'completed':
                                            $status_class = 'success';
                                            $status_text = 'Hoàn thành';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'danger';
                                            $status_text = 'Đã hủy';
                                            break;
                                        case 'pending':
                                            $status_class = 'warning';
                                            $status_text = 'Chờ xử lý';
                                            break;
                                        default:
                                            $status_class = 'default';
                                            $status_text = ucfirst($booking->status);
                                    }
                                    ?>
                                    <span class="badge badge-<?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.driver-report-search {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.search-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.form-actions {
    text-align: right;
}

.report-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-card.success {
    border-left: 4px solid #28a745;
}

.stat-card.danger {
    border-left: 4px solid #dc3545;
}

.stat-card.primary {
    border-left: 4px solid #007bff;
}

.stat-card.warning {
    border-left: 4px solid #ffc107;
}

.stat-icon {
    font-size: 32px;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

.stat-label {
    font-size: 13px;
    color: #666;
    margin-top: 4px;
}

.report-section {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.report-section h2 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 18px;
    color: #333;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-danger {
    background: #f8d7da;
    color: #721c24;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-default {
    background: #e9ecef;
    color: #495057;
}

.reviews-list {
    display: grid;
    gap: 15px;
}

.review-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #5b3a9d;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 5px;
}

.star {
    font-size: 16px;
    opacity: 0.3;
}

.star.filled {
    opacity: 1;
}

.rating-number {
    font-weight: 600;
    color: #333;
    margin-left: 5px;
}

.review-date {
    font-size: 13px;
    color: #666;
}

.review-route {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}

.review-comment {
    font-style: italic;
    color: #333;
    padding: 10px;
    background: #fff;
    border-radius: 4px;
}
</style>
