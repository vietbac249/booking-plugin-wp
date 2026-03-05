# Hướng Dẫn Debug Autocomplete - Nhanh

**Vấn đề:** Autocomplete không tìm thấy tài xế

---

## 🚀 BƯỚC 1: KIỂM TRA PREFIX (QUAN TRỌNG NHẤT!)

### Chạy file kiểm tra:
```
URL: /wp-content/plugins/booking-plugin/check-prefix.php
```

### Xem kết quả:

**Nếu thấy:**
```
✅ Prefix đang khớp!
```
→ Chuyển sang BƯỚC 2

**Nếu thấy:**
```
❌ WordPress đang dùng prefix: wp_
❌ Nhưng bảng thực tế dùng prefix: Xrs_default
```
→ **ĐÂY LÀ VẤN ĐỀ CHÍNH!** Làm theo BƯỚC 1A

---

### BƯỚC 1A: SỬA PREFIX (nếu không khớp)

**Cách 1: Sửa wp-config.php (KHUYẾN NGHỊ)**

1. Vào cPanel → File Manager
2. Mở file: `public_html/wp-config.php`
3. Tìm dòng:
```php
$table_prefix = 'wp_';
```

4. Sửa thành (theo kết quả từ check-prefix.php):
```php
$table_prefix = 'Xrs_default';
```

5. Lưu file
6. Reload trang admin WordPress
7. Chạy lại `check-prefix.php` để kiểm tra

**Kết quả mong đợi:**
```
✅ Prefix đang khớp!
```

---

## 🚀 BƯỚC 2: KIỂM TRA TÀI XẾ

### Chạy file debug:
```
URL: /wp-content/plugins/booking-plugin/debug-drivers.php
```

### Xem kết quả:

**Nếu thấy:**
```
❌ Không có tài xế nào trong database!
```
→ Quay lại BƯỚC 1, prefix chưa đúng

**Nếu thấy danh sách tài xế:**
- Kiểm tra cột "Status"
- Nếu status = "⚠️ Pending" → Click "Kích hoạt"
- Sau khi kích hoạt, status = "✅ Active"

---

## 🚀 BƯỚC 3: TEST AUTOCOMPLETE

1. Vào: **Đặt Xe → Đơn Hàng**
2. Click: **"Gán Tài Xế"** (nút xanh)
3. Gõ tên tài xế hoặc SĐT
4. Kết quả:
   - ✅ Hiển thị tài xế → **THÀNH CÔNG!**
   - ❌ "Không tìm thấy" → Xem BƯỚC 4

---

## 🚀 BƯỚC 4: DEBUG AJAX (nếu vẫn lỗi)

### Mở Console:
1. Nhấn **F12** (Chrome/Firefox)
2. Chọn tab **Console**
3. Thử autocomplete lại
4. Xem có lỗi không

### Các lỗi thường gặp:

**Lỗi 1: "ajaxurl is not defined"**
```
Giải pháp: Reload trang (Ctrl+F5)
```

**Lỗi 2: "403 Forbidden"**
```
Giải pháp: Nonce hết hạn, reload trang
```

**Lỗi 3: "500 Internal Server Error"**
```
Giải pháp: Lỗi PHP, kiểm tra error log
```

**Lỗi 4: Response = "Không tìm thấy tài xế"**
```
Giải pháp: 
- Kiểm tra lại prefix (BƯỚC 1)
- Kiểm tra status tài xế (BƯỚC 2)
```

---

## 🚀 BƯỚC 5: TEST AJAX TRỰC TIẾP

### Chạy file test:
```
URL: /wp-content/plugins/booking-plugin/test-ajax.php
```

File này sẽ:
- Gọi AJAX handler trực tiếp
- Hiển thị SQL query
- Hiển thị kết quả
- Có nút "Test AJAX" để test qua browser

**Kết quả mong đợi:**
```
✅ Tìm thấy 1 tài xế
Nguyễn Trần Bảo Nam - 0963134651 (19 B1 27726)
```

---

## 📊 TÓM TẮT CHECKLIST

| Bước | Làm Gì | Kết Quả Mong Đợi |
|------|--------|------------------|
| 1 | Chạy `check-prefix.php` | ✅ Prefix khớp |
| 2 | Chạy `debug-drivers.php` | ✅ Có tài xế, status = Active |
| 3 | Test autocomplete | ✅ Hiển thị tài xế |
| 4 | Mở Console (F12) | ✅ Không có lỗi |
| 5 | Chạy `test-ajax.php` | ✅ Tìm thấy tài xế |

---

## 🎯 NGUYÊN NHÂN THƯỜNG GẶP

### 1. Prefix Không Khớp (90% trường hợp)
```
Database: Xrs_defaultdrivers
WordPress: wp_drivers
→ Không tìm thấy bảng!
```

**Giải pháp:** Sửa wp-config.php

### 2. Status Không Phải Active (8% trường hợp)
```
Tài xế: status = 'pending'
Query: WHERE status = 'active'
→ Không tìm thấy!
```

**Giải pháp:** Kích hoạt tài xế

### 3. Lỗi AJAX/Nonce (2% trường hợp)
```
Nonce hết hạn hoặc AJAX không hoạt động
```

**Giải pháp:** Reload trang, clear cache

---

## 📞 NẾU VẪN LỖI

Chụp màn hình các file sau và gửi:

1. `check-prefix.php` - Kiểm tra prefix
2. `debug-drivers.php` - Danh sách tài xế
3. `test-ajax.php` - Test AJAX
4. Console (F12) - Lỗi JavaScript (nếu có)

---

## 🔗 FILES QUAN TRỌNG

| File | Mục Đích | URL |
|------|----------|-----|
| `check-prefix.php` | Kiểm tra prefix | `/wp-content/plugins/booking-plugin/check-prefix.php` |
| `debug-drivers.php` | Xem tài xế | `/wp-content/plugins/booking-plugin/debug-drivers.php` |
| `test-ajax.php` | Test AJAX | `/wp-content/plugins/booking-plugin/test-ajax.php` |

---

## ⚡ QUICK FIX

**Nếu không muốn đọc hướng dẫn dài:**

1. Chạy: `check-prefix.php`
2. Nếu prefix không khớp → Sửa `wp-config.php`
3. Chạy: `debug-drivers.php`
4. Nếu status = Pending → Click "Kích hoạt"
5. Test autocomplete lại
6. ✅ Xong!

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
