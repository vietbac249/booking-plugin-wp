# ĐỀ ÁN PHASE 2: HỆ THỐNG QUẢN TRỊ VÀ THỐNG KÊ NÂNG CAO

---

## MỤC LỤC

1. [Tổng quan Phase 2](#1-tổng-quan-phase-2)
2. [Kiến trúc hệ thống](#2-kiến-trúc-hệ-thống)
3. [Module 1: Quản lý đơn hàng](#3-module-1-quản-lý-đơn-hàng)
4. [Module 2: Quản lý tài xế](#4-module-2-quản-lý-tài-xế)
5. [Module 3: Dashboard thống kê](#5-module-3-dashboard-thống-kê)
6. [Module 4: Bảng xếp hạng](#6-module-4-bảng-xếp-hạng)
7. [Module 5: Hợp đồng điện tử](#7-module-5-hợp-đồng-điện-tử)
8. [Module 6: Đánh giá & Feedback](#8-module-6-đánh-giá--feedback)
9. [Cơ sở dữ liệu](#9-cơ-sở-dữ-liệu)
10. [Quy trình phát triển](#10-quy-trình-phát-triển)

---

## 1. TỔNG QUAN PHASE 2

### 1.1. Mục tiêu
Xây dựng hệ thống quản trị toàn diện cho dịch vụ đặt xe với:
- Quản lý đơn hàng chi tiết
- Quản lý tài xế và hợp đồng
- Thống kê và báo cáo
- Bảng xếp hạng và điểm thưởng
- Đánh giá chất lượng dịch vụ

### 1.2. Đối tượng sử dụng
- **Admin**: Quản lý toàn bộ hệ thống
- **Tài xế**: Nhận đơn, cập nhật trạng thái
- **Khách hàng**: Đánh giá, xem lịch sử

### 1.3. Tính năng chính
✅ Dashboard thống kê real-time
✅ Quản lý đơn hàng (pending, confirmed, completed, cancelled)
✅ Quản lý tài xế (đăng ký, hợp đồng, xác minh)
✅ Bảng xếp hạng tài xế
✅ Hệ thống điểm thưởng
✅ Đánh giá và feedback
✅ Báo cáo chi tiết (ngày, tuần, tháng, năm)
✅ Hợp đồng điện tử có giá trị pháp lý

---

## 2. KIẾN TRÚC HỆ THỐNG

### 2.1. Sơ đồ tổng thể

```
┌─────────────────────────────────────────────────────────┐
│                    ADMIN DASHBOARD                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Thống kê   │  │  Quản lý đơn │  │  Quản lý tài │ │
│  │   Dashboard  │  │    hàng      │  │     xế       │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │  Bảng xếp    │  │   Hợp đồng   │  │   Đánh giá   │ │
│  │    hạng      │  │   điện tử    │  │   & Review   │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    DATABASE LAYER                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Bookings   │  │    Drivers   │  │   Contracts  │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Reviews    │  │    Points    │  │   Analytics  │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────┘
```

### 2.2. Tech Stack
- **Backend**: PHP 7.4+, WordPress
- **Frontend**: HTML5, CSS3, JavaScript (jQuery), Chart.js
- **Database**: MySQL 5.7+
- **APIs**: Telegram, Zalo, Email
- **Security**: SSL, JWT, Data Encryption

---

## 3. MODULE 1: QUẢN LÝ ĐƠN HÀNG

### 3.1. Trạng thái đơn hàng

```
PENDING → CONFIRMED → IN_PROGRESS → COMPLETED
    ↓         ↓            ↓
CANCELLED  CANCELLED  CANCELLED
```

### 3.2. Chức năng

#### A. Danh sách đơn hàng
- Hiển thị tất cả đơn hàng
- Filter theo:
  - Trạng thái (pending, confirmed, completed, cancelled)
  - Ngày tạo
  - Tài xế
  - Khách hàng
- Search theo: Mã đơn, SĐT, Tên khách
- Pagination (20 đơn/trang)

#### B. Chi tiết đơn hàng
- Thông tin khách hàng
- Thông tin chuyến đi
- Thông tin tài xế (nếu đã nhận)
- Lịch sử thay đổi trạng thái
- Timeline xử lý đơn

#### C. Cập nhật trạng thái
- Admin có thể thay đổi trạng thái
- Ghi log mỗi lần thay đổi
- Gửi thông báo cho khách hàng và tài xế

#### D. Hủy đơn
- Chọn lý do hủy:
  - Khách hàng hủy
  - Tài xế hủy
  - Không tìm được tài xế
  - Thời tiết xấu
  - Lý do khác (nhập tay)
- Ghi nhận lý do vào database
- Thống kê lý do hủy phổ biến

### 3.3. Giao diện

```
┌─────────────────────────────────────────────────────────┐
│  QUẢN LÝ ĐƠN HÀNG                          [+ Tạo mới]  │
├─────────────────────────────────────────────────────────┤
│  Filter: [Tất cả ▼] [Hôm nay ▼] [Tìm kiếm...]         │
├─────────────────────────────────────────────────────────┤
│  Mã đơn  │ Khách hàng │ Tài xế │ Giá │ Trạng thái      │
│  #001    │ Nguyễn A   │ Tài xế │450K │ ✅ Hoàn thành   │
│  #002    │ Trần B     │ -      │350K │ ⏳ Chờ xác nhận │
│  #003    │ Lê C       │ Tài xế │500K │ 🚗 Đang đi      │
└─────────────────────────────────────────────────────────┘
```

---


## 4. MODULE 2: QUẢN LÝ TÀI XẾ

### 4.1. Đăng ký tài xế

#### A. Form đăng ký
- **Thông tin cá nhân**:
  - Họ và tên
  - CMND/CCCD
  - Ngày sinh
  - Địa chỉ
  - Số điện thoại
  - Email
  
- **Thông tin xe**:
  - Loại xe (4 chỗ, 7 chỗ, 16 chỗ...)
  - Biển số xe
  - Màu xe
  - Năm sản xuất
  - Hãng xe
  
- **Giấy tờ cần thiết**:
  - CMND/CCCD (ảnh 2 mặt)
  - Bằng lái xe (ảnh 2 mặt)
  - Đăng ký xe (ảnh)
  - Bảo hiểm xe (ảnh)
  - Giấy khám sức khỏe (ảnh)
  - Ảnh chân dung

#### B. Xác minh tài xế
- Admin kiểm tra giấy tờ
- Phê duyệt hoặc từ chối
- Gửi email thông báo kết quả
- Nếu phê duyệt → Chuyển sang bước ký hợp đồng

### 4.2. Danh sách tài xế

```
┌─────────────────────────────────────────────────────────┐
│  QUẢN LÝ TÀI XẾ                            [+ Thêm mới] │
├─────────────────────────────────────────────────────────┤
│  Filter: [Tất cả ▼] [Đang hoạt động ▼] [Tìm kiếm...]   │
├─────────────────────────────────────────────────────────┤
│  ID │ Tên │ SĐT │ Xe │ Điểm │ Đơn │ Trạng thái         │
│  01 │ A   │ 091 │ 4c │ 4.8★ │ 150 │ ✅ Hoạt động       │
│  02 │ B   │ 092 │ 7c │ 4.5★ │ 89  │ ⏸️ Tạm dừng        │
│  03 │ C   │ 093 │ 4c │ 4.9★ │ 200 │ ✅ Hoạt động       │
└─────────────────────────────────────────────────────────┘
```

### 4.3. Trạng thái tài xế
- **Chờ xác minh**: Mới đăng ký, chưa kiểm tra
- **Đã xác minh**: Đã kiểm tra giấy tờ
- **Chờ ký hợp đồng**: Đã xác minh, chưa ký hợp đồng
- **Đang hoạt động**: Đã ký hợp đồng, có thể nhận đơn
- **Tạm dừng**: Tạm thời không nhận đơn
- **Bị khóa**: Vi phạm quy định
- **Ngừng hoạt động**: Đã nghỉ việc

### 4.4. Hồ sơ tài xế

```
┌─────────────────────────────────────────────────────────┐
│  HỒ SƠ TÀI XẾ: NGUYỄN VĂN A                            │
├─────────────────────────────────────────────────────────┤
│  📸 Ảnh đại diện                                        │
│  ⭐ Đánh giá: 4.8/5 (150 đánh giá)                     │
│  🚗 Xe: Toyota Vios 4 chỗ - 30A-12345                  │
│  📞 SĐT: 0912345678                                     │
│  📧 Email: nguyenvana@gmail.com                         │
│  📍 Địa chỉ: Hà Nội                                     │
│  📅 Ngày tham gia: 01/01/2024                           │
│  ✅ Trạng thái: Đang hoạt động                          │
│                                                         │
│  THỐNG KÊ:                                              │
│  - Tổng đơn: 150                                        │
│  - Hoàn thành: 145 (96.7%)                              │
│  - Hủy: 5 (3.3%)                                        │
│  - Điểm thưởng: 1,450                                   │
│  - Xếp hạng: #3                                         │
│                                                         │
│  GIẤY TỜ:                                               │
│  ✅ CMND: 001234567890                                  │
│  ✅ Bằng lái: B2 - 012345678                            │
│  ✅ Đăng ký xe: 30A-12345                               │
│  ✅ Bảo hiểm: Còn hạn đến 31/12/2024                    │
│                                                         │
│  HỢP ĐỒNG:                                              │
│  📄 Hợp đồng #HD001 - Ký ngày 01/01/2024               │
│  [Xem hợp đồng] [Gia hạn] [Chấm dứt]                   │
└─────────────────────────────────────────────────────────┘
```

---

## 5. MODULE 3: DASHBOARD THỐNG KÊ

### 5.1. Tổng quan

```
┌─────────────────────────────────────────────────────────┐
│  DASHBOARD                        [Hôm nay ▼]           │
├─────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │ TỔNG ĐƠN     │  │ HOÀN THÀNH   │  │ DOANH THU    │ │
│  │    150       │  │    145       │  │  45,000,000đ │ │
│  │  +12% ↑      │  │  +8% ↑       │  │  +15% ↑      │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │ TÀI XẾ       │  │ KHÁCH HÀNG   │  │ ĐÁNH GIÁ TB  │ │
│  │    25        │  │    120       │  │    4.7★      │ │
│  │  +2 ↑        │  │  +15 ↑       │  │  +0.2 ↑      │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
├─────────────────────────────────────────────────────────┤
│  BIỂU ĐỒ DOANH THU THEO THÁNG                          │
│  [Chart.js Line Chart]                                  │
├─────────────────────────────────────────────────────────┤
│  TOP 5 TÀI XẾ XUẤT SẮC                                  │
│  1. Nguyễn Văn A - 50 đơn - 4.9★                       │
│  2. Trần Văn B - 45 đơn - 4.8★                         │
│  3. Lê Văn C - 40 đơn - 4.7★                           │
└─────────────────────────────────────────────────────────┘
```

### 5.2. Báo cáo chi tiết

#### A. Báo cáo theo thời gian
- **Hôm nay**: Thống kê trong ngày
- **Tuần này**: 7 ngày gần nhất
- **Tháng này**: Tháng hiện tại
- **Năm này**: Năm hiện tại
- **Tùy chỉnh**: Chọn khoảng thời gian

#### B. Các chỉ số thống kê
- Tổng số đơn hàng
- Đơn hoàn thành
- Đơn bị hủy (+ lý do)
- Doanh thu
- Số khách hàng mới
- Số tài xế mới
- Đánh giá trung bình
- Thời gian xử lý trung bình

#### C. Biểu đồ
- **Line Chart**: Doanh thu theo thời gian
- **Bar Chart**: Số đơn theo loại xe
- **Pie Chart**: Tỷ lệ trạng thái đơn hàng
- **Area Chart**: Số khách hàng mới theo tháng

### 5.3. Export báo cáo
- Export Excel (.xlsx)
- Export PDF
- Export CSV
- Gửi email báo cáo định kỳ

---

## 6. MODULE 4: BẢNG XẾP HẠNG

### 6.1. Hệ thống điểm

#### A. Cách tính điểm
```
Điểm = (Số đơn hoàn thành × 10) 
     + (Đánh giá trung bình × 100)
     - (Số đơn hủy × 20)
     + Điểm thưởng
```

#### B. Điểm thưởng
- Hoàn thành 10 đơn liên tiếp: +50 điểm
- Đạt 5 sao: +100 điểm
- Không hủy đơn trong tháng: +200 điểm
- Nhận đơn nhanh (< 5 phút): +10 điểm/đơn

### 6.2. Bảng xếp hạng

```
┌─────────────────────────────────────────────────────────┐
│  BẢNG XẾP HẠNG TÀI XẾ                  [Tháng này ▼]   │
├─────────────────────────────────────────────────────────┤
│  #  │ Tài xế │ Đơn │ Đánh giá │ Điểm │ Hạng            │
│  🥇 │ A      │ 50  │ 4.9★     │2,450 │ 💎 Kim cương    │
│  🥈 │ B      │ 45  │ 4.8★     │2,180 │ 🏆 Vàng         │
│  🥉 │ C      │ 40  │ 4.7★     │1,980 │ 🏆 Vàng         │
│  4  │ D      │ 35  │ 4.6★     │1,710 │ 🥈 Bạc          │
│  5  │ E      │ 30  │ 4.5★     │1,450 │ 🥈 Bạc          │
└─────────────────────────────────────────────────────────┘
```

### 6.3. Hạng tài xế

| Hạng | Điểm | Ưu đãi |
|------|------|--------|
| 💎 Kim cương | 2,000+ | Ưu tiên nhận đơn cao, Thưởng 10% |
| 🏆 Vàng | 1,500-1,999 | Ưu tiên nhận đơn, Thưởng 5% |
| 🥈 Bạc | 1,000-1,499 | Nhận đơn bình thường |
| 🥉 Đồng | 500-999 | Nhận đơn bình thường |
| 🆕 Mới | 0-499 | Hỗ trợ đào tạo |

### 6.4. Phần thưởng
- **Top 1**: 5,000,000đ + Cúp vàng
- **Top 2**: 3,000,000đ + Cúp bạc
- **Top 3**: 2,000,000đ + Cúp đồng
- **Top 4-10**: 500,000đ
- **Tài xế xuất sắc tháng**: 1,000,000đ

---


## 7. MODULE 5: HỢP ĐỒNG ĐIỆN TỬ

### 7.1. Mục đích
- Đảm bảo tính pháp lý
- Ràng buộc trách nhiệm 2 bên
- Bảo vệ quyền lợi công ty và tài xế
- Nâng cao chất lượng dịch vụ

### 7.2. Nội dung hợp đồng

#### A. Thông tin các bên
- **Bên A (Công ty)**:
  - Tên công ty
  - Mã số thuế
  - Địa chỉ
  - Người đại diện
  - Số điện thoại, Email
  
- **Bên B (Tài xế)**:
  - Họ và tên
  - CMND/CCCD
  - Địa chỉ
  - Số điện thoại, Email
  - Thông tin xe

#### B. Điều khoản chính

**Điều 1: Phạm vi công việc**
- Tài xế nhận và thực hiện các đơn đặt xe qua hệ thống
- Đảm bảo xe sạch sẽ, an toàn
- Đúng giờ hẹn (±10 phút)
- Thái độ lịch sự, chuyên nghiệp

**Điều 2: Quyền lợi tài xế**
- Nhận 80% giá trị đơn hàng
- Được hỗ trợ bảo hiểm
- Được đào tạo miễn phí
- Được thưởng theo thành tích

**Điều 3: Nghĩa vụ tài xế**
- Giữ gìn xe cẩn thận
- Không từ chối đơn hàng vô lý do
- Không hủy đơn sau khi đã nhận
- Tuân thủ luật giao thông
- Bảo mật thông tin khách hàng

**Điều 4: Chế độ phạt**
- Hủy đơn: -50,000đ/lần
- Đến muộn > 30 phút: -100,000đ
- Khách hàng khiếu nại: -200,000đ
- Vi phạm nghiêm trọng: Chấm dứt hợp đồng

**Điều 5: Thời hạn hợp đồng**
- Thời hạn: 12 tháng
- Có thể gia hạn
- Chấm dứt trước hạn: Báo trước 30 ngày

**Điều 6: Giải quyết tranh chấp**
- Thương lượng hòa giải
- Nếu không được → Tòa án có thẩm quyền

#### C. Chữ ký điện tử
- Tài xế ký bằng OTP qua SMS
- Lưu trữ chữ ký số
- Có giá trị pháp lý theo Luật Giao dịch điện tử

### 7.3. Quy trình ký hợp đồng

```
1. Tài xế đăng ký
   ↓
2. Admin xác minh giấy tờ
   ↓
3. Hệ thống tạo hợp đồng tự động
   ↓
4. Gửi hợp đồng qua email
   ↓
5. Tài xế đọc và đồng ý
   ↓
6. Nhập OTP để ký
   ↓
7. Hợp đồng có hiệu lực
   ↓
8. Tài xế có thể nhận đơn
```

### 7.4. Quản lý hợp đồng

```
┌─────────────────────────────────────────────────────────┐
│  QUẢN LÝ HỢP ĐỒNG                                       │
├─────────────────────────────────────────────────────────┤
│  Mã HĐ │ Tài xế │ Ngày ký │ Hết hạn │ Trạng thái      │
│  HD001 │ A      │01/01/24 │31/12/24 │ ✅ Còn hiệu lực │
│  HD002 │ B      │15/02/24 │14/02/25 │ ✅ Còn hiệu lực │
│  HD003 │ C      │01/03/24 │28/02/25 │ ⚠️ Sắp hết hạn  │
│  HD004 │ D      │10/01/24 │09/01/25 │ ❌ Đã chấm dứt  │
└─────────────────────────────────────────────────────────┘
```

### 7.5. Lưu trữ hợp đồng
- Lưu file PDF trên server
- Backup định kỳ
- Mã hóa dữ liệu nhạy cảm
- Lưu trữ tối thiểu 5 năm (theo quy định pháp luật)

---

## 8. MODULE 6: ĐÁNH GIÁ & FEEDBACK

### 8.1. Đánh giá tài xế

#### A. Sau khi hoàn thành đơn
- Khách hàng nhận SMS/Email yêu cầu đánh giá
- Form đánh giá:
  - Số sao: 1-5 ⭐
  - Tiêu chí:
    - Đúng giờ
    - Thái độ lịch sự
    - Xe sạch sẽ
    - Lái xe an toàn
  - Nhận xét (tùy chọn)
  - Ảnh (tùy chọn)

#### B. Hiển thị đánh giá
```
┌─────────────────────────────────────────────────────────┐
│  ĐÁNH GIÁ TÀI XẾ: NGUYỄN VĂN A                         │
│  ⭐⭐⭐⭐⭐ 4.8/5 (150 đánh giá)                        │
├─────────────────────────────────────────────────────────┤
│  5 sao: ████████████████████ 120 (80%)                 │
│  4 sao: ████ 20 (13%)                                   │
│  3 sao: ██ 7 (5%)                                       │
│  2 sao: █ 2 (1%)                                        │
│  1 sao: █ 1 (1%)                                        │
├─────────────────────────────────────────────────────────┤
│  NHẬN XÉT GẦN NHẤT:                                     │
│  ⭐⭐⭐⭐⭐ Trần Văn B - 09/02/2026                     │
│  "Tài xế rất nhiệt tình, đúng giờ, xe sạch sẽ"         │
│                                                         │
│  ⭐⭐⭐⭐⭐ Lê Thị C - 08/02/2026                       │
│  "Lái xe an toàn, thái độ tốt"                         │
└─────────────────────────────────────────────────────────┘
```

### 8.2. Xử lý đánh giá thấp
- Đánh giá < 3 sao → Thông báo cho admin
- Admin liên hệ tài xế để tìm hiểu
- Nếu có lỗi → Nhắc nhở, đào tạo lại
- Nếu vi phạm nghiêm trọng → Phạt hoặc chấm dứt hợp đồng

### 8.3. Khách hàng thân thiết
- Khách hàng đặt > 10 đơn → VIP
- Ưu đãi:
  - Giảm 5% mọi đơn hàng
  - Ưu tiên tài xế tốt nhất
  - Hỗ trợ 24/7

---

## 9. CƠ SỞ DỮ LIỆU

### 9.1. Database Schema

#### Table: wp_bookings
```sql
CREATE TABLE wp_bookings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    booking_code VARCHAR(20) UNIQUE,
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    customer_email VARCHAR(100),
    from_location TEXT,
    to_location TEXT,
    stops TEXT, -- JSON array
    car_type VARCHAR(50),
    trip_type VARCHAR(20), -- airport, long_distance
    is_round_trip BOOLEAN DEFAULT 0,
    has_vat BOOLEAN DEFAULT 0,
    trip_datetime DATETIME,
    distance DECIMAL(10,2),
    price DECIMAL(15,2),
    status VARCHAR(20), -- pending, confirmed, in_progress, completed, cancelled
    driver_id BIGINT,
    cancel_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES wp_drivers(id)
);
```

#### Table: wp_drivers
```sql
CREATE TABLE wp_drivers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100),
    id_card VARCHAR(20),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    birth_date DATE,
    car_type VARCHAR(50),
    car_plate VARCHAR(20),
    car_brand VARCHAR(50),
    car_color VARCHAR(30),
    car_year INT,
    license_number VARCHAR(20),
    status VARCHAR(20), -- pending, verified, active, suspended, inactive
    rating DECIMAL(3,2) DEFAULT 0,
    total_trips INT DEFAULT 0,
    completed_trips INT DEFAULT 0,
    cancelled_trips INT DEFAULT 0,
    points INT DEFAULT 0,
    rank VARCHAR(20), -- diamond, gold, silver, bronze, new
    joined_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Table: wp_driver_documents
```sql
CREATE TABLE wp_driver_documents (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    driver_id BIGINT,
    document_type VARCHAR(50), -- id_card, license, registration, insurance
    file_path VARCHAR(255),
    verified BOOLEAN DEFAULT 0,
    verified_by BIGINT,
    verified_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES wp_drivers(id)
);
```

#### Table: wp_contracts
```sql
CREATE TABLE wp_contracts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    contract_code VARCHAR(20) UNIQUE,
    driver_id BIGINT,
    start_date DATE,
    end_date DATE,
    status VARCHAR(20), -- active, expired, terminated
    signed_at TIMESTAMP,
    signature_otp VARCHAR(10),
    contract_file VARCHAR(255),
    created_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES wp_drivers(id)
);
```

#### Table: wp_reviews
```sql
CREATE TABLE wp_reviews (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT,
    driver_id BIGINT,
    customer_name VARCHAR(100),
    rating INT, -- 1-5
    on_time_rating INT,
    attitude_rating INT,
    cleanliness_rating INT,
    safety_rating INT,
    comment TEXT,
    images TEXT, -- JSON array
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES wp_bookings(id),
    FOREIGN KEY (driver_id) REFERENCES wp_drivers(id)
);
```

#### Table: wp_driver_points
```sql
CREATE TABLE wp_driver_points (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    driver_id BIGINT,
    points INT,
    reason VARCHAR(255),
    booking_id BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES wp_drivers(id)
);
```

#### Table: wp_booking_logs
```sql
CREATE TABLE wp_booking_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    booking_id BIGINT,
    old_status VARCHAR(20),
    new_status VARCHAR(20),
    changed_by BIGINT,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES wp_bookings(id)
);
```

---

## 10. QUY TRÌNH PHÁT TRIỂN

### 10.1. Timeline (12 tuần)

#### Tuần 1-2: Thiết kế & Chuẩn bị
- Thiết kế database
- Thiết kế UI/UX
- Setup môi trường dev
- Tạo mockup

#### Tuần 3-4: Module Quản lý đơn hàng
- CRUD đơn hàng
- Filter & search
- Cập nhật trạng thái
- Hủy đơn & lý do

#### Tuần 5-6: Module Quản lý tài xế
- Form đăng ký tài xế
- Upload giấy tờ
- Xác minh tài xế
- Quản lý hồ sơ

#### Tuần 7-8: Module Hợp đồng
- Template hợp đồng
- Chữ ký điện tử (OTP)
- Lưu trữ hợp đồng
- Quản lý hợp đồng

#### Tuần 9: Module Dashboard
- Thống kê tổng quan
- Biểu đồ (Chart.js)
- Báo cáo chi tiết
- Export Excel/PDF

#### Tuần 10: Module Xếp hạng & Điểm
- Hệ thống tính điểm
- Bảng xếp hạng
- Phần thưởng
- Hạng tài xế

#### Tuần 11: Module Đánh giá
- Form đánh giá
- Hiển thị đánh giá
- Xử lý đánh giá thấp
- Khách hàng thân thiết

#### Tuần 12: Testing & Deploy
- Unit testing
- Integration testing
- Bug fixing
- Deploy production

### 10.2. Công nghệ

- **Backend**: PHP 7.4+, WordPress
- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Charts**: Chart.js
- **Export**: PHPSpreadsheet (Excel), TCPDF (PDF)
- **Security**: JWT, SSL, Data Encryption
- **Database**: MySQL 5.7+

### 10.3. Team

- **1 Project Manager**: Quản lý dự án
- **2 Backend Developers**: PHP, WordPress
- **1 Frontend Developer**: HTML, CSS, JS
- **1 UI/UX Designer**: Thiết kế giao diện
- **1 QA Tester**: Kiểm thử
- **1 DevOps**: Deploy, maintain

---

## 11. BẢO MẬT & PHÁP LÝ

### 11.1. Bảo mật dữ liệu
- Mã hóa thông tin nhạy cảm (CMND, SĐT)
- SSL/TLS cho mọi kết nối
- Backup dữ liệu hàng ngày
- Log mọi thao tác quan trọng

### 11.2. Tuân thủ pháp luật
- Luật Giao dịch điện tử
- Luật Bảo vệ dữ liệu cá nhân
- Luật Giao thông đường bộ
- Luật Lao động (hợp đồng tài xế)

### 11.3. Quyền riêng tư
- Chính sách bảo mật rõ ràng
- Khách hàng đồng ý thu thập dữ liệu
- Quyền xóa dữ liệu cá nhân
- Không chia sẻ dữ liệu cho bên thứ 3

---

## 12. CHI PHÍ ƯỚC TÍNH

### 12.1. Chi phí phát triển

| Hạng mục | Chi phí |
|----------|---------|
| Nhân sự (6 người × 3 tháng) | 180,000,000đ |
| Server & Hosting (1 năm) | 12,000,000đ |
| SSL Certificate | 2,000,000đ |
| Google Maps API | 5,000,000đ/năm |
| Backup & Storage | 3,000,000đ/năm |
| **TỔNG** | **202,000,000đ** |

### 12.2. Chi phí vận hành (tháng)

| Hạng mục | Chi phí |
|----------|---------|
| Server | 1,000,000đ |
| APIs | 500,000đ |
| Backup | 300,000đ |
| Support | 2,000,000đ |
| **TỔNG** | **3,800,000đ/tháng** |

---

## 13. KẾT LUẬN

### 13.1. Lợi ích

**Cho Admin:**
- Quản lý toàn diện
- Thống kê chi tiết
- Tự động hóa quy trình
- Giảm chi phí vận hành

**Cho Tài xế:**
- Nhận đơn dễ dàng
- Thu nhập minh bạch
- Được đào tạo và hỗ trợ
- Cơ hội thăng tiến

**Cho Khách hàng:**
- Dịch vụ chất lượng
- Tài xế uy tín
- Giá cả minh bạch
- An toàn và tin cậy

### 13.2. Tính khả thi
- ✅ Công nghệ phổ biến, dễ triển khai
- ✅ Chi phí hợp lý
- ✅ Thời gian phát triển 3 tháng
- ✅ Có thể mở rộng sau này

### 13.3. Rủi ro & Giải pháp

| Rủi ro | Giải pháp |
|--------|-----------|
| Tài xế không tuân thủ hợp đồng | Phạt nặng, chấm dứt hợp đồng |
| Dữ liệu bị mất | Backup định kỳ, nhiều bản sao |
| Bảo mật bị xâm phạm | SSL, mã hóa, firewall |
| Tranh chấp pháp lý | Hợp đồng rõ ràng, luật sư tư vấn |

---

**Ngày hoàn thành**: 09/02/2026
**Phiên bản**: 2.0.0
**Tác giả**: [Tên của bạn]

---

*Tài liệu này mô tả chi tiết Phase 2 của dự án Hệ Thống Đặt Xe Nội Bài.*
