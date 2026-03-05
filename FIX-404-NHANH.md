# ⚡ Khắc Phục Lỗi 404 - Hướng Dẫn Nhanh

## 🎯 Giải Pháp Đơn Giản Nhất

### Bước 1: Tạo Page "Nhận Đơn Hàng"

Truy cập URL này:
```
http://your-site.com/wp-content/plugins/booking-plugin/create-accept-page.php
```

Script sẽ tự động:
- ✅ Tạo page "Nhận Đơn Hàng" với slug `nhan-don-hang`
- ✅ Thêm shortcode `[booking_accept_page]` vào page
- ✅ Publish page

### Bước 2: Test

Thử truy cập:
```
http://your-site.com/nhan-don-hang/?booking=1&token=test
```

Nếu hiển thị trang (dù báo "Link không hợp lệ") = ✅ Thành công!

### Bước 3: Gửi Đơn Thật

1. Tạo đơn hàng mới
2. Gán cho tài xế hoặc gửi vào group
3. Click link "Nhận Đơn" từ Telegram
4. Trang sẽ hiển thị thông tin đơn hàng

## 🔄 Nếu Vẫn Lỗi 404

### Cách 1: Flush Permalinks
1. Vào **Cài Đặt** → **Permalinks**
2. Click **Lưu Thay Đổi**
3. Thử lại

### Cách 2: Kiểm Tra Page
1. Vào **Pages** → **All Pages**
2. Tìm page "Nhận Đơn Hàng"
3. Kiểm tra:
   - Status: Published ✅
   - Slug: `nhan-don-hang` ✅
   - Content: `[booking_accept_page]` ✅

### Cách 3: Tạo Lại Page
1. Xóa page "Nhận Đơn Hàng" cũ
2. Chạy lại `create-accept-page.php`

## ✅ Xong!

Sau khi hoàn tất, link nhận đơn từ Telegram sẽ hoạt động:
```
https://datxe.nguyenvietbac.id.vn/nhan-don-hang/?booking=4&token=04vFy6R1Pe2nPEewAzwSTvRWhHxyeqaI
```

## 📝 Lưu Ý

- Page "Nhận Đơn Hàng" sử dụng shortcode `[booking_accept_page]`
- Plugin tự động xử lý shortcode này
- Không cần rewrite rules phức tạp
- Đơn giản và ổn định hơn

## 🎉 Kết Quả

Tài xế có thể:
- ✅ Click link từ Telegram
- ✅ Xem thông tin đơn hàng
- ✅ Nhận hoặc từ chối đơn
- ✅ Trạng thái cập nhật tự động
