<?php
/**
 * Booking Notifications System
 * Xử lý gửi thông báo qua Telegram và Zalo
 * 
 * @package BookingPlugin
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Booking_Notifications {
    
    /**
     * Gửi thông báo cho tài xế (Telegram hoặc Zalo)
     * 
     * @param int $driver_id ID tài xế
     * @param int $booking_id ID đơn hàng
     * @param string $type Loại thông báo: 'assigned' hoặc 'reminder'
     * @return array ['success' => bool, 'message' => string, 'channels' => array]
     */
    public static function send_driver_notification($driver_id, $booking_id, $type = 'assigned') {
        global $wpdb;
        
        // Get driver info
        $driver = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}drivers WHERE id = %d",
            $driver_id
        ));
        
        if (!$driver) {
            return ['success' => false, 'message' => 'Không tìm thấy tài xế'];
        }
        
        // Get booking info
        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}bookings WHERE id = %d",
            $booking_id
        ));
        
        if (!$booking) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }
        
        // Generate accept link
        $accept_link = self::generate_accept_link($booking_id);
        
        // Prepare message
        $message = self::format_booking_message($booking, $accept_link, $type);
        
        $results = [];
        $success_count = 0;
        
        // Try Telegram first
        if (!empty($driver->telegram_chat_id)) {
            $telegram_result = self::send_telegram_notification(
                $driver->telegram_chat_id,
                $message,
                $accept_link
            );
            $results['telegram'] = $telegram_result;
            if ($telegram_result['success']) {
                $success_count++;
            }
        }
        
        // Try Zalo
        if (!empty($driver->zalo_user_id)) {
            $zalo_result = self::send_zalo_notification(
                $driver->zalo_user_id,
                $message,
                $accept_link
            );
            $results['zalo'] = $zalo_result;
            if ($zalo_result['success']) {
                $success_count++;
            }
        }
        
        // Log notification
        self::log_notification($driver_id, $booking_id, $results);
        
        return [
            'success' => $success_count > 0,
            'message' => $success_count > 0 ? 
                "Đã gửi thông báo qua $success_count kênh" : 
                'Không thể gửi thông báo',
            'channels' => $results
        ];
    }
    
    /**
     * Gửi thông báo cho group (Telegram hoặc Zalo)
     * 
     * @param int $group_id ID group
     * @param int $booking_id ID đơn hàng
     * @return array ['success' => bool, 'message' => string]
     */
    public static function send_group_notification($group_id, $booking_id) {
        global $wpdb;
        
        // Get group info
        $group = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}booking_notification_groups WHERE id = %d AND is_active = 1",
            $group_id
        ));
        
        if (!$group) {
            return ['success' => false, 'message' => 'Không tìm thấy group hoặc group đã bị khóa'];
        }
        
        // Get booking info
        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}bookings WHERE id = %d",
            $booking_id
        ));
        
        if (!$booking) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }
        
        // Generate accept link
        $accept_link = self::generate_accept_link($booking_id);
        
        // Prepare message
        $message = self::format_booking_message($booking, $accept_link, 'group');
        
        // Send based on group type
        if ($group->type === 'telegram') {
            return self::send_telegram_group_notification(
                $group->chat_id,
                $group->bot_token,
                $message,
                $accept_link
            );
        } elseif ($group->type === 'zalo') {
            return self::send_zalo_group_notification(
                $group->group_id,
                $group->access_token,
                $message,
                $accept_link
            );
        }
        
        return ['success' => false, 'message' => 'Loại group không hợp lệ'];
    }
    
    /**
     * Gửi thông báo qua Telegram (cá nhân)
     */
    public static function send_telegram_notification($chat_id, $message, $accept_link) {
        $bot_token = get_option('booking_telegram_bot_token');
        
        if (empty($bot_token)) {
            return ['success' => false, 'message' => 'Chưa cấu hình Telegram Bot Token'];
        }
        
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [[
                    ['text' => '✅ Nhận Đơn', 'url' => $accept_link]
                ]]
            ])
        ];
        
        $response = wp_remote_post($url, [
            'body' => $data,
            'timeout' => 15
        ]);
        
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['ok']) && $body['ok']) {
            return ['success' => true, 'message' => 'Đã gửi Telegram thành công'];
        }
        
        return ['success' => false, 'message' => $body['description'] ?? 'Lỗi không xác định'];
    }
    
    /**
     * Gửi thông báo qua Telegram Group
     */
    public static function send_telegram_group_notification($chat_id, $bot_token, $message, $accept_link) {
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [[
                    ['text' => '✅ Nhận Đơn', 'url' => $accept_link]
                ]]
            ])
        ];
        
        $response = wp_remote_post($url, [
            'body' => $data,
            'timeout' => 15
        ]);
        
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['ok']) && $body['ok']) {
            return ['success' => true, 'message' => 'Đã gửi vào Telegram Group thành công'];
        }
        
        return ['success' => false, 'message' => $body['description'] ?? 'Lỗi không xác định'];
    }
    
    /**
     * Gửi thông báo qua Zalo (cá nhân)
     */
    public static function send_zalo_notification($user_id, $message, $accept_link) {
        $access_token = get_option('booking_zalo_access_token');
        
        if (empty($access_token)) {
            return ['success' => false, 'message' => 'Chưa cấu hình Zalo Access Token'];
        }
        
        $url = 'https://openapi.zalo.me/v2.0/oa/message';
        
        $data = [
            'recipient' => [
                'user_id' => $user_id
            ],
            'message' => [
                'text' => $message,
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'button',
                        'buttons' => [
                            [
                                'type' => 'oa.open.url',
                                'url' => $accept_link,
                                'title' => 'Nhận Đơn'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        $response = wp_remote_post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => $access_token
            ],
            'body' => json_encode($data),
            'timeout' => 15
        ]);
        
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error']) && $body['error'] == 0) {
            return ['success' => true, 'message' => 'Đã gửi Zalo thành công'];
        }
        
        return ['success' => false, 'message' => $body['message'] ?? 'Lỗi không xác định'];
    }
    
    /**
     * Gửi thông báo qua Zalo Group
     */
    public static function send_zalo_group_notification($group_id, $access_token, $message, $accept_link) {
        // Zalo Group API (cần research thêm)
        // Hiện tại Zalo OA chưa hỗ trợ gửi vào group tốt
        // Có thể cần dùng Zalo Group API riêng
        
        return ['success' => false, 'message' => 'Zalo Group chưa được hỗ trợ (đang phát triển)'];
    }
    
    /**
     * Format tin nhắn đơn hàng
     */
    private static function format_booking_message($booking, $accept_link, $type = 'assigned') {
        $title = $type === 'group' ? 
            '🚗 ĐƠN HÀNG MỚI (Ai nhanh tay nhận trước!)' : 
            '🚗 ĐƠN HÀNG MỚI';
        
        $message = "<b>{$title}</b>\n\n";
        $message .= "📍 <b>Từ:</b> {$booking->from_location}\n";
        $message .= "📍 <b>Đến:</b> {$booking->to_location}\n";
        $message .= "💰 <b>Giá:</b> " . number_format($booking->price) . "đ\n";
        $message .= "🕐 <b>Thời gian:</b> " . date('d/m/Y H:i', strtotime($booking->trip_datetime)) . "\n";
        
        if (!empty($booking->customer_name)) {
            $message .= "👤 <b>Khách:</b> {$booking->customer_name}\n";
        }
        
        if (!empty($booking->customer_phone)) {
            $message .= "📞 <b>SĐT:</b> {$booking->customer_phone}\n";
        }
        
        $message .= "\n👉 <b>Nhận đơn:</b> {$accept_link}";
        
        return $message;
    }
    
    /**
     * Tạo link nhận đơn
     */
    public static function generate_accept_link($booking_id) {
        // Generate secure token
        $token = wp_generate_password(32, false);
        $expires = time() + (60 * 60); // 1 giờ
        
        // Save token to database
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'bookings',
            [
                'accept_token' => $token,
                'token_expires' => $expires
            ],
            ['id' => $booking_id]
        );
        
        // Generate link
        $link = add_query_arg([
            'action' => 'accept_booking',
            'booking' => $booking_id,
            'token' => $token
        ], home_url('/nhan-don-hang/'));
        
        return $link;
    }
    
    /**
     * Log notification
     */
    private static function log_notification($driver_id, $booking_id, $results) {
        $log_file = BOOKING_PLUGIN_PATH . 'logs/notifications.log';
        $log_dir = dirname($log_file);
        
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }
        
        $log_entry = sprintf(
            "[%s] Driver: %d, Booking: %d, Results: %s\n",
            date('Y-m-d H:i:s'),
            $driver_id,
            $booking_id,
            json_encode($results)
        );
        
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
    
    /**
     * Test gửi thông báo (cho admin test)
     */
    public static function test_notification($type, $recipient) {
        $test_message = "🧪 <b>TEST NOTIFICATION</b>\n\n";
        $test_message .= "Đây là tin nhắn test từ hệ thống đặt xe.\n";
        $test_message .= "Nếu bạn nhận được tin nhắn này, cấu hình đã thành công!";
        
        $test_link = home_url();
        
        if ($type === 'telegram') {
            return self::send_telegram_notification($recipient, $test_message, $test_link);
        } elseif ($type === 'zalo') {
            return self::send_zalo_notification($recipient, $test_message, $test_link);
        }
        
        return ['success' => false, 'message' => 'Loại không hợp lệ'];
    }
}
