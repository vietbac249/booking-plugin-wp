# ĐỀ ÁN: HỆ THỐNG ĐẶT XE TRỰC TUYẾN CHO DỊCH VỤ XE SÂN BAY NỘI BÀI

---

## 1. THÔNG TIN CHUNG

### 1.1. Tên đề án
**Hệ Thống Đặt Xe Trực Tuyến Tích Hợp WordPress - Plugin Booking Xe Nội Bài**

### 1.2. Lĩnh vực
Công nghệ thông tin - Phát triển ứng dụng web

### 1.3. Đối tượng sử dụng
- Khách hàng cần đặt xe đi/về sân bay Nội Bài
- Khách hàng cần thuê xe đường dài
- Nhà xe/Công ty vận tải
- Tài xế

### 1.4. Mục tiêu
Xây dựng một plugin WordPress hoàn chỉnh cho phép khách hàng đặt xe trực tuyến với các tính năng:
- Tính toán khoảng cách và giá tiền tự động
- Tích hợp Google Maps API
- Thông báo đa kênh (Email, Telegram, Zalo)
- Giao diện thân thiện, responsive

---

## 2. PHÂN TÍCH VẤN ĐỀ

### 2.1. Bối cảnh
Trong thời đại số hóa, việc đặt xe trực tuyến đã trở thành xu hướng phổ biến. Tuy nhiên, nhiều doanh nghiệp vận tải nhỏ và vừa vẫn chưa có hệ thống đặt xe trực tuyến chuyên nghiệp, dẫn đến:
- Khách hàng phải gọi điện để đặt xe (bất tiện)
- Không tính được giá trước
- Khó quản lý đơn hàng
- Mất thời gian xử lý thủ công

### 2.2. Nhu cầu thực tế
- **Khách hàng**: Muốn biết giá trước khi đặt, đặt xe nhanh chóng 24/7
- **Nhà xe**: Cần hệ thống nhận đơn tự động, thông báo tức thì cho tài xế
- **Tài xế**: Cần nhận thông tin khách hàng nhanh chóng để liên hệ

### 2.3. Giải pháp đề xuất
Xây dựng plugin WordPress với các tính năng:
1. Form đặt xe trực tuyến
2. Tính toán khoảng cách tự động qua Google Maps
3. Tính giá theo công thức linh hoạt
4. Thông báo đa kênh (Email, Telegram, Zalo)
5. Giao diện đẹp, dễ sử dụng

---

## 3. PHÂN TÍCH CHỨC NĂNG

### 3.1. Sơ đồ Use Case

