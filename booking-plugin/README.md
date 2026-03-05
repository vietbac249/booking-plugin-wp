# Plugin Đặt Xe WordPress

**Tác giả**: Nguyễn Việt Bắc  
**Bản quyền**: © 2026 Nguyễn Việt Bắc. All Rights Reserved.

---

Plugin đặt xe cho WordPress với 2 tab: Sân bay Nội Bài và Đường dài.

## Tính năng

### Tab 1: Sân bay Nội Bài
- Nhập điểm đi tự do
- Điểm đến mặc định là Sân bay Nội Bài
- Nút đảo chiều để đổi từ sân bay về điểm đến
- Tính năng đi 2 chiều (giảm giá 10%)
- Tùy chọn xuất hóa đơn VAT (10%)
- Tự động tính khoảng cách và giá tiền

### Tab 2: Đường dài
- Nhập điểm đi và điểm đến tự do
- Tùy chọn xuất hóa đơn VAT (10%)
- Tự động tính khoảng cách theo Google Maps
- Tính giá tiền dựa trên khoảng cách

## Cài đặt

### Bước 1: Upload plugin
1. Nén thư mục `booking-plugin` thành file ZIP
2. Vào WordPress Admin → Plugins → Add New → Upload Plugin
3. Chọn file ZIP và nhấn Install Now
4. Nhấn Activate Plugin

### Bước 2: Cấu hình
1. Vào menu **Đặt Xe** trong WordPress Admin
2. Nhập Google Maps API Key (xem hướng dẫn bên dưới)
3. Cấu hình giá cước theo nhu cầu
4. Nhấn **Lưu Cài Đặt**

### Bước 3: Sử dụng
Thêm shortcode vào trang hoặc bài viết:
```
[dat_xe]
```

## Lấy Google Maps API Key

1. Truy cập: https://console.cloud.google.com/
2. Tạo project mới hoặc chọn project có sẵn
3. Bật các APIs sau:
   - Maps JavaScript API
   - Places API
   - Distance Matrix API
4. Vào **Credentials** → Create Credentials → API Key
5. Copy API Key và paste vào trang cài đặt plugin

## Cấu trúc thư mục

```
booking-plugin/
├── booking-plugin.php          # File chính của plugin
├── assets/
│   ├── css/
│   │   └── style.css          # CSS cho form đặt xe
│   └── js/
│       └── script.js          # JavaScript xử lý logic
├── templates/
│   ├── booking-form.php       # Template form đặt xe
│   └── admin-settings.php     # Trang cài đặt admin
└── README.md                  # File này
```

## Cấu hình giá mặc định

- **Sân bay**: 15,000 VNĐ/km
- **Đường dài**: 12,000 VNĐ/km
- **Hệ số đi 2 chiều**: 1.8 (giảm 10%)
- **VAT**: 10%

Có thể thay đổi trong trang cài đặt admin.

## Tùy chỉnh

### Thay đổi màu sắc
Chỉnh sửa file `assets/css/style.css`, tìm màu `#5b3a9d` và thay đổi.

### Thay đổi logic tính giá
Chỉnh sửa file `booking-plugin.php` trong hàm `ajax_calculate_distance()`.

## Yêu cầu hệ thống

- WordPress 5.0 trở lên
- PHP 7.0 trở lên
- Google Maps API Key (có bật Distance Matrix API)

## Hỗ trợ

Nếu có vấn đề, vui lòng liên hệ qua website: https://noibai.vn

---

## Bản Quyền

**© 2026 Nguyễn Việt Bắc. All Rights Reserved.**

Tất cả mã nguồn, tài liệu, thiết kế trong plugin này thuộc bản quyền của Nguyễn Việt Bắc.

### Giấy Phép Sử Dụng
- **Sử dụng cá nhân**: Miễn phí
- **Sử dụng thương mại**: Cần mua license
- **Phân phối lại**: Không được phép
- **Chỉnh sửa**: Được phép cho mục đích cá nhân

### Liên Hệ
**Email**: nguyenvietbac@example.com  
**Website**: https://noibai.vn
