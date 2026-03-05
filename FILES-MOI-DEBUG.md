# Danh Sách Files Mới - Debug Autocomplete

**Ngày tạo:** 04/03/2026  
**Mục đích:** Khắc phục lỗi autocomplete không tìm thấy tài xế

---

## 📁 FILES DEBUG (Chạy trực tiếp)

### 1. check-prefix.php ⭐ QUAN TRỌNG NHẤT
**Đường dẫn:** `/wp-content/plugins/booking-plugin/check-prefix.php`

**Chức năng:**
- Kiểm tra `$wpdb->prefix` của WordPress
- Liệt kê tất cả bảng trong database
- Phát hiện prefix thực tế của bảng
- So sánh và báo lỗi nếu không khớp

**Khi nào dùng:**
- Bước đầu tiên khi debug
- Khi autocomplete không hoạt động
- Khi tất cả queries trả về rỗng

**Kết quả:**
```
✅ Prefix đang khớp! → OK, chuyển sang file khác
❌ Prefix không khớp! → Cần sửa wp-config.php
```

---

### 2. debug-drivers.php
**Đường dẫn:** `/wp-content/plugins/booking-plugin/debug-drivers.php`

**Chức năng:**
- Hiển thị danh sách tất cả tài xế
- Hiển thị status của từng tài xế
- Có nút "Kích hoạt" cho tài xế pending
- Cập nhật status thành 'active'

**Khi nào dùng:**
- Sau khi fix prefix
- Khi cần kiểm tra status tài xế
- Khi cần kích hoạt tài xế

**Kết quả:**
```
✅ Tìm thấy X tài xế
- Nguyễn Trần Bảo Nam | Status: ✅ Active
```

---

### 3. test-ajax.php
**Đường dẫn:** `/wp-content/plugins/booking-plugin/test-ajax.php`

**Chức năng:**
- Gọi AJAX handler `search_drivers` trực tiếp
- Hiển thị SQL query thực tế
- Hiển thị kết quả JSON
- Có nút "Test AJAX" để test qua browser

**Khi nào dùng:**
- Sau khi fix prefix và kích hoạt tài xế
- Khi muốn test AJAX handler
- Khi cần debug chi tiết

**Kết quả:**
```
✅ Tìm thấy 1 tài xế
SQL: SELECT ... FROM Xrs_defaultdrivers WHERE ...
JSON: {"success":true,"data":{"drivers":[...]}}
```

---

## 📄 FILES HƯỚNG DẪN (Đọc để hiểu)

### 1. BAT-DAU-DEBUG.md ⭐ ĐỌC ĐẦU TIÊN
**Nội dung:**
- Tổng quan vấn đề
- Danh sách tất cả files
- Hướng dẫn nhanh 3 bước
- Bảng kiểm tra
- Checklist hoàn thành

**Dành cho:** Người mới bắt đầu debug

---

### 2. FIX-AUTOCOMPLETE-NHANH.md
**Nội dung:**
- Hướng dẫn siêu ngắn gọn
- 3 bước: Check prefix → Kích hoạt → Test
- Bảng tóm tắt

**Dành cho:** Người muốn fix nhanh, không đọc nhiều

---

### 3. HUONG-DAN-DEBUG-AUTOCOMPLETE.md
**Nội dung:**
- Hướng dẫn chi tiết từng bước
- Giải thích mỗi bước làm gì
- Kết quả mong đợi
- Các lỗi thường gặp
- Cách debug AJAX

**Dành cho:** Người muốn hiểu rõ từng bước

---

### 4. KHAC-PHUC-LOI-TABLE-PREFIX.md
**Nội dung:**
- Giải thích vấn đề prefix
- Tại sao xảy ra
- Cách sửa wp-config.php
- Giải pháp dự phòng
- Lưu ý quan trọng

**Dành cho:** Người muốn hiểu về vấn đề prefix

---

### 5. KHAC-PHUC-LOI-AUTOCOMPLETE.md
**Nội dung:**
- Giải thích vấn đề status
- Tại sao cần status = 'active'
- Cách kích hoạt tài xế
- Các status khác nhau
- Checklist

**Dành cho:** Người muốn hiểu về vấn đề status

---

## 🚀 HƯỚNG DẪN SỬ DỤNG

### Lần đầu debug:
```
1. Đọc: BAT-DAU-DEBUG.md
2. Chạy: check-prefix.php
3. Nếu lỗi prefix → Đọc: KHAC-PHUC-LOI-TABLE-PREFIX.md
4. Chạy: debug-drivers.php
5. Nếu lỗi status → Đọc: KHAC-PHUC-LOI-AUTOCOMPLETE.md
6. Chạy: test-ajax.php
7. Test autocomplete
```

### Đã biết vấn đề:
```
1. Đọc: FIX-AUTOCOMPLETE-NHANH.md
2. Làm theo 3 bước
3. Xong!
```

