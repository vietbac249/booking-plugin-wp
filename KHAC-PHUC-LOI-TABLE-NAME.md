# Khắc Phục Lỗi Tên Bảng Database

## ❌ Lỗi Gặp Phải

**Triệu chứng:**
- Đơn hàng được tạo thành công (hiển thị ở "Đơn Hàng Gần Đây")
- Nhưng trang "Quản Lý Đơn Hàng" hiển thị: "Chưa có đơn hàng nào"

## 🔍 Nguyên Nhân

**Tên bảng không khớp:**
- Bảng thực tế trong database: `wp_bookings`, `wp_drivers`
- Code đang query: `wp_booking_bookings`, `wp_booking_drivers` (SAI!)

**Tên cột không khớp:**
- Cột thực tế: `from_location`, `to_location`, `trip_datetime`
- Code đang dùng: `pickup_location`, `dropoff_location`, `pickup_datetime` (SAI!)

## ✅ Đã Sửa

### 1. Tên Bảng
```php
// ❌ SAI
$wpdb->prefix . 'booking_bookings'
$wpdb->prefix . 'booking_drivers'

// ✅ ĐÚNG
$wpdb->prefix . 'bookings'
$wpdb->prefix . 'drivers'
```

### 2. Tên Cột
```php
// ❌ SAI
$order->pickup_location
$order->dropoff_location
$order->pickup_datetime

// ✅ ĐÚNG
$order->from_location
$order->to_location
$order->trip_datetime
```

## 📦 Files Đã Sửa

### 1. booking-plugin.php
- Thay thế tất cả `booking_bookings` → `bookings`
- Thay thế tất cả `booking_drivers` → `drivers`
- Ảnh hưởng: 5 AJAX handlers

### 2. admin-orders.php
- Sửa query lấy danh sách đơn hàng
- Sửa query thống kê
- Sửa tên cột hiển thị
- Ảnh hưởng: Toàn bộ trang quản lý đơn hàng

## 🚀 Cách Cài Đặt

### Bước 1: Xóa Plugin Cũ
```
1. Vào: Plugins → Installed Plugins
2. Tìm: "Đặt Xe Nội Bài"
3. Click: "Deactivate"
4. Click: "Delete"
```

### Bước 2: Upload Plugin Mới
```
1. Tải file: booking-plugin-week2-complete.zip (mới nhất)
2. Vào: Plugins → Add New → Upload Plugin
3. Chọn file zip
4. Click: "Install Now"
5. Click: "Activate Plugin"
```

### Bước 3: Kiểm Tra
```
1. Vào: Đặt Xe → Đơn Hàng
2. Kiểm tra: Đơn hàng đã hiển thị
3. Kiểm tra: Thống kê hiển thị đúng
```

## 🧪 Test Sau Khi Sửa

### Test 1: Hiển Thị Đơn Hàng
1. Vào: Đặt Xe → Đơn Hàng
2. Kết quả: ✅ Hiển thị danh sách đơn hàng
3. Kiểm tra: Tên khách hàng, tuyến đường, giá tiền

### Test 2: Thống Kê
1. Xem phần thống kê trên cùng
2. Kết quả: ✅ Hiển thị số liệu đúng
3. Kiểm tra: Tổng đơn, Chờ xử lý, Hoàn thành, etc.

### Test 3: Gán Đơn Hàng
1. Click nút "👤 Gán Tài Xế"
2. Kết quả: ✅ Modal mở
3. Thử gán đơn hàng
4. Kết quả: ✅ Gán thành công

### Test 4: Tạo Đơn Mới
1. Tạo đơn hàng mới từ frontend
2. Vào: Đặt Xe → Đơn Hàng
3. Kết quả: ✅ Đơn mới hiển thị ngay

## 🐛 Nếu Vẫn Lỗi

### Lỗi: Vẫn không hiển thị đơn hàng

**Kiểm tra database:**
```sql
-- Kiểm tra tên bảng
SHOW TABLES LIKE '%bookings%';

-- Kết quả mong đợi:
-- wp_bookings (ĐÚNG)
-- wp_booking_bookings (SAI - nếu có thì xóa)

-- Kiểm tra dữ liệu
SELECT * FROM wp_bookings ORDER BY created_at DESC LIMIT 5;
```

**Nếu có cả 2 bảng:**
```sql
-- Copy dữ liệu từ bảng cũ sang bảng mới
INSERT INTO wp_bookings 
SELECT * FROM wp_booking_bookings;

-- Xóa bảng cũ
DROP TABLE wp_booking_bookings;
```

### Lỗi: Thiếu cột trong database

**Kiểm tra cột:**
```sql
DESCRIBE wp_bookings;
```

**Nếu thiếu cột, chạy lại database update:**
```
Truy cập: /wp-content/plugins/booking-plugin/update-database.php
```

### Lỗi: Tên cột vẫn sai

**Kiểm tra code:**
```php
// File: admin-orders.php
// Tìm dòng:
$order->from_location  // ĐÚNG
$order->pickup_location  // SAI

// Nếu thấy pickup_location, thay bằng from_location
```

## 📊 Mapping Tên Cột

| Code Cũ (SAI) | Code Mới (ĐÚNG) | Database Column |
|----------------|-----------------|-----------------|
| pickup_location | from_location | from_location |
| dropoff_location | to_location | to_location |
| pickup_datetime | trip_datetime | trip_datetime |
| booking_bookings | bookings | bookings |
| booking_drivers | drivers | drivers |

## 📝 Checklist

- [ ] Xóa plugin cũ
- [ ] Upload plugin mới
- [ ] Activate thành công
- [ ] Vào trang "Đơn Hàng"
- [ ] Đơn hàng hiển thị
- [ ] Thống kê hiển thị đúng
- [ ] Có thể gán đơn hàng
- [ ] Tạo đơn mới → Hiển thị ngay

## 🎯 Kết Luận

Lỗi đã được sửa hoàn toàn. Tất cả tên bảng và tên cột đã được cập nhật đúng với database schema.

**File mới:** `booking-plugin-week2-complete.zip`  
**Trạng thái:** ✅ Ready to use  
**Đã sửa:** Tên bảng + Tên cột

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
