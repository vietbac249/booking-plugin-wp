# Khắc Phục Lỗi "Liên kết bạn theo dõi đã hết hạn"

## ❌ VẤN ĐỀ

Khi lưu cài đặt trong admin, báo lỗi:
```
Liên kết bạn theo dõi đã hết hạn.
Xin vui lòng thử lại.
```

---

## 🔍 NGUYÊN NHÂN

### 1. Session Timeout
- Bạn ở trang admin quá lâu (> 24 giờ)
- WordPress nonce token đã hết hạn

### 2. Cache Plugin
- Plugin cache lưu form cũ với nonce hết hạn

### 3. Server Time Sai
- Thời gian server không đồng bộ

---

## ✅ GIẢI PHÁP

### Giải Pháp 1: Refresh Trang (NHANH NHẤT)

1. **Bấm F5** hoặc **Ctrl + F5** để refresh trang
2. Điền lại thông tin
3. Bấm "Lưu" ngay lập tức

**Lưu ý:** Nonce token có hiệu lực 24 giờ, nếu bạn mở trang admin quá lâu sẽ hết hạn.

---

### Giải Pháp 2: Clear Cache

**Nếu dùng Cache Plugin:**

1. **W3 Total Cache:**
   - Performance > Purge All Caches

2. **WP Super Cache:**
   - Settings > WP Super Cache > Delete Cache

3. **WP Rocket:**
   - Settings > Clear Cache

4. **Cloudflare:**
   - Caching > Purge Everything

**Sau đó:**
- Refresh trang admin (F5)
- Thử lưu lại

---

### Giải Pháp 3: Tăng Thời Gian Nonce

Thêm code này vào `wp-config.php`:

```php
// Tăng thời gian nonce lên 48 giờ
define('NONCE_SALT_LENGTH', 48);
```

Hoặc tăng session timeout:

```php
// Tăng session timeout lên 48 giờ
define('ADMIN_COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');
define('COOKIEPATH', '/');
define('SITECOOKIEPATH', '/');
```

---

### Giải Pháp 4: Kiểm Tra Thời Gian Server

1. **Vào phpMyAdmin**
2. Chạy query:
   ```sql
   SELECT NOW();
   ```
3. Kiểm tra thời gian có đúng không

**Nếu sai:**
- Liên hệ hosting để điều chỉnh timezone
- Hoặc thêm vào `wp-config.php`:
  ```php
  date_default_timezone_set('Asia/Ho_Chi_Minh');
  ```

---

### Giải Pháp 5: Tắt Cache Cho Admin

Thêm vào `.htaccess`:

```apache
# Disable cache for admin
<IfModule mod_headers.c>
    <FilesMatch "\.(php)$">
        <If "%{REQUEST_URI} =~ m#^/wp-admin/#">
            Header set Cache-Control "no-cache, no-store, must-revalidate"
            Header set Pragma "no-cache"
            Header set Expires 0
        </If>
    </FilesMatch>
</IfModule>
```

---

## 🎯 QUY TRÌNH LÀM VIỆC ĐÚNG

### Khi Lưu Cài Đặt:

1. **Vào trang admin**
2. **Điền thông tin ngay**
3. **Bấm "Lưu" trong vòng 5 phút**
4. ✅ Thành công!

### KHÔNG NÊN:

❌ Mở trang admin rồi để đó vài giờ  
❌ Mở nhiều tab admin cùng lúc  
❌ Copy/paste từ file cũ  

---

## 🔧 KIỂM TRA SAU KHI SỬA

### Test 1: Lưu Cài Đặt Chung

1. Vào **Đặt Xe > Cài Đặt > Tab "Cài Đặt Chung"**
2. Thay đổi 1 giá trị (VD: Giá sân bay)
3. Bấm "Lưu Cài Đặt"
4. ✅ Thấy thông báo "Cài đặt đã được lưu"

### Test 2: Lưu Chế Độ Tính Giá

1. Vào **Tab "Bảng Giá"**
2. Chọn "Tùy chỉnh (Options 2)"
3. Bấm "Lưu Chế Độ Tính Giá"
4. ✅ Thấy thông báo "Cài đặt đã được lưu"

### Test 3: Thêm Bảng Giá

1. Bấm "Thêm Bảng Giá"
2. Điền thông tin
3. Bấm "Lưu Bảng Giá"
4. ✅ Thấy thông báo "Đã thêm bảng giá mới!"

---

## 🐛 NẾU VẪN LỖI

### Debug Mode

Thêm vào `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

Xem log tại: `wp-content/debug.log`

### Kiểm Tra Nonce

Thêm code này vào `functions.php` để test:

```php
add_action('admin_notices', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'booking-settings') {
        $nonce = wp_create_nonce('booking_plugin_settings-options');
        echo '<div class="notice notice-info">';
        echo '<p>Current Nonce: ' . $nonce . '</p>';
        echo '<p>Server Time: ' . current_time('mysql') . '</p>';
        echo '</div>';
    }
});
```

### Liên Hệ Hosting

Nếu vẫn lỗi, có thể do:
- Server cấu hình sai
- PHP session không hoạt động
- Firewall chặn request

→ Liên hệ hosting support

---

## 💡 MẸO

### Mẹo 1: Lưu Nhanh
- Điền form → Lưu ngay
- Không để quá 5 phút

### Mẹo 2: Refresh Trước Khi Lưu
- Vào trang admin
- Bấm F5
- Điền form
- Lưu ngay

### Mẹo 3: Tắt Cache Cho Admin
- Dùng plugin cache? → Exclude `/wp-admin/`
- Hoặc tắt cache hoàn toàn khi làm việc

---

## 📞 HỖ TRỢ KHẨN CẤP

Nếu cần lưu gấp mà vẫn lỗi:

1. **Tắt tất cả plugin khác**
2. **Chuyển sang theme mặc định**
3. **Clear cache trình duyệt** (Ctrl + Shift + Delete)
4. **Thử lại**

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
