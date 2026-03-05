# 📱 Hướng Dẫn Cấu Hình Telegram & Zalo Chi Tiết

## 🎯 Mục Đích
Hệ thống sẽ gửi thông báo đơn hàng mới qua Telegram hoặc Zalo cho:
1. **Tài xế cụ thể** - Khi admin gán đơn trực tiếp cho 1 tài xế
2. **Group tài xế** - Khi admin gửi đơn vào group, ai nhanh tay nhận trước

## 📱 PHẦN 1: CẤU HÌNH TELEGRAM

### Bước 1: Tạo Telegram Bot

1. Mở Telegram và tìm **@BotFather**
2. Gửi lệnh: `/newbot`
3. Nhập tên bot (VD: `Đặt Xe Nội Bài Bot`)
4. Nhập username bot (phải kết thúc bằng `bot`, VD: `datxenoibai_bot`)
5. BotFather sẽ trả về **Bot Token** dạng: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`
6. **LƯU LẠI TOKEN NÀY** - sẽ dùng sau

### Bước 2: Cấu Hình Bot Token Chung (Cho Gửi Tài Xế Cá Nhân)

1. Vào WordPress Admin → **Đặt Xe** → **Cài Đặt** → Tab **Thông Báo**
2. Nhập **Bot Token** vào ô "Telegram Bot Token"
3. Click **Lưu Cài Đặt Thông Báo**

### Bước 3: Lấy Chat ID Của Tài Xế

Mỗi tài xế cần có Chat ID riêng để nhận thông báo cá nhân.

**Cách 1: Tự động (Khuyến nghị)**
1. Tài xế mở Telegram
2. Tìm bot của bạn (VD: `@datxenoibai_bot`)
3. Click **Start** hoặc gửi `/start`
4. Bot sẽ trả về Chat ID của tài xế
5. Admin vào **Quản Lý Tài Xế** → Sửa thông tin tài xế
6. Nhập Chat ID vào trường "Telegram Chat ID"

**Cách 2: Thủ công**
1. Tài xế gửi tin nhắn cho bot
2. Truy cập URL: `https://api.telegram.org/bot[BOT_TOKEN]/getUpdates`
   (Thay `[BOT_TOKEN]` bằng token thực tế)
3. Tìm `"chat":{"id":123456789}` - đó là Chat ID
4. Nhập vào thông tin tài xế

### Bước 4: Tạo Group Telegram (Cho Gửi Vào Group)

1. Tạo group Telegram mới (VD: "Nhóm Tài Xế Hà Nội")
2. Thêm bot vào group:
   - Click vào tên group → **Add Members**
   - Tìm và thêm bot của bạn
3. Gửi 1 tin nhắn bất kỳ trong group (VD: "Test")
4. Truy cập: `https://api.telegram.org/bot[BOT_TOKEN]/getUpdates`
5. Tìm `"chat":{"id":-1001234567890}` - Chat ID của group (số âm)
6. Vào WordPress Admin → **Đặt Xe** → **Groups**
7. Click **Thêm Group**:
   - Tên Group: `Nhóm Tài Xế Hà Nội`
   - Loại: `Telegram`
   - Bot Token: (token của bot)
   - Chat ID: `-1001234567890` (Chat ID của group)
   - Trạng thái: ✅ Kích hoạt
8. Click **Lưu Group**

### Bước 5: Test Gửi Thông Báo

**Test gửi cho tài xế:**
1. Tạo đơn hàng test
2. Vào **Quản Lý Đơn Hàng**
3. Click **Gán Tài Xế**
4. Chọn tài xế có Telegram Chat ID
5. Click **Gán Cho Tài Xế**
6. Tài xế sẽ nhận thông báo qua Telegram với nút "Nhận Đơn"

**Test gửi vào group:**
1. Tạo đơn hàng test
2. Click **Gán Group**
3. Chọn group Telegram
4. Click **Gửi Vào Group**
5. Tin nhắn sẽ xuất hiện trong group với nút "Nhận Đơn"

## 💬 PHẦN 2: CẤU HÌNH ZALO (Tùy Chọn)

### ⚠️ Lưu Ý Quan Trọng
- Zalo phức tạp hơn Telegram
- Cần đăng ký Zalo Official Account (OA)
- Cần xác minh doanh nghiệp
- Zalo Group chưa được hỗ trợ đầy đủ
- **KHUYẾN NGHỊ: Dùng Telegram thay vì Zalo**

### Bước 1: Đăng Ký Zalo Official Account

1. Truy cập: https://oa.zalo.me/
2. Đăng nhập bằng tài khoản Zalo
3. Click **Tạo Official Account**
4. Chọn loại tài khoản (Doanh nghiệp/Cá nhân)
5. Điền thông tin và xác minh

### Bước 2: Tạo Ứng Dụng Zalo

1. Truy cập: https://developers.zalo.me/
2. Click **Tạo ứng dụng**
3. Chọn **Official Account**
4. Liên kết với OA đã tạo
5. Lấy **Access Token** từ phần **Cài đặt**

### Bước 3: Cấu Hình Zalo

1. Vào WordPress Admin → **Đặt Xe** → **Cài Đặt** → Tab **Thông Báo**
2. Nhập **Zalo Access Token**
3. Nhập **Zalo Phone Number** (format: 84912345678, không có số 0 đầu)
4. Click **Lưu Cài Đặt Thông Báo**

### Bước 4: Lấy Zalo User ID Của Tài Xế

1. Tài xế cần quan tâm (follow) OA của bạn
2. Sử dụng Zalo API để lấy danh sách followers
3. Tìm User ID tương ứng với số điện thoại
4. Nhập vào thông tin tài xế

