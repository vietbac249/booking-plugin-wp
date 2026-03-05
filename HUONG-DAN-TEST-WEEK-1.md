# Hướng Dẫn Test Week 1 - Database Update

## 📦 File Đã Nén

**File:** `booking-plugin-week1-database.zip`

**Bao gồm:**
1. `update-database-assignment.php` - Script cập nhật database
2. `includes/notifications.php` - Hệ thống thông báo
3. `DE-XUAT-GAN-DON-HANG.md` - Đề xuất chi tiết
4. `ROADMAP-GAN-DON-HANG-FULL.md` - Kế hoạch 4 tuần
5. `HUONG-DAN-TRIEN-KHAI-GAN-DON.md` - Hướng dẫn triển khai
6. `TIEN-DO-WEEK-2.md` - Theo dõi tiến độ

---

## 🚀 Bước 1: Upload Files

### 1.1. Giải nén file
```
Giải nén: booking-plugin-week1-database.zip
```

### 1.2. Upload lên server
```
Upload vào: /wp-content/plugins/booking-plugin/
```

**Cấu trúc sau khi upload:**
```
/wp-content/plugins/booking-plugin/
├── update-database-assignment.php  ← File mới
├── includes/
│   ├── database.php               ← Đã có
│   └── notifications.php          ← File mới
├── DE-XUAT-GAN-DON-HANG.md       ← File mới
└── ... (các file khác)
```

---

## 🗄️ Bước 2: Chạy Database Update

### 2.1. Truy cập script
```
URL: https://your-site.com/wp-content/plugins/booking-plugin/update-database-assignment.php
```

**Lưu ý:** Thay `your-site.com` bằng domain thực của bạn

### 2.2. Kết quả mong đợi

Bạn sẽ thấy màn hình như này:

```
Cập Nhật Database - Tính Năng Gán Đơn Hàng

1. Cập nhật bảng bookings...
✅ Đã thêm cột thành công
✅ Đã thêm cột thành công
✅ Đã thêm cột thành công
... (7 cột)

2. Tạo bảng notification_groups...
✅ Đã tạo bảng notification_groups thành công

3. Cập nhật bảng drivers...
✅ Đã thêm cột thành công
✅ Đã thêm cột thành công

4. Cập nhật trạng thái đơn hàng...
✅ Đã cập nhật trạng thái thành công

5. Tạo bảng assignment_logs...
✅ Đã tạo bảng assignment_logs thành công

✅ Hoàn Thành Cập Nhật Database!
```

### 2.3. Nếu có lỗi

**Lỗi thường gặp:**

1. **"⚠️ Cột có thể đã tồn tại"**
   - Không sao, có thể bạn đã chạy script trước đó
   - Kiểm tra bảng trong phpMyAdmin

2. **"❌ Lỗi: Table already exists"**
   - Bảng đã tồn tại, không sao
   - Script an toàn, không ghi đè dữ liệu

3. **"Permission denied"**
   - Kiểm tra quyền file (chmod 644)
   - Kiểm tra user database có quyền ALTER TABLE

---

## ✅ Bước 3: Kiểm Tra Database

### 3.1. Vào phpMyAdmin

Truy cập: `https://your-site.com/phpmyadmin`

### 3.2. Kiểm tra bảng `wp_booking_bookings`

**Cột mới (7 cột):**
- `assigned_at` (DATETIME)
- `assigned_by` (INT)
- `accepted_at` (DATETIME)
- `assignment_type` (ENUM)
- `group_id` (INT)
- `accept_token` (VARCHAR)
- `token_expires` (INT)

**Cách kiểm tra:**
```sql
DESCRIBE wp_booking_bookings;
```

Hoặc click vào bảng → Tab "Structure"

### 3.3. Kiểm tra bảng mới

**Bảng 1:** `wp_booking_notification_groups`
```sql
SELECT * FROM wp_booking_notification_groups;
```
→ Bảng trống (chưa có data) là bình thường

**Bảng 2:** `wp_booking_assignment_logs`
```sql
SELECT * FROM wp_booking_assignment_logs;
```
→ Bảng trống (chưa có data) là bình thường

### 3.4. Kiểm tra bảng `wp_booking_drivers`

**Cột mới (2 cột):**
- `telegram_chat_id` (VARCHAR)
- `zalo_user_id` (VARCHAR)

```sql
DESCRIBE wp_booking_drivers;
```

### 3.5. Kiểm tra ENUM status

```sql
SHOW COLUMNS FROM wp_booking_bookings WHERE Field = 'status';
```

**Kết quả mong đợi:**
```
Type: enum('pending','assigned','accepted','in_progress','completed','cancelled')
```

---

## 📝 Bước 4: Xóa File Update (Bảo Mật)

**SAU KHI CHẠY XONG**, xóa file để bảo mật:

```
Xóa file: /wp-content/plugins/booking-plugin/update-database-assignment.php
```

