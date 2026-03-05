# Khắc Phục Lỗi Autocomplete Không Tìm Thấy Tài Xế

## ❌ Vấn Đề

**Triệu chứng:**
- Có tài xế trong hệ thống (hiển thị ở trang "Tài Xế")
- Nhưng khi gõ tên/SĐT trong modal "Gán Tài Xế"
- Hiển thị: "Không tìm thấy tài xế"

## 🔍 Nguyên Nhân

**Nguyên nhân chính:** Tài xế có `status != 'active'`

AJAX search chỉ tìm tài xế có:
```sql
WHERE status = 'active'
```

Nếu tài xế đang ở trạng thái:
- `pending` (Chờ xác minh)
- `inactive` (Không hoạt động)
- `suspended` (Tạm ngưng)

→ Sẽ KHÔNG hiển thị trong autocomplete!

## ✅ Giải Pháp

### Cách 1: Sử Dụng Debug Script (Khuyến Nghị)

**Bước 1: Truy cập debug script**
```
URL: /wp-content/plugins/booking-plugin/debug-drivers.php
```

**Bước 2: Xem danh sách tài xế**
- Script sẽ hiển thị tất cả tài xế
- Cột "Status" cho biết trạng thái hiện tại
- ✅ Active = Có thể gán đơn
- ⚠️ Pending/Inactive = KHÔNG thể gán đơn

**Bước 3: Kích hoạt tài xế**
- Click nút "Kích hoạt" bên cạnh tài xế cần kích hoạt
- Status sẽ chuyển từ "Pending" → "Active"

**Bước 4: Thử lại**
- Quay lại trang "Đơn Hàng"
- Click "Gán Tài Xế"
- Gõ tên tài xế
- ✅ Tài xế sẽ hiển thị!

### Cách 2: Qua Database (Nâng Cao)

**Bước 1: Kiểm tra status**
```sql
SELECT id, full_name, phone, status 
FROM wp_drivers 
WHERE full_name LIKE '%Nguyễn Trần Bảo Nam%';
```

**Bước 2: Cập nhật status**
```sql
UPDATE wp_drivers 
SET status = 'active' 
WHERE id = 1;
```

**Bước 3: Kiểm tra lại**
```sql
SELECT id, full_name, status FROM wp_drivers WHERE id = 1;
-- Kết quả: status = 'active'
```

### Cách 3: Qua Trang Quản Lý Tài Xế

**Bước 1: Vào trang Tài Xế**
```
Đặt Xe → Tài Xế
```

**Bước 2: Tìm tài xế**
- Tìm "Nguyễn Trần Bảo Nam"
- Xem cột "Trạng Thái"

**Bước 3: Cập nhật status**
- Click "Xem" hoặc "Sửa"
- Chọn Status = "Active"
- Lưu lại

## 🧪 Test Sau Khi Sửa

### Test 1: Kiểm tra status
1. Truy cập: `/wp-content/plugins/booking-plugin/debug-drivers.php`
2. Xem cột "Status"
3. Kết quả: ✅ Active (màu xanh)

### Test 2: Autocomplete
1. Vào: Đặt Xe → Đơn Hàng
2. Click: "Gán Tài Xế"
3. Gõ: "Nguyễn" hoặc "0963134651"
4. Kết quả: ✅ Hiển thị tài xế trong dropdown

### Test 3: Gán đơn hàng
1. Chọn tài xế từ dropdown
2. Click: "Gán Cho Tài Xế"
3. Kết quả: ✅ Gán thành công

## 🐛 Các Lỗi Khác

### Lỗi: Console hiển thị lỗi AJAX

**Kiểm tra Console (F12):**
```javascript
// Lỗi 1: 403 Forbidden
// Nguyên nhân: Nonce không đúng
// Giải pháp: Reload trang và thử lại

// Lỗi 2: 500 Internal Server Error
// Nguyên nhân: Lỗi PHP
// Giải pháp: Kiểm tra error log

// Lỗi 3: ajaxurl is not defined
// Nguyên nhân: WordPress không load đúng
// Giải pháp: Clear cache
```

### Lỗi: Không có tài xế nào

**Kiểm tra database:**
```sql
-- Kiểm tra bảng có tồn tại
SHOW TABLES LIKE '%drivers%';

-- Kiểm tra có dữ liệu
SELECT COUNT(*) FROM wp_drivers;

-- Nếu = 0, cần thêm tài xế
```

### Lỗi: Tìm được nhưng không gán được

**Kiểm tra:**
1. Console có lỗi không?
2. Tài xế có telegram_chat_id hoặc zalo_user_id chưa?
3. Đơn hàng có status = 'pending' không?

## 📊 Status Tài Xế

| Status | Mô Tả | Có Thể Gán Đơn? |
|--------|-------|-----------------|
| pending | Chờ xác minh | ❌ Không |
| active | Đang hoạt động | ✅ Có |
| inactive | Không hoạt động | ❌ Không |
| suspended | Tạm ngưng | ❌ Không |
| banned | Bị cấm | ❌ Không |

## 📝 Checklist

- [ ] Truy cập debug script
- [ ] Kiểm tra status tài xế
- [ ] Kích hoạt tài xế (nếu cần)
- [ ] Reload trang "Đơn Hàng"
- [ ] Thử autocomplete lại
- [ ] Tài xế hiển thị trong dropdown
- [ ] Gán đơn hàng thành công

## 🎯 Kết Luận

Vấn đề chính là **status tài xế không phải 'active'**. 

Sau khi kích hoạt tài xế, autocomplete sẽ hoạt động bình thường.

**File mới:** `booking-plugin-week2-complete.zip` (có debug script)  
**Debug URL:** `/wp-content/plugins/booking-plugin/debug-drivers.php`

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