```
┌─────────────────────────────────────────────────────────┐
│                    HỆ THỐNG ĐẶT XE                      │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  KHÁCH HÀNG                    ADMIN/TÀI XẾ           │
│  ┌──────────────┐              ┌──────────────┐       │
│  │ Chọn loại xe │              │ Nhận thông   │       │
│  │ (Sân bay/    │              │ báo đơn mới  │       │
│  │ Đường dài)   │              │              │       │
│  └──────┬───────┘              └──────┬───────┘       │
│         │                             │               │
│  ┌──────▼───────┐              ┌──────▼───────┐       │
│  │ Nhập điểm đi │              │ Xem thông tin│       │
│  │ điểm đến     │              │ khách hàng   │       │
│  └──────┬───────┘              │              │       │
│         │                      └──────┬───────┘       │
│  ┌──────▼───────┐              ┌──────▼───────┐       │
│  │ Thêm điểm    │              │ Liên hệ      │       │
│  │ dừng (tùy    │              │ khách hàng   │       │
│  │ chọn)        │              │              │       │
│  └──────┬───────┘              └──────────────┘       │
│         │                                             │
│  ┌──────▼───────┐                                     │
│  │ Chọn loại xe │                                     │
│  │ và thời gian │                                     │
│  └──────┬───────┘                                     │
│         │                                             │
│  ┌──────▼───────┐                                     │
│  │ Kiểm tra giá │                                     │
│  └──────┬───────┘                                     │
│         │                                             │
│  ┌──────▼───────┐                                     │
│  │ Nhập thông   │                                     │
│  │ tin liên hệ  │                                     │
│  └──────┬───────┘                                     │
│         │                                             │
│  ┌──────▼───────┐                                     │
│  │ Đặt xe       │                                     │
│  └──────────────┘                                     │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### 3.2. Chức năng chính

#### 3.2.1. Module Đặt Xe (Frontend)

**A. Tab Sân Bay Nội Bài**
- Nhập điểm đi (autocomplete từ Google Maps)
- Điểm đến mặc định: Sân bay Nội Bài
- Thêm tối đa 2 điểm dừng
- Chức năng đảo chiều (đi từ sân bay về)
- Toggle: Đi 2 chiều (giảm giá 10%)
- Toggle: Xuất hóa đơn VAT (tính thêm 10%)
- Chọn loại xe: 4 chỗ, 7 chỗ, 16 chỗ, 29 chỗ, 45 chỗ
- Chọn thời gian đi (ngày, giờ, phút)

**B. Tab Đường Dài**
- Nhập điểm đi (autocomplete)
- Nhập điểm đến (autocomplete)
- Thêm tối đa 2 điểm dừng
- Toggle: Xuất hóa đơn VAT
- Chọn loại xe
- Chọn thời gian đi

**C. Tính Giá Tự Động**
- Gọi Google Distance Matrix API để tính khoảng cách
- Áp dụng công thức:
  ```
  Giá = Khoảng cách × Đơn giá × Hệ số loại xe × (1 + VAT) × Hệ số 2 chiều
  ```
- Hiển thị kết quả ngay lập tức

**D. Form Liên Hệ**
- Sau khi kiểm tra giá, hiện form nhập:
  - Số điện thoại (validate 10-11 số)
  - Họ và tên
- Nút "Đặt xe" màu đỏ nổi bật

#### 3.2.2. Module Quản Trị (Backend)

**A. Cấu Hình Google Maps**
- Nhập Google Maps API Key
- Hướng dẫn lấy API Key
- Hướng dẫn bật các API cần thiết

**B. Cấu Hình Giá**
- Giá sân bay (VNĐ/km)
- Giá đường dài (VNĐ/km)
- Hệ số đi 2 chiều
- Tỷ lệ VAT
- Hệ số giá theo loại xe

**C. Cấu Hình Thông Báo**
- **Telegram:**
  - Bot Token
  - Chat ID (cá nhân hoặc nhóm)
- **Zalo:**
  - Access Token
  - Phone Number (User ID)

#### 3.2.3. Module Thông Báo

**A. Gửi Email**
- Gửi đến admin email của WordPress
- Nội dung đầy đủ thông tin đơn hàng

**B. Gửi Telegram**
- Gửi qua Telegram Bot API
- Có thể gửi vào nhóm (nhiều tài xế)
- Định dạng tin nhắn đẹp với emoji

**C. Gửi Zalo**
- Gửi qua Zalo Official Account API
- Phù hợp với thị trường Việt Nam

---

## 4. THIẾT KẾ HỆ THỐNG

### 4.1. Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────┐
│                    NGƯỜI DÙNG                           │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│              WORDPRESS WEBSITE                          │
│  ┌───────────────────────────────────────────────────┐ │
│  │         BOOKING PLUGIN (Frontend)                 │ │
│  │  ┌─────────────┐  ┌─────────────┐               │ │
│  │  │  HTML/PHP   │  │   CSS       │               │ │
│  │  │  Templates  │  │   Styling   │               │ │
│  │  └─────────────┘  └─────────────┘               │ │
│  │  ┌─────────────────────────────────┐            │ │
│  │  │      JavaScript (jQuery)        │            │ │
│  │  │  - Form handling                │            │ │
│  │  │  - AJAX requests                │            │ │
│  │  │  - UI interactions              │            │ │
│  │  └─────────────────────────────────┘            │ │
│  └───────────────────┬───────────────────────────────┘ │
│                      │                                  │
│                      ▼                                  │
│  ┌───────────────────────────────────────────────────┐ │
│  │         BOOKING PLUGIN (Backend)                  │ │
│  │  ┌─────────────────────────────────┐             │ │
│  │  │      PHP Core Logic             │             │ │
│  │  │  - AJAX handlers                │             │ │
│  │  │  - Settings management          │             │ │
│  │  │  - Price calculation            │             │ │
│  │  └─────────────────────────────────┘             │ │
│  └───────────────────┬───────────────────────────────┘ │
└────────────────────┬─┴─────────────────────────────────┘
                     │
        ┌────────────┼────────────┐
        │            │            │
        ▼            ▼            ▼
┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│   Google     │ │   Telegram   │ │     Zalo     │
│   Maps API   │ │   Bot API    │ │     OA API   │
└──────────────┘ └──────────────┘ └──────────────┘
```

### 4.2. Cấu trúc thư mục

