# Roadmap: Tính Năng Gán Đơn Hàng - Full Feature

## 📅 Timeline: 4 Tuần (160 giờ)

**Bắt đầu:** Ngay bây giờ  
**Kết thúc dự kiến:** 4 tuần sau  
**Phương pháp:** Agile - Làm từng tuần, test và feedback

---

## ✅ Week 1: Foundation (HOÀN THÀNH)

**Thời gian:** 12 giờ  
**Trạng thái:** ✅ Done

### Đã Hoàn Thành:
- [x] Database schema design
- [x] Update script (`update-database-assignment.php`)
- [x] Documentation (`DE-XUAT-GAN-DON-HANG.md`)
- [x] Roadmap (`HUONG-DAN-TRIEN-KHAI-GAN-DON.md`)

### Deliverables:
- ✅ 4 bảng database mới/cập nhật
- ✅ 2 tài liệu hướng dẫn
- ✅ 1 script migration

---

## 🚀 Week 2: Core Features (BẮT ĐẦU)

**Thời gian:** 40 giờ  
**Trạng thái:** 🔄 In Progress

### Backend (20 giờ)

#### 1. Notification System (8 giờ)
**File:** `includes/notifications.php`
- [ ] Class `Booking_Notifications`
- [ ] Function: `send_telegram_notification()`
- [ ] Function: `send_zalo_notification()`
- [ ] Function: `send_group_notification()`
- [ ] Function: `generate_accept_link()`
- [ ] Error handling & logging

#### 2. AJAX Handlers (12 giờ)
**File:** `booking-plugin.php`
- [ ] `ajax_search_drivers()` - Autocomplete search
- [ ] `ajax_assign_to_driver()` - Gán cho tài xế
- [ ] `ajax_assign_to_group()` - Gán cho group
- [ ] `ajax_accept_booking()` - Tài xế nhận đơn
- [ ] `ajax_reject_booking()` - Tài xế từ chối
- [ ] Security: Nonce validation
- [ ] Database transactions

### Frontend (20 giờ)

#### 3. Admin Orders Page Update (10 giờ)
**File:** `templates/admin-orders.php`
- [ ] Thêm cột "Trạng thái Gán"
- [ ] Nút "Gán Tài Xế" (chỉ hiện với status = pending)
- [ ] Nút "Gán Group" (chỉ hiện với status = pending)
- [ ] Badge hiển thị trạng thái
- [ ] Filter theo trạng thái gán

#### 4. Modal Gán Tài Xế (5 giờ)
**File:** `templates/admin-orders.php` (inline)
- [ ] Modal popup
- [ ] Input autocomplete tìm tài xế
- [ ] Hiển thị: Avatar + Tên + SĐT + Rating
- [ ] Nút "Gán Cho Tài Xế"
- [ ] Loading state
- [ ] Success/Error messages

#### 5. Modal Gán Group (5 giờ)
**File:** `templates/admin-orders.php` (inline)
- [ ] Modal popup
- [ ] Dropdown chọn group
- [ ] Hiển thị: Tên group + Loại (Zalo/Telegram)
- [ ] Preview tin nhắn
- [ ] Nút "Gán Cho Group"
- [ ] Loading state

### CSS & JavaScript (10 giờ)

#### 6. Admin Styles (5 giờ)
**File:** `assets/css/admin-style.css`
- [ ] Modal styles
- [ ] Autocomplete dropdown
- [ ] Badge styles (assigned, accepted, etc.)
- [ ] Button styles
- [ ] Loading spinner
- [ ] Responsive design

#### 7. Admin Scripts (5 giờ)
**File:** `assets/js/admin-script.js`
- [ ] Modal open/close
- [ ] Autocomplete logic
- [ ] AJAX calls
- [ ] Form validation
- [ ] Success/Error handling
- [ ] Real-time updates

### Deliverables Week 2:
- ✅ Gán trực tiếp cho tài xế (working)
- ✅ Gửi thông báo Telegram (basic)
- ✅ UI/UX hoàn chỉnh
- ✅ AJAX working
- ⏳ Zalo integration (basic)

---

## 🎯 Week 3: Advanced Features

**Thời gian:** 40 giờ  
**Trạng thái:** ⏳ Pending

