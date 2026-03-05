# Hướng Dẫn Test Week 2 - Tính Năng Gán Đơn Hàng

## 📦 Cài Đặt

### Bước 1: Upload Plugin
```
1. Tải file: booking-plugin-week2-complete.zip
2. Upload lên: /wp-content/plugins/
3. Giải nén (hoặc thay thế folder booking-plugin cũ)
4. Không cần activate lại (đã active rồi)
```

### Bước 2: Kiểm Tra Database
```
Đảm bảo đã chạy: update-database-assignment.php (Week 1)

Kiểm tra các bảng:
- wp_booking_bookings (có cột: assigned_at, assigned_by, accepted_at, etc.)
- wp_booking_drivers (có cột: telegram_chat_id, zalo_user_id)
- wp_booking_notification_groups (bảng mới)
- wp_booking_assignment_logs (bảng mới)
```

### Bước 3: Chuẩn Bị Telegram Bot (Optional)
```
1. Mở Telegram, tìm: @BotFather
2. Gửi: /newbot
3. Đặt tên bot: "Đặt Xe Bot" (hoặc tên khác)
4. Lấy Bot Token (dạng: 123456:ABC-DEF...)
5. Lưu token này
```

### Bước 4: Lấy Telegram Chat ID (Optional)
```
1. Mở Telegram, tìm: @userinfobot
2. Gửi: /start
3. Bot trả về Chat ID (dạng: 123456789)
4. Lưu Chat ID này
```

---

## 🧪 Test Cases

### Test 1: Kiểm Tra UI

**Mục tiêu:** Xem UI có hiển thị đúng không

**Các bước:**
1. Đăng nhập WordPress Admin
2. Vào: Đặt Xe → Đơn Hàng
3. Kiểm tra:
   - ✅ Có cột "Gán Đơn" mới
   - ✅ Stats có "Đã gán" và "Đã nhận"
   - ✅ Đơn hàng "Chờ xử lý" có 2 nút:
     * 👤 Gán Tài Xế
     * 👥 Gán Group

**Kết quả mong đợi:**
- UI hiển thị đẹp, không bị lỗi
- Nút chỉ hiện với đơn hàng pending

---

### Test 2: Modal Gán Tài Xế

**Mục tiêu:** Test modal và autocomplete

**Các bước:**
1. Click nút "👤 Gán Tài Xế" ở một đơn hàng
2. Modal hiện ra
3. Gõ tên tài xế vào ô tìm kiếm
4. Chờ 0.3 giây (debounce)
5. Dropdown hiện danh sách tài xế
6. Click chọn 1 tài xế
7. Xem thông tin tài xế hiển thị
8. Click "Gán Cho Tài Xế"

**Kết quả mong đợi:**
- ✅ Modal mở đẹp
- ✅ Autocomplete hoạt động
- ✅ Hiển thị: Tên, SĐT, Xe, Rating, Kênh thông báo
- ✅ Nút "Gán" chỉ enable khi đã chọn tài xế
- ✅ Sau khi gán: Thông báo thành công
- ✅ Trang reload tự động
- ✅ Status đổi thành "Đã gán"

**Kiểm tra database:**
```sql
SELECT * FROM wp_booking_bookings WHERE id = [booking_id];
-- Kiểm tra:
-- - driver_id = [driver_id đã chọn]
-- - status = 'assigned'
-- - assigned_at = [thời gian hiện tại]
-- - assigned_by = [admin user id]
-- - assignment_type = 'direct'

SELECT * FROM wp_booking_assignment_logs WHERE booking_id = [booking_id];
-- Kiểm tra có log mới
```

---

### Test 3: Modal Gán Group

**Mục tiêu:** Test gán vào group

**Điều kiện:** Cần có ít nhất 1 group trong database

**Tạo group test:**
```sql
INSERT INTO wp_booking_notification_groups 
(name, type, group_id, bot_token, chat_id, is_active) 
VALUES 
('Group Test Telegram', 'telegram', '-123456789', 'YOUR_BOT_TOKEN', '-123456789', 1);
```

**Các bước:**
1. Click nút "👥 Gán Group" ở một đơn hàng
2. Modal hiện ra
3. Chọn group từ dropdown
4. Xem preview tin nhắn
5. Click "Gửi Vào Group"

**Kết quả mong đợi:**
- ✅ Modal mở đẹp
- ✅ Dropdown hiển thị danh sách groups
- ✅ Preview tin nhắn hiển thị đúng thông tin đơn hàng
- ✅ Nút "Gửi" chỉ enable khi đã chọn group
- ✅ Sau khi gửi: Thông báo thành công
- ✅ Trang reload tự động
- ✅ Status đổi thành "Đã gán"

**Kiểm tra database:**
```sql
SELECT * FROM wp_booking_bookings WHERE id = [booking_id];
-- Kiểm tra:
-- - status = 'assigned'
-- - assigned_at = [thời gian hiện tại]
-- - assignment_type = 'group'
-- - group_id = [group_id đã chọn]

SELECT * FROM wp_booking_assignment_logs WHERE booking_id = [booking_id];
-- Kiểm tra có log mới với group_id
```

---

### Test 4: Thông Báo Telegram (Optional)

**Điều kiện:** 
- Đã có Telegram Bot Token
- Tài xế đã có telegram_chat_id

**Chuẩn bị:**
```sql
-- Cập nhật telegram_chat_id cho tài xế
UPDATE wp_booking_drivers 
SET telegram_chat_id = 'YOUR_CHAT_ID' 
WHERE id = [driver_id];
```

**Các bước:**
1. Gán đơn hàng cho tài xế (có telegram_chat_id)
2. Kiểm tra Telegram của tài xế

