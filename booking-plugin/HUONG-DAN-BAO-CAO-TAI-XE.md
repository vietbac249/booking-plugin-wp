# Hướng Dẫn Sử Dụng Báo Cáo Tài Xế

## Tổng Quan

Tính năng Báo Cáo Tài Xế cho phép bạn tìm kiếm và xem báo cáo chi tiết về hiệu suất làm việc của từng tài xế trong một khoảng thời gian cụ thể.

## Truy Cập

1. Đăng nhập vào WordPress Admin
2. Vào menu **Đặt Xe** → **Báo Cáo**

## Tìm Kiếm Báo Cáo

### Các Trường Tìm Kiếm:

1. **Tên Tài Xế** (Bắt buộc)
   - Chọn tài xế từ dropdown
   - Hiển thị tên và số điện thoại

2. **Từ Ngày** (Tùy chọn)
   - Chọn ngày bắt đầu
   - Để trống = Từ đầu

3. **Đến Ngày** (Tùy chọn)
   - Chọn ngày kết thúc
   - Để trống = Đến hiện tại

4. **Trạng Thái** (Tùy chọn)
   - **Tất cả**: Hiển thị tất cả đơn hàng
   - **Hoàn thành**: Chỉ đơn hàng hoàn thành
   - **Đã hủy**: Chỉ đơn hàng bị hủy
   - **Chờ xử lý**: Chỉ đơn hàng đang chờ

### Cách Tìm Kiếm:

1. Chọn tài xế từ dropdown
2. Chọn khoảng thời gian (nếu cần)
3. Chọn trạng thái đơn hàng (nếu cần)
4. Click nút **🔍 Tìm Kiếm**

## Kết Quả Báo Cáo

### 1. Thống Kê Tổng Quan

Hiển thị 5 thẻ thống kê:

- **📦 Tổng Đơn Hàng**: Tổng số đơn hàng trong khoảng thời gian
- **✅ Hoàn Thành**: Số đơn hàng hoàn thành thành công
- **❌ Đã Hủy**: Số đơn hàng bị hủy
- **💰 Doanh Số**: Tổng doanh thu từ đơn hàng hoàn thành
- **⭐ Đánh Giá TB**: Điểm đánh giá trung bình (1-5 sao)

### 2. Lý Do Hủy Đơn

Nếu có đơn hàng bị hủy, hiển thị bảng:
- Lý do hủy
- Số lượng đơn hàng cho mỗi lý do

### 3. Đánh Giá Từ Khách Hàng

Hiển thị tất cả đánh giá:
- Số sao (1-5)
- Tuyến đường (Điểm đi → Điểm đến)
- Nhận xét của khách hàng
- Thời gian đánh giá

### 4. Chi Tiết Đơn Hàng

Bảng danh sách tất cả đơn hàng:
- ID đơn hàng
- Điểm đi
- Điểm đến
- Thời gian đi
- Giá tiền
- Trạng thái

## Ví Dụ Sử Dụng

### Ví Dụ 1: Xem Tất Cả Đơn Hàng Của Tài Xế

```
Tên Tài Xế: Nguyễn Văn A
Từ Ngày: (để trống)
Đến Ngày: (để trống)
Trạng Thái: Tất cả
```

→ Hiển thị tất cả đơn hàng của tài xế Nguyễn Văn A từ trước đến nay

### Ví Dụ 2: Xem Đơn Hàng Tháng 3/2026

```
Tên Tài Xế: Trần Thị B
Từ Ngày: 01/03/2026
Đến Ngày: 31/03/2026
Trạng Thái: Tất cả
```

→ Hiển thị tất cả đơn hàng của tài xế Trần Thị B trong tháng 3/2026

### Ví Dụ 3: Xem Đơn Hàng Bị Hủy

```
Tên Tài Xế: Lê Văn C
Từ Ngày: 01/01/2026
Đến Ngày: 31/03/2026
Trạng Thái: Đã hủy
```