**Lý do:** File này có thể chạy lại và gây lỗi nếu ai đó truy cập URL

---

## 🧪 Bước 5: Test Notification System (Optional)

### 5.1. Chuẩn bị

**Cần có:**
- Telegram Bot Token (từ @BotFather)
- Telegram Chat ID (từ @userinfobot)

### 5.2. Tạo file test

Tạo file: `/wp-content/plugins/booking-plugin/test-notification.php`

```php
<?php
require_once('../../../wp-load.php');
require_once('includes/notifications.php');

// Cấu hình
update_option('booking_telegram_bot_token', 'YOUR_BOT_TOKEN_HERE');

// Test gửi
$result = Booking_Notifications::test_notification(
    'telegram',
    'YOUR_CHAT_ID_HERE'
);

echo '<pre>';
print_r($result);
echo '</pre>';
?>
```

### 5.3. Chạy test

```
URL: https://your-site.com/wp-content/plugins/booking-plugin/test-notification.php
```

**Kết quả mong đợi:**
```
Array
(
    [success] => 1
    [message] => Đã gửi Telegram thành công
)
```

Và bạn sẽ nhận được tin nhắn test trên Telegram!

### 5.4. Xóa file test

```
Xóa: /wp-content/plugins/booking-plugin/test-notification.php
```

---

## ✅ Checklist Hoàn Thành

Đánh dấu ✅ khi hoàn thành:

- [ ] Upload files lên server
- [ ] Chạy `update-database-assignment.php`
- [ ] Thấy thông báo "✅ Hoàn Thành"
- [ ] Kiểm tra bảng `bookings` có 7 cột mới
- [ ] Kiểm tra bảng `notification_groups` đã tạo
- [ ] Kiểm tra bảng `assignment_logs` đã tạo
- [ ] Kiểm tra bảng `drivers` có 2 cột mới
- [ ] Kiểm tra ENUM status có 6 giá trị
- [ ] Xóa file `update-database-assignment.php`
- [ ] (Optional) Test notification system
- [ ] Backup database sau khi update

---

## 📸 Screenshot Cần Chụp

Để tôi kiểm tra, vui lòng chụp màn hình:

1. **Kết quả chạy script**
   - Toàn bộ màn hình sau khi chạy `update-database-assignment.php`

2. **phpMyAdmin - Bảng bookings**
   - Tab "Structure" của bảng `wp_booking_bookings`
   - Cuộn xuống để thấy 7 cột mới

3. **phpMyAdmin - Bảng notification_groups**
   - Tab "Structure" của bảng `wp_booking_notification_groups`

4. **phpMyAdmin - Bảng assignment_logs**
   - Tab "Structure" của bảng `wp_booking_assignment_logs`

5. **phpMyAdmin - Bảng drivers**
   - Tab "Structure" của bảng `wp_booking_drivers`
   - Cuộn xuống để thấy 2 cột mới

---

## ❓ Câu Hỏi Thường Gặp

### Q1: Script chạy xong có cần chạy lại không?

**A:** Không. Chỉ chạy 1 lần duy nhất. Nếu chạy lại sẽ thấy cảnh báo "Cột đã tồn tại" nhưng không sao.

### Q2: Có mất dữ liệu cũ không?

**A:** KHÔNG. Script chỉ THÊM cột/bảng mới, không xóa hay sửa dữ liệu cũ.

### Q3: Nếu có lỗi thì làm sao?

**A:** 
1. Chụp màn hình lỗi
2. Gửi cho tôi
3. Tôi sẽ fix ngay

### Q4: Có cần backup trước không?

**A:** NÊN backup để an toàn, nhưng script rất an toàn.

### Q5: Sau khi test xong thì sao?

**A:** Báo cho tôi kết quả:
- ✅ OK → Tôi tiếp tục Week 2
- ❌ Có lỗi → Tôi fix trước

---

## 🎯 Sau Khi Test Xong

**Vui lòng trả lời:**

```
1. Database update: [OK / Có lỗi]
2. Số bảng đã tạo: [2 bảng / Khác]
3. Số cột đã thêm: [9 cột / Khác]
4. Có lỗi gì không: [Không / Có - mô tả]
5. Đã xóa file update chưa: [Rồi / Chưa]
6. Sẵn sàng Week 2: [Có / Chưa]
```

**Sau khi nhận được xác nhận OK, tôi sẽ:**
- Tiếp tục code Week 2 (AJAX handlers + UI)
- Tạo thêm 4-5 files
- Hoàn thành gán tài xế cơ bản
- Có thể test được trong 1-2 ngày

---

## 📞 Liên Hệ

Nếu có vấn đề:
- Gửi screenshot lỗi
- Mô tả chi tiết vấn đề
- Tôi sẽ hỗ trợ ngay

---

**Chúc bạn test thành công!** 🚀

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
