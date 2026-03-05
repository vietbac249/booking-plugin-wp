# Đề Xuất: Tính Năng Gán Đơn Hàng Cho Tài Xế

## 📋 Tổng Quan

Tính năng cho phép admin gán đơn hàng "Chờ xử lý" cho tài xế theo 2 cách:
1. **Gán trực tiếp** cho 1 tài xế cụ thể
2. **Gán cho group** Zalo/Telegram (nhiều tài xế cạnh tranh)

## 🎯 Yêu Cầu Chi Tiết

### Option 1: Gán Trực Tiếp Cho Tài Xế

**Luồng hoạt động:**

1. **Admin chọn đơn hàng "Chờ xử lý"**
   - Click nút "Gán Tài Xế" trên đơn hàng

2. **Popup/Modal hiện ra với:**
   - Input autocomplete tìm tài xế (gõ tên → gợi ý)
   - Hiển thị: Tên + SĐT + Trạng thái
   - Nút "Gán Cho Tài Xế"

3. **Sau khi gán:**
   - Cập nhật database: `driver_id`, `status = 'assigned'`
   - Gửi thông báo Zalo + Telegram cho tài xế:
     ```
     🚗 ĐƠN HÀNG MỚI
     
     Từ: [Điểm đi]
     Đến: [Điểm đến]
     Giá: [Giá tiền]đ
     Thời gian: [Ngày giờ]
     
     👉 Nhận đơn: [Link]
     ```

4. **Tài xế nhận đơn:**
   - Click link → Trang xác nhận
   - Click "Nhận Đơn" → Cập nhật `status = 'accepted'`
   - Ghi nhận vào hệ thống để tính doanh thu

### Option 2: Gán Cho Group Zalo/Telegram

**Luồng hoạt động:**

1. **Admin chọn đơn hàng "Chờ xử lý"**
   - Click nút "Gán Cho Group"

2. **Popup/Modal hiện ra với:**
   - Dropdown chọn group (Zalo hoặc Telegram)
   - Hiển thị: Tên group + Loại (Zalo/Telegram)
   - Nút "Gán Cho Group"

3. **Sau khi gán:**
   - Gửi tin nhắn vào group:
     ```
     🚗 ĐƠN HÀNG MỚI (Ai nhanh tay nhận trước!)
     
     Từ: [Điểm đi]
     Đến: [Điểm đến]
     Giá: [Giá tiền]đ
     Thời gian: [Ngày giờ]
     
     👉 Nhận đơn: [Link]
     ```

4. **Tài xế trong group nhận đơn:**
   - Ai click link trước → Nhận được đơn
   - Người khác click sau → Thông báo "Đơn đã có người nhận"
   - Cập nhật `driver_id`, `status = 'accepted'`

## 🗄️ Database Changes

### 1. Thêm Cột Vào Bảng `bookings`

```sql
ALTER TABLE wp_booking_bookings 
ADD COLUMN assigned_at DATETIME NULL COMMENT 'Thời gian admin gán',
ADD COLUMN assigned_by INT NULL COMMENT 'Admin ID người gán',
ADD COLUMN accepted_at DATETIME NULL COMMENT 'Thời gian tài xế nhận',
ADD COLUMN assignment_type ENUM('direct', 'group') DEFAULT 'direct' COMMENT 'Loại gán: trực tiếp hoặc group',
ADD COLUMN group_id INT NULL COMMENT 'ID group nếu gán cho group';
```

### 2. Tạo Bảng Mới: `notification_groups`