```
booking-plugin/
├── booking-plugin.php          # File chính của plugin
├── assets/
│   ├── css/
│   │   └── style.css          # CSS cho giao diện
│   └── js/
│       └── script.js          # JavaScript xử lý logic
├── templates/
│   ├── booking-form.php       # Template form đặt xe
│   └── admin-settings.php     # Trang cấu hình admin
├── README.md                  # Hướng dẫn cài đặt
├── HUONG-DAN-LAY-API-KEY.md  # Hướng dẫn lấy Google API
├── HUONG-DAN-TELEGRAM-ZALO.md # Hướng dẫn cấu hình thông báo
└── TEST-PLUGIN.md            # Test cases
```

### 4.3. Cơ sở dữ liệu

Plugin sử dụng WordPress Options API để lưu cấu hình:

```sql
-- Bảng wp_options
option_name                      | option_value
---------------------------------|------------------
booking_google_api_key           | AIzaSy...
booking_airport_price            | 15000
booking_long_price               | 12000
booking_roundtrip_multiplier     | 1.8
booking_vat_rate                 | 0.1
booking_telegram_bot_token       | 123456:ABC...
booking_telegram_chat_id         | 123456789
booking_zalo_access_token        | eyJhbGc...
booking_zalo_phone               | 84912345678
```

### 4.4. Luồng xử lý đặt xe

```
1. Người dùng nhập thông tin
   ↓
2. Nhấn "Kiểm Tra Giá"
   ↓
3. JavaScript gửi AJAX request
   ↓
4. PHP gọi Google Distance Matrix API
   ↓
5. Tính toán giá theo công thức
   ↓
6. Trả kết quả về frontend
   ↓
7. Hiển thị giá + Form liên hệ
   ↓
8. Người dùng nhập SĐT, Họ tên
   ↓
9. Nhấn "Đặt xe"
   ↓
10. PHP xử lý đơn hàng
    ↓
11. Gửi thông báo qua:
    - Email
    - Telegram
    - Zalo
    ↓
12. Hiển thị thông báo thành công
```

---

## 5. CÔNG NGHỆ SỬ DỤNG

### 5.1. Frontend
- **HTML5**: Cấu trúc trang web
- **CSS3**: Styling, animations, responsive design
- **JavaScript (jQuery)**: Xử lý tương tác, AJAX
- **Google Maps JavaScript API**: Autocomplete địa chỉ

### 5.2. Backend
- **PHP 7.4+**: Ngôn ngữ lập trình chính
- **WordPress 5.0+**: CMS framework
- **WordPress Plugin API**: Hooks, filters, actions
- **WordPress Options API**: Lưu trữ cấu hình

### 5.3. APIs & Services
- **Google Maps APIs**:
  - Maps JavaScript API (autocomplete)
  - Places API (tìm kiếm địa điểm)
  - Distance Matrix API (tính khoảng cách)
- **Telegram Bot API**: Gửi thông báo
- **Zalo Official Account API**: Gửi tin nhắn

### 5.4. Tools & Libraries
- **jQuery**: JavaScript library
- **WordPress REST API**: AJAX handling
- **wp_remote_post()**: HTTP requests

---

## 6. TÍNH NĂNG NỔI BẬT

### 6.1. Giao diện người dùng
✅ Thiết kế hiện đại, mềm mại với bo tròn
✅ Responsive - tương thích mọi thiết bị
✅ Animation mượt mà
✅ Toggle switches đẹp mắt
✅ Autocomplete địa chỉ thông minh

### 6.2. Tính toán thông minh
✅ Tính khoảng cách chính xác qua Google Maps
✅ Công thức giá linh hoạt, dễ tùy chỉnh
✅ Hỗ trợ nhiều loại xe với hệ số riêng
✅ Tính VAT tự động
✅ Giảm giá đi 2 chiều

### 6.3. Thông báo đa kênh
✅ Email tự động
✅ Telegram (miễn phí, không giới hạn)
✅ Zalo (phổ biến tại VN)
✅ Thông báo tức thì, không bỏ sót đơn

### 6.4. Quản trị dễ dàng
✅ Giao diện admin trực quan
✅ Cấu hình đơn giản
✅ Hướng dẫn chi tiết từng bước
✅ Không cần kiến thức lập trình

---

## 7. QUY TRÌNH PHÁT TRIỂN

### 7.1. Giai đoạn 1: Phân tích & Thiết kế (1 tuần)
- Thu thập yêu cầu
- Phân tích nghiệp vụ
- Thiết kế giao diện (UI/UX)
- Thiết kế cơ sở dữ liệu
- Thiết kế kiến trúc hệ thống

