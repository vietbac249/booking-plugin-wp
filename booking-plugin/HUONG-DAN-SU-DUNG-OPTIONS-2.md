# Hướng Dẫn Sử Dụng Options 2 - Bảng Giá Thủ Công

## Tóm Tắt Nhanh

Options 2 cho phép bạn test hệ thống đặt xe KHÔNG CẦN Google Maps API Key.

---

## 3 BƯỚC ĐƠN GIẢN

### 1️⃣ CHỌN CHẾ ĐỘ
- Vào: **Đặt Xe > Cài Đặt > Tab "Bảng Giá"**
- Chọn: **"Tùy chỉnh (Options 2 - Bảng giá thủ công)"**
- Bấm: **"Lưu Chế Độ Tính Giá"** ← QUAN TRỌNG!

### 2️⃣ THÊM BẢNG GIÁ
- Bấm nút: **"Thêm Bảng Giá"**
- Điền thông tin:
  ```
  Loại xe: 7 chỗ
  Loại chuyến: Sân bay
  Giá cơ bản: 200000
  Giá/Km: 15000
  VAT: 0.1
  ✓ Kích hoạt
  ```
- Bấm: **"Lưu Bảng Giá"**
- Lặp lại cho các loại xe khác (4 chỗ, 16 chỗ, 29 chỗ, 45 chỗ)

### 3️⃣ TEST
- Vào trang đặt xe
- Nhập điểm đi, điểm đến
- Chọn loại xe
- Bấm **"Kiểm Tra Giá"**
- ✅ Thấy giá hiện ra (không cần Google API)

---

## BẢNG GIÁ GỢI Ý

| Loại Xe | Giá Cơ Bản | Giá/Km |
|---------|------------|--------|
| 4 chỗ cốp nhỏ | 130,000đ | 10,000đ |
| 4 chỗ cốp rộng | 150,000đ | 12,000đ |
| 7 chỗ | 200,000đ | 15,000đ |
| 16 chỗ | 300,000đ | 20,000đ |
| 29 chỗ | 500,000đ | 30,000đ |
| 45 chỗ | 800,000đ | 40,000đ |

---

## CÁCH TÍNH GIÁ

```
Giá = Giá cơ bản + (30km × Giá/km)

Ví dụ xe 7 chỗ:
= 200,000 + (30 × 15,000)
= 200,000 + 450,000
= 650,000đ

Nếu có VAT (10%):
= 650,000 × 1.1
= 715,000đ
```

**Lưu ý:** Hệ thống dùng khoảng cách giả định 30km để test.

---

## CHUYỂN SANG OPTIONS 1 (TỰ ĐỘNG)

Khi có Google Maps API Key:

1. Vào: **Cài Đặt > Tab "Cài Đặt Chung"**
2. Nhập: **Google Maps API Key**
3. Lưu lại
4. Vào: **Tab "Bảng Giá"**
5. Chọn: **"Tự động (Options 1 - Theo code)"**
6. Bấm: **"Lưu Chế Độ Tính Giá"**

Xong! Hệ thống sẽ tính khoảng cách thực tế.

---

## KHẮC PHỤC LỖI

### ❌ "Chưa có bảng giá cho loại xe này"
→ Thêm bảng giá cho loại xe đó

### ❌ Vẫn báo lỗi Google Maps
→ Kiểm tra đã chọn "Tùy chỉnh" và bấm "Lưu Chế Độ Tính Giá" chưa

### ❌ Không thấy nút "Thêm Bảng Giá"
→ Chọn "Tùy chỉnh (Options 2)" rồi lưu lại

---

## VIDEO HƯỚNG DẪN (Các Bước)

1. **Mở Cài Đặt** → Đặt Xe > Cài Đặt
2. **Click Tab "Bảng Giá"**
3. **Chọn "Tùy chỉnh (Options 2)"**
4. **Bấm "Lưu Chế Độ Tính Giá"**
5. **Bấm "Thêm Bảng Giá"**
6. **Điền thông tin** (loại xe, giá cơ bản, giá/km)
7. **Bấm "Lưu Bảng Giá"**
8. **Lặp lại** cho các loại xe khác
9. **Test** trên trang đặt xe

---

## HỖ TRỢ

Nếu gặp vấn đề, kiểm tra:
- ✅ Đã chọn đúng chế độ "Tùy chỉnh"?
- ✅ Đã bấm "Lưu Chế Độ Tính Giá"?
- ✅ Đã thêm bảng giá cho loại xe đang test?
- ✅ Trạng thái bảng giá là "✅ Hoạt động"?

---

© 2026 Nguyễn Việt Bắc. All Rights Reserved.
