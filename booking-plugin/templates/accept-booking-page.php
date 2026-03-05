<?php
/**
 * Template: Trang nhận đơn hàng cho tài xế
 * URL: /nhan-don-hang/?booking=123&token=abc123
 */

// Check if called via shortcode or direct access
$is_shortcode = !did_action('get_header');

// Get parameters
$booking_id = isset($_GET['booking']) ? intval($_GET['booking']) : 0;
$token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';

if (!$booking_id || !$token) {
    if (!$is_shortcode) get_header();
    echo '<div class="booking-accept-container"><div class="alert alert-danger">Link không hợp lệ</div></div>';
    if (!$is_shortcode) get_footer();
    return;
}

global $wpdb;

// Get booking info
$booking = $wpdb->get_row($wpdb->prepare(
    "SELECT b.*, d.full_name as driver_name, d.phone as driver_phone
    FROM {$wpdb->prefix}bookings b
    LEFT JOIN {$wpdb->prefix}drivers d ON b.driver_id = d.id
    WHERE b.id = %d AND b.accept_token = %s",
    $booking_id,
    $token
));

if (!$booking) {
    if (!$is_shortcode) get_header();
    echo '<div class="booking-accept-container"><div class="alert alert-danger">Link không hợp lệ hoặc đã hết hạn</div></div>';
    if (!$is_shortcode) get_footer();
    return;
}

// Check if already accepted
$already_accepted = $booking->status === 'accepted';

// Check if token expired
$token_expired = false;
if ($booking->token_expires && $booking->token_expires < time()) {
    $token_expired = true;
}

if (!$is_shortcode) get_header();
?>

<!-- Ensure jQuery is loaded -->
<?php if ($is_shortcode): ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php endif; ?>