```sql
CREATE TABLE wp_booking_notification_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Tên group',
    type ENUM('zalo', 'telegram') NOT NULL COMMENT 'Loại group',
    group_id VARCHAR(255) NOT NULL COMMENT 'ID group từ Zalo/Telegram',
    access_token TEXT NULL COMMENT 'Token để gửi tin (Zalo)',
    bot_token VARCHAR(255) NULL COMMENT 'Bot token (Telegram)',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Trạng thái hoạt động',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Cập Nhật Trạng Thái Đơn Hàng

**Trạng thái mới:**
- `pending` → Chờ xử lý (chưa gán)
- `assigned` → Đã gán cho tài xế/group (chờ nhận)
- `accepted` → Tài xế đã nhận
- `in_progress` → Đang thực hiện
- `completed` → Hoàn thành
- `cancelled` → Đã hủy

## 🔧 Implementation Plan

### Phase 1: UI/UX (Tuần 1)

**Files cần tạo/sửa:**

1. **admin-orders.php**
   - Thêm nút "Gán Tài Xế" và "Gán Group"
   - Modal popup cho 2 options
   - Autocomplete search tài xế
   - Dropdown chọn group

2. **admin-style.css**
   - Style cho modal
   - Style cho autocomplete
   - Style cho buttons

3. **admin-script.js**
   - AJAX gán tài xế
   - AJAX gán group
   - Autocomplete logic
   - Modal open/close

### Phase 2: Backend Logic (Tuần 2)

**Files cần tạo/sửa:**

1. **booking-plugin.php**
   - AJAX handler: `ajax_assign_to_driver()`
   - AJAX handler: `ajax_assign_to_group()`
   - AJAX handler: `ajax_accept_booking()`
   - AJAX handler: `ajax_search_drivers()`

2. **includes/notifications.php** (Mới)
   - Function: `send_zalo_notification()`
   - Function: `send_telegram_notification()`
   - Function: `send_group_notification()`

3. **database.php**
   - Update schema
   - Migration script

### Phase 3: Trang Nhận Đơn (Tuần 3)

**Files cần tạo:**

1. **templates/driver-accept-booking.php**
   - Trang public (không cần login)
   - Hiển thị thông tin đơn hàng
   - Nút "Nhận Đơn"
   - Xác thực token

2. **Shortcode mới:**
   ```php
   [nhan_don_hang token="xxx"]
   ```

### Phase 4: Quản Lý Groups (Tuần 4)

**Files cần tạo:**

1. **templates/admin-notification-groups.php**
   - Danh sách groups
   - Thêm/Sửa/Xóa group
   - Test gửi tin nhắn

2. **Menu mới:**
   - Đặt Xe → Cài Đặt → Tab "Groups Thông Báo"

## 📱 Notification Templates

### Template Zalo

```json
{
  "recipient": {
    "user_id": "DRIVER_ZALO_ID"
  },
  "message": {
    "text": "🚗 ĐƠN HÀNG MỚI\n\nTừ: {from}\nĐến: {to}\nGiá: {price}đ\nThời gian: {datetime}\n\n👉 Nhận đơn: {link}",
    "attachment": {
      "type": "template",
      "payload": {
        "template_type": "button",
        "buttons": [
          {
            "type": "web_url",
            "url": "{link}",
            "title": "Nhận Đơn"
          }
        ]
      }
    }
  }
}
```

### Template Telegram

```php
$message = "🚗 *ĐƠN HÀNG MỚI*\n\n";
$message .= "Từ: {from}\n";
$message .= "Đến: {to}\n";
$message .= "Giá: {price}đ\n";
$message .= "Thời gian: {datetime}\n\n";
$message .= "👉 [Nhận đơn]({link})";

$keyboard = [
    'inline_keyboard' => [
        [
            ['text' => '✅ Nhận Đơn', 'url' => $link]
        ]
    ]
];
```

## 🔐 Security

### 1. Token-Based Authentication

```php
// Tạo token khi gán đơn
$token = wp_generate_password(32, false);
update_post_meta($booking_id, '_accept_token', $token);
update_post_meta($booking_id, '_token_expires', time() + 3600); // 1 giờ

// Link nhận đơn
$link = home_url("/nhan-don-hang/?token=$token&booking=$booking_id");
```

### 2. Validation

```php
// Kiểm tra token
$saved_token = get_post_meta($booking_id, '_accept_token', true);
$expires = get_post_meta($booking_id, '_token_expires', true);

if ($token !== $saved_token || time() > $expires) {
    wp_die('Link không hợp lệ hoặc đã hết hạn');
}

// Kiểm tra đơn hàng chưa được nhận
if ($booking->status !== 'assigned') {
    wp_die('Đơn hàng đã có người nhận');
}
```

## 💰 Tính Doanh Thu

### Logic Tính Tiền

```php
// Chỉ tính doanh thu khi:
// 1. Tài xế đã nhận đơn (status = 'accepted')
// 2. Đơn hàng hoàn thành (status = 'completed')