### Hạn Chế Của Zalo
- ❌ Không gửi được vào Zalo Group dễ dàng như Telegram
- ❌ Cần xác minh doanh nghiệp
- ❌ API phức tạp hơn
- ❌ Giới hạn số lượng tin nhắn

## 🎯 PHẦN 3: LUỒNG HOẠT ĐỘNG

### Kịch Bản 1: Gán Đơn Cho Tài Xế Cụ Thể

```
1. Khách đặt xe → Đơn hàng mới (status: pending)
2. Admin vào Quản Lý Đơn Hàng
3. Click "Gán Tài Xế" → Tìm tài xế
4. Chọn tài xế → Click "Gán Cho Tài Xế"
5. Hệ thống:
   - Cập nhật đơn hàng (status: assigned, driver_id, assignment_type: direct)
   - Tạo token nhận đơn (expires sau 1 giờ)
   - Gửi thông báo qua Telegram/Zalo cho tài xế
6. Tài xế nhận thông báo với nút "Nhận Đơn"
7. Tài xế click "Nhận Đơn"
8. Hệ thống cập nhật (status: accepted)
9. Admin thấy đơn đã được nhận
```

### Kịch Bản 2: Gửi Đơn Vào Group (Ai Nhanh Tay Nhận Trước)

```
1. Khách đặt xe → Đơn hàng mới (status: pending)
2. Admin vào Quản Lý Đơn Hàng
3. Click "Gán Group" → Chọn group
4. Click "Gửi Vào Group"
5. Hệ thống:
   - Cập nhật đơn hàng (status: assigned, assignment_type: group, group_id)
   - Tạo token nhận đơn
   - Gửi tin nhắn vào group Telegram/Zalo
6. Tất cả tài xế trong group thấy thông báo
7. Tài xế A click "Nhận Đơn" trước
8. Hệ thống:
   - Cập nhật đơn hàng (status: accepted, driver_id: A)
   - Vô hiệu hóa link nhận đơn
9. Tài xế B click sau → Thông báo "Đơn đã có người nhận"
```

## 📋 PHẦN 4: CHECKLIST

### Để Gửi Cho Tài Xế Cá Nhân:
- [ ] Đã tạo Telegram Bot
- [ ] Đã lấy Bot Token
- [ ] Đã nhập Bot Token vào Cài Đặt
- [ ] Tài xế đã gửi /start cho bot
- [ ] Đã lấy Chat ID của tài xế
- [ ] Đã nhập Chat ID vào thông tin tài xế
- [ ] Test gửi thông báo thành công

### Để Gửi Vào Group:
- [ ] Đã tạo Telegram Bot
- [ ] Đã tạo Telegram Group
- [ ] Đã thêm bot vào group
- [ ] Đã lấy Chat ID của group
- [ ] Đã tạo Group trong WordPress Admin
- [ ] Đã nhập Bot Token và Chat ID
- [ ] Test gửi vào group thành công

## 🔧 TROUBLESHOOTING

### Lỗi: "Không thể gửi thông báo"
**Nguyên nhân:**
- Bot Token sai
- Chat ID sai
- Bot chưa được thêm vào group
- Tài xế chưa gửi /start cho bot

**Giải pháp:**
1. Kiểm tra Bot Token trong Cài Đặt
2. Kiểm tra Chat ID của tài xế/group
3. Đảm bảo bot đã được thêm vào group
4. Yêu cầu tài xế gửi /start cho bot

### Lỗi: "Chưa cấu hình Telegram Bot Token"
**Giải pháp:**
- Vào Cài Đặt → Tab Thông Báo
- Nhập Bot Token
- Click Lưu

### Lỗi: "Link đã hết hạn"
**Nguyên nhân:**
- Token nhận đơn hết hạn sau 1 giờ

**Giải pháp:**
- Admin gán lại đơn hàng
- Hoặc tăng thời gian hết hạn trong code

### Tài xế không nhận được thông báo
**Kiểm tra:**
1. Tài xế có Chat ID chưa?
2. Chat ID có đúng không?
3. Tài xế đã block bot chưa?
4. Bot Token có đúng không?

## 📊 PHẦN 5: MẪU TIN NHẮN

### Tin nhắn gửi cho tài xế cá nhân:
```
🚗 ĐƠN HÀNG MỚI

📍 Từ: 247 Cầu Giấy, Hà Nội
📍 Đến: Sân bay Nội Bài
💰 Giá: 350,000đ
🕐 Thời gian: 15/03/2024 14:30
👤 Khách: Nguyễn Văn A
📞 SĐT: 0912345678

👉 Nhận đơn: [Link]
```

### Tin nhắn gửi vào group:
```
🚗 ĐƠN HÀNG MỚI (Ai nhanh tay nhận trước!)

📍 Từ: 247 Cầu Giấy, Hà Nội
📍 Đến: Sân bay Nội Bài
💰 Giá: 350,000đ
🕐 Thời gian: 15/03/2024 14:30

👉 Nhận đơn: [Link]
```

## 🎉 KẾT LUẬN

Sau khi hoàn tất cấu hình:
- ✅ Admin có thể gán đơn cho tài xế cụ thể
- ✅ Admin có thể gửi đơn vào group
- ✅ Tài xế nhận thông báo real-time
- ✅ Tài xế có thể nhận/từ chối đơn
- ✅ Hệ thống tự động cập nhật trạng thái

**Khuyến nghị:** Sử dụng Telegram vì đơn giản, miễn phí, và ổn định hơn Zalo.