<style>
.booking-accept-container {
    max-width: 600px;
    margin: 50px auto;
    padding: 30px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.booking-accept-container h1 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

.booking-info {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.booking-info-row {
    display: flex;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.booking-info-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.booking-info-label {
    font-weight: bold;
    width: 120px;
    color: #666;
}

.booking-info-value {
    flex: 1;
    color: #333;
}

.booking-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    flex: 1;
    padding: 15px 30px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-accept {
    background: #28a745;
    color: #fff;
}

.btn-accept:hover {
    background: #218838;
}

.btn-reject {
    background: #dc3545;
    color: #fff;
}

.btn-reject:hover {
    background: #c82333;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.loading {
    text-align: center;
    padding: 20px;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="booking-accept-container">
    <h1>🚗 Thông Tin Đơn Hàng</h1>
    
    <?php if ($already_accepted): ?>
        <div class="alert alert-warning">
            <strong>⚠️ Đơn hàng đã được nhận</strong><br>
            <?php if ($booking->driver_name): ?>
                Tài xế: <strong><?php echo esc_html($booking->driver_name); ?></strong> (<?php echo esc_html($booking->driver_phone); ?>)
            <?php endif; ?>
        </div>
    <?php elseif ($token_expired): ?>
        <div class="alert alert-danger">
            <strong>❌ Link đã hết hạn</strong><br>
            Vui lòng liên hệ admin để được hỗ trợ.
        </div>
    <?php endif; ?>
    
    <div class="booking-info">
        <div class="booking-info-row">
            <div class="booking-info-label">📋 Mã đơn:</div>
            <div class="booking-info-value"><strong><?php echo esc_html($booking->booking_code); ?></strong></div>
        </div>
        
        <div class="booking-info-row">
            <div class="booking-info-label">📍 Điểm đi:</div>
            <div class="booking-info-value"><?php echo esc_html($booking->from_location); ?></div>
        </div>
        
        <div class="booking-info-row">
            <div class="booking-info-label">📍 Điểm đến:</div>
            <div class="booking-info-value"><?php echo esc_html($booking->to_location); ?></div>
        </div>
        
        <div class="booking-info-row">
            <div class="booking-info-label">🚙 Loại xe:</div>
            <div class="booking-info-value"><?php echo esc_html($booking->car_type); ?></div>
        </div>
        
        <div class="booking-info-row">
            <div class="booking-info-label">🕐 Thời gian:</div>
            <div class="booking-info-value"><?php echo date('d/m/Y H:i', strtotime($booking->trip_datetime)); ?></div>
        </div>
        
        <div class="booking-info-row">
            <div class="booking-info-label">💰 Giá:</div>
            <div class="booking-info-value"><strong style="color: #28a745; font-size: 18px;"><?php echo number_format($booking->price); ?>đ</strong></div>
        </div>
        
        <?php if ($booking->distance): ?>
        <div class="booking-info-row">
            <div class="booking-info-label">📏 Khoảng cách:</div>
            <div class="booking-info-value"><?php echo number_format($booking->distance, 1); ?> km</div>
        </div>
        <?php endif; ?>
        
        <div class="booking-info-row">
            <div class="booking-info-label">👤 Khách hàng:</div>
            <div class="booking-info-value">
                <strong><?php echo esc_html($booking->customer_name); ?></strong><br>
                📞 <?php echo esc_html($booking->customer_phone); ?>
            </div>
        </div>
    </div>
    
    <div id="message-container"></div>
    
    <?php if (!$already_accepted && !$token_expired): ?>
        <?php if ($booking->assignment_type === 'group'): ?>
            <!-- Nếu là group assignment, cần nhập thông tin tài xế -->
            <div id="driver-info-form" style="margin-top: 20px; padding: 20px; background: #f9f9f9; border-radius: 4px;">
                <h3>👤 Thông Tin Tài Xế</h3>
                <p>Vui lòng nhập số điện thoại để xác nhận:</p>
                <input type="text" id="driver-phone" class="form-control" placeholder="Số điện thoại (VD: 0912345678)" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
                <p class="description" style="font-size: 13px; color: #666;">Số điện thoại phải trùng với số đã đăng ký trong hệ thống</p>
            </div>
        <?php endif; ?>
        
        <div class="booking-actions">
            <button type="button" class="btn btn-accept" id="accept-btn">
                ✅ Nhận Đơn
            </button>
            <button type="button" class="btn btn-reject" id="reject-btn">
                ❌ Từ Chối
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    console.log('🔵 Script loaded');
    
    var bookingId = <?php echo $booking_id; ?>;
    var token = '<?php echo esc_js($token); ?>';
    var assignmentType = '<?php echo esc_js($booking->assignment_type); ?>';
    
    console.log('📋 Booking ID:', bookingId);
    console.log('🔑 Token:', token);
    console.log('📦 Assignment Type:', assignmentType);
    
    // Accept booking
    $('#accept-btn').on('click', function() {
        console.log('🟢 Accept button clicked');
        
        // Nếu là group assignment, cần kiểm tra số điện thoại
        var driverPhone = '';
        if (assignmentType === 'group') {
            driverPhone = $('#driver-phone').val().trim();
            console.log('📞 Driver phone:', driverPhone);
            
            if (!driverPhone) {
                alert('Vui lòng nhập số điện thoại!');
                $('#driver-phone').focus();
                return;
            }
            
            // Validate phone format
            if (!/^0\d{9}$/.test(driverPhone)) {
                alert('Số điện thoại không hợp lệ! Phải có 10 số và bắt đầu bằng 0.');
                $('#driver-phone').focus();
                return;
            }
        }
        
        if (!confirm('Bạn có chắc muốn nhận đơn hàng này?')) {
            console.log('❌ User cancelled');
            return;
        }
        
        console.log('⏳ Sending AJAX request...');
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('Đang xử lý...');
        $('#reject-btn').prop('disabled', true);
        
        var ajaxData = {
            action: 'accept_booking',
            booking_id: bookingId,
            token: token
        };
        
        // Thêm driver_phone nếu là group assignment
        if (assignmentType === 'group') {
            ajaxData.driver_phone = driverPhone;
        }
        
        console.log('📤 AJAX data:', ajaxData);
        console.log('🌐 AJAX URL:', '<?php echo admin_url('admin-ajax.php'); ?>');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: ajaxData,
            beforeSend: function() {
                console.log('📡 Request sent');
            },
            success: function(response) {
                console.log('✅ Response received:', response);
                
                if (response.success) {
                    $('#message-container').html(
                        '<div class="alert alert-success">' +
                        '<strong>✅ Đã nhận đơn hàng thành công!</strong><br>' +
                        response.data.message +
                        '</div>'
                    );
                    $('.booking-actions').hide();
                    $('#driver-info-form').hide();
                    
                    // Reload after 2 seconds
                    setTimeout(function() {
                        console.log('🔄 Reloading page...');
                        location.reload();
                    }, 2000);
                } else {
                    console.log('❌ Error:', response.data.message);
                    $('#message-container').html(
                        '<div class="alert alert-danger">' +
                        '<strong>❌ Lỗi:</strong> ' + response.data.message +
                        '</div>'
                    );
                    $btn.prop('disabled', false).text('✅ Nhận Đơn');
                    $('#reject-btn').prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                $('#message-container').html(
                    '<div class="alert alert-danger">' +
                    '<strong>❌ Có lỗi xảy ra.</strong> Vui lòng thử lại.<br>' +
                    '<small>Chi tiết: ' + error + '</small>' +
                    '</div>'
                );
                $btn.prop('disabled', false).text('✅ Nhận Đơn');
                $('#reject-btn').prop('disabled', false);
            }
        });
    });
    
    // Reject booking
    $('#reject-btn').on('click', function() {
        var reason = prompt('Lý do từ chối (tùy chọn):');
        if (reason === null) {
            return; // User cancelled
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('Đang xử lý...');
        $('#accept-btn').prop('disabled', true);
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'reject_booking',
                booking_id: bookingId,
                token: token,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    $('#message-container').html(
                        '<div class="alert alert-success">' +
                        '<strong>✅ Đã từ chối đơn hàng</strong><br>' +
                        'Cảm ơn bạn đã phản hồi.' +
                        '</div>'
                    );
                    $('.booking-actions').hide();
                } else {
                    $('#message-container').html(
                        '<div class="alert alert-danger">' +
                        '<strong>❌ Lỗi:</strong> ' + response.data.message +
                        '</div>'
                    );
                    $btn.prop('disabled', false).text('❌ Từ Chối');
                    $('#accept-btn').prop('disabled', false);
                }
            },
            error: function() {
                $('#message-container').html(
                    '<div class="alert alert-danger">' +
                    '<strong>❌ Có lỗi xảy ra.</strong> Vui lòng thử lại.' +
                    '</div>'
                );
                $btn.prop('disabled', false).text('❌ Từ Chối');
                $('#accept-btn').prop('disabled', false);
            }
        });
    });
});
</script>

<?php
if (!$is_shortcode) get_footer();
?>
