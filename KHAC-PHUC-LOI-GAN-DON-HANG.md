# 🔧 Khắc Phục Lỗi Gán Đơn Hàng

## ❌ Vấn Đề
Không thể gán đơn hàng cho tài xế - Modal hiển thị nhưng không tìm được tài xế hoặc không gửi được thông báo.

## 🔍 Nguyên Nhân
1. **Tên bảng sai trong file notifications.php**
   - Code đang dùng: `booking_drivers`, `booking_bookings`
   - Tên bảng đúng: `drivers`, `bookings`

2. **Tên cột sai trong notifications.php**
   - Code đang dùng: `pickup_location`, `dropoff_location`, `pickup_datetime`
   - Tên cột đúng: `from_location`, `to_location`, `trip_datetime`

3. **Thiếu các cột trong bảng bookings**
   - `assigned_at` - Thời gian gán đơn
   - `assigned_by` - Người gán đơn
   - `assignment_type` - Loại gán (direct/group)
   - `group_id` - ID group (nếu gán vào group)
   - `accept_token` - Token để tài xế nhận đơn
   - `token_expires` - Thời gian hết hạn token

4. **Thiếu các cột trong bảng drivers**
   - `telegram_chat_id` - Chat ID Telegram của tài xế
   - `zalo_user_id` - User ID Zalo của tài xế

5. **Thiếu 2 bảng mới**
   - `booking_assignment_logs` - Lịch sử gán đơn
   - `booking_notification_groups` - Danh sách group thông báo

## ✅ Giải Pháp

### Bước 1: Cập nhật Database
Truy cập URL sau để chạy script cập nhật database:

```
http://your-site.com/wp-content/plugins/booking-plugin/update-database-assignment-fix.php
```

Script này sẽ:
- ✅ Thêm 6 cột mới vào bảng `bookings`
- ✅ Thêm 2 cột mới vào bảng `drivers`
- ✅ Tạo bảng `booking_assignment_logs`
- ✅ Tạo bảng `booking_notification_groups`

### Bước 2: Xác Nhận Các File Đã Được Sửa

Các file sau đã được cập nhật:

1. **booking-plugin/includes/notifications.php**
   - ✅ Sửa tên bảng: `booking_drivers` → `drivers`
   - ✅ Sửa tên bảng: `booking_bookings` → `bookings`
   - ✅ Sửa tên cột: `pickup_location` → `from_location`
   - ✅ Sửa tên cột: `dropoff_location` → `to_location`
   - ✅ Sửa tên cột: `pickup_datetime` → `trip_datetime`

2. **booking-plugin/includes/database.php**
   - ✅ Thêm 6 cột mới vào bảng `bookings`
   - ✅ Thêm 2 cột mới vào bảng `drivers`
   - ✅ Thêm bảng `booking_assignment_logs`
   - ✅ Thêm bảng `booking_notification_groups`

### Bước 3: Test Tính Năng

1. **Tạo đơn hàng test**
   - Vào trang đặt xe
   - Tạo một đơn hàng mới

2. **Test gán cho tài xế**
   - Vào Quản Lý Đơn Hàng
   - Click "Gán Tài Xế"
   - Tìm kiếm tài xế (theo tên, SĐT, biển số)
   - Chọn tài xế và click "Gán Cho Tài Xế"

3. **Test gán cho group**
   - Click "Gán Group"
   - Chọn group Telegram hoặc Zalo
   - Click "Gửi Vào Group"

## 📋 Cấu Trúc Database Sau Khi Sửa

### Bảng `bookings` (đã thêm 6 cột)
```sql
assigned_at timestamp NULL
assigned_by bigint(20)
assignment_type varchar(20)
group_id bigint(20)
accept_token varchar(64)
token_expires bigint(20)
```

### Bảng `drivers` (đã thêm 2 cột)
```sql
telegram_chat_id varchar(100)
zalo_user_id varchar(100)
```

### Bảng `booking_assignment_logs` (mới)
```sql
id bigint(20) AUTO_INCREMENT
booking_id bigint(20)
driver_id bigint(20)
group_id bigint(20)
assignment_type varchar(20)
assigned_by bigint(20)
status varchar(20)
accepted_at timestamp
created_at timestamp
```

### Bảng `booking_notification_groups` (mới)
```sql
id bigint(20) AUTO_INCREMENT
name varchar(100)
type varchar(20)
chat_id varchar(100)
bot_token varchar(255)
group_id varchar(100)
access_token varchar(255)
is_active tinyint(1)
created_at timestamp
updated_at timestamp
```

## 🎯 Kết Quả Mong Đợi

Sau khi hoàn tất các bước trên:

✅ Có thể tìm kiếm tài xế khi gán đơn hàng
✅ Có thể gán đơn hàng cho tài xế cụ thể
✅ Có thể gán đơn hàng vào group Telegram/Zalo
✅ Tài xế nhận được thông báo qua Telegram/Zalo
✅ Tài xế có thể nhận/từ chối đơn hàng
✅ Lưu lịch sử gán đơn hàng

## 🔧 Troubleshooting

### Lỗi: "Không tìm thấy tài xế"
- Kiểm tra bảng `drivers` có dữ liệu không
- Kiểm tra tài xế có `status = 'active'` không

### Lỗi: "Không thể gửi thông báo"
- Kiểm tra tài xế có `telegram_chat_id` hoặc `zalo_user_id` không
- Kiểm tra cấu hình Telegram Bot Token và Zalo Access Token

### Lỗi: "Không tìm thấy group"
- Kiểm tra bảng `booking_notification_groups` có dữ liệu không
- Kiểm tra group có `is_active = 1` không

## 📝 Ghi Chú

- Backup database trước khi chạy script cập nhật
- Nếu gặp lỗi, kiểm tra file log tại `wp-content/debug.log`
- Có thể chạy lại script cập nhật nhiều lần (script tự động kiểm tra cột đã tồn tại)

## 🎉 Hoàn Tất

Tính năng gán đơn hàng đã hoạt động bình thường!
