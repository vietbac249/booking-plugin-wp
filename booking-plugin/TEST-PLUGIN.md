# Hướng Dẫn Test Plugin Đặt Xe

## Chuẩn Bị

✅ Đã cài đặt plugin
✅ Đã có Google Maps API Key
✅ Đã cấu hình API Key trong WordPress Admin

## Test Case 1: Test Autocomplete Địa Chỉ

### Bước test:

1. Mở trang có shortcode `[dat_xe]`
2. Nhấn vào ô **"Điểm đi"**
3. Gõ: `Hà Nội`
4. **Kết quả mong đợi:** Hiện danh sách gợi ý địa chỉ từ Google Maps

### Nếu không có gợi ý:

- Mở Console (F12) → Tab Console
- Xem có lỗi gì không
- Kiểm tra Places API đã bật chưa

## Test Case 2: Test Tab Sân Bay

### Bước test:

1. Chọn tab **"Sân bay"**
2. Nhập điểm đi: `Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội`
3. Điểm đến mặc định: `Sân bay Nội Bài`
4. Chọn loại xe: **4 chỗ cốp rộng**
5. Chọn thời gian đi (bất kỳ)
6. Nhấn **"Kiểm Tra Giá"**

### Kết quả mong đợi:

```
Khoảng cách: ~30 km
Loại xe: 4 chỗ cốp rộng
Giá tiền: ~450,000 VNĐ
```

## Test Case 3: Test Đi 2 Chiều

### Bước test:

1. Tab **"Sân bay"**
2. Nhập điểm đi: `Hồ Gươm, Hoàn Kiếm, Hà Nội`
3. Tích ✅ **"Đi 2 chiều"**
4. Nhấn **"Kiểm Tra Giá"**

### Kết quả mong đợi:

- Khoảng cách: ~60 km (gấp đôi)
- Giá tiền: Giảm ~10% so với tính 2 lần 1 chiều

## Test Case 4: Test VAT

### Bước test:

1. Nhập điểm đi và đến
2. Tích ✅ **"Xuất hóa đơn VAT"**
3. Nhấn **"Kiểm Tra Giá"**

### Kết quả mong đợi:

- Giá tiền tăng thêm 10% so với không có VAT

## Test Case 5: Test Đảo Chiều

### Bước test:

1. Tab **"Sân bay"**
2. Nhập điểm đi: `Hà Nội`
3. Nhấn nút **"🔄 Đảo chiều"**

### Kết quả mong đợi:

- Điểm đi: `Sân bay Nội Bài` (readonly)
- Điểm đến: `Hà Nội` (có thể nhập)

## Test Case 6: Test Thêm Điểm Dừng

### Bước test:

1. Nhập điểm đi: `Hà Nội`
2. Nhấn nút **"+"** màu đỏ
3. Nhập điểm dừng 1: `Sóc Sơn, Hà Nội`
4. Nhấn nút **"+"** lần nữa
5. Nhập điểm dừng 2: `Đông Anh, Hà Nội`
6. Thử nhấn **"+"** lần 3

### Kết quả mong đợi:

- Hiện 2 ô điểm dừng với icon 🟢
- Mỗi ô có nút **"-"** để xóa
- Nút **"+"** ẩn sau khi thêm 2 điểm dừng
- Không thể thêm điểm dừng thứ 3

## Test Case 7: Test Xóa Điểm Dừng

### Bước test:

1. Thêm 2 điểm dừng
2. Nhấn nút **"-"** ở điểm dừng 1

### Kết quả mong đợi:

- Điểm dừng 1 bị xóa
- Điểm dừng 2 đổi thành "Điểm dừng 1"
- Nút **"+"** hiện lại

## Test Case 8: Test Loại Xe

### Bước test:

1. Nhập điểm đi: `Hà Nội`
2. Chọn loại xe: **7 chỗ**
3. Nhấn **"Kiểm Tra Giá"**
4. Ghi nhớ giá tiền
5. Đổi sang **4 chỗ cốp rộng**
6. Nhấn **"Kiểm Tra Giá"** lại

### Kết quả mong đợi:

- Giá xe 7 chỗ = Giá xe 4 chỗ × 1.3
- Hiển thị đúng loại xe trong kết quả

## Test Case 9: Test Tab Đường Dài

### Bước test:

1. Chọn tab **"Đường dài"**
2. Điểm đi: `Hà Nội`
3. Điểm đến: `Hải Phòng`
4. Chọn loại xe: **4 chỗ cốp rộng**
5. Chọn thời gian
6. Nhấn **"Kiểm Tra Giá"**

### Kết quả mong đợi:

```
Khoảng cách: ~100 km
Loại xe: 4 chỗ cốp rộng
Giá tiền: ~1,200,000 VNĐ
```

