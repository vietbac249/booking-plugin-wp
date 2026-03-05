# Bắt Đầu Debug - Autocomplete Không Hoạt Động

**Vấn đề:** Gõ tên tài xế nhưng không hiển thị gợi ý

---

## 🚨 QUAN TRỌNG: ĐỌC ĐẦU TIÊN

Vấn đề có thể do 2 nguyên nhân:

1. **Table prefix không khớp** (90% trường hợp)
   - Database dùng prefix: `Xrs_default`
   - WordPress dùng prefix: `wp_`
   - → Code query sai bảng!

2. **Tài xế chưa được kích hoạt** (10% trường hợp)
   - Tài xế có status = `pending`
   - Autocomplete chỉ tìm status = `active`

---

## 📋 CÁC FILE ĐÃ TẠO

### 1. File Debug (Chạy ngay!)

| File | Mục đích | URL |
|------|----------|-----|
| **check-prefix.php** | Kiểm tra prefix có khớp không | `/wp-content/plugins/booking-plugin/check-prefix.php` |
| **debug-drivers.php** | Xem danh sách tài xế & kích hoạt | `/wp-content/plugins/booking-plugin/debug-drivers.php` |
| **test-ajax.php** | Test AJAX handler trực tiếp | `/wp-content/plugins/booking-plugin/test-ajax.php` |

### 2. File Hướng Dẫn (Đọc nếu cần)

| File | Nội dung |
|------|----------|
| **FIX-AUTOCOMPLETE-NHANH.md** | Hướng dẫn siêu ngắn (3 bước) |
| **HUONG-DAN-DEBUG-AUTOCOMPLETE.md** | Hướng dẫn chi tiết từng bước |
| **KHAC-PHUC-LOI-TABLE-PREFIX.md** | Giải thích về vấn đề prefix |
| **KHAC-PHUC-LOI-AUTOCOMPLETE.md** | Giải thích về vấn đề status |

---

## ⚡ HƯỚNG DẪN NHANH (5 PHÚT)

### Bước 1: Kiểm tra prefix
```
1. Mở trình duyệt
2. Truy cập: /wp-content/plugins/booking-plugin/check-prefix.php
3. Xem kết quả
```

**Nếu thấy:**
```
✅ Prefix đang khớp!
```
→ Chuyển sang Bước 2

**Nếu thấy:**
```
❌ WordPress đang dùng prefix: wp_
❌ Nhưng bảng thực tế dùng prefix: Xrs_default
```
→ **LÀM NGAY:**

1. Vào cPanel → File Manager
2. Mở: `public_html/wp-config.php`
3. Tìm dòng: `$table_prefix = 'wp_';`
4. Sửa thành: `$table_prefix = 'Xrs_default';`
5. Lưu file
6. Reload trang admin WordPress
7. Chạy lại `check-prefix.php`

### Bước 2: Kích hoạt tài xế
```
1. Truy cập: /wp-content/plugins/booking-plugin/debug-drivers.php
2. Xem cột "Status"
3. Nếu = "⚠️ Pending" → Click "Kích hoạt"
4. Đợi trang reload
5. Status chuyển thành "✅ Active"
```

### Bước 3: Test autocomplete
```
1. Vào: Đặt Xe → Đơn Hàng
2. Click: "Gán Tài Xế"
3. Gõ: Tên tài xế (ví dụ: "Nguyễn")
4. Kết quả: ✅ Hiển thị tài xế!
```

---

## 🔍 KIỂM TRA CHI TIẾT

Nếu vẫn lỗi sau 3 bước trên, chạy file test:

```
URL: /wp-content/plugins/booking-plugin/test-ajax.php
```

File này sẽ:
- Gọi AJAX handler trực tiếp
- Hiển thị SQL query
- Hiển thị kết quả JSON
- Có nút "Test AJAX" để test

**Kết quả mong đợi:**
```
✅ Tìm thấy 1 tài xế
Nguyễn Trần Bảo Nam - 0963134651 (19 B1 27726)
```

---

## 📊 BẢNG KIỂM TRA

| # | Kiểm tra | Kết quả | Hành động |
|---|----------|---------|-----------|
| 1 | `check-prefix.php` | ✅ Khớp | Sang bước 2 |
| 1 | `check-prefix.php` | ❌ Không khớp | Sửa wp-config.php |
| 2 | `debug-drivers.php` | ✅ Active | Sang bước 3 |
| 2 | `debug-drivers.php` | ⚠️ Pending | Kích hoạt tài xế |
| 3 | Autocomplete | ✅ Hiển thị | Hoàn thành! |
| 3 | Autocomplete | ❌ Không hiển thị | Chạy test-ajax.php |

---

## 🎯 NGUYÊN NHÂN & GIẢI PHÁP

### Nguyên nhân 1: Prefix không khớp (90%)

**Triệu chứng:**
- `check-prefix.php` báo không khớp
- `debug-drivers.php` báo "Không có tài xế"
- phpMyAdmin thấy có dữ liệu

**Giải pháp:**
- Sửa `$table_prefix` trong `wp-config.php`

### Nguyên nhân 2: Status không active (10%)

**Triệu chứng:**
- `check-prefix.php` báo khớp
- `debug-drivers.php` hiển thị tài xế nhưng status = Pending
- Autocomplete không tìm thấy

**Giải pháp:**
- Click "Kích hoạt" trong `debug-drivers.php`

---

## 📞 HỖ TRỢ

Nếu làm theo hướng dẫn mà vẫn lỗi:

**Chụp màn hình 3 file:**
1. `check-prefix.php` - Kết quả kiểm tra prefix
2. `debug-drivers.php` - Danh sách tài xế
3. `test-ajax.php` - Kết quả test AJAX

**Mở Console (F12):**
- Tab Console
- Thử autocomplete
- Chụp màn hình lỗi (nếu có)

**Gửi thông tin:**
- 3 ảnh màn hình trên
- Ảnh Console
- Mô tả vấn đề

---

## 🔗 LINKS NHANH

### Debug Files:
- Check Prefix: `/wp-content/plugins/booking-plugin/check-prefix.php`
- Debug Drivers: `/wp-content/plugins/booking-plugin/debug-drivers.php`
- Test AJAX: `/wp-content/plugins/booking-plugin/test-ajax.php`

### Admin Pages:
- Đơn Hàng: `Đặt Xe → Đơn Hàng`
- Tài Xế: `Đặt Xe → Tài Xế`
- Cài Đặt: `Đặt Xe → Cài Đặt`

---

## ⚠️ LƯU Ý

1. **Backup trước khi sửa wp-config.php**
2. **Chỉ sửa dòng $table_prefix**
3. **Không sửa gì khác trong wp-config.php**
4. **Nếu sai, restore backup ngay**

---

## ✅ CHECKLIST HOÀN THÀNH

- [ ] Chạy `check-prefix.php`
- [ ] Sửa wp-config.php (nếu cần)
- [ ] Chạy lại `check-prefix.php` để xác nhận
- [ ] Chạy `debug-drivers.php`
- [ ] Kích hoạt tài xế (nếu cần)
- [ ] Test autocomplete
- [ ] Gán đơn hàng thành công
- [ ] Xóa các file debug (bảo mật)

---

## 🎉 THÀNH CÔNG!

Sau khi hoàn thành:
- ✅ Autocomplete hoạt động
- ✅ Tìm được tài xế
- ✅ Gán đơn thành công
- ✅ Thông báo gửi đến tài xế

**Xóa các file debug để bảo mật:**
- `check-prefix.php`
- `debug-drivers.php`
- `test-ajax.php`

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
