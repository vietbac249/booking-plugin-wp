# Khắc Phục Lỗi: Table Prefix Không Khớp

**Ngày:** 04/03/2026  
**Vấn đề:** Database dùng prefix `Xrs_default` nhưng WordPress dùng `wp_`

---

## ❌ TRIỆU CHỨNG

1. **Autocomplete không tìm thấy tài xế** mặc dù đã kích hoạt
2. **Debug script báo "Không có tài xế"** nhưng phpMyAdmin thấy có dữ liệu
3. **Đơn hàng không hiển thị** trong trang Quản Lý Đơn Hàng
4. **Tất cả queries đều trả về rỗng**

---

## 🔍 NGUYÊN NHÂN

### Database Thực Tế:
- Database: `csrdexkohosting_sitbaoidivn_5`
- Prefix: `Xrs_default`
- Bảng: `Xrs_defaultdrivers`, `Xrs_defaultbookings`, etc.

### WordPress Config:
- `$wpdb->prefix` = `wp_`
- Code query: `wp_drivers`, `wp_bookings`
- Kết quả: **Bảng không tồn tại!**

### Tại Sao Xảy Ra?
1. Hosting đã cài WordPress với prefix tùy chỉnh
2. File `wp-config.php` có: `$table_prefix = 'Xrs_default';`
3. Nhưng WordPress không load đúng prefix
4. Hoặc plugin được cài trên site khác rồi chuyển sang

---

## ✅ GIẢI PHÁP

### Bước 1: Kiểm Tra Prefix

**Chạy file kiểm tra:**
```
URL: /wp-content/plugins/booking-plugin/check-prefix.php
```

File này sẽ:
- Hiển thị `$wpdb->prefix` hiện tại
- Liệt kê tất cả bảng trong database
- Phát hiện prefix thực tế
- So sánh và báo lỗi nếu không khớp

**Kết quả mong đợi:**
```
WordPress đang dùng prefix: wp_
Nhưng bảng thực tế dùng prefix: Xrs_default
⚠️ KHÔNG KHỚP!
```

### Bước 2: Sửa wp-config.php (KHUYẾN NGHỊ)

**Cách 1: Qua File Manager**

1. Vào cPanel → File Manager
2. Mở file: `public_html/wp-config.php`
3. Tìm dòng:
```php
$table_prefix = 'wp_';
```

4. Sửa thành:
```php
$table_prefix = 'Xrs_default';
```

5. Lưu file
6. Reload trang admin

**Cách 2: Qua FTP**

1. Kết nối FTP
2. Download file `wp-config.php`
3. Mở bằng text editor
4. Sửa `$table_prefix`
5. Upload lại
6. Reload trang

**Lưu ý:**
- Backup file `wp-config.php` trước khi sửa
- Prefix phải có dấu gạch dưới `_` ở cuối: `Xrs_default` (KHÔNG có `_`)
- Nếu bảng là `Xrs_defaultdrivers` thì prefix là `Xrs_default`

### Bước 3: Kiểm Tra Lại

**Test 1: Check prefix**
```
URL: /wp-content/plugins/booking-plugin/check-prefix.php
```
Kết quả: ✅ Prefix đang khớp!

**Test 2: Debug drivers**
```
URL: /wp-content/plugins/booking-plugin/debug-drivers.php
```
Kết quả: ✅ Hiển thị danh sách tài xế

**Test 3: Autocomplete**
1. Vào: Đặt Xe → Đơn Hàng
2. Click: "Gán Tài Xế"
3. Gõ tên tài xế
4. Kết quả: ✅ Hiển thị tài xế

---

## 🔧 GIẢI PHÁP DỰ PHÒNG

Nếu KHÔNG thể sửa `wp-config.php` (do quyền hạn hosting), có thể sửa code plugin:

### Cách 1: Hardcode Prefix (Tạm Thời)

**File:** `booking-plugin/booking-plugin.php`

Thêm vào đầu class `BookingPlugin`:

```php
class BookingPlugin {
    
    // Force correct prefix
    private $table_prefix = 'Xrs_default';
    
    public function __construct() {
        // Override WordPress prefix
        global $wpdb;
        $wpdb->prefix = $this->table_prefix;
        
        // ... rest of code
    }
}
```

**Ưu điểm:**
- Không cần sửa wp-config.php
- Hoạt động ngay lập tức

**Nhược điểm:**
- Chỉ áp dụng cho plugin này
- Các plugin khác vẫn bị lỗi
- Không phải giải pháp lâu dài

