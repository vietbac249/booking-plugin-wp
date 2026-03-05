# 🔍 Debug: Không Nhận Được Đơn Hàng

## ❌ Vấn Đề
Sau khi nhập số điện thoại và click "Nhận Đơn", không có phản hồi gì. Trang đứng im.

## 🔧 Các Bước Debug

### Bước 1: Mở Console (F12)

1. Mở trang nhận đơn hàng
2. Nhấn **F12** (hoặc Ctrl+Shift+I)
3. Chọn tab **Console**
4. Nhập số điện thoại và click "Nhận Đơn"
5. Xem có lỗi gì không

**Các log mong đợi:**
```
🔵 Script loaded
📋 Booking ID: 4
🔑 Token: 04vFy6R1Pe2nPEewAzwSTvRWhHxyeqaI
📦 Assignment Type: group
🟢 Accept button clicked
📞 Driver phone: 0904885057
⏳ Sending AJAX request...
📤 AJAX data: {action: "accept_booking", booking_id: 4, token: "...", driver_phone: "0904885057"}
📡 Request sent
✅ Response received: {success: true, data: {...}}
```

**Nếu thấy lỗi:**
- `jQuery is not defined` → jQuery chưa load
- `Uncaught ReferenceError` → Lỗi JavaScript
- `404 Not Found` → URL AJAX sai
- `500 Internal Server Error` → Lỗi PHP

### Bước 2: Kiểm Tra Network

1. Trong DevTools, chọn tab **Network**
2. Click "Nhận Đơn"
3. Tìm request đến `admin-ajax.php`
4. Click vào request đó
5. Xem:
   - **Headers** → Status code (200 = OK)
   - **Payload** → Dữ liệu gửi đi
   - **Response** → Dữ liệu trả về

**Response mong đợi:**
```json
{
  "success": true,
  "data": {
    "message": "Đã nhận đơn hàng thành công",
    "driver_name": "Nguyễn Văn A",
    "booking_id": 4
  }
}
```

### Bước 3: Test AJAX Trực Tiếp

Truy cập URL này (thay YOUR_TOKEN bằng token thật):
```
http://your-site.com/wp-content/plugins/booking-plugin/test-accept-ajax.php?booking_id=4&token=YOUR_TOKEN&phone=0904885057
```

Script sẽ:
- ✅ Kiểm tra booking có tồn tại không
- ✅ Kiểm tra token có khớp không
- ✅ Kiểm tra token có hết hạn không
- ✅ Kiểm tra tài xế có tồn tại không
- ✅ Kiểm tra tài xế đã kích hoạt chưa
- ✅ Test AJAX call trực tiếp

### Bước 4: Kiểm Tra Database

**Kiểm tra booking:**
```sql
SELECT id, booking_code, status, assignment_type, driver_id, group_id, accept_token, token_expires
FROM wp_bookings
WHERE id = 4;
```

**Kiểm tra tài xế:**
```sql
SELECT id, full_name, phone, status
FROM wp_drivers
WHERE phone = '0904885057';
```

**Kiểm tra assignment log:**
```sql
SELECT * FROM wp_booking_assignment_logs
WHERE booking_id = 4
ORDER BY created_at DESC;
```

## 🐛 Các Lỗi Thường Gặp

### Lỗi 1: jQuery Không Load
**Triệu chứng:** Console báo `jQuery is not defined`

**Giải pháp:**
- Đã thêm jQuery CDN vào template
- Kiểm tra theme có load jQuery không
- Thử thêm `wp_enqueue_script('jquery')` vào plugin

### Lỗi 2: AJAX URL Sai
**Triệu chứng:** Network tab báo 404

**Giải pháp:**
- Kiểm tra `admin_url('admin-ajax.php')` có đúng không
- Thử hardcode: `/wp-admin/admin-ajax.php`

### Lỗi 3: Token Không Khớp
**Triệu chứng:** Response báo "Link không hợp lệ"

**Giải pháp:**
- Kiểm tra token trong URL
- Kiểm tra token trong database
- Token có thể bị thay đổi sau khi gán lại đơn

### Lỗi 4: Token Hết Hạn
**Triệu chứng:** Response báo "Link đã hết hạn"

**Giải pháp:**
- Token hết hạn sau 1 giờ
- Admin cần gán lại đơn để tạo token mới
- Hoặc tăng thời gian hết hạn trong code

### Lỗi 5: Tài Xế Không Tồn Tại
**Triệu chứng:** Response báo "Không tìm thấy tài xế"

**Giải pháp:**
- Kiểm tra số điện thoại có đúng không
- Kiểm tra tài xế có trong database không
- Số điện thoại phải trùng khớp 100%

### Lỗi 6: Tài Xế Chưa Kích Hoạt
**Triệu chứng:** Response báo "Tài xế chưa được kích hoạt"

**Giải pháp:**
- Vào Quản Lý Tài Xế
- Tìm tài xế theo SĐT
- Đổi status thành "active"

### Lỗi 7: CORS Error
**Triệu chứng:** Console báo CORS policy

**Giải pháp:**
- Thường không xảy ra vì AJAX cùng domain
- Kiểm tra URL có đúng không

### Lỗi 8: PHP Error
**Triệu chứng:** Response trả về HTML thay vì JSON

**Giải pháp:**
- Enable WordPress debug
- Kiểm tra `wp-content/debug.log`
- Có thể là lỗi syntax PHP

## 🔧 Quick Fixes

### Fix 1: Thêm jQuery Explicitly
Thêm vào đầu template:
```php
<?php wp_enqueue_script('jquery'); ?>
```

### Fix 2: Hardcode AJAX URL
Thay:
```javascript
url: '<?php echo admin_url('admin-ajax.php'); ?>'
```

Bằng:
```javascript
url: '/wp-admin/admin-ajax.php'
```

### Fix 3: Disable Cache
Thêm vào AJAX data:
```javascript
_: new Date().getTime()
```

### Fix 4: Test Với Vanilla JavaScript
Thay jQuery bằng vanilla JS:
```javascript
document.getElementById('accept-btn').addEventListener('click', function() {
    // ...
});
```

## 📊 Checklist Debug

- [ ] Mở Console (F12) và xem log
- [ ] Kiểm tra Network tab
- [ ] Chạy test-accept-ajax.php
- [ ] Kiểm tra booking trong database
- [ ] Kiểm tra tài xế trong database
- [ ] Kiểm tra token có khớp không
- [ ] Kiểm tra token có hết hạn không
- [ ] Kiểm tra tài xế có status = 'active'
- [ ] Kiểm tra WordPress debug log
- [ ] Test với browser khác

## 🎯 Kết Quả Mong Đợi

Sau khi debug và fix:
1. Click "Nhận Đơn"
2. Console log hiển thị đầy đủ
3. Network tab thấy request thành công (200)
4. Response trả về JSON với success: true
5. Trang hiển thị "✅ Đã nhận đơn hàng thành công"
6. Trang reload sau 2 giây
7. Đơn hàng status = 'accepted' trong database

## 📞 Hỗ Trợ

Nếu vẫn không được, gửi cho tôi:
1. Screenshot Console (F12)
2. Screenshot Network tab
3. Kết quả từ test-accept-ajax.php
4. Query kết quả từ database

Tôi sẽ giúp bạn debug tiếp! 🚀