### Backend Advanced (20 giờ)

#### 8. Accept Booking Page (8 giờ)
**File:** `templates/driver-accept-booking.php`
- [ ] Public page (không cần login)
- [ ] Token validation
- [ ] Hiển thị thông tin đơn hàng
- [ ] Nút "Nhận Đơn" / "Từ Chối"
- [ ] Race condition handling
- [ ] Success/Error pages
- [ ] Redirect sau khi nhận

#### 9. Group Assignment Logic (8 giờ)
**File:** `booking-plugin.php`
- [ ] Send to multiple drivers
- [ ] First-come-first-served
- [ ] Lock mechanism (prevent double accept)
- [ ] Notification to others (đơn đã có người nhận)
- [ ] Timeout mechanism (15 phút)
- [ ] Auto-reassign nếu timeout

#### 10. Zalo OA Integration (4 giờ)
**File:** `includes/notifications.php`
- [ ] Zalo API client
- [ ] Send message to user
- [ ] Send message to group
- [ ] Handle errors
- [ ] Fallback to Telegram

### Frontend Advanced (10 giờ)

#### 11. Groups Management Page (10 giờ)
**File:** `templates/admin-notification-groups.php`
- [ ] Danh sách groups
- [ ] Thêm group mới (form)
- [ ] Sửa group
- [ ] Xóa group
- [ ] Test gửi tin nhắn
- [ ] Active/Inactive toggle
- [ ] Statistics (số đơn đã gán)

### Settings Integration (5 giờ)

#### 12. Settings Tab (5 giờ)
**File:** `templates/admin-settings.php`
- [ ] Tab "Thông Báo"
- [ ] Telegram Bot Token
- [ ] Zalo OA Access Token
- [ ] Default timeout (phút)
- [ ] Auto-reassign toggle
- [ ] Test connection buttons

### Testing & Bug Fixes (5 giờ)
- [ ] Test gán trực tiếp
- [ ] Test gán group
- [ ] Test race condition
- [ ] Test timeout
- [ ] Fix bugs

### Deliverables Week 3:
- ✅ Group assignment working
- ✅ Accept booking page
- ✅ Zalo integration
- ✅ Groups management
- ✅ Race condition handled
- ✅ Timeout mechanism

---

## 🎨 Week 4: Polish & Production

**Thời gian:** 20 giờ  
**Trạng thái:** ⏳ Pending

### UI/UX Improvements (8 giờ)

#### 13. Dashboard Updates (4 giờ)
**File:** `templates/admin-dashboard.php`
- [ ] Widget: Đơn hàng chờ gán
- [ ] Widget: Đơn hàng đã gán (chờ nhận)
- [ ] Widget: Tỷ lệ nhận đơn
- [ ] Chart: Thời gian nhận đơn trung bình

#### 14. Driver Profile (4 giờ)
**File:** `templates/admin-drivers.php`
- [ ] Thêm trường Telegram Chat ID
- [ ] Thêm trường Zalo User ID
- [ ] Hướng dẫn lấy Chat ID
- [ ] Test gửi tin nhắn
- [ ] Lịch sử nhận đơn

### Documentation (6 giờ)

#### 15. User Guides (6 giờ)
- [ ] `HUONG-DAN-GAN-DON-HANG.md` - Admin guide
- [ ] `HUONG-DAN-TAI-XE-NHAN-DON.md` - Driver guide
- [ ] `HUONG-DAN-SETUP-TELEGRAM.md` - Telegram setup
- [ ] `HUONG-DAN-SETUP-ZALO.md` - Zalo setup
- [ ] Video tutorials (optional)

### Security & Performance (4 giờ)

#### 16. Security Hardening (2 giờ)
- [ ] SQL injection prevention
- [ ] XSS prevention
- [ ] CSRF tokens
- [ ] Rate limiting
- [ ] Input sanitization

#### 17. Performance Optimization (2 giờ)
- [ ] Database indexes
- [ ] Query optimization
- [ ] Caching (transients)
- [ ] Lazy loading
- [ ] Minify CSS/JS

### Final Testing (2 giờ)
- [ ] End-to-end testing
- [ ] Load testing
- [ ] Security testing
- [ ] Browser compatibility
- [ ] Mobile responsive

