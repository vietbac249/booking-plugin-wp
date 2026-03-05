<?php
/**
 * Plugin Name: Đặt Xe Nội Bài
 * Plugin URI: https://noibai.vn
 * Description: Plugin đặt xe sân bay Nội Bài và đường dài với tính năng tính km và giá tiền tự động
 * Version: 1.0.0
 * Author: NoiBai.vn
 * Author URI: https://noibai.vn
 * License: GPL v2 or later
 * Text Domain: booking-plugin
 */

// Ngăn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Định nghĩa constants
define('BOOKING_PLUGIN_VERSION', '2.0.0');
define('BOOKING_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include database class
require_once BOOKING_PLUGIN_PATH . 'includes/database.php';

// Include notifications class
require_once BOOKING_PLUGIN_PATH . 'includes/notifications.php';

class BookingPlugin {
    
    public function __construct() {
        // Activation hook
        register_activation_hook(__FILE__, array($this, 'activate_plugin'));
        
        // Deactivation hook
        register_deactivation_hook(__FILE__, array($this, 'deactivate_plugin'));
        
        // Add rewrite rules - MUST be on init hook
        add_action('init', array($this, 'add_rewrite_rules'), 10);
        add_filter('query_vars', array($this, 'add_query_vars'), 10);
        add_action('template_redirect', array($this, 'handle_accept_booking_page'), 10);
        
        // Enqueue scripts và styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Đăng ký shortcode
        add_shortcode('dat_xe', array($this, 'render_booking_form'));
        add_shortcode('dang_ky_tai_xe', array($this, 'render_driver_registration'));
        add_shortcode('booking_accept_page', array($this, 'render_accept_booking_page'));
        
        // Đăng ký AJAX handlers
        add_action('wp_ajax_calculate_distance', array($this, 'ajax_calculate_distance'));
        add_action('wp_ajax_nopriv_calculate_distance', array($this, 'ajax_calculate_distance'));
        
        add_action('wp_ajax_calculate_custom_price', array($this, 'ajax_calculate_custom_price'));
        add_action('wp_ajax_nopriv_calculate_custom_price', array($this, 'ajax_calculate_custom_price'));
        
        add_action('wp_ajax_submit_booking', array($this, 'ajax_submit_booking'));
        add_action('wp_ajax_nopriv_submit_booking', array($this, 'ajax_submit_booking'));
        
        add_action('wp_ajax_register_driver', array($this, 'ajax_register_driver'));
        add_action('wp_ajax_nopriv_register_driver', array($this, 'ajax_register_driver'));
        
        // Assignment AJAX handlers
        add_action('wp_ajax_search_drivers', array($this, 'ajax_search_drivers'));
        add_action('wp_ajax_assign_to_driver', array($this, 'ajax_assign_to_driver'));
        add_action('wp_ajax_assign_to_group', array($this, 'ajax_assign_to_group'));
        add_action('wp_ajax_accept_booking', array($this, 'ajax_accept_booking'));
        add_action('wp_ajax_nopriv_accept_booking', array($this, 'ajax_accept_booking'));
        add_action('wp_ajax_reject_booking', array($this, 'ajax_reject_booking'));
        add_action('wp_ajax_nopriv_reject_booking', array($this, 'ajax_reject_booking'));
        
        // Thêm menu admin
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Đăng ký settings
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    // Kích hoạt plugin
    public function activate_plugin() {
        Booking_Database::create_tables();
        $this->add_rewrite_rules();
        flush_rewrite_rules();
    }
    
    // Hủy kích hoạt plugin
    public function deactivate_plugin() {
        flush_rewrite_rules();
    }
    
    // Add rewrite rules for accept booking page
    public function add_rewrite_rules() {
        add_rewrite_rule('^nhan-don-hang/?', 'index.php?accept_booking=1', 'top');
    }
    
    // Add query vars
    public function add_query_vars($vars) {
        $vars[] = 'accept_booking';
        return $vars;
    }
    
    // Handle accept booking page
    public function handle_accept_booking_page() {
        if (get_query_var('accept_booking')) {
            include BOOKING_PLUGIN_PATH . 'templates/accept-booking-page.php';
            exit;
        }
    }
    
    // Enqueue CSS và JavaScript
    public function enqueue_assets() {
        // Flatpickr CSS
        wp_enqueue_style(
            'flatpickr-css',
            'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
            array(),
            '4.6.13'
        );
        
        wp_enqueue_style(
            'booking-plugin-style',
            BOOKING_PLUGIN_URL . 'assets/css/style.css',
            array('flatpickr-css'),
            BOOKING_PLUGIN_VERSION
        );
        
        // Flatpickr JS
        wp_enqueue_script(
            'flatpickr-js',
            'https://cdn.jsdelivr.net/npm/flatpickr',
            array(),
            '4.6.13',
            true
        );
        
        // Flatpickr Vietnamese locale
        wp_enqueue_script(
            'flatpickr-vi',
            'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js',
            array('flatpickr-js'),
            '4.6.13',
            true
        );
        
        wp_enqueue_script(
            'booking-plugin-script',
            BOOKING_PLUGIN_URL . 'assets/js/script.js',
            array('jquery', 'flatpickr-js', 'flatpickr-vi'),
            BOOKING_PLUGIN_VERSION,
            true
        );
        
        // Google Maps API
        $google_api_key = get_option('booking_google_api_key', '');
        if (!empty($google_api_key)) {
            wp_enqueue_script(
                'google-maps',
                'https://maps.googleapis.com/maps/api/js?key=' . $google_api_key . '&libraries=places',
                array(),
                null,
                true
            );
        }
        
        // Localize script
        wp_localize_script('booking-plugin-script', 'bookingAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('booking_nonce'),
            'pricingMode' => get_option('booking_pricing_mode', 'auto'), // auto or custom
            'pricing' => array(
                'airport' => array(
                    'basePrice' => floatval(get_option('booking_airport_price', 15000)),
                    'roundTripMultiplier' => floatval(get_option('booking_roundtrip_multiplier', 1.8)),
                    'vatRate' => floatval(get_option('booking_vat_rate', 0.1))
                ),
                'longDistance' => array(
                    'basePrice' => floatval(get_option('booking_long_price', 12000)),
                    'vatRate' => floatval(get_option('booking_vat_rate', 0.1))
                )
            )
        ));
    }
    
    // Enqueue admin assets
    public function enqueue_admin_assets($hook) {
        // Chỉ load trên trang admin của plugin
        if (strpos($hook, 'booking-plugin') === false) {
            return;
        }
        
        // Chart.js cho dashboard
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js', array(), '3.9.1', true);
        
        // Admin CSS
        wp_enqueue_style('booking-admin-style', BOOKING_PLUGIN_URL . 'assets/css/admin-style.css', array(), BOOKING_PLUGIN_VERSION);
        
        // Admin JS
        wp_enqueue_script('booking-admin-script', BOOKING_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery', 'chart-js'), BOOKING_PLUGIN_VERSION, true);
    }
    
    // Render form đặt xe
    public function render_booking_form($atts) {
        ob_start();
        include BOOKING_PLUGIN_PATH . 'templates/booking-form.php';
        return ob_get_clean();
    }
    
    // Render form đăng ký tài xế
    public function render_driver_registration($atts) {
        ob_start();
        include BOOKING_PLUGIN_PATH . 'templates/driver-registration.php';
        return ob_get_clean();
    }
    
    // Render trang nhận đơn hàng
    public function render_accept_booking_page($atts) {
        ob_start();
        include BOOKING_PLUGIN_PATH . 'templates/accept-booking-page.php';
        return ob_get_clean();
    }
    
    // AJAX: Tính khoảng cách
    public function ajax_calculate_distance() {
        check_ajax_referer('booking_nonce', 'nonce');
        
        $origin = sanitize_text_field($_POST['origin']);
        $destination = sanitize_text_field($_POST['destination']);
        $google_api_key = get_option('booking_google_api_key', '');
        
        if (empty($google_api_key)) {
            wp_send_json_error(array('message' => 'Chưa cấu hình Google API Key'));
        }
        
        // Gọi Google Distance Matrix API
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?';
        $url .= 'origins=' . urlencode($origin);
        $url .= '&destinations=' . urlencode($destination);
        $url .= '&key=' . $google_api_key;
        
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => 'Không thể kết nối đến Google Maps'));
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($data['status'] === 'OK') {
            $distance = $data['rows'][0]['elements'][0]['distance']['value'] / 1000; // km
            wp_send_json_success(array('distance' => round($distance, 1)));
        } else {
            wp_send_json_error(array('message' => 'Không thể tính khoảng cách'));
        }
    }
    
    // AJAX: Tính giá theo bảng tùy chỉnh (không cần Google API)
    public function ajax_calculate_custom_price() {
        check_ajax_referer('booking_nonce', 'nonce');
        
        global $wpdb;
        
        $car_type = sanitize_text_field($_POST['car_type']);
        $trip_type = sanitize_text_field($_POST['trip_type']); // airport or long_distance
        $is_round_trip = isset($_POST['is_round_trip']) && $_POST['is_round_trip'] === 'true';
        $has_vat = isset($_POST['has_vat']) && $_POST['has_vat'] === 'true';
        $from_location = isset($_POST['from']) ? sanitize_text_field($_POST['from']) : '';
        $to_location = isset($_POST['to']) ? sanitize_text_field($_POST['to']) : '';
        
        // Tìm bảng giá - Đơn giản hóa: Chỉ tìm theo car_type và trip_type
        // Bỏ qua điểm đi/đến trong version này để đảm bảo hoạt động
        $pricing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}custom_pricing 
            WHERE car_type = %s AND trip_type = %s AND is_active = 1 
            ORDER BY id DESC LIMIT 1",
            $car_type,
            $trip_type
        ));
        
        if (!$pricing) {
            // Debug: Kiểm tra xem có bảng giá nào không
            $all_pricing = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_pricing WHERE is_active = 1");
            $debug_info = "Không tìm thấy bảng giá cho: car_type='$car_type', trip_type='$trip_type'. ";
            $debug_info .= "Có " . count($all_pricing) . " bảng giá active trong database.";
            
            wp_send_json_error(array(
                'message' => 'Chưa có bảng giá cho loại xe này. Vui lòng thêm bảng giá trong Cài Đặt.',
                'debug' => $debug_info
            ));
            return;
        }
        
        // Khoảng cách giả định để test (30km)
        $distance = 30;
        
        // Tính giá
        $base_price = floatval($pricing->base_price);
        $price_per_km = floatval($pricing->price_per_km);
        $vat_rate = floatval($pricing->vat_rate);
        
        // Giá = Giá cơ bản + (Khoảng cách × Giá/km)
        $price = $base_price + ($distance * $price_per_km);
        
        // Nếu đi 2 chiều (chỉ áp dụng cho đường dài)
        if ($is_round_trip && $trip_type === 'long_distance') {
            $price = $price * 1.8; // Giảm 10% (x1.8 thay vì x2)
        }
        
        // Thêm VAT nếu có
        if ($has_vat) {
            $price = $price * (1 + $vat_rate);
        }
        
        $route_info = '';
        if (isset($pricing->from_location) && !empty($pricing->from_location) && isset($pricing->to_location) && !empty($pricing->to_location)) {
            $route_info = ' cho tuyến ' . $pricing->from_location . ' → ' . $pricing->to_location;
        } elseif (isset($pricing->to_location) && !empty($pricing->to_location)) {
            $route_info = ' đến ' . $pricing->to_location;
        } elseif (isset($pricing->from_location) && !empty($pricing->from_location)) {
            $route_info = ' từ ' . $pricing->from_location;
        }
        
        wp_send_json_success(array(
            'distance' => $distance,
            'price' => round($price, 0),
            'message' => 'Giá tính theo bảng tùy chỉnh' . $route_info . ' (khoảng cách giả định: ' . $distance . 'km)'
        ));
    }
    
    // AJAX: Submit booking
    public function ajax_submit_booking() {
        check_ajax_referer('booking_nonce', 'nonce');
        
        global $wpdb;
        
        // Tạo mã đơn hàng
        $booking_code = 'BK' . date('ymd') . rand(1000, 9999);
        
        // Lấy dữ liệu từ POST
        $booking_data = array(
            'booking_code' => $booking_code,
            'customer_name' => sanitize_text_field($_POST['name']),
            'customer_phone' => sanitize_text_field($_POST['phone']),
            'from_location' => sanitize_text_field($_POST['from']),
            'to_location' => sanitize_text_field($_POST['to']),
            'stops' => isset($_POST['stops']) ? json_encode($_POST['stops']) : null,
            'car_type' => sanitize_text_field($_POST['car_type']),
            'trip_type' => sanitize_text_field($_POST['type']),
            'is_round_trip' => isset($_POST['is_round_trip']) ? 1 : 0,
            'has_vat' => isset($_POST['has_vat']) ? 1 : 0,
            'trip_datetime' => sanitize_text_field($_POST['datetime']),
            'distance' => isset($_POST['distance']) ? floatval($_POST['distance']) : null,
            'price' => floatval(str_replace(',', '', $_POST['price'])),
            'status' => 'pending',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );
        
        // Lưu vào database
        $table_name = $wpdb->prefix . 'bookings';
        $inserted = $wpdb->insert($table_name, $booking_data);
        
        if (!$inserted) {
            wp_send_json_error(array('message' => 'Không thể lưu đơn hàng'));
            return;
        }
        
        $booking_id = $wpdb->insert_id;
        
        // Tạo nội dung thông báo
        $message = "🚗 ĐƠN ĐẶT XE MỚI\n\n";
        $message .= "📋 Mã đơn: " . $booking_code . "\n";
        $message .= "👤 Họ tên: " . $booking_data['customer_name'] . "\n";
        $message .= "📞 SĐT: " . $booking_data['customer_phone'] . "\n";
        $message .= "📍 Từ: " . $booking_data['from_location'] . "\n";
        $message .= "📍 Đến: " . $booking_data['to_location'] . "\n";
        $message .= "🚙 Loại xe: " . $booking_data['car_type'] . "\n";
        $message .= "🕐 Thời gian: " . $booking_data['trip_datetime'] . "\n";
        $message .= "💰 Giá: " . number_format($booking_data['price']) . "đ\n";
        $message .= "⏰ Đặt lúc: " . current_time('d/m/Y H:i') . "\n";
        
        // Gửi email
        $admin_email = get_option('admin_email');
        $subject = '🚗 Đơn đặt xe mới từ ' . $booking_data['customer_name'];
        wp_mail($admin_email, $subject, $message);
        
        // Gửi Telegram
        $this->send_telegram_notification($message);
        
        // Gửi Zalo
        $this->send_zalo_notification($message);
        
        wp_send_json_success(array(
            'message' => 'Đặt xe thành công',
            'booking_code' => $booking_code,
            'booking_id' => $booking_id
        ));
    }
    
    // Gửi thông báo qua Telegram
    private function send_telegram_notification($message) {
        $bot_token = get_option('booking_telegram_bot_token', '');
        $chat_id = get_option('booking_telegram_chat_id', '');
        
        if (empty($bot_token) || empty($chat_id)) {
            return false;
        }
        
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        
        $data = array(
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        );
        
        $response = wp_remote_post($url, array(
            'body' => $data,
            'timeout' => 10
        ));
        
        return !is_wp_error($response);
    }
    
    // Gửi thông báo qua Zalo
    private function send_zalo_notification($message) {
        $access_token = get_option('booking_zalo_access_token', '');
        $phone = get_option('booking_zalo_phone', '');
        
        if (empty($access_token) || empty($phone)) {
            return false;
        }
        
        $url = "https://openapi.zalo.me/v2.0/oa/message";
        
        $data = array(
            'recipient' => array(
                'user_id' => $phone
            ),
            'message' => array(
                'text' => $message
            )
        );
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'access_token' => $access_token
            ),
            'body' => json_encode($data),
            'timeout' => 10
        ));
        
        return !is_wp_error($response);
    }
    
    // Thêm menu admin
    public function add_admin_menu() {
        // Menu chính
        add_menu_page(
            'Đặt Xe Nội Bài',
            'Đặt Xe',
            'manage_options',
            'booking-plugin',
            array($this, 'admin_dashboard_page'),
            'dashicons-car',
            30
        );
        
        // Dashboard
        add_submenu_page(
            'booking-plugin',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'booking-plugin',
            array($this, 'admin_dashboard_page')
        );
        
        // Quản lý đơn hàng
        add_submenu_page(
            'booking-plugin',
            'Quản Lý Đơn Hàng',
            'Đơn Hàng',
            'manage_options',
            'booking-orders',
            array($this, 'admin_orders_page')
        );
        
        // Quản lý tài xế
        add_submenu_page(
            'booking-plugin',
            'Quản Lý Tài Xế',
            'Tài Xế',
            'manage_options',
            'booking-drivers',
            array($this, 'admin_drivers_page')
        );
        
        // Bảng xếp hạng
        add_submenu_page(
            'booking-plugin',
            'Bảng Xếp Hạng',
            'Xếp Hạng',
            'manage_options',
            'booking-leaderboard',
            array($this, 'admin_leaderboard_page')
        );
        
        // Hợp đồng
        add_submenu_page(
            'booking-plugin',
            'Quản Lý Hợp Đồng',
            'Hợp Đồng',
            'manage_options',
            'booking-contracts',
            array($this, 'admin_contracts_page')
        );
        
        // Đánh giá
        add_submenu_page(
            'booking-plugin',
            'Đánh Giá & Feedback',
            'Đánh Giá',
            'manage_options',
            'booking-reviews',
            array($this, 'admin_reviews_page')
        );
        
        // Báo cáo tài xế
        add_submenu_page(
            'booking-plugin',
            'Báo Cáo Tài Xế',
            'Báo Cáo',
            'manage_options',
            'booking-driver-report',
            array($this, 'admin_driver_report_page')
        );
        
        // Groups thông báo
        add_submenu_page(
            'booking-plugin',
            'Quản Lý Groups',
            'Groups',
            'manage_options',
            'booking-notification-groups',
            array($this, 'admin_notification_groups_page')
        );
        
        // Cài đặt
        add_submenu_page(
            'booking-plugin',
            'Cài Đặt',
            'Cài Đặt',
            'manage_options',
            'booking-settings',
            array($this, 'admin_settings_page')
        );
    }
    
    // Đăng ký settings
    public function register_settings() {
        register_setting('booking_plugin_settings', 'booking_google_api_key');
        register_setting('booking_plugin_settings', 'booking_airport_price');
        register_setting('booking_plugin_settings', 'booking_long_price');
        register_setting('booking_plugin_settings', 'booking_roundtrip_multiplier');
        register_setting('booking_plugin_settings', 'booking_vat_rate');
        register_setting('booking_plugin_settings', 'booking_telegram_bot_token');
        register_setting('booking_plugin_settings', 'booking_telegram_chat_id');
        register_setting('booking_plugin_settings', 'booking_zalo_access_token');
        register_setting('booking_plugin_settings', 'booking_zalo_phone');
        register_setting('booking_plugin_settings', 'booking_pricing_mode'); // auto or custom
    }
    
    // Trang cài đặt admin
    public function admin_settings_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-settings.php';
    }
    
    // Dashboard page
    public function admin_dashboard_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-dashboard.php';
    }
    
    // Orders page
    public function admin_orders_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-orders.php';
    }
    
    // Drivers page
    public function admin_drivers_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-drivers.php';
    }
    
    // Leaderboard page
    public function admin_leaderboard_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-leaderboard.php';
    }
    
    // Contracts page
    public function admin_contracts_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-contracts.php';
    }
    
    // Reviews page
    public function admin_reviews_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-reviews.php';
    }
    
    // Driver Report page
    public function admin_driver_report_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-driver-report.php';
    }
    
    // Notification Groups page
    public function admin_notification_groups_page() {
        include BOOKING_PLUGIN_PATH . 'templates/admin-notification-groups.php';
    }
    
    // AJAX: Đăng ký tài xế
    public function ajax_register_driver() {
        check_ajax_referer('booking_nonce', 'nonce');
        
        global $wpdb;
        
        // Validate required fields
        $required_fields = array('driver_name', 'driver_phone', 'driver_address', 'car_type', 'car_plate');
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                wp_send_json_error(array('message' => 'Vui lòng điền đầy đủ thông tin bắt buộc: ' . $field));
                return;
            }
        }
        
        // Validate eKYC
        if (empty($_POST['ekyc_photo'])) {
            wp_send_json_error(array('message' => 'Vui lòng chụp ảnh khuôn mặt để xác thực'));
            return;
        }
        
        // Check if phone exists
        $phone = sanitize_text_field($_POST['driver_phone']);
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}drivers WHERE phone = %s",
            $phone
        ));
        
        if ($exists) {
            wp_send_json_error(array('message' => 'Số điện thoại đã được đăng ký'));
            return;
        }
        
        // Setup upload directory
        $upload_dir = wp_upload_dir();
        $driver_upload_dir = $upload_dir['basedir'] . '/drivers/';
        
        if (!file_exists($driver_upload_dir)) {
            wp_mkdir_p($driver_upload_dir);
        }
        
        $id_card_front = '';
        $id_card_back = '';
        $ekyc_photo = '';
        
        // Upload CCCD front
        if (!empty($_FILES['id_card_front']['name']) && $_FILES['id_card_front']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['id_card_front'];
            $filename = time() . '_front_' . uniqid() . '_' . sanitize_file_name($file['name']);
            $filepath = $driver_upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $id_card_front = $upload_dir['baseurl'] . '/drivers/' . $filename;
            }
        }
        
        // Upload CCCD back
        if (!empty($_FILES['id_card_back']['name']) && $_FILES['id_card_back']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['id_card_back'];
            $filename = time() . '_back_' . uniqid() . '_' . sanitize_file_name($file['name']);
            $filepath = $driver_upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $id_card_back = $upload_dir['baseurl'] . '/drivers/' . $filename;
            }
        }
        
        // Save eKYC photo from base64
        if (!empty($_POST['ekyc_photo'])) {
            $image_data = $_POST['ekyc_photo'];
            
            // Remove data URI prefix if present
            if (strpos($image_data, 'data:image') !== false) {
                $image_data = preg_replace('/^data:image\/\w+;base64,/', '', $image_data);
            }
            
            $image_data = str_replace(' ', '+', $image_data);
            $decoded = base64_decode($image_data);
            
            if ($decoded !== false) {
                $filename = time() . '_ekyc_' . uniqid() . '.jpg';
                $filepath = $driver_upload_dir . $filename;
                
                if (file_put_contents($filepath, $decoded)) {
                    $ekyc_photo = $upload_dir['baseurl'] . '/drivers/' . $filename;
                }
            }
        }
        
        // Insert driver (eKYC is required, CCCD optional for now)
        if (empty($ekyc_photo)) {
            wp_send_json_error(array('message' => 'Không thể lưu ảnh khuôn mặt. Vui lòng thử lại.'));
            return;
        }
        
        $driver_data = array(
            'full_name' => sanitize_text_field($_POST['driver_name']),
            'phone' => $phone,
            'email' => !empty($_POST['driver_email']) ? sanitize_email($_POST['driver_email']) : '',
            'address' => sanitize_textarea_field($_POST['driver_address']),
            'car_type' => sanitize_text_field($_POST['car_type']),
            'car_plate' => sanitize_text_field($_POST['car_plate']),
            'car_brand' => !empty($_POST['car_brand']) ? sanitize_text_field($_POST['car_brand']) : '',
            'car_color' => !empty($_POST['car_color']) ? sanitize_text_field($_POST['car_color']) : '',
            'id_card' => '',
            'license_number' => '',
            'id_card_front' => $id_card_front,
            'id_card_back' => $id_card_back,
            'ekyc_photo' => $ekyc_photo,
            'status' => 'pending',
            'rating' => 0,
            'total_trips' => 0,
            'completed_trips' => 0,
            'cancelled_trips' => 0,
            'points' => 0,
            'rank' => 'new',
            'joined_date' => date('Y-m-d'),
            'created_at' => current_time('mysql')
        );
        
        $inserted = $wpdb->insert($wpdb->prefix . 'drivers', $driver_data);
        
        if ($inserted) {
            // Send notification to admin
            $admin_email = get_option('admin_email');
            $subject = '🚗 Tài xế mới đăng ký: ' . $driver_data['full_name'];
            $message = "Có tài xế mới đăng ký:\n\n";
            $message .= "Họ tên: " . $driver_data['full_name'] . "\n";
            $message .= "SĐT: " . $driver_data['phone'] . "\n";
            $message .= "Email: " . $driver_data['email'] . "\n";
            $message .= "Địa chỉ: " . $driver_data['address'] . "\n";
            $message .= "Loại xe: " . $driver_data['car_type'] . "\n";
            $message .= "Biển số: " . $driver_data['car_plate'] . "\n";
            $message .= "Hãng xe: " . $driver_data['car_brand'] . "\n";
            $message .= "Màu xe: " . $driver_data['car_color'] . "\n\n";
            $message .= "CCCD mặt trước: " . ($id_card_front ? 'Đã upload' : 'Chưa có') . "\n";
            $message .= "CCCD mặt sau: " . ($id_card_back ? 'Đã upload' : 'Chưa có') . "\n";
            $message .= "Ảnh eKYC: " . ($ekyc_photo ? 'Đã upload' : 'Chưa có') . "\n\n";
            $message .= "Vui lòng vào admin để xác minh thông tin và giấy tờ.";
            
            wp_mail($admin_email, $subject, $message);
            
            // Send Telegram notification
            $telegram_message = "🚗 TÀI XẾ MỚI ĐĂNG KÝ\n\n";
            $telegram_message .= "👤 Họ tên: " . $driver_data['full_name'] . "\n";
            $telegram_message .= "📞 SĐT: " . $driver_data['phone'] . "\n";
            $telegram_message .= "🚙 Loại xe: " . $driver_data['car_type'] . "\n";
            $telegram_message .= "🔢 Biển số: " . $driver_data['car_plate'] . "\n";
            $telegram_message .= "📄 CCCD: " . ($id_card_front && $id_card_back ? '✅' : '⚠️') . "\n";
            $telegram_message .= "📸 eKYC: " . ($ekyc_photo ? '✅' : '⚠️') . "\n";
            $telegram_message .= "⏰ Đăng ký lúc: " . current_time('d/m/Y H:i') . "\n";
            $this->send_telegram_notification($telegram_message);
            
            wp_send_json_success(array(
                'message' => 'Đăng ký thành công! Chúng tôi sẽ xác minh thông tin và liên hệ lại với bạn trong 24-48 giờ.',
                'uploaded_files' => array(
                    'id_card_front' => !empty($id_card_front),
                    'id_card_back' => !empty($id_card_back),
                    'ekyc_photo' => !empty($ekyc_photo)
                )
            ));
        } else {
            wp_send_json_error(array(
                'message' => 'Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại.',
                'debug' => $wpdb->last_error
            ));
        }
    }
    
    /**
     * AJAX: Search drivers (autocomplete)
     */
    public function ajax_search_drivers() {
        check_ajax_referer('booking_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Không có quyền truy cập']);
            return;
        }
        
        global $wpdb;
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        
        // Remove spaces and special characters for flexible search
        $search_clean = str_replace([' ', '-', '.'], '', $search);
        
        $drivers = $wpdb->get_results($wpdb->prepare(
            "SELECT id, full_name, phone, car_type, car_plate, rating, status, telegram_chat_id, zalo_user_id
            FROM {$wpdb->prefix}drivers
            WHERE status = 'active' 
            AND (
                full_name LIKE %s 
                OR phone LIKE %s 
                OR car_plate LIKE %s
                OR REPLACE(REPLACE(REPLACE(car_plate, ' ', ''), '-', ''), '.', '') LIKE %s
            )
            ORDER BY rating DESC, full_name ASC
            LIMIT 10",
            '%' . $wpdb->esc_like($search) . '%',
            '%' . $wpdb->esc_like($search) . '%',
            '%' . $wpdb->esc_like($search) . '%',
            '%' . $wpdb->esc_like($search_clean) . '%'
        ));
        
        $results = [];
        foreach ($drivers as $driver) {
            $has_telegram = !empty($driver->telegram_chat_id);
            $has_zalo = !empty($driver->zalo_user_id);
            
            $results[] = [
                'id' => $driver->id,
                'name' => $driver->full_name,
                'phone' => $driver->phone,
                'car_type' => $driver->car_type,
                'car_plate' => $driver->car_plate,
                'rating' => number_format($driver->rating, 1),
                'has_telegram' => $has_telegram,
                'has_zalo' => $has_zalo,
                'label' => sprintf(
                    '%s - %s (%s) - ⭐ %s %s',
                    $driver->full_name,
                    $driver->phone,
                    $driver->car_plate,
                    number_format($driver->rating, 1),
                    ($has_telegram ? '📱' : '') . ($has_zalo ? '💬' : '')
                )
            ];
        }
        
        wp_send_json_success(['drivers' => $results]);
    }
    
    /**
     * AJAX: Assign booking to driver
     */
    public function ajax_assign_to_driver() {
        check_ajax_referer('booking_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Không có quyền truy cập']);
            return;
        }
        
        $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
        $driver_id = isset($_POST['driver_id']) ? intval($_POST['driver_id']) : 0;
        
        if (!$booking_id || !$driver_id) {
            wp_send_json_error(['message' => 'Thiếu thông tin đơn hàng hoặc tài xế']);
            return;
        }
        
        global $wpdb;
        
        // Start transaction
        $wpdb->query('START TRANSACTION');
        
        try {
            // Check booking exists and is pending
            $booking = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}bookings WHERE id = %d",
                $booking_id
            ));
            
            if (!$booking) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            if ($booking->status !== 'pending') {
                throw new Exception('Đơn hàng đã được xử lý');
            }
            
            // Check driver exists and is active
            $driver = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}drivers WHERE id = %d AND status = 'active'",
                $driver_id
            ));
            
            if (!$driver) {
                throw new Exception('Không tìm thấy tài xế hoặc tài xế không hoạt động');
            }
            
            // Update booking
            $updated = $wpdb->update(
                $wpdb->prefix . 'bookings',
                [
                    'driver_id' => $driver_id,
                    'status' => 'assigned',
                    'assigned_at' => current_time('mysql'),
                    'assigned_by' => get_current_user_id(),
                    'assignment_type' => 'direct'
                ],
                ['id' => $booking_id]
            );
            
            if ($updated === false) {
                throw new Exception('Không thể cập nhật đơn hàng');
            }
            
            // Log assignment
            $wpdb->insert(
                $wpdb->prefix . 'booking_assignment_logs',
                [
                    'booking_id' => $booking_id,
                    'driver_id' => $driver_id,
                    'assignment_type' => 'direct',
                    'assigned_by' => get_current_user_id(),
                    'status' => 'assigned',
                    'created_at' => current_time('mysql')
                ]
            );
            
            // Send notification
            $notification_result = Booking_Notifications::send_driver_notification(
                $driver_id,
                $booking_id,
                'assigned'
            );
            
            $wpdb->query('COMMIT');
            
            wp_send_json_success([
                'message' => 'Đã gán đơn hàng cho tài xế thành công',
                'notification' => $notification_result,
                'driver_name' => $driver->full_name,
                'driver_phone' => $driver->phone
            ]);
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * AJAX: Assign booking to group
     */
    public function ajax_assign_to_group() {
        check_ajax_referer('booking_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Không có quyền truy cập']);
            return;
        }
        
        $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
        
        if (!$booking_id || !$group_id) {
            wp_send_json_error(['message' => 'Thiếu thông tin đơn hàng hoặc group']);
            return;
        }
        
        global $wpdb;
        
        // Start transaction
        $wpdb->query('START TRANSACTION');
        
        try {
            // Check booking exists and is pending
            $booking = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}bookings WHERE id = %d",
                $booking_id
            ));
            
            if (!$booking) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            
            if ($booking->status !== 'pending') {
                throw new Exception('Đơn hàng đã được xử lý');
            }
            
            // Check group exists and is active
            $group = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}booking_notification_groups WHERE id = %d AND is_active = 1",
                $group_id
            ));
            
            if (!$group) {
                throw new Exception('Không tìm thấy group hoặc group không hoạt động');
            }
            
            // Update booking
            $updated = $wpdb->update(
                $wpdb->prefix . 'bookings',
                [
                    'status' => 'assigned',
                    'assigned_at' => current_time('mysql'),
                    'assigned_by' => get_current_user_id(),
                    'assignment_type' => 'group',
                    'group_id' => $group_id
                ],
                ['id' => $booking_id]
            );
            
            if ($updated === false) {
                throw new Exception('Không thể cập nhật đơn hàng');
            }
            
            // Log assignment
            $wpdb->insert(
                $wpdb->prefix . 'booking_assignment_logs',
                [
                    'booking_id' => $booking_id,
                    'group_id' => $group_id,
                    'assignment_type' => 'group',
                    'assigned_by' => get_current_user_id(),
                    'status' => 'assigned',
                    'created_at' => current_time('mysql')
                ]
            );
            
            // Send notification to group
            $notification_result = Booking_Notifications::send_group_notification(
                $group_id,
                $booking_id
            );
            
            $wpdb->query('COMMIT');
            
            wp_send_json_success([
                'message' => 'Đã gửi đơn hàng vào group thành công',
                'notification' => $notification_result,
                'group_name' => $group->name,
                'group_type' => $group->type
            ]);
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * AJAX: Driver accepts booking
     */
    public function ajax_accept_booking() {
        $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
        $driver_phone = isset($_POST['driver_phone']) ? sanitize_text_field($_POST['driver_phone']) : '';
        
        if (!$booking_id || !$token) {
            wp_send_json_error(['message' => 'Thiếu thông tin']);
            return;
        }
        
        global $wpdb;
        
        // Start transaction
        $wpdb->query('START TRANSACTION');
        
        try {
            // Verify token
            $booking = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}bookings 
                WHERE id = %d AND accept_token = %s",
                $booking_id,
                $token
            ));
            
            if (!$booking) {
                throw new Exception('Link không hợp lệ');
            }
            
            // Check token expiry
            if ($booking->token_expires && $booking->token_expires < time()) {
                throw new Exception('Link đã hết hạn');
            }
            
            // Check if already accepted
            if ($booking->status === 'accepted') {
                throw new Exception('Đơn hàng đã có người nhận');
            }
            
            // Determine driver_id
            $driver_id = 0;
            
            if ($booking->assignment_type === 'group') {
                // For group assignment, find driver by phone
                if (empty($driver_phone)) {
                    throw new Exception('Vui lòng nhập số điện thoại');
                }
                
                $driver = $wpdb->get_row($wpdb->prepare(
                    "SELECT id, full_name, status FROM {$wpdb->prefix}drivers 
                    WHERE phone = %s",
                    $driver_phone
                ));
                
                if (!$driver) {
                    throw new Exception('Không tìm thấy tài xế với số điện thoại này. Vui lòng kiểm tra lại hoặc liên hệ admin.');
                }
                
                if ($driver->status !== 'active') {
                    throw new Exception('Tài xế chưa được kích hoạt. Vui lòng liên hệ admin.');
                }
                
                $driver_id = $driver->id;
            } else {
                // For direct assignment, use existing driver_id
                $driver_id = $booking->driver_id;
                
                if (!$driver_id) {
                    throw new Exception('Thiếu thông tin tài xế');
                }
            }
            
            // Update booking
            $update_data = [
                'status' => 'accepted',
                'accepted_at' => current_time('mysql'),
                'accept_token' => null, // Clear token
                'token_expires' => null,
                'driver_id' => $driver_id
            ];
            
            $updated = $wpdb->update(
                $wpdb->prefix . 'bookings',
                $update_data,
                ['id' => $booking_id]
            );
            
            if ($updated === false) {
                throw new Exception('Không thể cập nhật đơn hàng');
            }
            
            // Update assignment log
            $wpdb->update(
                $wpdb->prefix . 'booking_assignment_logs',
                [
                    'driver_id' => $driver_id,
                    'status' => 'accepted',
                    'accepted_at' => current_time('mysql')
                ],
                [
                    'booking_id' => $booking_id,
                    'status' => 'assigned'
                ]
            );
            
            $wpdb->query('COMMIT');
            
            $driver = $wpdb->get_row($wpdb->prepare(
                "SELECT full_name FROM {$wpdb->prefix}drivers WHERE id = %d",
                $driver_id
            ));
            
            wp_send_json_success([
                'message' => 'Đã nhận đơn hàng thành công',
                'driver_name' => $driver ? $driver->full_name : '',
                'booking_id' => $booking_id
            ]);
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }
    
    /**
     * AJAX: Driver rejects booking
     */
    public function ajax_reject_booking() {
        $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
        $reason = isset($_POST['reason']) ? sanitize_text_field($_POST['reason']) : '';
        
        if (!$booking_id || !$token) {
            wp_send_json_error(['message' => 'Thiếu thông tin']);
            return;
        }
        
        global $wpdb;
        
        // Verify token
        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}bookings 
            WHERE id = %d AND accept_token = %s",
            $booking_id,
            $token
        ));
        
        if (!$booking) {
            wp_send_json_error(['message' => 'Link không hợp lệ']);
            return;
        }
        
        // Update assignment log
        $wpdb->update(
            $wpdb->prefix . 'booking_assignment_logs',
            [
                'status' => 'rejected',
                'accepted_at' => current_time('mysql')
            ],
            [
                'booking_id' => $booking_id,
                'driver_id' => $booking->driver_id,
                'status' => 'assigned'
            ]
        );
        
        // Clear token
        $wpdb->update(
            $wpdb->prefix . 'bookings',
            [
                'accept_token' => null,
                'token_expires' => null
            ],
            ['id' => $booking_id]
        );
        
        wp_send_json_success([
            'message' => 'Đã từ chối đơn hàng',
            'reason' => $reason
        ]);
    }
}

// Khởi tạo plugin
new BookingPlugin();
