# 🔧 Khắc Phục Lỗi 404 Khi Nhận Đơn Hàng

## ❌ Vấn Đề
Khi tài xế click vào link "Nhận Đơn" từ Telegram, trang báo lỗi 404 "Không tìm thấy trang".

**URL mẫu:**
```
https://datxe.nguyenvietbac.id.vn/nhan-don-hang/?action=accept_booking&booking=4&token=04vFy6R1Pe2nPEewAzwSTvRWhHxyeqaI
```

## 🔍 Nguyên Nhân
WordPress chưa nhận diện được URL `/nhan-don-hang/` vì:
1. Rewrite rule chưa được flush sau khi thêm code mới
2. Permalink structure chưa được cập nhật
3. Query var chưa được đăng ký đúng cách

## ✅ Giải Pháp

### Cách 1: Flush Permalinks (KHUYẾN NGHỊ - Nhanh Nhất)

1. Đăng nhập WordPress Admin
2. Vào **Cài Đặt** → **Permalinks** (hoặc **Settings** → **Permalinks**)
3. Không cần thay đổi gì
4. Kéo xuống dưới và click **Lưu Thay Đổi** (Save Changes)
5. Thử lại link nhận đơn

**Giải thích:** Khi bạn save permalinks, WordPress sẽ tự động flush tất cả rewrite rules và đăng ký lại, bao gồm cả rule `/nhan-don-hang/` của chúng ta.

### Cách 2: Deactivate/Activate Plugin

1. Vào **Plugins** → **Installed Plugins**
2. Tìm plugin **"Đặt Xe Nội Bài"**
3. Click **Deactivate**
4. Đợi 2 giây
5. Click **Activate** lại
6. Thử lại link

**Giải thích:** Khi activate plugin, hàm `activate_plugin()` sẽ chạy và flush rewrite rules.

### Cách 3: Chạy Script Test (Nếu 2 cách trên không được)

1. Truy cập: `http://your-site.com/wp-content/plugins/booking-plugin/test-rewrite.php`
2. Script sẽ:
   - Kiểm tra xem rule `nhan-don-hang` có tồn tại không
   - Tự động flush rewrite rules nếu cần
   - Hiển thị tất cả rewrite rules hiện tại
3. Reload trang test để xác nhận
4. Thử lại link nhận đơn

### Cách 4: Thêm Rule Thủ Công Vào .htaccess (Backup Plan)

Nếu tất cả các cách trên đều không được, thêm rule này vào file `.htaccess`:

```apache
# BEGIN Booking Plugin
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^nhan-don-hang/?$ /index.php?accept_booking=1 [QSA,L]
</IfModule>
# END Booking Plugin
```

**Lưu ý:** Thêm TRƯỚC các rule của WordPress (trước dòng `# BEGIN WordPress`)

## 🧪 Kiểm Tra Sau Khi Sửa

### Test 1: Truy Cập Trực Tiếp
```
http://your-site.com/nhan-don-hang/?booking=1&token=test
```

**Kết quả mong đợi:**
- ✅ Trang hiển thị (có thể báo "Link không hợp lệ" - đó là bình thường vì token test)
- ❌ Không được báo 404

### Test 2: Kiểm Tra Query Var
Thêm code này vào `functions.php` của theme (tạm thời):

```php
add_action('wp', function() {
    if (get_query_var('accept_booking')) {
        echo "✅ Query var 'accept_booking' hoạt động!";
        exit;
    }
});
```

Truy cập: `http://your-site.com/nhan-don-hang/`

**Kết quả mong đợi:** Hiển thị "✅ Query var 'accept_booking' hoạt động!"

### Test 3: Gửi Đơn Thật
1. Tạo đơn hàng mới
2. Gán cho tài xế hoặc gửi vào group
3. Click link "Nhận Đơn" từ Telegram
4. Trang phải hiển thị thông tin đơn hàng

## 🔍 Debug Nâng Cao

### Kiểm Tra Rewrite Rules Trong Database

Chạy query này trong phpMyAdmin:

```sql
SELECT option_value 
FROM wp_options 
WHERE option_name = 'rewrite_rules';
```

Tìm xem có rule `nhan-don-hang` không.

### Kiểm Tra Code

Đảm bảo các hàm sau có trong `booking-plugin.php`:

```php
// 1. Add rewrite rules
public function add_rewrite_rules() {
    add_rewrite_rule('^nhan-don-hang/?', 'index.php?accept_booking=1', 'top');
}

// 2. Add query vars
public function add_query_vars($vars) {
    $vars[] = 'accept_booking';
    return $vars;
}

// 3. Handle template
public function handle_accept_booking_page() {
    if (get_query_var('accept_booking')) {
        include BOOKING_PLUGIN_PATH . 'templates/accept-booking-page.php';
        exit;
    }
}
```

### Enable WordPress Debug

Thêm vào `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Kiểm tra file `wp-content/debug.log` để xem có lỗi gì không.

## 📋 Checklist Khắc Phục

- [ ] Đã flush permalinks (Cài Đặt → Permalinks → Lưu)
- [ ] Đã deactivate/activate plugin
- [ ] Đã chạy test-rewrite.php
- [ ] Đã kiểm tra file .htaccess
- [ ] Đã test truy cập trực tiếp `/nhan-don-hang/`
- [ ] Đã kiểm tra WordPress debug log
- [ ] Link nhận đơn từ Telegram hoạt động

## 🎯 Kết Quả Mong Đợi

Sau khi khắc phục:

✅ Truy cập `/nhan-don-hang/?booking=123&token=abc` → Hiển thị trang nhận đơn
✅ Click "Nhận Đơn" từ Telegram → Hiển thị thông tin đơn hàng
✅ Tài xế có thể nhận/từ chối đơn
✅ Trạng thái đơn hàng được cập nhật

## 🆘 Nếu Vẫn Không Được

### Plan B: Sử dụng Page Template

Nếu rewrite rule vẫn không hoạt động, tạo một page thật trong WordPress:

1. Vào **Pages** → **Add New**
2. Tiêu đề: "Nhận Đơn Hàng"
3. Slug: `nhan-don-hang`
4. Template: Chọn "Full Width" hoặc "Blank"
5. Publish

Sau đó sửa code trong `booking-plugin.php`:

```php
// Thay vì dùng rewrite rule, dùng page template
add_filter('page_template', array($this, 'accept_booking_template'));

public function accept_booking_template($template) {
    if (is_page('nhan-don-hang')) {
        return BOOKING_PLUGIN_PATH . 'templates/accept-booking-page.php';
    }
    return $template;
}
```

### Plan C: Sử dụng Shortcode

Tạo shortcode để nhúng vào page:

```php
add_shortcode('nhan_don_hang', array($this, 'render_accept_booking'));

public function render_accept_booking() {
    ob_start();
    include BOOKING_PLUGIN_PATH . 'templates/accept-booking-page.php';
    return ob_get_clean();
}
```

Sau đó thêm shortcode `[nhan_don_hang]` vào page "Nhận Đơn Hàng".

## 📝 Ghi Chú

- Mỗi khi sửa code liên quan đến rewrite rules, phải flush permalinks
- Nên test trên localhost trước khi deploy lên production
- Backup database và files trước khi thử các cách khắc phục

## 🎉 Hoàn Tất

Sau khi khắc phục thành công, link nhận đơn từ Telegram sẽ hoạt động bình thường!