## Test Case 10: Test Responsive Mobile

### Bước test:

1. Mở trên điện thoại hoặc F12 → Toggle device toolbar
2. Chọn iPhone/Android
3. Test tất cả tính năng

### Kết quả mong đợi:

- Giao diện hiển thị đẹp trên mobile
- Các nút dễ nhấn
- Form không bị vỡ layout

## Test Case 11: Test Validation

### Bước test:

1. Để trống điểm đi
2. Nhấn **"Kiểm Tra Giá"**

### Kết quả mong đợi:

- Hiện alert: "Vui lòng nhập đầy đủ thông tin điểm đi và điểm đến"

### Bước test 2:

1. Nhập điểm đi và đến
2. Xóa thời gian đi
3. Nhấn **"Kiểm Tra Giá"**

### Kết quả mong đợi:

- Hiện alert: "Vui lòng chọn thời gian đi"

## Test Case 12: Test Loading State

### Bước test:

1. Nhập đầy đủ thông tin
2. Nhấn **"Kiểm Tra Giá"**
3. Quan sát nút trong lúc đang tính

### Kết quả mong đợi:

- Nút hiển thị: "Đang tính..." với icon loading
- Nút bị disable (không nhấn được)
- Sau khi có kết quả, nút trở lại: "Kiểm Tra Giá ➔"

## Công Thức Tính Giá

### Tab Sân Bay:

```
Giá cơ bản = Khoảng cách × 15,000 VNĐ/km × Hệ số loại xe

Nếu đi 2 chiều:
Giá = (Khoảng cách × 2) × 15,000 × Hệ số xe × 1.8

Nếu có VAT:
Giá cuối = Giá × 1.1
```

### Tab Đường Dài:

```
Giá cơ bản = Khoảng cách × 12,000 VNĐ/km × Hệ số loại xe

Nếu có VAT:
Giá cuối = Giá × 1.1
```

### Hệ Số Loại Xe:

- 4 chỗ cốp rộng: **×1.0**
- 7 chỗ: **×1.3**
- 4 chỗ cốp nhỏ: **×0.9**
- 16 chỗ: **×2.0**
- 29 chỗ: **×3.0**
- 45 chỗ: **×4.0**

## Ví Dụ Tính Giá

### Ví dụ 1: Sân bay 1 chiều, không VAT

```
Điểm đi: Hà Nội
Điểm đến: Sân bay Nội Bài
Khoảng cách: 30 km
Loại xe: 4 chỗ cốp rộng (×1.0)

Giá = 30 × 15,000 × 1.0 = 450,000 VNĐ
```

### Ví dụ 2: Sân bay 2 chiều, có VAT

```
Khoảng cách: 30 km
Loại xe: 7 chỗ (×1.3)
Đi 2 chiều: ✅
VAT: ✅

Giá = (30 × 2) × 15,000 × 1.3 × 1.8 × 1.1
    = 60 × 15,000 × 1.3 × 1.8 × 1.1
    = 1,544,400 VNĐ
```

### Ví dụ 3: Đường dài, có VAT

```
Điểm đi: Hà Nội
Điểm đến: Hải Phòng
Khoảng cách: 100 km
Loại xe: 4 chỗ cốp rộng (×1.0)
VAT: ✅

Giá = 100 × 12,000 × 1.0 × 1.1
    = 1,320,000 VNĐ
```

## Checklist Hoàn Chỉnh

- [ ] Autocomplete địa chỉ hoạt động
- [ ] Tính khoảng cách chính xác
- [ ] Tính giá đúng công thức
- [ ] Đi 2 chiều giảm giá đúng
- [ ] VAT tính đúng 10%
- [ ] Đảo chiều hoạt động
- [ ] Thêm/xóa điểm dừng hoạt động
- [ ] Chọn loại xe ảnh hưởng giá
- [ ] Chọn thời gian hoạt động
- [ ] Validation form đúng
- [ ] Loading state hiển thị
- [ ] Responsive trên mobile
- [ ] Tab chuyển đổi mượt mà

## Lỗi Thường Gặp

### 1. Không có gợi ý địa chỉ

**Nguyên nhân:** Places API chưa bật hoặc API key sai

**Giải pháp:** 
- Kiểm tra Places API đã enable
- Kiểm tra API key trong WordPress Admin

### 2. Không tính được khoảng cách

**Nguyên nhân:** Distance Matrix API chưa bật hoặc chưa có billing

**Giải pháp:**
- Bật Distance Matrix API
- Thêm thẻ tín dụng vào Billing

### 3. Giá tiền không đúng

**Nguyên nhân:** Cấu hình giá sai

**Giải pháp:**
- Vào WordPress Admin → Đặt Xe
- Kiểm tra lại giá cơ bản

---

**Chúc bạn test thành công! 🎉**
