# Hướng Dẫn Bảng Giá Theo Tuyến Đường

## Tính Năng Mới: Điểm Đi & Điểm Đến

Bây giờ bạn có thể tạo bảng giá cụ thể cho từng tuyến đường!

---

## CÁC LOẠI BẢNG GIÁ

### 1️⃣ Bảng Giá Theo Tuyến Cụ Thể
Áp dụng cho tuyến đường cụ thể (ưu tiên cao nhất)

**Ví dụ:**
- Điểm đi: `Hà Nội`
- Điểm đến: `Sân bay Nội Bài`
- Loại xe: `7 chỗ`
- Giá: `500,000đ + 10,000đ/km`

### 2️⃣ Bảng Giá Theo Điểm Đến
Áp dụng cho mọi điểm đi đến một điểm đến cụ thể

**Ví dụ:**
- Điểm đi: `(để trống)`
- Điểm đến: `Hải Phòng`
- Loại xe: `7 chỗ`
- Giá: `300,000đ + 15,000đ/km`

### 3️⃣ Bảng Giá Theo Điểm Đi
Áp dụng cho một điểm đi đến mọi điểm đến

**Ví dụ:**
- Điểm đi: `Hà Nội`
- Điểm đến: `(để trống)`
- Loại xe: `7 chỗ`
- Giá: `200,000đ + 12,000đ/km`

### 4️⃣ Bảng Giá Chung
Áp dụng cho mọi tuyến đường (ưu tiên thấp nhất)

**Ví dụ:**
- Điểm đi: `(để trống)`
- Điểm đến: `(để trống)`
- Loại xe: `7 chỗ`
- Giá: `150,000đ + 10,000đ/km`

---

## THỨ TỰ ƯU TIÊN

Hệ thống sẽ tìm bảng giá theo thứ tự:

```
1. Khớp CẢ điểm đi VÀ điểm đến
   ↓ (nếu không tìm thấy)
2. Khớp điểm đến (bất kỳ điểm đi)
   ↓ (nếu không tìm thấy)
3. Khớp điểm đi (bất kỳ điểm đến)
   ↓ (nếu không tìm thấy)
4. Bảng giá chung (không điểm nào)
```

---

## VÍ DỤ CÀI ĐẶT

### Ví dụ 1: Giá Riêng Cho Sân Bay Nội Bài

**Bảng giá 1:**
- Điểm đi: `(để trống)`
- Điểm đến: `Sân bay Nội Bài`
- Loại xe: `4 chỗ cốp rộng`
- Giá cơ bản: `300,000đ`
- Giá/km: `15,000đ`

**Bảng giá 2:**
- Điểm đi: `(để trống)`
- Điểm đến: `Sân bay Nội Bài`
- Loại xe: `7 chỗ`
- Giá cơ bản: `400,000đ`
- Giá/km: `18,000đ`

**Kết quả:**
- Khách đặt từ BẤT KỲ đâu đến Sân bay Nội Bài → Dùng giá này
- Khách đặt đi nơi khác → Dùng bảng giá chung (nếu có)

---

### Ví dụ 2: Giá Khác Nhau Theo Tuyến

**Bảng giá 1:**
- Điểm đi: `Hà Nội`
- Điểm đến: `Hải Phòng`
- Loại xe: `7 chỗ`
- Giá cơ bản: `500,000đ`
- Giá/km: `20,000đ`

**Bảng giá 2:**
- Điểm đi: `Hà Nội`
- Điểm đến: `Ninh Bình`
- Loại xe: `7 chỗ`
- Giá cơ bản: `400,000đ`
- Giá/km: `18,000đ`

**Bảng giá 3:**
- Điểm đi: `Hà Nội`
- Điểm đến: `(để trống)`
- Loại xe: `7 chỗ`
- Giá cơ bản: `200,000đ`
- Giá/km: `15,000đ`

**Kết quả:**
- Hà Nội → Hải Phòng: 500k + 20k/km
- Hà Nội → Ninh Bình: 400k + 18k/km
- Hà Nội → Nơi khác: 200k + 15k/km

---

## CÁCH THÊM BẢNG GIÁ

### Bước 1: Vào Cài Đặt
```
Đặt Xe > Cài Đặt > Tab "Bảng Giá"
```

### Bước 2: Chọn Chế Độ
```
Chọn: "Tùy chỉnh (Options 2)"
Bấm: "Lưu Chế Độ Tính Giá"
```

### Bước 3: Thêm Bảng Giá
```
Bấm: "Thêm Bảng Giá"
```

### Bước 4: Điền Thông Tin

**Điểm Đi:**
- Nhập tên điểm đi (VD: `Hà Nội`, `247 Cầu Giấy`)
- Hoặc để trống nếu áp dụng cho mọi điểm đi

