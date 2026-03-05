# Khắc Phục Lỗi "Có lỗi xảy ra. Vui lòng thử lại"

## Vấn Đề
Đã setup bảng giá trong admin nhưng khi test ở trang chủ vẫn báo lỗi.

---

## NGUYÊN NHÂN

Database chưa được cập nhật với cột mới `from_location` và `to_location`.

---

## GIẢI PHÁP

### Cách 1: Chạy Script Cập Nhật (KHUYẾN NGHỊ)

1. **Upload file `update-database.php`** lên thư mục plugin:
   ```
   wp-content/plugins/booking-plugin/update-database.php
   ```

2. **Truy cập URL:**
   ```
   https://your-domain.com/wp-content/plugins/booking-plugin/update-database.php
   ```

3. **Xem kết quả:**
   - ✅ Đã thêm cột from_location
   - ✅ Đã thêm cột to_location
   - ✅ Đã thêm index

4. **Xóa file `update-database.php`** sau khi chạy xong

---

### Cách 2: Kích Hoạt Lại Plugin

1. Vào **Plugins** trong WordPress Admin
2. **Deactivate** plugin "Đặt Xe Nội Bài"
3. **Activate** lại plugin
4. Database sẽ tự động cập nhật

---

### Cách 3: Chạy SQL Trực Tiếp (Nâng Cao)

Vào **phpMyAdmin** và chạy các câu lệnh sau:

```sql
-- Thêm cột from_location
ALTER TABLE wp_custom_pricing 
ADD COLUMN from_location VARCHAR(255) AFTER id;

-- Thêm cột to_location
ALTER TABLE wp_custom_pricing 
ADD COLUMN to_location VARCHAR(255) AFTER from_location;

-- Thêm index
ALTER TABLE wp_custom_pricing 
ADD INDEX idx_from_location (from_location);

ALTER TABLE wp_custom_pricing 
ADD INDEX idx_to_location (to_location);
```

**Lưu ý:** Thay `wp_` bằng prefix database của bạn.

---

## KIỂM TRA SAU KHI SỬA

### 1. Kiểm Tra Database

Vào **phpMyAdmin** > Chọn bảng `wp_custom_pricing` > Tab "Structure"

Phải thấy các cột:
- ✅ id
- ✅ from_location (VARCHAR 255)
- ✅ to_location (VARCHAR 255)
- ✅ car_type
- ✅ trip_type
- ✅ base_price
- ✅ price_per_km
- ✅ min_distance
- ✅ max_distance
- ✅ vat_rate
- ✅ is_active
- ✅ created_at
- ✅ updated_at

### 2. Kiểm Tra Bảng Giá

Vào **Đặt Xe > Cài Đặt > Tab "Bảng Giá"**

Phải thấy:
- ✅ Chế độ: "Tùy chỉnh (Options 2)"
- ✅ Có ít nhất 1 bảng giá "Hoạt động"
- ✅ Bảng giá có đầy đủ thông tin

### 3. Test Lại

1. Vào trang đặt xe
2. Nhập:
   - Điểm đi: `247 Cầu Giấy Hà Nội`
   - Điểm đến: `Sân bay Nội Bài`
   - Loại xe: `4 chỗ cốp rộng` (hoặc loại xe đã setup)
3. Bấm "Kiểm Tra Giá"
4. ✅ Phải thấy giá hiển thị

---

## NẾU VẪN LỖI

### Debug Mode

Thêm code này vào `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Sau đó test lại và xem file log tại:
```
wp-content/debug.log
```

### Kiểm Tra Console

1. Mở trang đặt xe
2. Bấm F12 (Developer Tools)
3. Vào tab "Console"
4. Bấm "Kiểm Tra Giá"
5. Xem lỗi gì hiện ra

### Kiểm Tra Network

1. Mở trang đặt xe
2. Bấm F12 > Tab "Network"
3. Bấm "Kiểm Tra Giá"
4. Tìm request `admin-ajax.php`
5. Xem Response có gì

---

## CÁC LỖI THƯỜNG GẶP

### Lỗi 1: "Chưa có bảng giá cho loại xe này"

**Nguyên nhân:** Tên loại xe không khớp

**Giải pháp:**
- Trong admin, loại xe phải là: `4 chỗ cốp rộng`, `7 chỗ`, `4 chỗ cốp nhỏ`, `16 chỗ`, `29 chỗ`, `45 chỗ`
- KHÔNG được viết sai chính tả

### Lỗi 2: "Có lỗi xảy ra"

**Nguyên nhân:** Lỗi JavaScript hoặc AJAX

**Giải pháp:**
1. Clear cache trình duyệt
2. Hard refresh (Ctrl + F5)
3. Kiểm tra Console có lỗi JS không

### Lỗi 3: Không hiển thị gì

**Nguyên nhân:** Chế độ tính giá chưa đúng

**Giải pháp:**
1. Vào Cài Đặt > Bảng Giá
2. Chọn "Tùy chỉnh (Options 2)"
3. Bấm "Lưu Chế Độ Tính Giá"

---

## LIÊN HỆ HỖ TRỢ

Nếu vẫn không giải quyết được, gửi thông tin sau:

1. Screenshot bảng giá trong admin
2. Screenshot lỗi trên trang chủ
3. Screenshot Console (F12)
4. File `debug.log` (nếu có)

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