### Deliverables Week 4:
- ✅ Production-ready code
- ✅ Complete documentation
- ✅ Security hardened
- ✅ Performance optimized
- ✅ Tested thoroughly

---

## 📦 Final Deliverables

### Code Files (15+ files)
1. `update-database-assignment.php` ✅
2. `includes/notifications.php` ⏳
3. `templates/admin-orders.php` (updated) ⏳
4. `templates/admin-notification-groups.php` ⏳
5. `templates/driver-accept-booking.php` ⏳
6. `templates/admin-settings.php` (updated) ⏳
7. `templates/admin-dashboard.php` (updated) ⏳
8. `templates/admin-drivers.php` (updated) ⏳
9. `assets/css/admin-style.css` (updated) ⏳
10. `assets/js/admin-script.js` (updated) ⏳
11. `booking-plugin.php` (updated) ⏳

### Documentation (6+ files)
1. `DE-XUAT-GAN-DON-HANG.md` ✅
2. `HUONG-DAN-TRIEN-KHAI-GAN-DON.md` ✅
3. `ROADMAP-GAN-DON-HANG-FULL.md` ✅
4. `HUONG-DAN-GAN-DON-HANG.md` ⏳
5. `HUONG-DAN-TAI-XE-NHAN-DON.md` ⏳
6. `HUONG-DAN-SETUP-TELEGRAM.md` ⏳
7. `HUONG-DAN-SETUP-ZALO.md` ⏳

### Database
- 4 tables created/updated ✅
- 10+ columns added ✅
- Indexes optimized ⏳

---

## 🎯 Success Metrics

### Functional Requirements
- [x] Admin có thể gán đơn cho tài xế
- [ ] Admin có thể gán đơn cho group
- [ ] Tài xế nhận được thông báo
- [ ] Tài xế có thể nhận/từ chối đơn
- [ ] Hệ thống ghi nhận trạng thái
- [ ] Tính doanh thu chính xác
- [ ] Không có double-accept
- [ ] Timeout working

### Non-Functional Requirements
- [ ] Response time < 2s
- [ ] 99% uptime
- [ ] Mobile responsive
- [ ] Secure (no vulnerabilities)
- [ ] Well documented
- [ ] Easy to use

---

## ⚠️ Risks & Mitigation

### Technical Risks
| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Zalo API changes | High | Medium | Use latest SDK, monitor changelog |
| Telegram rate limit | Medium | Low | Implement queue system |
| Race condition bugs | High | Medium | Use database locks, thorough testing |
| Performance issues | Medium | Low | Optimize queries, use caching |

### Business Risks
| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Drivers don't use | High | Medium | Training, incentives |
| Too many timeouts | Medium | Medium | Adjust timeout, notifications |
| Admin confusion | Low | Low | Good UI/UX, documentation |

---

## 📞 Support & Communication

### Weekly Check-ins
- **Monday**: Planning & priorities
- **Wednesday**: Progress update
- **Friday**: Demo & feedback

### Communication Channels
- **Urgent**: Telegram/Zalo
- **Questions**: Email/Chat
- **Bugs**: Issue tracker
- **Feedback**: Weekly meeting

---

## 🚀 Next Steps (Ngay Bây Giờ)

### Bước 1: Chuẩn Bị (Bạn làm)
- [ ] Chạy `update-database-assignment.php`
- [ ] Tạo Telegram Bot (nếu chưa có)
- [ ] Đăng ký Zalo OA (nếu chưa có)
- [ ] Backup database
- [ ] Backup code

### Bước 2: Development (Tôi làm)
- [ ] Tạo `includes/notifications.php`
- [ ] Cập nhật `booking-plugin.php` (AJAX handlers)
- [ ] Cập nhật `admin-orders.php` (UI)
- [ ] Cập nhật `admin-style.css`
- [ ] Cập nhật `admin-script.js`

### Bước 3: Testing (Cùng làm)
- [ ] Test gán tài xế
- [ ] Test gửi thông báo
- [ ] Test nhận đơn
- [ ] Fix bugs
- [ ] Iterate

---

**Bắt đầu Week 2 ngay bây giờ!** 🚀

Tôi sẽ tạo các file cần thiết trong vài phút tới...

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