**Điểm Đến:**
- Nhập tên điểm đến (VD: `Sân bay Nội Bài`, `Hải Phòng`)
- Hoặc để trống nếu áp dụng cho mọi điểm đến

**Loại Xe:**
- Chọn loại xe (4 chỗ, 7 chỗ, 16 chỗ, 29 chỗ, 45 chỗ)

**Loại Chuyến:**
- Sân bay hoặc Đường dài

**Giá Cơ Bản:**
- Giá khởi điểm (VD: `200000`)

**Giá/Km:**
- Giá mỗi km (VD: `15000`)

**VAT:**
- Tỷ lệ VAT (VD: `0.1` = 10%)

**Trạng Thái:**
- ✓ Kích hoạt

### Bước 5: Lưu
```
Bấm: "Lưu Bảng Giá"
```

---

## TÌM KIẾM THÔNG MINH

Hệ thống tìm kiếm bảng giá theo từ khóa:

**Ví dụ bảng giá:**
- Điểm đến: `Sân bay Nội Bài`

**Khách nhập:**
- `Sân bay Nội Bài` → ✅ Khớp
- `Nội Bài` → ✅ Khớp
- `sân bay` → ✅ Khớp
- `Noibai` → ❌ Không khớp

**Lưu ý:** Tìm kiếm KHÔNG phân biệt hoa thường, nhưng cần khớp từ khóa.

---

## CÔNG THỨC TÍNH GIÁ

```
Giá = Giá cơ bản + (Khoảng cách × Giá/km)

Ví dụ:
- Giá cơ bản: 200,000đ
- Khoảng cách: 30km (giả định)
- Giá/km: 15,000đ

Tính:
= 200,000 + (30 × 15,000)
= 200,000 + 450,000
= 650,000đ

Nếu có VAT 10%:
= 650,000 × 1.1
= 715,000đ
```

---

## CHIẾN LƯỢC SETUP

### Chiến lược 1: Giá Theo Điểm Đến Phổ Biến
Tạo bảng giá cho các điểm đến hay đi:
- Sân bay Nội Bài
- Hải Phòng
- Ninh Bình
- Hạ Long
- ...

### Chiến lược 2: Giá Theo Khu Vực
Tạo bảng giá theo khu vực xuất phát:
- Từ Hà Nội
- Từ Hải Phòng
- Từ Đà Nẵng
- ...

### Chiến lược 3: Giá Chung + Ngoại Lệ
- Tạo 1 bảng giá chung (không điểm đi/đến)
- Tạo bảng giá riêng cho các tuyến đặc biệt

---

## KIỂM TRA

### Test 1: Tuyến Cụ Thể
1. Thêm bảng giá: Hà Nội → Sân bay Nội Bài
2. Vào trang đặt xe
3. Nhập: Hà Nội → Sân bay Nội Bài
4. Bấm "Kiểm Tra Giá"
5. ✅ Thấy thông báo "cho tuyến Hà Nội → Sân bay Nội Bài"

### Test 2: Điểm Đến
1. Thêm bảng giá: (trống) → Hải Phòng
2. Vào trang đặt xe
3. Nhập: Bất kỳ → Hải Phòng
4. Bấm "Kiểm Tra Giá"
5. ✅ Thấy thông báo "đến Hải Phòng"

### Test 3: Bảng Giá Chung
1. Thêm bảng giá: (trống) → (trống)
2. Vào trang đặt xe
3. Nhập: Bất kỳ → Bất kỳ
4. Bấm "Kiểm Tra Giá"
5. ✅ Thấy giá hiển thị

---

## KHẮC PHỤC SỰ CỐ

### Lỗi: "Chưa có bảng giá cho tuyến đường này"
**Nguyên nhân:** Không tìm thấy bảng giá phù hợp  
**Giải pháp:**
1. Thêm bảng giá cho tuyến đó
2. Hoặc thêm bảng giá chung (không điểm đi/đến)

### Giá không đúng
**Nguyên nhân:** Hệ thống chọn sai bảng giá  
**Giải pháp:**
1. Kiểm tra thứ tự ưu tiên
2. Xem bảng giá nào đang active
3. Kiểm tra từ khóa tìm kiếm

### Không tìm thấy bảng giá
**Nguyên nhân:** Từ khóa không khớp  
**Giải pháp:**
1. Nhập chính xác tên điểm (VD: `Sân bay Nội Bài` thay vì `Noibai`)
2. Hoặc tạo bảng giá chung

---

## LƯU Ý QUAN TRỌNG

✅ Điểm đi/đến có thể để trống  
✅ Hệ thống tìm kiếm theo từ khóa (LIKE)  
✅ Ưu tiên bảng giá cụ thể hơn bảng giá chung  
✅ Có thể có nhiều bảng giá cho cùng tuyến (khác loại xe)  
✅ Chỉ bảng giá "Hoạt động" mới được dùng  

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
