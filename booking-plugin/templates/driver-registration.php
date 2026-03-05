<!-- Form Đăng Ký Tài Xế -->
<div class="driver-registration-form">
    <h2>Đăng Ký Làm Tài Xế</h2>
    <p class="form-description">Vui lòng điền đầy đủ thông tin để đăng ký làm tài xế của chúng tôi</p>
    
    <form id="driver-registration-form" enctype="multipart/form-data">
        <!-- Thông tin cá nhân -->
        <div class="form-section">
            <h3>Thông Tin Cá Nhân</h3>
            
            <div class="form-group">
                <label for="driver_name">Họ và Tên <span class="required">*</span></label>
                <input type="text" id="driver_name" name="driver_name" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="driver_phone">Số Điện Thoại <span class="required">*</span></label>
                    <input type="tel" id="driver_phone" name="driver_phone" required>
                </div>
                
                <div class="form-group">
                    <label for="driver_email">Email</label>
                    <input type="email" id="driver_email" name="driver_email">
                </div>
            </div>
            
            <div class="form-group">
                <label for="driver_address">Địa Chỉ <span class="required">*</span></label>
                <textarea id="driver_address" name="driver_address" rows="2" required></textarea>
            </div>
        </div>
        
        <!-- Giấy tờ tùy thân -->
        <div class="form-section">
            <h3>Giấy Tờ Tùy Thân</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="id_card_front">CCCD Mặt Trước <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" id="id_card_front" name="id_card_front" accept="image/*" required>
                        <div class="file-preview" id="preview_front"></div>
                    </div>
                    <small>Chụp rõ ràng, đầy đủ 4 góc</small>
                </div>
                
                <div class="form-group">
                    <label for="id_card_back">CCCD Mặt Sau <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" id="id_card_back" name="id_card_back" accept="image/*" required>
                        <div class="file-preview" id="preview_back"></div>
                    </div>
                    <small>Chụp rõ ràng, đầy đủ 4 góc</small>
                </div>
            </div>
        </div>
        
        <!-- eKYC Khuôn Mặt -->
        <div class="form-section">
            <h3>Xác Thực Khuôn Mặt (eKYC)</h3>
            
            <div class="form-group">
                <label>Chụp Ảnh Khuôn Mặt <span class="required">*</span></label>
                <div class="ekyc-container">
                    <video id="ekyc-video" autoplay playsinline></video>
                    <canvas id="ekyc-canvas" style="display:none;"></canvas>
                    <div class="ekyc-preview" id="ekyc-preview"></div>
                    <input type="hidden" id="ekyc_photo" name="ekyc_photo">
                </div>
                <div class="ekyc-buttons">
                    <button type="button" id="start-camera" class="button">Bật Camera</button>
                    <button type="button" id="capture-photo" class="button" style="display:none;">Chụp Ảnh</button>
                    <button type="button" id="retake-photo" class="button" style="display:none;">Chụp Lại</button>
                </div>
                <small>Đảm bảo khuôn mặt rõ ràng, không đeo khẩu trang hoặc kính đen</small>
            </div>
        </div>
        
        <!-- Thông tin xe -->
        <div class="form-section">
            <h3>Thông Tin Xe</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="car_type">Loại Xe <span class="required">*</span></label>
                    <select id="car_type" name="car_type" required>
                        <option value="">-- Chọn loại xe --</option>
                        <option value="4 chỗ cốp rộng">4 chỗ cốp rộng</option>
                        <option value="7 chỗ">7 chỗ</option>
                        <option value="4 chỗ cốp nhỏ">4 chỗ cốp nhỏ</option>
                        <option value="16 chỗ">16 chỗ</option>
                        <option value="29 chỗ">29 chỗ</option>
                        <option value="45 chỗ">45 chỗ</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="car_plate">Biển Số Xe <span class="required">*</span></label>
                    <input type="text" id="car_plate" name="car_plate" placeholder="VD: 30A-12345" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="car_brand">Hãng Xe</label>
                    <input type="text" id="car_brand" name="car_brand" placeholder="VD: Toyota, Honda">
                </div>
                
                <div class="form-group">
                    <label for="car_color">Màu Xe</label>
                    <input type="text" id="car_color" name="car_color" placeholder="VD: Trắng, Đen">
                </div>
            </div>
        </div>
        
        <!-- Điều khoản -->
        <div class="form-section">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="agree_terms" name="agree_terms" required>
                    Tôi đồng ý với <a href="#" target="_blank">Điều khoản dịch vụ</a> và <a href="#" target="_blank">Chính sách bảo mật</a>
                </label>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="button button-primary">Đăng Ký</button>
            <div class="form-message" id="form-message"></div>
        </div>
    </form>
</div>

<style>
.driver-registration-form {
    max-width: 800px;
    margin: 40px auto;
    padding: 30px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.driver-registration-form h2 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 28px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.form-description {
    color: #666;
    margin-bottom: 30px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid #eee;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section h3 {
    margin: 0 0 20px 0;
    color: #2196F3;
    font-size: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.required {
    color: #f44336;
}

.form-group input[type="text"],
.form-group input[type="tel"],
.form-group input[type="email"],
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #2196F3;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #999;
    font-size: 12px;
}

.file-upload input[type="file"] {
    display: block;
    margin-bottom: 10px;
}

.file-preview {
    width: 100%;
    max-width: 300px;
    min-height: 200px;
    border: 2px dashed #ddd;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.file-preview img {
    max-width: 100%;
    height: auto;
}

.ekyc-container {
    position: relative;
    max-width: 400px;
    margin-bottom: 15px;
}

#ekyc-video {
    width: 100%;
    border-radius: 8px;
    border: 2px solid #2196F3;
}

.ekyc-preview {
    width: 100%;
    min-height: 300px;
    border: 2px dashed #ddd;
    border-radius: 8px;
    display: none;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.ekyc-preview img {
    max-width: 100%;
    height: auto;
}

.ekyc-buttons {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: normal;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
}

.form-actions {
    text-align: center;
    margin-top: 30px;
}

.button-primary {
    background: #2196F3;
    color: #fff;
    padding: 15px 40px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.button-primary:hover {
    background: #1976D2;
}

.form-message {
    margin-top: 15px;
    padding: 12px;
    border-radius: 8px;
    display: none;
}

.form-message.success {
    background: #4CAF50;
    color: #fff;
    display: block;
}

.form-message.error {
    background: #f44336;
    color: #fff;
    display: block;
}

@media (max-width: 768px) {
    .driver-registration-form {
        padding: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    let stream = null;
    
    // Preview CCCD
    $('#id_card_front, #id_card_back').on('change', function() {
        const file = this.files[0];
        const previewId = this.id === 'id_card_front' ? '#preview_front' : '#preview_back';
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(previewId).html('<img src="' + e.target.result + '">');
            }
            reader.readAsDataURL(file);
        }
    });
    
    // eKYC Camera
    $('#start-camera').on('click', function() {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(s) {
                stream = s;
                $('#ekyc-video')[0].srcObject = stream;
                $('#ekyc-video').show();
                $('#start-camera').hide();
                $('#capture-photo').show();
            })
            .catch(function(err) {
                alert('Không thể truy cập camera: ' + err.message);
            });
    });
    
    $('#capture-photo').on('click', function() {
        const video = $('#ekyc-video')[0];
        const canvas = $('#ekyc-canvas')[0];
        const context = canvas.getContext('2d');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);
        
        const imageData = canvas.toDataURL('image/jpeg');
        $('#ekyc_photo').val(imageData);
        
        $('#ekyc-preview').html('<img src="' + imageData + '">').show();
        $('#ekyc-video').hide();
        $('#capture-photo').hide();
        $('#retake-photo').show();
        
        // Stop camera
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
    
    $('#retake-photo').on('click', function() {
        $('#ekyc-preview').hide().html('');
        $('#ekyc_photo').val('');
        $('#retake-photo').hide();
        $('#start-camera').show();
    });
    
    // Submit form
    $('#driver-registration-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate eKYC
        if (!$('#ekyc_photo').val()) {
            $('#form-message').removeClass('success').addClass('error').text('Vui lòng chụp ảnh khuôn mặt để xác thực').show();
            return;
        }
        
        // Show loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Đang xử lý...');
        
        const formData = new FormData(this);
        formData.append('action', 'register_driver');
        formData.append('nonce', bookingAjax.nonce);
        
        $.ajax({
            url: bookingAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#form-message').hide();
            },
            success: function(response) {
                submitBtn.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    $('#form-message').removeClass('error').addClass('success').text(response.data.message).show();
                    $('#driver-registration-form')[0].reset();
                    $('#preview_front, #preview_back, #ekyc-preview').html('').hide();
                    $('#ekyc_photo').val('');
                    $('#retake-photo, #capture-photo').hide();
                    $('#start-camera').show();
                    
                    // Scroll to message
                    $('html, body').animate({
                        scrollTop: $('#form-message').offset().top - 100
                    }, 500);
                } else {
                    $('#form-message').removeClass('success').addClass('error').text(response.data.message).show();
                }
            },
            error: function(xhr, status, error) {
                submitBtn.prop('disabled', false).text(originalText);
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                $('#form-message').removeClass('success').addClass('error').text('Có lỗi xảy ra, vui lòng thử lại. Chi tiết: ' + error).show();
            }
        });
    });
});
</script>
