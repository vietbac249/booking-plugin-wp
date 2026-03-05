# Hướng Dẫn Cấu Hình Telegram & Zalo

## 📱 Cấu Hình Telegram Bot

### Bước 1: Tạo Telegram Bot

1. Mở Telegram và tìm kiếm **@BotFather**
2. Gửi lệnh: `/newbot`
3. Đặt tên cho bot (VD: `Xe Nội Bài Bot`)
4. Đặt username cho bot (VD: `xenoibai_bot`)
5. BotFather sẽ trả về **Bot Token** (dạng: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)
6. **Copy Bot Token** này

### Bước 2: Lấy Chat ID

#### Cách 1: Nhận thông báo cá nhân

1. Tìm bot của bạn trên Telegram (theo username vừa tạo)
2. Nhấn **Start** để kích hoạt bot
3. Mở trình duyệt và truy cập:
   ```
   https://api.telegram.org/bot<BOT_TOKEN>/getUpdates
   ```
   (Thay `<BOT_TOKEN>` bằng token của bạn)
4. Tìm `"chat":{"id":123456789}` trong kết quả
5. **Copy Chat ID** này (VD: `123456789`)

#### Cách 2: Nhận thông báo trong nhóm

1. Tạo nhóm Telegram mới
2. Thêm bot vào nhóm (Add Members → tìm bot)
3. Gửi 1 tin nhắn bất kỳ trong nhóm
4. Mở trình duyệt và truy cập:
   ```
   https://api.telegram.org/bot<BOT_TOKEN>/getUpdates
   ```
5. Tìm `"chat":{"id":-123456789}` (Chat ID nhóm có dấu `-` ở đầu)
6. **Copy Chat ID** này (VD: `-123456789`)

#### Cách 3: Dùng bot hỗ trợ

1. Tìm bot **@userinfobot** trên Telegram
2. Nhấn **Start**
3. Bot sẽ trả về Chat ID của bạn
4. **Copy Chat ID**

### Bước 3: Cấu Hình trong WordPress

1. Vào **WordPress Admin** → **Đặt Xe**
2. Cuộn xuống phần **Cấu Hình Thông Báo**
3. Dán **Bot Token** vào ô **Telegram Bot Token**
4. Dán **Chat ID** vào ô **Telegram Chat ID**
5. Nhấn **Lưu Cài Đặt**

### Bước 4: Test

1. Tạo 1 đơn đặt xe thử
2. Kiểm tra Telegram xem có nhận được thông báo không

---

## 💬 Cấu Hình Zalo OA (Official Account)

### Bước 1: Tạo Zalo Official Account

1. Truy cập: **https://oa.zalo.me/**
2. Đăng nhập bằng tài khoản Zalo
3. Nhấn **Tạo OA mới**
4. Điền thông tin:
   - Tên OA: `Xe Nội Bài`
   - Loại OA: Chọn phù hợp với doanh nghiệp
5. Hoàn tất đăng ký

### Bước 2: Lấy Access Token

1. Vào **https://developers.zalo.me/**
2. Đăng nhập và chọn OA vừa tạo
3. Vào **App Dashboard** → **Tạo ứng dụng mới**
4. Điền thông tin ứng dụng
5. Sau khi tạo xong, vào **Settings** → **App Credentials**
6. **Copy App ID** và **App Secret**
7. Vào **Tools** → **Access Token Generator**
8. Chọn quyền: `message` và `user.info`
9. Nhấn **Generate Token**
10. **Copy Access Token** (có thời hạn, cần refresh định kỳ)

### Bước 3: Lấy User ID (Phone Number)

#### Cách 1: Qua Zalo OA

1. Mở app Zalo trên điện thoại
2. Tìm và theo dõi OA vừa tạo
3. Vào **OA Dashboard** → **Người quan tâm**
4. Tìm tài khoản của bạn
5. **Copy User ID** hoặc số điện thoại (format: 84912345678)

#### Cách 2: Qua API

1. Gọi API:
   ```
   GET https://openapi.zalo.me/v2.0/oa/getfollowers
   ```
   Headers:
   ```
   access_token: <YOUR_ACCESS_TOKEN>
   ```
2. Lấy `user_id` từ response

### Bước 4: Cấu Hình trong WordPress

1. Vào **WordPress Admin** → **Đặt Xe**
2. Cuộn xuống phần **Cấu Hình Thông Báo**
3. Dán **Access Token** vào ô **Zalo Access Token**
4. Nhập **Số điện thoại** (format: 84912345678) vào ô **Zalo Phone Number**
5. Nhấn **Lưu Cài Đặt**

### Bước 5: Test

1. Tạo 1 đơn đặt xe thử
2. Kiểm tra Zalo xem có nhận được thông báo không

---

## 🔧 Troubleshooting

### Telegram không nhận được thông báo

**Nguyên nhân:**
- Bot Token sai
- Chat ID sai
- Bot chưa được Start
- Bot bị kick khỏi nhóm

**Giải pháp:**
1. Kiểm tra lại Bot Token và Chat ID
2. Đảm bảo đã nhấn Start với bot
3. Nếu là nhóm, đảm bảo bot vẫn còn trong nhóm
4. Test bằng cách gửi tin nhắn thủ công:
   ```
   https://api.telegram.org/bot<BOT_TOKEN>/sendMessage?chat_id=<CHAT_ID>&text=Test
   ```

### Zalo không nhận được thông báo

**Nguyên nhân:**
- Access Token hết hạn
- User ID sai
- Chưa theo dõi OA
- Quyền API chưa đủ

**Giải pháp:**
1. Refresh Access Token (token có thời hạn)
2. Kiểm tra User ID đúng format (84912345678)
3. Đảm bảo đã theo dõi OA
4. Kiểm tra quyền API có bao gồm `message`

### Access Token Zalo hết hạn

**Giải pháp:**
1. Vào **https://developers.zalo.me/**
2. Chọn ứng dụng
3. Vào **Tools** → **Access Token Generator**
4. Generate token mới
5. Cập nhật vào WordPress

---

## 📝 Lưu Ý Quan Trọng

### Telegram
- ✅ Miễn phí hoàn toàn
- ✅ Không giới hạn số tin nhắn
- ✅ Token không hết hạn
- ✅ Có thể gửi vào nhóm (nhiều người nhận)
- ⚠️ Cần Start bot trước khi nhận tin

### Zalo
- ⚠️ Access Token có thời hạn (cần refresh)
- ⚠️ Có giới hạn số tin nhắn/ngày (tùy gói)
- ⚠️ Cần xác thực OA (có thể mất phí)
- ⚠️ User phải theo dõi OA mới nhận được tin
- ✅ Phổ biến tại Việt Nam

### Khuyến nghị
- Nên dùng **Telegram** vì đơn giản, miễn phí, ổn định
- Dùng **Zalo** nếu khách hàng chủ yếu dùng Zalo
- Có thể bật cả 2 để đảm bảo nhận được thông báo

---

## 🎯 Kết Quả

Sau khi cấu hình xong, mỗi khi có đơn đặt xe mới, bạn sẽ nhận được thông báo qua:
- ✉️ Email
- 📱 Telegram
- 💬 Zalo

Thông báo bao gồm đầy đủ thông tin:
- Họ tên khách hàng
- Số điện thoại
- Điểm đi - Điểm đến
- Loại xe
- Thời gian đi
- Giá tiền
- Thời gian đặt

---

**Chúc bạn cấu hình thành công! 🚗**
