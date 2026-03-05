# Hướng Dẫn Test Bảng Giá Tùy Chỉnh (Options 2)

## Mục Đích
Chế độ này cho phép bạn test hệ thống đặt xe KHÔNG CẦN Google Maps API Key. Hệ thống sẽ dùng khoảng cách giả định (30km) để tính giá.

---

## BƯỚC 1: Chọn Chế Độ Tùy Chỉnh

1. Vào **WordPress Admin > Đặt Xe > Cài Đặt**
2. Click vào tab **"Bảng Giá"**
3. Trong phần "Chế Độ Tính Giá", chọn **"Tùy chỉnh (Options 2 - Bảng giá thủ công)"**
4. Bấm nút **"Lưu Chế Độ Tính Giá"** (quan trọng!)
5. Sau khi lưu, phần "Bảng Giá Tùy Chỉnh" sẽ hiện ra bên dưới

---

## BƯỚC 2: Thêm Bảng Giá Cho Từng Loại Xe

### Thêm bảng giá cho xe 4 chỗ cốp rộng:
1. Bấm nút **"Thêm Bảng Giá"**
2. Nhập thông tin:
   - **Loại xe:** 4 chỗ cốp rộng
   - **Loại chuyến:** Sân bay
   - **Giá cơ bản:** 150000
   - **Giá/Km:** 12000
   - **Km tối thiểu:** 0
   - **Km tối đa:** 0
   - **VAT:** 0.1
   - **Trạng thái:** ✓ Tích chọn "Kích hoạt"
3. Bấm **"Lưu Bảng Giá"**

### Thêm bảng giá cho xe 7 chỗ:
1. Bấm nút **"Thêm Bảng Giá"**
2. Nhập thông tin:
   - **Loại xe:** 7 chỗ
   - **Loại chuyến:** Sân bay
   - **Giá cơ bản:** 200000
   - **Giá/Km:** 15000
   - **Km tối thiểu:** 0
   - **Km tối đa:** 0
   - **VAT:** 0.1
   - **Trạng thái:** ✓ Tích chọn "Kích hoạt"
3. Bấm **"Lưu Bảng Giá"**

### Thêm bảng giá cho các loại xe còn lại:

**4 chỗ cốp nhỏ - Sân bay:**
- Giá cơ bản: 130000, Giá/km: 10000

**16 chỗ - Sân bay:**
- Giá cơ bản: 300000, Giá/km: 20000

**29 chỗ - Sân bay:**
- Giá cơ bản: 500000, Giá/km: 30000

**45 chỗ - Sân bay:**
- Giá cơ bản: 800000, Giá/km: 40000

### Thêm bảng giá cho Đường Dài (tùy chọn):
Lặp lại các bước trên nhưng chọn **"Loại chuyến: Đường dài"**

---

## BƯỚC 3: Kiểm Tra Cài Đặt

1. Vào lại tab **"Bảng Giá"**
2. Kiểm tra:
   - ✅ Chế độ tính giá đang là **"Tùy chỉnh (Options 2)"**
   - ✅ Bảng giá đã hiển thị đầy đủ các loại xe
   - ✅ Trạng thái của các bảng giá là **"✅ Hoạt động"**

---

## BƯỚC 4: Test Trên Trang Đặt Xe

1. Vào trang có shortcode `[dat_xe]` (trang đặt xe của bạn)
2. Chọn tab **"Sân bay"**
3. Nhập thông tin:
   - **Điểm đi:** Nhập bất kỳ (VD: 247 Cầu Giấy, Hà Nội)
   - **Điểm đến:** Sân bay Nội Bài
   - **Loại xe:** Chọn "7 chỗ"
   - **Thời gian đi:** Chọn thời gian bất kỳ
4. Bấm nút **"Kiểm Tra Giá"**

---

## KẾT QUẢ MONG ĐỢI

Sau khi bấm "Kiểm Tra Giá", bạn sẽ thấy:

1. **Thông báo màu vàng:**
   ```
   ⚠️ Giá tính theo bảng tùy chỉnh (khoảng cách giả định: 30km)
   ```

2. **Giá được tính:**
   ```
   Giá = Giá cơ bản + (30km × Giá/km)
   Ví dụ xe 7 chỗ: 200,000 + (30 × 15,000) = 650,000đ
   ```

3. **Form liên hệ hiện ra** để nhập SĐT và Họ tên

4. **KHÔNG có lỗi** về Google Maps API

---

## CÔNG THỨC TÍNH GIÁ

```
Giá cơ bản = Giá khởi điểm
Giá theo km = Khoảng cách × Giá/km
Giá trước VAT = Giá cơ bản + Giá theo km

Nếu chọn "Đi 2 chiều":
  Giá trước VAT = Giá trước VAT × 1.8 (giảm 10%)

Nếu chọn "Xuất VAT":
  Giá cuối = Giá trước VAT × 1.1 (thêm 10% VAT)
```

### Ví dụ cụ thể (xe 7 chỗ):
- Giá cơ bản: 200,000đ
- Khoảng cách: 30km (giả định)
- Giá/km: 15,000đ
- **Tổng:** 200,000 + (30 × 15,000) = **650,000đ**
- **Nếu có VAT:** 650,000 × 1.1 = **715,000đ**

---

## CHUYỂN SANG CHẾ ĐỘ TỰ ĐỘNG

Khi đã có Google Maps API Key và muốn tính khoảng cách thực tế:

1. Vào **Đặt Xe > Cài Đặt > Tab "Cài Đặt Chung"**
2. Nhập **Google Maps API Key**
3. Bấm **"Lưu Cài Đặt"**
4. Vào tab **"Bảng Giá"**
5. Chọn **"Tự động (Options 1 - Theo code)"**
6. Bấm **"Lưu Chế Độ Tính Giá"**

Hệ thống sẽ tự động chuyển sang dùng Google Maps API để tính khoảng cách chính xác.

---

## KHẮC PHỤC SỰ CỐ

### Lỗi: "Chưa có bảng giá cho loại xe này"
- **Nguyên nhân:** Chưa thêm bảng giá cho loại xe đó
- **Giải pháp:** Thêm bảng giá theo BƯỚC 2

### Lỗi: Vẫn báo lỗi Google Maps API
- **Nguyên nhân:** Chưa lưu chế độ tính giá
- **Giải pháp:** Vào tab "Bảng Giá", chọn "Tùy chỉnh", bấm "Lưu Chế Độ Tính Giá"

### Không hiện form thêm bảng giá
- **Nguyên nhân:** Chưa chọn chế độ "Tùy chỉnh"
- **Giải pháp:** Chọn "Tùy chỉnh (Options 2)" và lưu lại

---

## LƯU Ý QUAN TRỌNG

✅ Chế độ tùy chỉnh dùng khoảng cách giả định (30km) - phù hợp để TEST  
✅ KHÔNG CẦN Google Maps API Key  
✅ Có thể thêm/sửa/xóa bảng giá bất kỳ lúc nào  
✅ Sau khi test OK, chuyển sang chế độ tự động để có khoảng cách chính xác  

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