→ Hiển thị chỉ các đơn hàng bị hủy của tài xế Lê Văn C trong quý 1/2026

### Ví Dụ 4: Xem Hiệu Suất Tuần Này

```
Tên Tài Xế: Phạm Văn D
Từ Ngày: 17/03/2026 (Thứ 2)
Đến Ngày: 23/03/2026 (Chủ nhật)
Trạng Thái: Hoàn thành
```

→ Hiển thị các đơn hàng hoàn thành của tài xế Phạm Văn D trong tuần này

## Phân Tích Dữ Liệu

### Đánh Giá Hiệu Suất Tài Xế:

1. **Tỷ Lệ Hoàn Thành**
   ```
   Tỷ lệ = (Đơn hoàn thành / Tổng đơn) × 100%
   ```
   - Tốt: > 90%
   - Trung bình: 70-90%
   - Cần cải thiện: < 70%

2. **Tỷ Lệ Hủy Đơn**
   ```
   Tỷ lệ = (Đơn hủy / Tổng đơn) × 100%
   ```
   - Tốt: < 5%
   - Trung bình: 5-10%
   - Cần cải thiện: > 10%

3. **Đánh Giá Khách Hàng**
   - Xuất sắc: 4.5-5.0 sao
   - Tốt: 4.0-4.4 sao
   - Trung bình: 3.5-3.9 sao
   - Cần cải thiện: < 3.5 sao

4. **Doanh Số Trung Bình**
   ```
   Doanh số TB = Tổng doanh số / Số đơn hoàn thành
   ```

## Lưu Ý

1. **Chỉ Tài Xế Active**
   - Dropdown chỉ hiển thị tài xế đang hoạt động
   - Tài xế bị khóa không hiển thị

2. **Doanh Số**
   - Chỉ tính từ đơn hàng hoàn thành
   - Đơn hàng hủy không tính vào doanh số

3. **Đánh Giá**
   - Chỉ hiển thị đánh giá đã được khách hàng gửi
   - Đơn hàng chưa có đánh giá không hiển thị

4. **Lý Do Hủy**
   - Chỉ hiển thị nếu có đơn hàng bị hủy
   - Nhóm theo lý do hủy

## Xuất Báo Cáo

Hiện tại chưa có tính năng xuất báo cáo tự động. Bạn có thể:

1. **Chụp Màn Hình**: Sử dụng công cụ chụp màn hình
2. **Copy Dữ Liệu**: Copy từ bảng và paste vào Excel
3. **In Trang**: Sử dụng Ctrl+P để in báo cáo

## Câu Hỏi Thường Gặp

### Q1: Tại sao không thấy tài xế trong dropdown?

**A**: Có thể do:
- Tài xế chưa được thêm vào hệ thống
- Tài xế bị khóa (status không phải 'active')
- Kiểm tra trong menu **Tài Xế** để xem trạng thái

### Q2: Tại sao doanh số bằng 0?

**A**: Có thể do:
- Tài xế chưa có đơn hàng hoàn thành
- Khoảng thời gian tìm kiếm không có đơn hàng
- Đơn hàng đang ở trạng thái "Chờ xử lý" hoặc "Đã hủy"

### Q3: Làm sao để xem báo cáo tất cả tài xế?

**A**: Hiện tại phải tìm kiếm từng tài xế một. Để xem tổng quan tất cả tài xế, vào menu **Dashboard** hoặc **Xếp Hạng**.

### Q4: Có thể xuất báo cáo ra Excel không?

**A**: Hiện tại chưa có tính năng này. Bạn có thể copy dữ liệu từ bảng và paste vào Excel.

### Q5: Báo cáo có cập nhật real-time không?

**A**: Có, mỗi lần tìm kiếm sẽ lấy dữ liệu mới nhất từ database.

## Hỗ Trợ

Nếu gặp vấn đề, vui lòng liên hệ:
- Email: support@noibai.vn
- Website: https://noibai.vn

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
