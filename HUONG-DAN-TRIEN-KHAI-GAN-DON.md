# Hướng Dẫn Triển Khai Tính Năng Gán Đơn Hàng

## 📋 Tổng Quan

Tính năng **Gán Đơn Hàng Cho Tài Xế** là một tính năng lớn và phức tạp, cần triển khai theo nhiều bước.

## ⚠️ Quan Trọng

Đây là tính năng **PHASE 2** - rất phức tạp và cần:
- **4 tuần phát triển** (160 giờ)
- **Nhiều file mới** (15+ files)
- **Tích hợp API** (Zalo OA + Telegram Bot)
- **Testing kỹ lưỡng**

## 🎯 Quyết Định Cần Thiết

Trước khi tiếp tục, bạn cần quyết định:

### 1. Phạm Vi Triển Khai

**Option A: MVP (Minimum Viable Product) - 1 tuần**
- ✅ Gán trực tiếp cho tài xế
- ✅ Gửi thông báo Telegram (đơn giản)
- ✅ Trang nhận đơn cơ bản
- ❌ Không có Zalo
- ❌ Không có group assignment
- ❌ Không có autocomplete fancy

**Option B: Full Feature - 4 tuần**
- ✅ Tất cả tính năng trong đề xuất
- ✅ Zalo OA integration
- ✅ Telegram Bot integration
- ✅ Group assignment
- ✅ Autocomplete search
- ✅ Race condition handling
- ✅ Timeout mechanism

**Option C: Từng Bước (Recommended)**
- **Tuần 1**: Database + Backend cơ bản
- **Tuần 2**: UI + Gán trực tiếp + Telegram
- **Tuần 3**: Group assignment + Zalo
- **Tuần 4**: Polish + Testing + Documentation

### 2. Yêu Cầu Kỹ Thuật

**Bạn cần chuẩn bị:**

#### Telegram Bot (Bắt buộc cho Option A, B, C)
```
1. Mở Telegram, tìm @BotFather
2. Gửi: /newbot
3. Đặt tên bot: "NoiBai Booking Bot"
4. Đặt username: "noibai_booking_bot"
5. Nhận Bot Token: 123456789:ABCdefGHIjklMNOpqrsTUVwxyz
6. Lưu token này
```

#### Zalo OA (Chỉ cần cho Option B, C)
```
1. Đăng ký Zalo OA tại: https://oa.zalo.me/
2. Xác thực doanh nghiệp
3. Lấy Access Token từ Developer Console
4. Cấu hình Webhook (nếu cần)
```

#### SSL Certificate (Bắt buộc)
```
- Website phải có HTTPS
- Cần cho webhook và secure links
```

## 🚀 Bắt Đầu Triển Khai

### Bước 1: Cập Nhật Database

**File đã tạo:** `update-database-assignment.php`

**Cách chạy:**
```
1. Upload file lên: /wp-content/plugins/booking-plugin/
2. Truy cập: https://your-site.com/wp-content/plugins/booking-plugin/update-database-assignment.php
3. Chờ script chạy xong
4. Kiểm tra kết quả
5. Xóa file sau khi chạy xong (bảo mật)
```

**Kết quả mong đợi:**
- ✅ Bảng `bookings` có thêm 7 cột mới
- ✅ Bảng `notification_groups` được tạo
- ✅ Bảng `drivers` có thêm 2 cột mới
- ✅ Bảng `assignment_logs` được tạo
- ✅ ENUM `status` được cập nhật

### Bước 2: Chọn Phương Án Triển Khai

**Tôi đề xuất: Option C - Từng Bước**

Lý do:
- ✅ Dễ kiểm soát
- ✅ Test từng phần
- ✅ Có thể dừng bất cứ lúc nào
- ✅ Ít rủi ro
- ✅ Dễ debug

## 📝 Checklist Triển Khai

### Week 1: Foundation ✅ (Đã hoàn thành)
- [x] Database schema
- [x] Update script
- [x] Documentation

### Week 2: Core Features (Cần làm)
- [ ] Backend: AJAX handlers
- [ ] Backend: Notification functions
- [ ] UI: Modal gán tài xế
- [ ] UI: Autocomplete search
- [ ] Frontend: Accept booking page
- [ ] Integration: Telegram basic

### Week 3: Advanced Features (Tùy chọn)
- [ ] UI: Modal gán group
- [ ] Backend: Group assignment logic
- [ ] Integration: Zalo OA
- [ ] Race condition handling
- [ ] Timeout mechanism

### Week 4: Polish & Testing (Tùy chọn)
- [ ] UI/UX improvements
- [ ] Security hardening
- [ ] Performance optimization
- [ ] Documentation
- [ ] User training