if ($booking->status === 'completed' && !empty($booking->driver_id)) {
    // Thêm vào bảng driver_points
    $wpdb->insert(
        $wpdb->prefix . 'booking_driver_points',
        [
            'driver_id' => $booking->driver_id,
            'booking_id' => $booking->id,
            'points' => calculate_points($booking->price),
            'revenue' => $booking->price,
            'earned_at' => current_time('mysql')
        ]
    );
}
```

## 📊 Báo Cáo

### Thêm Vào Dashboard

**Thống kê mới:**
- Đơn hàng đã gán: X
- Đơn hàng chờ nhận: Y
- Tỷ lệ nhận đơn: Z%
- Thời gian nhận đơn trung bình: T phút

## ⚠️ Lưu Ý Quan Trọng

### 1. Zalo OA API

**Yêu cầu:**
- Phải có Zalo OA (Official Account)
- Phải có Access Token
- User phải follow OA mới nhận được tin
- Có giới hạn số tin nhắn/ngày

**Giải pháp:**
- Hướng dẫn tài xế follow OA
- Lưu Zalo User ID vào database
- Fallback sang SMS nếu Zalo fail

### 2. Telegram Bot

**Yêu cầu:**
- Tạo bot qua @BotFather
- Lấy Bot Token
- User phải start bot trước
- Lưu Chat ID vào database

**Giải pháp:**
- Hướng dẫn tài xế start bot
- Command: `/start` để lấy Chat ID
- Lưu vào bảng drivers

### 3. Race Condition (Group Assignment)

**Vấn đề:**
- 2 tài xế click cùng lúc
- Cả 2 đều thấy "Nhận thành công"

**Giải pháp:**
```php
// Sử dụng database transaction
$wpdb->query('START TRANSACTION');

$booking = $wpdb->get_row("SELECT * FROM bookings WHERE id = $id FOR UPDATE");

if ($booking->status === 'assigned') {
    // Cập nhật
    $wpdb->update(...);
    $wpdb->query('COMMIT');
} else {
    $wpdb->query('ROLLBACK');
    wp_die('Đơn đã có người nhận');
}
```

## 🚀 Roadmap

### Version 1.0 (MVP)
- ✅ Gán trực tiếp cho tài xế
- ✅ Gửi thông báo Telegram
- ✅ Trang nhận đơn
- ✅ Cập nhật trạng thái

### Version 1.1
- ✅ Gán cho group Telegram
- ✅ Quản lý groups
- ✅ Race condition handling

### Version 1.2
- ✅ Tích hợp Zalo OA
- ✅ Gửi thông báo Zalo
- ✅ Gán cho group Zalo

### Version 2.0
- ⏳ Auto-assign (AI chọn tài xế phù hợp)
- ⏳ Push notification (mobile app)
- ⏳ Real-time tracking

## 💡 Đề Xuất Bổ Sung

### 1. Timeout Mechanism

Nếu tài xế không nhận đơn sau X phút:
- Tự động hủy gán
- Gửi lại cho group hoặc tài xế khác
- Thông báo cho admin

### 2. Priority System

Ưu tiên gán đơn cho:
- Tài xế gần điểm đón nhất
- Tài xế có rating cao
- Tài xế đang rảnh

### 3. Blacklist

Tài xế từ chối đơn quá nhiều:
- Tự động giảm priority
- Cảnh báo admin
- Tạm khóa tài khoản

## 📞 Câu Hỏi Cần Xác Nhận

1. **Zalo OA**: Bạn đã có Zalo OA chưa? Cần hướng dẫn đăng ký không?

2. **Telegram Bot**: Bạn đã có Bot Token chưa? Cần hướng dẫn tạo không?

3. **Timeout**: Sau bao lâu không nhận đơn thì tự động hủy gán? (Đề xuất: 15 phút)

4. **Group**: Có bao nhiêu group cần quản lý? (Zalo + Telegram)

5. **SMS Backup**: Có cần gửi SMS backup nếu Zalo/Telegram fail không?

6. **Mobile App**: Có kế hoạch làm app mobile cho tài xế không?

---

**Thời gian ước tính:** 4 tuần (1 tháng)

**Chi phí phát triển:** [Cần thảo luận]

**Yêu cầu kỹ thuật:**
- Zalo OA Account + Access Token
- Telegram Bot Token
- SSL Certificate (HTTPS) cho webhook
- Server có thể nhận webhook từ Zalo/Telegram

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
