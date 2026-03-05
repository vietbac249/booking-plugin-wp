# 🔧 Debug Autocomplete - Hướng Dẫn Nhanh

## ❌ Vấn Đề
Autocomplete không tìm thấy tài xế khi gõ tên/SĐT trong modal "Gán Tài Xế"

## ✅ Giải Pháp Nhanh (3 Bước)

### 1️⃣ Kiểm tra prefix
```
Truy cập: /wp-content/plugins/booking-plugin/check-prefix.php
```
- Nếu báo "không khớp" → Sửa `wp-config.php`
- Tìm: `$table_prefix = 'wp_';`
- Sửa thành: `$table_prefix = 'Xrs_default';`

### 2️⃣ Kích hoạt tài xế
```
Truy cập: /wp-content/plugins/booking-plugin/debug-drivers.php
```
- Nếu status = "Pending" → Click "Kích hoạt"

### 3️⃣ Test
```
Đặt Xe → Đơn Hàng → Gán Tài Xế → Gõ tên
```
- Kết quả: ✅ Hiển thị tài xế!

---

## 📚 Hướng Dẫn Chi Tiết

| File | Mô tả |
|------|-------|
| **BAT-DAU-DEBUG.md** | Hướng dẫn đầy đủ, đọc đầu tiên |
| **FIX-AUTOCOMPLETE-NHANH.md** | Hướng dẫn siêu ngắn |
| **FILES-MOI-DEBUG.md** | Danh sách tất cả files mới |

---

## 🔗 Files Debug

- `check-prefix.php` - Kiểm tra table prefix
- `debug-drivers.php` - Xem & kích hoạt tài xế
- `test-ajax.php` - Test AJAX handler

---

## ⚡ Quick Start

```bash
# Bước 1: Check prefix
/wp-content/plugins/booking-plugin/check-prefix.php

# Bước 2: Debug drivers
/wp-content/plugins/booking-plugin/debug-drivers.php

# Bước 3: Test AJAX
/wp-content/plugins/booking-plugin/test-ajax.php
```

---

## 🎯 Nguyên Nhân Chính

1. **Prefix không khớp** (90%)
   - Database: `Xrs_defaultdrivers`
   - WordPress: `wp_drivers`
   - → Không tìm thấy bảng!

2. **Status không active** (10%)
   - Tài xế: `status = 'pending'`
   - Query: `WHERE status = 'active'`
   - → Không tìm thấy!

---

## 📞 Cần Trợ Giúp?

Chụp màn hình 3 files:
1. `check-prefix.php`
2. `debug-drivers.php`
3. `test-ajax.php`

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
