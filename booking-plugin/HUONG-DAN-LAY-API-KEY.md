# Hướng Dẫn Lấy Google Maps API Key

## Bước 1: Truy cập Google Cloud Console

1. Mở trình duyệt và truy cập: **https://console.cloud.google.com/**
2. Đăng nhập bằng tài khoản Google của bạn

## Bước 2: Tạo Project Mới

1. Nhấn vào dropdown **Select a project** ở góc trên bên trái
2. Nhấn **NEW PROJECT**
3. Đặt tên project: `Dat-Xe-Noi-Bai` (hoặc tên bạn muốn)
4. Nhấn **CREATE**
5. Đợi vài giây để project được tạo

## Bước 3: Bật Các API Cần Thiết

### 3.1. Bật Maps JavaScript API

1. Vào menu bên trái, chọn **APIs & Services** → **Library**
2. Tìm kiếm: `Maps JavaScript API`
3. Nhấn vào kết quả đầu tiên
4. Nhấn nút **ENABLE**

### 3.2. Bật Places API

1. Quay lại **Library**
2. Tìm kiếm: `Places API`
3. Nhấn vào kết quả
4. Nhấn **ENABLE**

### 3.3. Bật Distance Matrix API

1. Quay lại **Library**
2. Tìm kiếm: `Distance Matrix API`
3. Nhấn vào kết quả
4. Nhấn **ENABLE**

## Bước 4: Tạo API Key

1. Vào menu bên trái: **APIs & Services** → **Credentials**
2. Nhấn **+ CREATE CREDENTIALS** ở trên
3. Chọn **API key**
4. API key sẽ được tạo và hiển thị trong popup
5. **COPY** API key này (dạng: `AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX`)

## Bước 5: Bảo Mật API Key (Khuyến nghị)

1. Trong popup API key, nhấn **RESTRICT KEY**
2. Chọn tab **API restrictions**
3. Chọn **Restrict key**
4. Tích chọn 3 APIs:
   - ✅ Maps JavaScript API
   - ✅ Places API
   - ✅ Distance Matrix API
5. Nhấn **SAVE**

### Giới hạn theo Website (Tùy chọn)

1. Chọn tab **Application restrictions**
2. Chọn **HTTP referrers (web sites)**
3. Thêm domain của bạn:
   ```
   yourdomain.com/*
   *.yourdomain.com/*
   ```
4. Nhấn **SAVE**

## Bước 6: Cấu Hình Billing (Bắt buộc)

Google Maps API yêu cầu bật billing, nhưng có **$200 credit miễn phí mỗi tháng**.

1. Vào menu: **Billing**
2. Nhấn **LINK A BILLING ACCOUNT**
3. Chọn **CREATE BILLING ACCOUNT**
4. Điền thông tin thẻ tín dụng (không bị trừ tiền nếu dùng dưới $200/tháng)
5. Hoàn tất

## Bước 7: Cài Đặt API Key Vào Plugin

### Cách 1: Qua WordPress Admin (Khuyến nghị)

1. Đăng nhập WordPress Admin
2. Vào menu **Đặt Xe** (bên trái)
3. Dán API Key vào ô **Google Maps API Key**
4. Nhấn **Lưu Cài Đặt**

### Cách 2: Qua Code (Nâng cao)

Mở file `wp-config.php` và thêm:

```php
define('BOOKING_GOOGLE_API_KEY', 'AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
```

## Bước 8: Test Plugin

1. Tạo trang mới trong WordPress
2. Thêm shortcode: `[dat_xe]`
3. Publish trang
4. Mở trang và test:
   - Nhập địa chỉ → Xem có gợi ý không
   - Nhấn "Kiểm Tra Giá" → Xem có tính được khoảng cách không

## Giới Hạn Miễn Phí

Google Maps API cho phép:

- **$200 credit miễn phí/tháng**
- Tương đương:
  - ~28,000 lượt tính khoảng cách (Distance Matrix)
  - ~100,000 lượt autocomplete địa chỉ (Places)
  - Không giới hạn Maps JavaScript API

## Lưu Ý Quan Trọng

⚠️ **BẢO MẬT API KEY:**
- Không share API key công khai
- Nên giới hạn theo domain
- Theo dõi usage hàng tháng

⚠️ **NẾU VƯỢT QUÁ $200/THÁNG:**
- Google sẽ gửi email cảnh báo
- Bạn có thể set budget alerts
- Có thể tắt API để tránh bị charge

## Kiểm Tra Usage

1. Vào **APIs & Services** → **Dashboard**
2. Xem biểu đồ usage của từng API
3. Vào **Billing** → **Reports** để xem chi phí

## Troubleshooting

### Lỗi: "This API project is not authorized to use this API"

**Giải pháp:** Bật lại 3 APIs (Maps JavaScript, Places, Distance Matrix)

### Lỗi: "You must enable Billing on the Google Cloud Project"

**Giải pháp:** Thêm thẻ tín dụng vào Billing (không bị trừ tiền nếu < $200/tháng)

### Lỗi: "The provided API key is invalid"

**Giải pháp:** 
- Kiểm tra lại API key có đúng không
- Đợi 5-10 phút sau khi tạo key mới
- Xóa cache trình duyệt

### Không có gợi ý địa chỉ

**Giải pháp:**
- Kiểm tra Places API đã bật chưa
- Xem Console log trong trình duyệt (F12)
- Kiểm tra API restrictions

## Liên Hệ Hỗ Trợ

Nếu gặp vấn đề, liên hệ:
- Email: support@noibai.vn
- Website: https://noibai.vn

---

**Chúc bạn cài đặt thành công! 🚗**
