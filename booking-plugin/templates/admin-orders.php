<?php
if (!defined('ABSPATH')) exit;
global $wpdb;

// Xử lý cập nhật trạng thái
if (isset($_POST['update_status']) && check_admin_referer('update_order_status')) {
    $order_id = intval($_POST['order_id']);
    $new_status = sanitize_text_field($_POST['status']);
    
    $updated = $wpdb->update(
        $wpdb->prefix . 'bookings',
        array('status' => $new_status, 'updated_at' => current_time('mysql')),
        array('id' => $order_id)
    );
    
    if ($updated) {
        echo '<div class="notice notice-success"><p>Đã cập nhật trạng thái đơn hàng!</p></div>';
    }
}

// Xử lý xóa đơn hàng
if (isset($_GET['delete']) && check_admin_referer('delete_order_' . $_GET['delete'])) {
    $order_id = intval($_GET['delete']);
    $wpdb->delete($wpdb->prefix . 'bookings', array('id' => $order_id));
    echo '<div class="notice notice-success"><p>Đã xóa đơn hàng!</p></div>';
}

// Lấy danh sách đơn hàng
$orders = $wpdb->get_results("
    SELECT b.*, d.full_name as driver_name, d.phone as driver_phone
    FROM {$wpdb->prefix}bookings b
    LEFT JOIN {$wpdb->prefix}drivers d ON b.driver_id = d.id
    ORDER BY b.created_at DESC 
    LIMIT 100
");

// Lấy danh sách groups
$groups = $wpdb->get_results("
    SELECT * FROM {$wpdb->prefix}booking_notification_groups 
    WHERE is_active = 1 
    ORDER BY name ASC
");

// Thống kê
$stats = array(
    'total' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings"),
    'pending' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE status = 'pending'"),
    'assigned' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE status = 'assigned'"),
    'accepted' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE status = 'accepted'"),
    'completed' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE status = 'completed'"),
    'cancelled' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bookings WHERE status = 'cancelled'")
);
?>

<div class="wrap">
    <h1>📋 Quản Lý Đơn Hàng</h1>
    
    <!-- Thống kê nhanh -->
    <div class="order-stats">
        <div class="stat-box">
            <div class="stat-number"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Tổng đơn</div>
        </div>
        <div class="stat-box stat-pending">
            <div class="stat-number"><?php echo $stats['pending']; ?></div>
            <div class="stat-label">Chờ xử lý</div>
        </div>
        <div class="stat-box stat-assigned">
            <div class="stat-number"><?php echo $stats['assigned']; ?></div>
            <div class="stat-label">Đã gán</div>
        </div>
        <div class="stat-box stat-accepted">
            <div class="stat-number"><?php echo $stats['accepted']; ?></div>
            <div class="stat-label">Đã nhận</div>
        </div>
        <div class="stat-box stat-completed">
            <div class="stat-number"><?php echo $stats['completed']; ?></div>
            <div class="stat-label">Hoàn thành</div>
        </div>
        <div class="stat-box stat-cancelled">
            <div class="stat-number"><?php echo $stats['cancelled']; ?></div>
            <div class="stat-label">Đã hủy</div>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 80px;">Mã Đơn</th>
                <th>Khách Hàng</th>
                <th>Tuyến Đường</th>
                <th>Loại Xe</th>
                <th>Thời Gian</th>
                <th style="width: 100px;">Giá</th>
                <th style="width: 120px;">Trạng Thái</th>
                <th style="width: 180px;">Gán Đơn</th>
                <th style="width: 100px;">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($orders): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong><?php echo esc_html($order->booking_code); ?></strong></td>
                        <td>
                            <strong><?php echo esc_html($order->customer_name); ?></strong><br>
                            <small>📞 <?php echo esc_html($order->customer_phone); ?></small>
                            <?php if ($order->driver_name): ?>
                                <br><small>🚗 <?php echo esc_html($order->driver_name); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small>
                                <strong>Từ:</strong> <?php echo esc_html(substr($order->from_location, 0, 40)); ?><br>
                                <strong>Đến:</strong> <?php echo esc_html(substr($order->to_location, 0, 40)); ?>
                                <?php if ($order->distance): ?>
                                    <br><strong>Khoảng cách:</strong> <?php echo number_format($order->distance, 1); ?> km
                                <?php endif; ?>
                            </small>
                        </td>
                        <td>
                            <?php echo esc_html($order->car_type); ?>
                            <?php if ($order->is_round_trip): ?>
                                <br><small>🔄 2 chiều</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small><?php echo date('d/m/Y H:i', strtotime($order->trip_datetime)); ?></small><br>
                            <small class="text-muted">Đặt: <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></small>
                        </td>
                        <td><strong><?php echo number_format($order->price); ?>đ</strong></td>
                        <td>
                            <form method="post" class="status-form" style="margin: 0;">
                                <?php wp_nonce_field('update_order_status'); ?>
                                <input type="hidden" name="update_status" value="1">
                                <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
                                <select name="status" class="status-select status-<?php echo esc_attr($order->status); ?>" onchange="this.form.submit()">
                                    <option value="pending" <?php selected($order->status, 'pending'); ?>>Chờ xử lý</option>
                                    <option value="confirmed" <?php selected($order->status, 'confirmed'); ?>>Đã xác nhận</option>
                                    <option value="assigned" <?php selected($order->status, 'assigned'); ?>>Đã phân xe</option>
                                    <option value="in_progress" <?php selected($order->status, 'in_progress'); ?>>Đang thực hiện</option>
                                    <option value="completed" <?php selected($order->status, 'completed'); ?>>Hoàn thành</option>
                                    <option value="cancelled" <?php selected($order->status, 'cancelled'); ?>>Đã hủy</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <?php if ($order->status === 'pending'): ?>
                                <button class="button button-small button-primary assign-driver-btn" 
                                        data-booking-id="<?php echo $order->id; ?>"
                                        data-booking-code="<?php echo esc_attr($order->booking_code); ?>">
                                    👤 Gán Tài Xế
                                </button>
                                <button class="button button-small assign-group-btn" 
                                        data-booking-id="<?php echo $order->id; ?>"
                                        data-booking-code="<?php echo esc_attr($order->booking_code); ?>">
                                    👥 Gán Group
                                </button>
                            <?php elseif ($order->status === 'assigned'): ?>
                                <span class="badge badge-assigned">⏳ Chờ nhận</span>
                                <?php if ($order->driver_name): ?>
                                    <br><small><?php echo esc_html($order->driver_name); ?></small>
                                <?php endif; ?>
                            <?php elseif ($order->status === 'accepted'): ?>
                                <span class="badge badge-accepted">✅ Đã nhận</span>
                                <?php if ($order->driver_name): ?>
                                    <br><small><?php echo esc_html($order->driver_name); ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge badge-default">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="#" class="button button-small view-order" data-id="<?php echo $order->id; ?>">Xem</a>
                            <a href="?page=booking-orders&delete=<?php echo $order->id; ?>&_wpnonce=<?php echo wp_create_nonce('delete_order_' . $order->id); ?>" 
                               class="button button-small" 
                               onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align: center; padding: 40px;">
                    <p style="font-size: 16px; color: #666;">Chưa có đơn hàng nào</p>
                </td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Gán Tài Xế -->
<div id="assignDriverModal" class="booking-modal" style="display: none;">
    <div class="booking-modal-content">
        <span class="booking-modal-close">&times;</span>
        <h2>👤 Gán Đơn Hàng Cho Tài Xế</h2>
        <p>Đơn hàng: <strong id="modal-booking-code-driver"></strong></p>
        
        <div class="form-group">
            <label>Tìm tài xế:</label>
            <input type="text" id="driver-search" class="widefat" placeholder="Nhập tên, SĐT hoặc biển số xe..." autocomplete="off">
            <div id="driver-suggestions" class="autocomplete-suggestions"></div>
        </div>
        
        <div id="selected-driver-info" style="display: none; margin-top: 15px; padding: 15px; background: #f0f0f1; border-radius: 4px;">
            <h4>Tài xế đã chọn:</h4>
            <p><strong id="driver-name"></strong></p>
            <p>📞 <span id="driver-phone"></span></p>
            <p>🚗 <span id="driver-car"></span></p>
            <p>⭐ <span id="driver-rating"></span></p>
            <p id="driver-channels"></p>
        </div>
        
        <div class="modal-actions">
            <button type="button" class="button button-primary" id="confirm-assign-driver" disabled>
                Gán Cho Tài Xế
            </button>
            <button type="button" class="button" id="cancel-assign-driver">Hủy</button>
        </div>
        
        <div id="assign-driver-message" class="notice" style="display: none; margin-top: 15px;"></div>
    </div>
</div>

<!-- Modal Gán Group -->
<div id="assignGroupModal" class="booking-modal" style="display: none;">
    <div class="booking-modal-content">
        <span class="booking-modal-close">&times;</span>
        <h2>👥 Gán Đơn Hàng Cho Group</h2>
        <p>Đơn hàng: <strong id="modal-booking-code-group"></strong></p>
        
        <div class="form-group">
            <label>Chọn group:</label>
            <select id="group-select" class="widefat">
                <option value="">-- Chọn group --</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?php echo $group->id; ?>" data-type="<?php echo $group->type; ?>">
                        <?php echo esc_html($group->name); ?> 
                        (<?php echo $group->type === 'telegram' ? '📱 Telegram' : '💬 Zalo'; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div id="selected-group-info" style="display: none; margin-top: 15px; padding: 15px; background: #f0f0f1; border-radius: 4px;">
            <h4>Preview tin nhắn:</h4>
            <div style="background: #fff; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px;">
                <strong>🚗 ĐƠN HÀNG MỚI (Ai nhanh tay nhận trước!)</strong><br><br>
                📍 <strong>Từ:</strong> <span id="preview-from"></span><br>
                📍 <strong>Đến:</strong> <span id="preview-to"></span><br>
                💰 <strong>Giá:</strong> <span id="preview-price"></span><br>
                🕐 <strong>Thời gian:</strong> <span id="preview-time"></span><br><br>
                👉 <strong>Nhận đơn:</strong> [Link]
            </div>
        </div>
        
        <div class="modal-actions">
            <button type="button" class="button button-primary" id="confirm-assign-group" disabled>
                Gửi Vào Group
            </button>
            <button type="button" class="button" id="cancel-assign-group">Hủy</button>
        </div>
        
        <div id="assign-group-message" class="notice" style="display: none; margin-top: 15px;"></div>
    </div>
</div>

<style>
.order-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.stat-box {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #2271b1;
}

.stat-box.stat-pending {
    border-left-color: #f0ad4e;
}

.stat-box.stat-assigned {
    border-left-color: #007bff;
}

.stat-box.stat-accepted {
    border-left-color: #17a2b8;
}

.stat-box.stat-completed {
    border-left-color: #5cb85c;
}

.stat-box.stat-cancelled {
    border-left-color: #d9534f;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #2271b1;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 13px;
    color: #666;
    text-transform: uppercase;
}

.status-select {
    width: 100%;
    padding: 5px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    border: 2px solid #ddd;
}

.status-select.status-pending {
    background: #fff3cd;
    color: #856404;
    border-color: #ffc107;
}

.status-select.status-confirmed {
    background: #d1ecf1;
    color: #0c5460;
    border-color: #17a2b8;
}

.status-select.status-assigned {
    background: #cce5ff;
    color: #004085;
    border-color: #007bff;
}

.status-select.status-in_progress {
    background: #e7e7ff;
    color: #383d41;
    border-color: #6c757d;
}

.status-select.status-completed {
    background: #d4edda;
    color: #155724;
    border-color: #28a745;
}

.status-select.status-cancelled {
    background: #f8d7da;
    color: #721c24;
    border-color: #dc3545;
}

.text-muted {
    color: #999;
}

.wp-list-table td {
    vertical-align: top;
}

/* Assignment buttons */
.assign-driver-btn, .assign-group-btn {
    display: block;
    width: 100%;
    margin-bottom: 5px;
    text-align: center;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.badge-assigned {
    background: #cce5ff;
    color: #004085;
}

.badge-accepted {
    background: #d4edda;
    color: #155724;
}

.badge-default {
    background: #e9ecef;
    color: #6c757d;
}

/* Modal styles */
.booking-modal {
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.booking-modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 30px;
    border: 1px solid #888;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.booking-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.booking-modal-close:hover,
.booking-modal-close:focus {
    color: #000;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.autocomplete-suggestions {
    position: relative;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    display: none;
    z-index: 1000;
}

.autocomplete-suggestion {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}

.autocomplete-suggestion:hover {
    background: #f0f0f1;
}

.modal-actions {
    margin-top: 20px;
    text-align: right;
}

.modal-actions button {
    margin-left: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    let currentBookingId = 0;
    let currentBookingData = {};
    let selectedDriverId = 0;
    let selectedGroupId = 0;
    
    // Debug: Check if ajaxurl exists
    if (typeof ajaxurl === 'undefined') {
        console.error('❌ ajaxurl is not defined!');
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        console.log('✅ ajaxurl set to:', ajaxurl);
    } else {
        console.log('✅ ajaxurl exists:', ajaxurl);
    }
    
    // View order details
    $('.view-order').on('click', function(e) {
        e.preventDefault();
        var orderId = $(this).data('id');
        alert('Xem chi tiết đơn hàng #' + orderId + '\n(Tính năng đang phát triển)');
    });
    
    // Open Assign Driver Modal
    $('.assign-driver-btn').on('click', function() {
        console.log('🔵 Open Assign Driver Modal');
        currentBookingId = $(this).data('booking-id');
        const bookingCode = $(this).data('booking-code');
        
        $('#modal-booking-code-driver').text(bookingCode);
        $('#driver-search').val('');
        $('#driver-suggestions').hide().empty();
        $('#selected-driver-info').hide();
        $('#confirm-assign-driver').prop('disabled', true);
        $('#assign-driver-message').hide();
        selectedDriverId = 0;
        
        $('#assignDriverModal').fadeIn();
    });
    
    // Open Assign Group Modal
    $('.assign-group-btn').on('click', function() {
        currentBookingId = $(this).data('booking-id');
        const bookingCode = $(this).data('booking-code');
        const $row = $(this).closest('tr');
        
        // Get booking data from row
        currentBookingData = {
            from: $row.find('td:eq(2) small').text().split('Từ:')[1].split('Đến:')[0].trim(),
            to: $row.find('td:eq(2) small').text().split('Đến:')[1].split('Khoảng cách:')[0].trim(),
            price: $row.find('td:eq(5)').text().trim(),
            time: $row.find('td:eq(4) small:first').text().trim()
        };
        
        $('#modal-booking-code-group').text(bookingCode);
        $('#group-select').val('');
        $('#selected-group-info').hide();
        $('#confirm-assign-group').prop('disabled', true);
        $('#assign-group-message').hide();
        selectedGroupId = 0;
        
        $('#assignGroupModal').fadeIn();
    });
    
    // Close modals
    $('.booking-modal-close, #cancel-assign-driver, #cancel-assign-group').on('click', function() {
        $('.booking-modal').fadeOut();
    });
    
    // Close modal when clicking outside
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('booking-modal')) {
            $('.booking-modal').fadeOut();
        }
    });
    
    // Driver search autocomplete
    let searchTimeout;
    $('#driver-search').on('input', function() {
        const search = $(this).val();
        console.log('🔍 Search input:', search);
        
        clearTimeout(searchTimeout);
        
        if (search.length < 2) {
            $('#driver-suggestions').hide().empty();
            console.log('⚠️ Search too short, minimum 2 characters');
            return;
        }
        
        console.log('⏳ Waiting 300ms before search...');
        searchTimeout = setTimeout(function() {
            console.log('🚀 Sending AJAX request...');
            console.log('URL:', ajaxurl);
            console.log('Action: search_drivers');
            console.log('Search term:', search);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'search_drivers',
                    nonce: '<?php echo wp_create_nonce("booking_nonce"); ?>',
                    search: search
                },
                beforeSend: function() {
                    console.log('📤 Request sent');
                    $('#driver-suggestions').html('<div style="padding: 10px; color: #999;">Đang tìm kiếm...</div>').show();
                },
                success: function(response) {
                    console.log('📥 Response received:', response);
                    
                    if (response.success && response.data.drivers.length > 0) {
                        console.log('✅ Found', response.data.drivers.length, 'drivers');
                        let html = '';
                        response.data.drivers.forEach(function(driver) {
                            html += '<div class="autocomplete-suggestion" data-driver-id="' + driver.id + '" ' +
                                    'data-name="' + driver.name + '" ' +
                                    'data-phone="' + driver.phone + '" ' +
                                    'data-car="' + driver.car_type + ' - ' + driver.car_plate + '" ' +
                                    'data-rating="' + driver.rating + '" ' +
                                    'data-telegram="' + driver.has_telegram + '" ' +
                                    'data-zalo="' + driver.has_zalo + '">' +
                                    driver.label +
                                    '</div>';
                        });
                        $('#driver-suggestions').html(html).show();
                    } else {
                        console.log('⚠️ No drivers found');
                        $('#driver-suggestions').html('<div style="padding: 10px; color: #999;">Không tìm thấy tài xế</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    $('#driver-suggestions').html('<div style="padding: 10px; color: red;">Lỗi: ' + error + '</div>').show();
                }
            });
        }, 300);
    });
    
    // Select driver from suggestions
    $(document).on('click', '.autocomplete-suggestion', function() {
        selectedDriverId = $(this).data('driver-id');
        
        $('#driver-name').text($(this).data('name'));
        $('#driver-phone').text($(this).data('phone'));
        $('#driver-car').text($(this).data('car'));
        $('#driver-rating').text($(this).data('rating'));
        
        let channels = '';
        if ($(this).data('telegram')) channels += '📱 Telegram ';
        if ($(this).data('zalo')) channels += '💬 Zalo';
        if (!channels) channels = '⚠️ Chưa cấu hình kênh thông báo';
        $('#driver-channels').html('<strong>Kênh thông báo:</strong> ' + channels);
        
        $('#driver-search').val($(this).data('name'));
        $('#driver-suggestions').hide();
        $('#selected-driver-info').show();
        $('#confirm-assign-driver').prop('disabled', false);
    });
    
    // Group select change
    $('#group-select').on('change', function() {
        selectedGroupId = $(this).val();
        
        if (selectedGroupId) {
            $('#preview-from').text(currentBookingData.from);
            $('#preview-to').text(currentBookingData.to);
            $('#preview-price').text(currentBookingData.price);
            $('#preview-time').text(currentBookingData.time);
            
            $('#selected-group-info').show();
            $('#confirm-assign-group').prop('disabled', false);
        } else {
            $('#selected-group-info').hide();
            $('#confirm-assign-group').prop('disabled', true);
        }
    });
    
    // Confirm assign to driver
    $('#confirm-assign-driver').on('click', function() {
        if (!selectedDriverId) return;
        
        const $btn = $(this);
        $btn.prop('disabled', true).text('Đang gán...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'assign_to_driver',
                nonce: '<?php echo wp_create_nonce("booking_nonce"); ?>',
                booking_id: currentBookingId,
                driver_id: selectedDriverId
            },
            success: function(response) {
                if (response.success) {
                    $('#assign-driver-message')
                        .removeClass('notice-error')
                        .addClass('notice-success')
                        .html('<p>✅ ' + response.data.message + '</p>')
                        .show();
                    
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#assign-driver-message')
                        .removeClass('notice-success')
                        .addClass('notice-error')
                        .html('<p>❌ ' + response.data.message + '</p>')
                        .show();
                    
                    $btn.prop('disabled', false).text('Gán Cho Tài Xế');
                }
            },
            error: function() {
                $('#assign-driver-message')
                    .removeClass('notice-success')
                    .addClass('notice-error')
                    .html('<p>❌ Có lỗi xảy ra. Vui lòng thử lại.</p>')
                    .show();
                
                $btn.prop('disabled', false).text('Gán Cho Tài Xế');
            }
        });
    });
    
    // Confirm assign to group
    $('#confirm-assign-group').on('click', function() {
        if (!selectedGroupId) return;
        
        const $btn = $(this);
        $btn.prop('disabled', true).text('Đang gửi...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'assign_to_group',
                nonce: '<?php echo wp_create_nonce("booking_nonce"); ?>',
                booking_id: currentBookingId,
                group_id: selectedGroupId
            },
            success: function(response) {
                if (response.success) {
                    $('#assign-group-message')
                        .removeClass('notice-error')
                        .addClass('notice-success')
                        .html('<p>✅ ' + response.data.message + '</p>')
                        .show();
                    
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#assign-group-message')
                        .removeClass('notice-success')
                        .addClass('notice-error')
                        .html('<p>❌ ' + response.data.message + '</p>')
                        .show();
                    
                    $btn.prop('disabled', false).text('Gửi Vào Group');
                }
            },
            error: function() {
                $('#assign-group-message')
                    .removeClass('notice-success')
                    .addClass('notice-error')
                    .html('<p>❌ Có lỗi xảy ra. Vui lòng thử lại.</p>')
                    .show();
                
                $btn.prop('disabled', false).text('Gửi Vào Group');
            }
        });
    });
});
</script>
