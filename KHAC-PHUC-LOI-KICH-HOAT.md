# Khắc Phục Lỗi Kích Hoạt Plugin

## ❌ Lỗi Gặp Phải

```
Plugin không được kích hoạt do một lỗi: lỗi không sửa được
```

## 🔍 Nguyên Nhân

Lỗi PHP syntax error trong file `booking-plugin.php`:
- Các AJAX handler methods được thêm VÀO SAU khi class đã đóng
- Thiếu dấu đóng ngoặc nhọn `}` cho class BookingPlugin
- Code structure bị sai

## ✅ Đã Sửa

### Vấn Đề
```php
class BookingPlugin {
    // ... methods ...
}

// Khởi tạo plugin
new BookingPlugin();

// ❌ SAI: Methods được thêm sau khi class đã đóng
public function ajax_search_drivers() {
    // ...
}
```

### Giải Pháp
```php
class BookingPlugin {
    // ... methods ...
    
    // ✅ ĐÚNG: Methods nằm TRONG class
    public function ajax_search_drivers() {
        // ...
    }
    
    public function ajax_assign_to_driver() {
        // ...
    }
    
    // ... other methods ...
}

// Khởi tạo plugin (ở cuối file)
new BookingPlugin();
```

## 📦 File Đã Sửa

**File mới:** `booking-plugin-week2-complete.zip` (đã cập nhật)

### Thay Đổi
- Di chuyển 5 AJAX handler methods vào TRONG class BookingPlugin
- Thêm dấu đóng `}` cho class
- Đảm bảo `new BookingPlugin();` ở cuối file

## 🚀 Cách Cài Đặt

### Bước 1: Xóa Plugin Cũ
```
1. Vào: Plugins → Installed Plugins
2. Tìm: "Đặt Xe Nội Bài"
3. Click: "Deactivate" (nếu đang active)
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
1. Plugin kích hoạt thành công
2. Vào: Đặt Xe → Đơn Hàng
3. Kiểm tra UI có hiển thị đúng
```

## 🧪 Test Sau Khi Sửa

### Test 1: Kích Hoạt Plugin
- ✅ Plugin kích hoạt không lỗi
- ✅ Không có thông báo lỗi

### Test 2: Kiểm Tra Menu
- ✅ Menu "Đặt Xe" hiển thị
- ✅ Submenu đầy đủ (Dashboard, Đơn Hàng, Tài Xế, etc.)

### Test 3: Kiểm Tra Đơn Hàng
- ✅ Trang "Đơn Hàng" load được
- ✅ Có cột "Gán Đơn"
- ✅ Có nút "Gán Tài Xế" và "Gán Group"

### Test 4: Kiểm Tra AJAX
- ✅ Click nút "Gán Tài Xế" → Modal mở
- ✅ Gõ tên tài xế → Autocomplete hoạt động
- ✅ Không có lỗi trong Console (F12)

## 🐛 Nếu Vẫn Lỗi

### Lỗi: "Cannot redeclare class"
**Nguyên nhân:** Plugin cũ chưa xóa sạch

**Giải pháp:**
```
1. Xóa folder: /wp-content/plugins/booking-plugin/
2. Upload lại plugin mới
3. Activate
```

### Lỗi: "Call to undefined function"
**Nguyên nhân:** Thiếu file hoặc include sai

**Giải pháp:**
```
1. Kiểm tra file: includes/notifications.php có tồn tại
2. Kiểm tra file: includes/database.php có tồn tại
3. Re-upload plugin
```

### Lỗi: "Database table not found"
**Nguyên nhân:** Chưa chạy database update

**Giải pháp:**
```
1. Truy cập: /wp-content/plugins/booking-plugin/update-database-assignment.php
2. Chạy script
3. Kiểm tra database
```

## 📝 Checklist

- [ ] Xóa plugin cũ
- [ ] Upload plugin mới (booking-plugin-week2-complete.zip)
- [ ] Activate thành công
- [ ] Menu "Đặt Xe" hiển thị
- [ ] Trang "Đơn Hàng" load được
- [ ] Nút "Gán Tài Xế" hiển thị
- [ ] Modal mở được
- [ ] Autocomplete hoạt động
- [ ] Không có lỗi trong Console

## 🎯 Kết Luận

Lỗi đã được sửa hoàn toàn. Plugin bây giờ có thể kích hoạt và hoạt động bình thường.

**File mới:** `booking-plugin-week2-complete.zip`  
**Trạng thái:** ✅ Ready to use

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