### Cách 2: Tạo Bảng Mới Với Prefix Đúng

**File:** `booking-plugin/fix-tables.php`

```php
<?php
// Load WordPress
require_once('../../../wp-load.php');

global $wpdb;

// Copy data from Xrs_default to wp_
$tables = ['drivers', 'bookings', 'booking_notification_groups', 'booking_assignment_logs'];

foreach ($tables as $table) {
    $old_table = 'Xrs_default' . $table;
    $new_table = $wpdb->prefix . $table;
    
    // Create new table structure
    $wpdb->query("CREATE TABLE {$new_table} LIKE {$old_table}");
    
    // Copy data
    $wpdb->query("INSERT INTO {$new_table} SELECT * FROM {$old_table}");
    
    echo "✅ Copied {$old_table} → {$new_table}<br>";
}

echo "<p>✅ Hoàn thành! Reload trang admin.</p>";
?>
```

**Chạy file:**
```
URL: /wp-content/plugins/booking-plugin/fix-tables.php
```

**Lưu ý:**
- Backup database trước khi chạy
- Chỉ chạy 1 lần
- Xóa file sau khi chạy xong

---

## 🧪 KIỂM TRA SAU KHI SỬA

### Test 1: WordPress Admin
```
Đặt Xe → Dashboard
```
- ✅ Hiển thị thống kê
- ✅ Có số liệu đơn hàng
- ✅ Có số liệu tài xế

### Test 2: Đơn Hàng
```
Đặt Xe → Đơn Hàng
```
- ✅ Hiển thị danh sách đơn hàng
- ✅ Thống kê đúng
- ✅ Có thể gán tài xế

### Test 3: Tài Xế
```
Đặt Xe → Tài Xế
```
- ✅ Hiển thị danh sách tài xế
- ✅ Có thể sửa thông tin
- ✅ Có thể kích hoạt/vô hiệu hóa

### Test 4: Autocomplete
```
Đơn Hàng → Gán Tài Xế → Gõ tên
```
- ✅ Hiển thị gợi ý tài xế
- ✅ Có thể chọn tài xế
- ✅ Gán đơn thành công

---

## 📊 SO SÁNH PREFIX

| Loại | Giá Trị | Ví Dụ Bảng |
|------|---------|------------|
| **Mặc định WordPress** | `wp_` | `wp_posts`, `wp_users` |
| **Hosting tùy chỉnh** | `Xrs_default` | `Xrs_defaultposts`, `Xrs_defaultusers` |
| **Plugin của bạn** | `$wpdb->prefix` | Phụ thuộc vào wp-config.php |

**Lưu ý:** 
- Prefix KHÔNG có dấu `_` ở cuối trong wp-config.php
- Nhưng khi dùng sẽ tự động thêm: `$wpdb->prefix . 'drivers'` → `Xrs_defaultdrivers`

---

## 🎯 CHECKLIST

- [ ] Chạy `check-prefix.php` để xác định prefix
- [ ] Backup file `wp-config.php`
- [ ] Sửa `$table_prefix` trong wp-config.php
- [ ] Reload trang admin WordPress
- [ ] Chạy lại `check-prefix.php` để kiểm tra
- [ ] Test debug-drivers.php
- [ ] Test autocomplete
- [ ] Test gán đơn hàng
- [ ] Xóa file check-prefix.php (bảo mật)

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **Backup trước khi sửa:**
   - Backup database
   - Backup wp-config.php
   - Backup toàn bộ site (nếu có thể)

2. **Không sửa prefix trong database:**
   - KHÔNG đổi tên bảng trong phpMyAdmin
   - KHÔNG chạy query ALTER TABLE
   - Chỉ sửa wp-config.php

3. **Sau khi sửa:**
   - Tất cả plugin sẽ hoạt động lại
   - Không cần cài lại plugin
   - Không mất dữ liệu

4. **Nếu vẫn lỗi:**
   - Kiểm tra lại wp-config.php
   - Clear cache (nếu dùng cache plugin)
   - Liên hệ hosting support

---

## 📞 HỖ TRỢ

Nếu vẫn gặp vấn đề sau khi làm theo hướng dẫn:

1. Chạy `check-prefix.php` và chụp màn hình
2. Chạy `debug-drivers.php` và chụp màn hình
3. Mở Console (F12) và chụp lỗi (nếu có)
4. Gửi thông tin để được hỗ trợ

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