**Kết quả mong đợi:**
- ✅ Tài xế nhận được tin nhắn Telegram
- ✅ Tin nhắn có:
  * Tiêu đề: 🚗 ĐƠN HÀNG MỚI
  * Từ: [địa điểm]
  * Đến: [địa điểm]
  * Giá: [giá tiền]
  * Thời gian: [ngày giờ]
  * Khách hàng: [tên + SĐT]
  * Nút: ✅ Nhận Đơn (link)

**Kiểm tra log:**
```
/wp-content/plugins/booking-plugin/logs/notifications.log
```

---

### Test 5: Error Handling

**Test 5.1: Gán đơn đã được gán**
1. Gán đơn hàng cho tài xế A
2. Thử gán lại đơn hàng đó cho tài xế B
3. Kết quả: Lỗi "Đơn hàng đã được xử lý"

**Test 5.2: Tài xế không active**
1. Vô hiệu hóa tài xế (status = 'inactive')
2. Thử tìm tài xế đó
3. Kết quả: Không hiện trong autocomplete

**Test 5.3: Group không active**
1. Vô hiệu hóa group (is_active = 0)
2. Thử chọn group đó
3. Kết quả: Không hiện trong dropdown

**Test 5.4: Network error**
1. Tắt internet
2. Thử gán đơn hàng
3. Kết quả: Hiển thị lỗi "Có lỗi xảy ra"

---

### Test 6: Badge Hiển Thị

**Các bước:**
1. Tạo đơn hàng mới (status = pending)
2. Kiểm tra: Có 2 nút gán
3. Gán cho tài xế
4. Kiểm tra: Badge "⏳ Chờ nhận" + tên tài xế
5. Cập nhật status = 'accepted' (manual)
6. Reload trang
7. Kiểm tra: Badge "✅ Đã nhận" + tên tài xế

**Kết quả mong đợi:**
- ✅ Badge hiển thị đúng theo status
- ✅ Màu sắc phù hợp
- ✅ Tên tài xế hiển thị

---

### Test 7: Responsive Design

**Các bước:**
1. Mở trang trên mobile (hoặc resize browser)
2. Click nút gán
3. Kiểm tra modal

**Kết quả mong đợi:**
- ✅ Modal hiển thị đẹp trên mobile
- ✅ Không bị tràn màn hình
- ✅ Có thể scroll
- ✅ Nút đóng dễ click

---

## 🐛 Troubleshooting

### Lỗi: "Không tìm thấy tài xế"
**Nguyên nhân:** Không có tài xế active
**Giải pháp:** 
```sql
UPDATE wp_booking_drivers SET status = 'active' WHERE id = [driver_id];
```

### Lỗi: "Không tìm thấy đơn hàng"
**Nguyên nhân:** Sai table name
**Giải pháp:** Kiểm tra table có tên `wp_booking_bookings` (không phải `wp_bookings`)

### Lỗi: "Không có quyền truy cập"
**Nguyên nhân:** Không phải admin
**Giải pháp:** Đăng nhập với tài khoản admin

### Autocomplete không hoạt động
**Nguyên nhân:** JavaScript error
**Giải pháp:** 
1. Mở Console (F12)
2. Kiểm tra lỗi
3. Đảm bảo jQuery đã load

### Modal không mở
**Nguyên nhân:** CSS conflict
**Giải pháp:**
1. Kiểm tra z-index
2. Kiểm tra display: none
3. Clear cache

### Thông báo không gửi
**Nguyên nhân:** 
- Chưa có Bot Token
- Sai Chat ID
- Bot bị block

**Giải pháp:**
1. Kiểm tra Bot Token trong Settings
2. Kiểm tra Chat ID của tài xế
3. Tài xế phải /start bot trước

---

## ✅ Checklist Hoàn Thành

### UI/UX
- [ ] Cột "Gán Đơn" hiển thị
- [ ] Nút "Gán Tài Xế" hoạt động
- [ ] Nút "Gán Group" hoạt động
- [ ] Modal mở/đóng mượt
- [ ] Autocomplete hoạt động
- [ ] Badge hiển thị đúng
- [ ] Responsive trên mobile

### Functionality
- [ ] Gán tài xế thành công
- [ ] Gán group thành công
- [ ] Database cập nhật đúng
- [ ] Log được tạo
- [ ] Thông báo Telegram gửi (nếu có)
- [ ] Error handling hoạt động
- [ ] Auto reload sau khi gán

### Database
- [ ] Bảng booking_bookings có đủ cột
- [ ] Bảng booking_drivers có đủ cột
- [ ] Bảng notification_groups tồn tại
- [ ] Bảng assignment_logs tồn tại
- [ ] Status ENUM có đủ giá trị

---

## 📊 Báo Cáo Kết Quả

Sau khi test xong, vui lòng báo cáo:

### Thành Công ✅
- Test nào pass?
- Có gì hoạt động tốt?
- UI/UX có ổn không?

### Lỗi ❌
- Test nào fail?
- Lỗi gì xảy ra?
- Console có lỗi gì?
- Screenshot (nếu có)

### Góp Ý 💡
- Cần cải thiện gì?
- Thiếu tính năng gì?
- UX có khó hiểu không?

---

## 🚀 Sau Khi Test

### Nếu Tất Cả OK
→ Bắt đầu Week 3:
- Accept booking page
- Race condition handling
- Timeout mechanism
- Groups management

### Nếu Có Lỗi
→ Fix bugs trước:
- Báo cáo chi tiết lỗi
- Tôi sẽ fix ngay
- Test lại

---

## 📞 Liên Hệ

Nếu gặp vấn đề, cung cấp:
1. Screenshot lỗi
2. Console log (F12)
3. Database structure (nếu cần)
4. Các bước tái hiện lỗi

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
