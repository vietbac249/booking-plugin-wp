# Fix Autocomplete - Siêu Nhanh ⚡

## Vấn đề: Không tìm thấy tài xế khi gõ tên

---

## ✅ GIẢI PHÁP 3 BƯỚC

### BƯỚC 1: Kiểm tra prefix
```
Truy cập: /wp-content/plugins/booking-plugin/check-prefix.php
```

**Nếu thấy lỗi prefix:**
1. Mở file: `wp-config.php`
2. Tìm: `$table_prefix = 'wp_';`
3. Sửa thành: `$table_prefix = 'Xrs_default';` (theo kết quả hiển thị)
4. Lưu file
5. Reload trang admin

### BƯỚC 2: Kích hoạt tài xế
```
Truy cập: /wp-content/plugins/booking-plugin/debug-drivers.php
```

**Nếu thấy status = "Pending":**
- Click nút "Kích hoạt"
- Đợi reload
- Status chuyển thành "Active"

### BƯỚC 3: Test
```
Vào: Đặt Xe → Đơn Hàng → Gán Tài Xế
Gõ: Tên tài xế hoặc SĐT
```

**Kết quả:** ✅ Hiển thị tài xế!

---

## 🎯 TÓM TẮT

| Vấn đề | Nguyên nhân | Giải pháp |
|--------|-------------|-----------|
| Không tìm thấy tài xế | Prefix sai | Sửa wp-config.php |
| Không tìm thấy tài xế | Status = pending | Kích hoạt tài xế |
| Vẫn lỗi | Cache | Reload (Ctrl+F5) |

---

## 📞 Cần trợ giúp?

Chụp màn hình 2 file này:
1. `check-prefix.php`
2. `debug-drivers.php`

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
