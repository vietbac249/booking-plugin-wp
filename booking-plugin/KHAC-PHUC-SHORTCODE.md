# Khắc Phục Lỗi Shortcode & Form

## ❌ CÁC VẤN ĐỀ

### 1. Form đặt xe yêu cầu đăng nhập
### 2. Shortcode `[dang_ky_tai_xe]` hiển thị form đặt xe
### 3. Admin không thay đổi được trạng thái đơn hàng

---

## ✅ GIẢI PHÁP

### Vấn Đề 1: Form Đặt Xe Yêu Cầu Đăng Nhập

**Nguyên nhân:** WordPress cache hoặc plugin conflict

**Giải pháp:**

1. **Clear Cache:**
   - Nếu dùng cache plugin (W3 Total Cache, WP Super Cache): Clear cache
   - Nếu dùng CDN (Cloudflare): Purge cache

2. **Kiểm tra Plugin Conflict:**
   - Tắt tất cả plugin khác
   - Chỉ bật plugin "Đặt Xe Nội Bài"
   - Test lại

3. **Kiểm tra Theme:**
   - Chuyển sang theme mặc định (Twenty Twenty-Three)
   - Test lại
   - Nếu OK → Lỗi do theme

4. **Hard Refresh:**
   - Bấm Ctrl + F5 (Windows)
   - Bấm Cmd + Shift + R (Mac)

---

### Vấn Đề 2: Shortcode Hiển thị Sai Form

**Nguyên nhân:** Có 2 thư mục plugin trùng tên

**Giải pháp:**

1. **Xóa thư mục duplicate:**
   ```
   wp-content/plugins/booking-plugin-1/  ← XÓA thư mục này
   ```

2. **Chỉ giữ lại:**
   ```
   wp-content/plugins/booking-plugin/  ← Giữ thư mục này
   ```

3. **Deactivate & Activate lại plugin**

4. **Test shortcode:**
   - `[dat_xe]` → Form đặt xe ✅
   - `[dang_ky_tai_xe]` → Form đăng ký tài xế ✅

---

### Vấn Đề 3: Không Thay Đổi Được Trạng Thái

**Đã sửa!** File `admin-orders.php` mới có:

✅ Dropdown thay đổi trạng thái  
✅ Tự động submit khi chọn  
✅ Color-coded theo trạng thái  
✅ Thống kê nhanh  
✅ Nút xóa đơn hàng  

**Các trạng thái:**
- 🟡 Chờ xử lý (pending)
- 🔵 Đã xác nhận (confirmed)
- 🟣 Đã phân xe (assigned)
- ⚫ Đang thực hiện (in_progress)
- 🟢 Hoàn thành (completed)
- 🔴 Đã hủy (cancelled)

---

## 📝 HƯỚNG DẪN SỬ DỤNG

### Thay Đổi Trạng Thái Đơn Hàng

1. Vào **Đặt Xe > Đơn Hàng**
2. Tìm đơn hàng cần cập nhật
3. Click vào dropdown "Trạng Thái"
4. Chọn trạng thái mới
5. ✅ Tự động lưu!

### Sử Dụng Shortcode

**Form Đặt Xe:**
```
[dat_xe]
```

**Form Đăng Ký Tài Xế:**
```
[dang_ky_tai_xe]
```

**Lưu ý:** 
- Mỗi trang chỉ nên có 1 shortcode
- Không đặt 2 shortcode trên cùng 1 trang

---

## 🔍 KIỂM TRA SAU KHI SỬA

### Test 1: Form Đặt Xe
1. Vào trang có shortcode `[dat_xe]`
2. KHÔNG cần đăng nhập
3. Điền thông tin và bấm "Kiểm Tra Giá"
4. ✅ Phải thấy giá hiển thị

### Test 2: Form Đăng Ký Tài Xế
1. Vào trang có shortcode `[dang_ky_tai_xe]`
2. KHÔNG thấy form đặt xe
3. ✅ Phải thấy form đăng ký tài xế với:
   - Họ tên, SĐT, Email
   - Địa chỉ
   - Loại xe, Biển số
   - Upload CCCD
   - Chụp ảnh eKYC

### Test 3: Thay Đổi Trạng Thái
1. Vào **Đặt Xe > Đơn Hàng**
2. Chọn 1 đơn hàng
3. Click dropdown trạng thái
4. Chọn "Hoàn thành"
5. ✅ Trang tự động reload
6. ✅ Trạng thái đã đổi sang "Hoàn thành"

---

## 🐛 NẾU VẪN LỖI

### Lỗi: "Không có quyền truy cập"

**Giải pháp:**
1. Đăng nhập với tài khoản Admin
2. Vào **Users > Your Profile**
3. Kiểm tra Role = Administrator

### Lỗi: Shortcode hiển thị text thô

**Ví dụ:** `[dat_xe]` hiển thị nguyên văn trên trang

**Giải pháp:**
1. Kiểm tra plugin đã Activate chưa
2. Deactivate rồi Activate lại
3. Clear cache

### Lỗi: Form không submit được

**Giải pháp:**
1. Mở Console (F12)
2. Xem có lỗi JavaScript không
3. Kiểm tra AJAX URL có đúng không
4. Tắt plugin conflict

---

## 📞 DEBUG MODE

Nếu vẫn lỗi, bật debug mode:

**Thêm vào `wp-config.php`:**
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

**Xem log tại:**
```
wp-content/debug.log
```

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