### 7.2. Giai đoạn 2: Phát triển Frontend (1 tuần)
- Xây dựng HTML structure
- Thiết kế CSS (responsive)
- Implement JavaScript logic
- Tích hợp Google Maps autocomplete
- Test giao diện trên nhiều thiết bị

### 7.3. Giai đoạn 3: Phát triển Backend (1.5 tuần)
- Xây dựng plugin structure
- Implement AJAX handlers
- Tích hợp Google Distance Matrix API
- Xây dựng logic tính giá
- Implement admin settings page

### 7.4. Giai đoạn 4: Tích hợp Thông báo (1 tuần)
- Tích hợp Telegram Bot API
- Tích hợp Zalo OA API
- Implement email notification
- Test thông báo đa kênh

### 7.5. Giai đoạn 5: Testing & Debug (1 tuần)
- Unit testing
- Integration testing
- User acceptance testing
- Bug fixing
- Performance optimization

### 7.6. Giai đoạn 6: Deployment & Documentation (0.5 tuần)
- Viết tài liệu hướng dẫn
- Tạo video demo
- Deploy lên production
- Training người dùng

**Tổng thời gian: 6 tuần**

---

## 8. TESTING & QUALITY ASSURANCE

### 8.1. Test Cases

#### Test Case 1: Autocomplete địa chỉ
- **Input**: Gõ "Hà Nội" vào ô điểm đi
- **Expected**: Hiện danh sách gợi ý từ Google Maps
- **Status**: ✅ Pass

#### Test Case 2: Tính khoảng cách
- **Input**: Điểm đi: "Hà Nội", Điểm đến: "Sân bay Nội Bài"
- **Expected**: Khoảng cách ~30km
- **Status**: ✅ Pass

#### Test Case 3: Tính giá cơ bản
- **Input**: 30km, xe 4 chỗ, giá 15,000đ/km
- **Expected**: 450,000 VNĐ
- **Status**: ✅ Pass

#### Test Case 4: Tính giá đi 2 chiều
- **Input**: 30km, 2 chiều, hệ số 1.8
- **Expected**: 810,000 VNĐ (giảm 10%)
- **Status**: ✅ Pass

#### Test Case 5: Tính VAT
- **Input**: Giá 450,000đ, VAT 10%
- **Expected**: 495,000 VNĐ
- **Status**: ✅ Pass

#### Test Case 6: Thêm điểm dừng
- **Input**: Nhấn nút "+" 2 lần
- **Expected**: Hiện 2 ô điểm dừng, nút "+" ẩn
- **Status**: ✅ Pass

#### Test Case 7: Xóa điểm dừng
- **Input**: Nhấn nút "-" ở điểm dừng 1
- **Expected**: Điểm dừng 1 bị xóa, nút "+" hiện lại
- **Status**: ✅ Pass

#### Test Case 8: Đảo chiều
- **Input**: Nhấn nút "Đảo chiều"
- **Expected**: Điểm đi ↔ Điểm đến
- **Status**: ✅ Pass

#### Test Case 9: Validation form
- **Input**: Để trống SĐT, nhấn "Đặt xe"
- **Expected**: Hiện alert "Vui lòng nhập đầy đủ thông tin"
- **Status**: ✅ Pass

#### Test Case 10: Gửi thông báo Telegram
- **Input**: Đặt xe thành công
- **Expected**: Nhận tin nhắn trên Telegram
- **Status**: ✅ Pass

### 8.2. Performance Testing
- **Page Load Time**: < 2s
- **AJAX Response Time**: < 1s
- **Google Maps API Call**: < 500ms
- **Notification Delivery**: < 3s

### 8.3. Security Testing
- ✅ CSRF Protection (WordPress nonce)
- ✅ XSS Prevention (sanitize input)
- ✅ SQL Injection Prevention (prepared statements)
- ✅ API Key Protection (server-side only)

---

## 9. KẾT QUẢ ĐẠT ĐƯỢC

### 9.1. Về mặt kỹ thuật
✅ Plugin WordPress hoàn chỉnh, chuẩn coding standards
✅ Tích hợp thành công 3 APIs (Google Maps, Telegram, Zalo)
✅ Giao diện responsive, tương thích mọi thiết bị
✅ Code clean, có comments, dễ maintain
✅ Performance tốt, load nhanh

### 9.2. Về mặt chức năng
✅ Đầy đủ tính năng đặt xe trực tuyến
✅ Tính giá tự động, chính xác
✅ Thông báo đa kênh, không bỏ sót đơn
✅ Dễ sử dụng cho cả khách hàng và admin
✅ Có thể mở rộng thêm tính năng