## 💰 Ước Tính Chi Phí

### Option A: MVP (1 tuần)
- Development: 40 giờ
- Testing: 8 giờ
- Documentation: 2 giờ
- **Total: 50 giờ**

### Option B: Full Feature (4 tuần)
- Development: 120 giờ
- Testing: 24 giờ
- Documentation: 8 giờ
- Integration: 8 giờ
- **Total: 160 giờ**

### Option C: Từng Bước
- Week 1: 12 giờ (Done)
- Week 2: 40 giờ
- Week 3: 40 giờ (Optional)
- Week 4: 20 giờ (Optional)
- **Total: 52-112 giờ**

## 🎯 Quyết Định Ngay Bây Giờ

**Bạn muốn:**

### A. Tiếp tục với MVP (1 tuần)
```
- Tôi sẽ code ngay Week 2
- Chỉ có gán trực tiếp + Telegram
- Đơn giản, nhanh, ít rủi ro
- Có thể mở rộng sau
```

### B. Tiếp tục với Full Feature (4 tuần)
```
- Tôi sẽ code từng tuần
- Đầy đủ tính năng
- Phức tạp, cần nhiều thời gian
- Cần Zalo OA + Telegram Bot
```

### C. Tạm Dừng - Cần Thêm Thông Tin
```
- Bạn cần tìm hiểu thêm về Zalo OA
- Bạn cần tạo Telegram Bot trước
- Bạn cần chuẩn bị SSL certificate
- Bạn cần ngân sách rõ ràng
```

### D. Hủy Bỏ - Không Cần Tính Năng Này
```
- Quay lại các tính năng khác
- Tập trung vào Ver3 form
- Hoàn thiện các tính năng hiện tại
```

## 📞 Câu Hỏi Quan Trọng

Trước khi tôi tiếp tục code, vui lòng trả lời:

1. **Bạn chọn Option nào?** (A, B, C, hay D)

2. **Bạn đã có Telegram Bot Token chưa?** (Có/Chưa)

3. **Bạn đã có Zalo OA chưa?** (Có/Chưa/Không cần)

4. **Website có HTTPS chưa?** (Có/Chưa)

5. **Ngân sách dự kiến?** (VNĐ hoặc số giờ)

6. **Deadline mong muốn?** (Ngày cụ thể)

## 🔄 Quy Trình Làm Việc

### Nếu chọn Option A (MVP):
```
1. Bạn trả lời 6 câu hỏi trên
2. Tôi code Week 2 (40 giờ)
3. Bạn test và feedback
4. Tôi fix bugs
5. Deploy lên production
6. Training và documentation
```

### Nếu chọn Option B (Full):
```
1. Bạn trả lời 6 câu hỏi trên
2. Bạn chuẩn bị Zalo OA + Telegram Bot
3. Tôi code Week 2 (40 giờ)
4. Bạn test Week 2
5. Tôi code Week 3 (40 giờ)
6. Bạn test Week 3
7. Tôi code Week 4 (20 giờ)
8. Final testing
9. Deploy
10. Training
```

## ⚠️ Rủi Ro & Lưu Ý

### Rủi Ro Kỹ Thuật:
- Zalo OA API có thể thay đổi
- Telegram Bot có rate limit
- Race condition khó test
- Webhook có thể fail

### Rủi Ro Nghiệp Vụ:
- Tài xế không follow OA/Bot
- Tài xế không nhận đơn
- Nhiều tài xế nhận cùng lúc
- Admin quên gán đơn

### Giải Pháp:
- Có backup plan (SMS)
- Training tài xế kỹ
- Test kỹ trước khi deploy
- Monitor logs thường xuyên

## 📚 Tài Liệu Tham Khảo

- [Zalo OA API Documentation](https://developers.zalo.me/docs/official-account)
- [Telegram Bot API](https://core.telegram.org/bots/api)
- [WordPress AJAX](https://codex.wordpress.org/AJAX_in_Plugins)

---

## 🎬 Hành Động Tiếp Theo

**Vui lòng trả lời:**

```
Option: [A/B/C/D]
Telegram Bot: [Có/Chưa]
Zalo OA: [Có/Chưa/Không cần]
HTTPS: [Có/Chưa]
Ngân sách: [Số tiền hoặc số giờ]
Deadline: [Ngày/tháng/năm]
```

**Sau khi nhận được câu trả lời, tôi sẽ:**
- Lập kế hoạch chi tiết
- Bắt đầu code ngay
- Cung cấp timeline cụ thể
- Hỗ trợ setup Telegram/Zalo nếu cần

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