### Muốn hiểu sâu:
```
1. Đọc: KHAC-PHUC-LOI-TABLE-PREFIX.md
2. Đọc: KHAC-PHUC-LOI-AUTOCOMPLETE.md
3. Đọc: HUONG-DAN-DEBUG-AUTOCOMPLETE.md
```

---

## 📊 BẢNG SO SÁNH FILES

| File | Loại | Độ dài | Dành cho |
|------|------|--------|----------|
| **check-prefix.php** | Debug | - | Tất cả |
| **debug-drivers.php** | Debug | - | Tất cả |
| **test-ajax.php** | Debug | - | Nâng cao |
| **BAT-DAU-DEBUG.md** | Hướng dẫn | Trung bình | Người mới |
| **FIX-AUTOCOMPLETE-NHANH.md** | Hướng dẫn | Ngắn | Người bận |
| **HUONG-DAN-DEBUG-AUTOCOMPLETE.md** | Hướng dẫn | Dài | Người muốn hiểu |
| **KHAC-PHUC-LOI-TABLE-PREFIX.md** | Giải thích | Dài | Người muốn hiểu prefix |
| **KHAC-PHUC-LOI-AUTOCOMPLETE.md** | Giải thích | Trung bình | Người muốn hiểu status |

---

## 🎯 WORKFLOW KHUYẾN NGHỊ

### Bước 1: Đọc tổng quan
```
File: BAT-DAU-DEBUG.md
Thời gian: 2 phút
```

### Bước 2: Check prefix
```
File: check-prefix.php
Thời gian: 30 giây
```

**Nếu lỗi prefix:**
```
1. Đọc: KHAC-PHUC-LOI-TABLE-PREFIX.md (5 phút)
2. Sửa: wp-config.php (2 phút)
3. Kiểm tra lại: check-prefix.php (30 giây)
```

### Bước 3: Check tài xế
```
File: debug-drivers.php
Thời gian: 30 giây
```

**Nếu status = pending:**
```
1. Click: "Kích hoạt" (5 giây)
2. Đợi: Reload (2 giây)
```

### Bước 4: Test
```
1. Vào: Đơn Hàng → Gán Tài Xế
2. Gõ: Tên tài xế
3. Kết quả: ✅ Hiển thị!
```

**Tổng thời gian:** 10-15 phút (bao gồm đọc hướng dẫn)

---

## ⚠️ LƯU Ý QUAN TRỌNG

### Trước khi bắt đầu:
- [ ] Backup database
- [ ] Backup wp-config.php
- [ ] Có quyền truy cập cPanel/FTP
- [ ] Có quyền admin WordPress

### Trong quá trình:
- [ ] Chỉ sửa dòng `$table_prefix`
- [ ] Không sửa gì khác trong wp-config.php
- [ ] Reload trang sau mỗi thay đổi
- [ ] Kiểm tra kết quả sau mỗi bước

### Sau khi hoàn thành:
- [ ] Test autocomplete hoạt động
- [ ] Test gán đơn hàng
- [ ] Xóa các file debug (bảo mật)
- [ ] Backup lại (đã fix)

---

## 🔒 BẢO MẬT

**Các file debug chứa thông tin nhạy cảm:**
- Database structure
- Table names
- Prefix
- Driver information

**Sau khi debug xong, XÓA các file:**
```
- check-prefix.php
- debug-drivers.php
- test-ajax.php
```

**Giữ lại các file hướng dẫn:**
```
- BAT-DAU-DEBUG.md
- FIX-AUTOCOMPLETE-NHANH.md
- HUONG-DAN-DEBUG-AUTOCOMPLETE.md
- KHAC-PHUC-LOI-TABLE-PREFIX.md
- KHAC-PHUC-LOI-AUTOCOMPLETE.md
```

---

## 📞 HỖ TRỢ

Nếu vẫn gặp vấn đề:

**Chuẩn bị thông tin:**
1. Chụp màn hình: `check-prefix.php`
2. Chụp màn hình: `debug-drivers.php`
3. Chụp màn hình: `test-ajax.php`
4. Chụp màn hình: Console (F12)
5. Mô tả vấn đề chi tiết

**Gửi thông tin để được hỗ trợ**

---

## ✅ CHECKLIST HOÀN THÀNH

- [ ] Đọc BAT-DAU-DEBUG.md
- [ ] Chạy check-prefix.php
- [ ] Sửa wp-config.php (nếu cần)
- [ ] Chạy debug-drivers.php
- [ ] Kích hoạt tài xế (nếu cần)
- [ ] Chạy test-ajax.php
- [ ] Test autocomplete
- [ ] Gán đơn hàng thành công
- [ ] Xóa files debug
- [ ] Backup lại

---

## 🎉 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành tất cả:

✅ Autocomplete hoạt động  
✅ Tìm được tài xế khi gõ tên/SĐT  
✅ Gán đơn hàng thành công  
✅ Thông báo gửi đến tài xế  
✅ Tài xế nhận được thông báo  
✅ Tài xế có thể nhận/từ chối đơn  

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