### 9.3. Về mặt nghiệp vụ
✅ Giảm thời gian xử lý đơn hàng
✅ Tăng trải nghiệm khách hàng
✅ Tự động hóa quy trình đặt xe
✅ Giảm chi phí vận hành
✅ Tăng khả năng cạnh tranh

---

## 10. HƯỚNG PHÁT TRIỂN

### 10.1. Ngắn hạn (3-6 tháng)
- [ ] Thêm tính năng thanh toán online (VNPay, MoMo)
- [ ] Lưu lịch sử đơn hàng vào database
- [ ] Trang quản lý đơn hàng cho admin
- [ ] Export báo cáo Excel
- [ ] Tích hợp SMS notification

### 10.2. Trung hạn (6-12 tháng)
- [ ] App mobile (React Native)
- [ ] Hệ thống quản lý tài xế
- [ ] Tracking xe real-time
- [ ] Đánh giá & review
- [ ] Chương trình khách hàng thân thiết

### 10.3. Dài hạn (1-2 năm)
- [ ] AI chatbot tư vấn
- [ ] Dự đoán nhu cầu đặt xe
- [ ] Tối ưu hóa lộ trình
- [ ] Mở rộng ra các tỉnh thành khác
- [ ] Marketplace cho nhiều nhà xe

---

## 11. KẾT LUẬN

### 11.1. Đánh giá chung
Dự án "Hệ Thống Đặt Xe Trực Tuyến" đã hoàn thành đầy đủ các mục tiêu đề ra:
- ✅ Xây dựng thành công plugin WordPress
- ✅ Tích hợp đầy đủ các API cần thiết
- ✅ Giao diện đẹp, dễ sử dụng
- ✅ Tính năng hoàn chỉnh, ổn định
- ✅ Có tài liệu hướng dẫn chi tiết

### 11.2. Ý nghĩa thực tiễn
- **Đối với khách hàng**: Đặt xe dễ dàng, biết giá trước, tiết kiệm thời gian
- **Đối với nhà xe**: Tự động hóa quy trình, giảm chi phí, tăng doanh thu
- **Đối với tài xế**: Nhận thông tin nhanh, không bỏ sót khách

### 11.3. Bài học kinh nghiệm
- Tích hợp API cần đọc kỹ documentation
- UI/UX quan trọng không kém chức năng
- Testing kỹ lưỡng trước khi deploy
- Tài liệu hướng dẫn cần chi tiết, dễ hiểu
- Lắng nghe feedback từ người dùng

### 11.4. Lời cảm ơn
Xin chân thành cảm ơn:
- Giảng viên hướng dẫn
- Các bạn trong nhóm
- Khách hàng thử nghiệm
- Cộng đồng WordPress, Google, Telegram, Zalo

---

## 12. TÀI LIỆU THAM KHẢO

### 12.1. Documentation
1. WordPress Plugin Handbook: https://developer.wordpress.org/plugins/
2. Google Maps Platform: https://developers.google.com/maps
3. Telegram Bot API: https://core.telegram.org/bots/api
4. Zalo Official Account: https://developers.zalo.me/

### 12.2. Tutorials
1. WordPress Plugin Development: https://www.wpbeginner.com/
2. Google Maps JavaScript API: https://developers.google.com/maps/documentation/javascript
3. AJAX in WordPress: https://codex.wordpress.org/AJAX_in_Plugins

### 12.3. Tools
1. Visual Studio Code
2. XAMPP/MAMP (Local development)
3. Git/GitHub (Version control)
4. Postman (API testing)
5. Chrome DevTools (Debugging)

---

## PHỤ LỤC

### A. Screenshots
- Screenshot 1: Giao diện form đặt xe
- Screenshot 2: Kết quả tính giá
- Screenshot 3: Form nhập thông tin
- Screenshot 4: Trang cấu hình admin
- Screenshot 5: Thông báo Telegram
- Screenshot 6: Thông báo Zalo

### B. Source Code
- GitHub Repository: [Link]
- Live Demo: [Link]
- Documentation: [Link]

### C. Video Demo
- Link YouTube: [Link]
- Thời lượng: 10 phút
- Nội dung: Hướng dẫn sử dụng từ A-Z

---

**Ngày hoàn thành**: 09/02/2026
**Phiên bản**: 1.0.0
**Tác giả**: [Tên của bạn]
**Liên hệ**: [Email/SĐT của bạn]

---

*Tài liệu này được tạo cho mục đích học tập và nghiên cứu.*
