<div class="wrap">
    <h1>Cài Đặt Plugin Đặt Xe</h1>
    
    <?php
    global $wpdb;
    
    // Biến để lưu tab hiện tại
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
    $pricing_saved = false;
    
    // Xử lý thêm/sửa bảng giá tùy chỉnh
    if (isset($_POST['save_custom_pricing']) && check_admin_referer('save_custom_pricing_action')) {
        $pricing_data = array(
            'from_location' => sanitize_text_field($_POST['from_location']),
            'to_location' => sanitize_text_field($_POST['to_location']),
            'car_type' => sanitize_text_field($_POST['car_type']),
            'trip_type' => sanitize_text_field($_POST['trip_type']),
            'base_price' => floatval($_POST['base_price']),
            'price_per_km' => floatval($_POST['price_per_km']),
            'min_distance' => floatval($_POST['min_distance']),
            'max_distance' => floatval($_POST['max_distance']),
            'vat_rate' => floatval($_POST['vat_rate']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        );
        
        if (!empty($_POST['pricing_id'])) {
            $wpdb->update(
                $wpdb->prefix . 'custom_pricing',
                $pricing_data,
                array('id' => intval($_POST['pricing_id']))
            );
            $pricing_saved = 'updated';
        } else {
            $wpdb->insert($wpdb->prefix . 'custom_pricing', $pricing_data);
            $pricing_saved = 'added';
        }
        
        // Chuyển về tab Bảng Giá
        $active_tab = 'pricing';
    }
    
    // Xóa bảng giá
    if (isset($_GET['delete_pricing']) && check_admin_referer('delete_pricing_' . $_GET['delete_pricing'])) {
        $wpdb->delete($wpdb->prefix . 'custom_pricing', array('id' => intval($_GET['delete_pricing'])));
        $pricing_saved = 'deleted';
        $active_tab = 'pricing';
    }
    
    // Lấy danh sách bảng giá tùy chỉnh
    $custom_pricings = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}custom_pricing ORDER BY from_location, to_location, car_type, trip_type");
    ?>
    
    <?php if ($pricing_saved): ?>
        <div class="notice notice-success is-dismissible" id="pricing-success-notice">
            <p>
                <?php 
                if ($pricing_saved === 'added') echo '✅ Đã thêm bảng giá mới thành công!';
                elseif ($pricing_saved === 'updated') echo '✅ Đã cập nhật bảng giá thành công!';
                elseif ($pricing_saved === 'deleted') echo '✅ Đã xóa bảng giá thành công!';
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=booking-settings&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">Cài Đặt Chung</a>
        <a href="?page=booking-settings&tab=pricing" class="nav-tab <?php echo $active_tab === 'pricing' ? 'nav-tab-active' : ''; ?>">Bảng Giá</a>
        <a href="?page=booking-settings&tab=notifications" class="nav-tab <?php echo $active_tab === 'notifications' ? 'nav-tab-active' : ''; ?>">Thông Báo</a>
    </h2>
    
    <!-- Tab Cài Đặt Chung -->
    <div id="tab-general" class="tab-content" style="display:<?php echo $active_tab === 'general' ? 'block' : 'none'; ?>;">
        <form method="post" action="options.php">
            <?php settings_fields('booking_plugin_settings'); ?>
            <?php do_settings_sections('booking_plugin_settings'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="booking_google_api_key">Google Maps API Key</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="booking_google_api_key" 
                               name="booking_google_api_key" 
                               value="<?php echo esc_attr(get_option('booking_google_api_key', '')); ?>" 
                               class="regular-text">
                        <p class="description">
                            Nhập Google Maps API Key. 
                            <a href="https://console.cloud.google.com/" target="_blank">Lấy API Key tại đây</a>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="booking_airport_price">Giá Sân Bay (VNĐ/km)</label>
                    </th>
                    <td>
                        <input type="number" 
                               id="booking_airport_price" 
                               name="booking_airport_price" 
                               value="<?php echo esc_attr(get_option('booking_airport_price', 15000)); ?>" 
                               class="regular-text">
                        <p class="description">Giá mỗi km cho chuyến đi sân bay</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="booking_long_price">Giá Đường Dài (VNĐ/km)</label>
                    </th>
                    <td>
                        <input type="number" 
                               id="booking_long_price" 
                               name="booking_long_price" 
                               value="<?php echo esc_attr(get_option('booking_long_price', 12000)); ?>" 
                               class="regular-text">
                        <p class="description">Giá mỗi km cho chuyến đi đường dài</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="booking_roundtrip_multiplier">Hệ Số Đi 2 Chiều</label>
                    </th>
                    <td>
                        <input type="number" 
                               id="booking_roundtrip_multiplier" 
                               name="booking_roundtrip_multiplier" 
                               value="<?php echo esc_attr(get_option('booking_roundtrip_multiplier', 1.8)); ?>" 
                               step="0.1"
                               class="regular-text">
                        <p class="description">Hệ số nhân cho chuyến đi 2 chiều (1.8 = giảm 10%)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="booking_vat_rate">Thuế VAT (%)</label>
                    </th>
                    <td>
                        <input type="number" 
                               id="booking_vat_rate" 
                               name="booking_vat_rate" 
                               value="<?php echo esc_attr(get_option('booking_vat_rate', 0.1)); ?>" 
                               step="0.01"
                               class="regular-text">
                        <p class="description">Tỷ lệ VAT (0.1 = 10%)</p>
                    </td>
                </tr>
            </table>
            
            <h2>Hệ Số Loại Xe</h2>
            <table class="form-table">
                <tr>
                    <td>
                        <p><strong>4 chỗ cốp rộng:</strong> x1.0 (giá gốc)</p>
                        <p><strong>7 chỗ:</strong> x1.3</p>
                        <p><strong>4 chỗ cốp nhỏ:</strong> x0.9</p>
                        <p><strong>16 chỗ:</strong> x2.0</p>
                        <p><strong>29 chỗ:</strong> x3.0</p>
                        <p><strong>45 chỗ:</strong> x4.0</p>
                        <p class="description">Hệ số này được nhân với giá cơ bản để tính giá cuối cùng</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Lưu Cài Đặt'); ?>
        </form>
    </div>
    
    <!-- Tab Bảng Giá -->
    <div id="tab-pricing" class="tab-content" style="display:<?php echo $active_tab === 'pricing' ? 'block' : 'none'; ?>;">
        <h2>Hệ Thống Bảng Giá</h2>
        
        <form method="post" action="options.php">
            <?php settings_fields('booking_plugin_settings'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="booking_pricing_mode">Chế Độ Tính Giá</label>
                    </th>
                    <td>
                        <select id="booking_pricing_mode" name="booking_pricing_mode" onchange="togglePricingMode(this.value)">
                            <option value="auto" <?php selected(get_option('booking_pricing_mode', 'auto'), 'auto'); ?>>
                                Tự động (Options 1 - Theo code)
                            </option>
                            <option value="custom" <?php selected(get_option('booking_pricing_mode'), 'custom'); ?>>
                                Tùy chỉnh (Options 2 - Bảng giá thủ công)
                            </option>
                        </select>
                        <p class="description">
                            <strong>Tự động:</strong> Sử dụng công thức tính giá theo code (giá/km × hệ số xe)<br>
                            <strong>Tùy chỉnh:</strong> Sử dụng bảng giá do admin tự xây dựng
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Lưu Chế Độ Tính Giá'); ?>
        </form>
        
        <hr>
        
        <div id="custom-pricing-section" style="<?php echo get_option('booking_pricing_mode') === 'custom' ? '' : 'display:none;'; ?>">
            <h3>Bảng Giá Tùy Chỉnh <button type="button" class="page-title-action" id="add-pricing-btn">Thêm Bảng Giá</button></h3>
            
            <!-- Form thêm/sửa bảng giá -->
            <div id="pricing-form-container" style="display:none; margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
                <h4 id="pricing-form-title">Thêm Bảng Giá Mới</h4>
                <form method="post" id="pricing-form">
                    <?php wp_nonce_field('save_custom_pricing_action'); ?>
                    <input type="hidden" name="save_custom_pricing" value="1">
                    <input type="hidden" name="pricing_id" id="pricing_id">
                    
                    <table class="form-table">
                        <tr>
                            <th><label>Điểm Đi</label></th>
                            <td>
                                <input type="text" name="from_location" id="pricing_from_location" class="regular-text" placeholder="VD: Hà Nội, 247 Cầu Giấy">
                                <p class="description">Để trống nếu áp dụng cho mọi điểm đi</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Điểm Đến</label></th>
                            <td>
                                <input type="text" name="to_location" id="pricing_to_location" class="regular-text" placeholder="VD: Sân bay Nội Bài, Hải Phòng">
                                <p class="description">Để trống nếu áp dụng cho mọi điểm đến</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Loại Xe *</label></th>
                            <td>
                                <select name="car_type" id="pricing_car_type" required style="width: 300px;">
                                    <option value="4 chỗ cốp rộng">4 chỗ cốp rộng</option>
                                    <option value="7 chỗ">7 chỗ</option>
                                    <option value="4 chỗ cốp nhỏ">4 chỗ cốp nhỏ</option>
                                    <option value="16 chỗ">16 chỗ</option>
                                    <option value="29 chỗ">29 chỗ</option>
                                    <option value="45 chỗ">45 chỗ</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Loại Chuyến *</label></th>
                            <td>
                                <select name="trip_type" id="pricing_trip_type" required style="width: 300px;">
                                    <option value="airport">Sân bay</option>
                                    <option value="long_distance">Đường dài</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Giá Cơ Bản (VNĐ) *</label></th>
                            <td>
                                <input type="number" name="base_price" id="pricing_base_price" required class="regular-text" placeholder="VD: 200000">
                                <p class="description">Giá khởi điểm cho chuyến đi</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Giá/Km (VNĐ) *</label></th>
                            <td>
                                <input type="number" name="price_per_km" id="pricing_price_per_km" required class="regular-text" placeholder="VD: 15000">
                                <p class="description">Giá mỗi km</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Km Tối Thiểu</label></th>
                            <td>
                                <input type="number" name="min_distance" id="pricing_min_distance" value="0" step="0.1" class="regular-text" placeholder="VD: 5">
                                <p class="description">Số km tối thiểu áp dụng bảng giá này</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Km Tối Đa</label></th>
                            <td>
                                <input type="number" name="max_distance" id="pricing_max_distance" value="0" step="0.1" class="regular-text" placeholder="VD: 100">
                                <p class="description">Để 0 nếu không giới hạn</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>VAT</label></th>
                            <td>
                                <input type="number" name="vat_rate" id="pricing_vat_rate" value="0.1" step="0.01" class="regular-text" placeholder="VD: 0.1">
                                <p class="description">Tỷ lệ VAT (0.1 = 10%)</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Trạng Thái</label></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="is_active" id="pricing_is_active" value="1" checked>
                                    Kích hoạt bảng giá này
                                </label>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <button type="submit" class="button button-primary" id="save-pricing-btn">
                            <span class="button-text">Lưu Bảng Giá</span>
                            <span class="spinner-icon" style="display:none;">
                                <span class="dashicons dashicons-update spin-animation"></span> Đang lưu...
                            </span>
                        </button>
                        <button type="button" class="button" id="cancel-pricing-btn">Hủy</button>
                    </p>
                </form>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Điểm Đi</th>
                        <th>Điểm Đến</th>
                        <th>Loại Xe</th>
                        <th>Loại Chuyến</th>
                        <th>Giá Cơ Bản</th>
                        <th>Giá/Km</th>
                        <th>VAT (%)</th>
                        <th>Trạng Thái</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($custom_pricings)): ?>
                        <?php foreach ($custom_pricings as $pricing): ?>
                            <tr>
                                <td><?php echo !empty($pricing->from_location) ? esc_html($pricing->from_location) : '<em>Tất cả</em>'; ?></td>
                                <td><?php echo !empty($pricing->to_location) ? esc_html($pricing->to_location) : '<em>Tất cả</em>'; ?></td>
                                <td><?php echo esc_html($pricing->car_type); ?></td>
                                <td><?php echo $pricing->trip_type === 'airport' ? 'Sân bay' : 'Đường dài'; ?></td>
                                <td><?php echo number_format($pricing->base_price); ?>đ</td>
                                <td><?php echo number_format($pricing->price_per_km); ?>đ</td>
                                <td><?php echo $pricing->vat_rate * 100; ?>%</td>
                                <td><?php echo $pricing->is_active ? '✅ Hoạt động' : '❌ Tắt'; ?></td>
                                <td>
                                    <a href="#" class="button button-small edit-pricing" data-id="<?php echo $pricing->id; ?>">Sửa</a>
                                    <a href="?page=booking-settings&tab=pricing&delete_pricing=<?php echo $pricing->id; ?>&_wpnonce=<?php echo wp_create_nonce('delete_pricing_' . $pricing->id); ?>" 
                                       class="button button-small" 
                                       onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">Chưa có bảng giá nào. Hãy thêm bảng giá mới.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Tab Thông Báo -->
    <div id="tab-notifications" class="tab-content" style="display:<?php echo $active_tab === 'notifications' ? 'block' : 'none'; ?>;">
        <form method="post" action="options.php">
            <?php settings_fields('booking_plugin_settings'); ?>
        
            <h2>Cấu Hình Thông Báo</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="booking_telegram_bot_token">Telegram Bot Token</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="booking_telegram_bot_token" 
                               name="booking_telegram_bot_token" 
                               value="<?php echo esc_attr(get_option('booking_telegram_bot_token', '')); ?>" 
                               class="regular-text">
                        <p class="description">
                            Token của Telegram Bot. 
                            <a href="https://core.telegram.org/bots#6-botfather" target="_blank">Hướng dẫn tạo bot</a>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="booking_telegram_chat_id">Telegram Chat ID</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="booking_telegram_chat_id" 
                               name="booking_telegram_chat_id" 
                               value="<?php echo esc_attr(get_option('booking_telegram_chat_id', '')); ?>" 
                               class="regular-text">
                        <p class="description">
                            Chat ID của nhóm hoặc người nhận. 
                            <a href="https://t.me/userinfobot" target="_blank">Lấy Chat ID</a>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="booking_zalo_access_token">Zalo Access Token</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="booking_zalo_access_token" 
                               name="booking_zalo_access_token" 
                               value="<?php echo esc_attr(get_option('booking_zalo_access_token', '')); ?>" 
                               class="regular-text">
                        <p class="description">
                            Access Token của Zalo OA. 
                            <a href="https://developers.zalo.me/" target="_blank">Hướng dẫn lấy token</a>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="booking_zalo_phone">Zalo Phone Number</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="booking_zalo_phone" 
                               name="booking_zalo_phone" 
                               value="<?php echo esc_attr(get_option('booking_zalo_phone', '')); ?>" 
                               class="regular-text">
                        <p class="description">Số điện thoại Zalo nhận thông báo (không có số 0 đầu, VD: 84912345678)</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Lưu Cài Đặt Thông Báo'); ?>
        </form>
        
        <hr>
        
        <h2>Hướng Dẫn Sử Dụng</h2>
        <p>Để hiển thị form đặt xe trên trang hoặc bài viết, sử dụng shortcode:</p>
        <code>[dat_xe]</code>
        
        <h3>Các bước cài đặt:</h3>
        <ol>
            <li>Lấy Google Maps API Key từ <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
            <li>Bật các API sau: Maps JavaScript API, Places API, Distance Matrix API</li>
            <li>Nhập API Key vào ô bên trên</li>
            <li>Chọn chế độ tính giá (Tự động hoặc Tùy chỉnh)</li>
            <li>Cấu hình thông báo Telegram/Zalo (tùy chọn)</li>
            <li>Thêm shortcode <code>[dat_xe]</code> vào trang đặt xe</li>
            <li>Thêm shortcode <code>[dang_ky_tai_xe]</code> vào trang đăng ký tài xế</li>
        </ol>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Auto dismiss success notice after 5 seconds
    setTimeout(function() {
        $('#pricing-success-notice').fadeOut();
    }, 5000);
    
    // Tab switching - không cần nữa vì dùng URL params
    $('.nav-tab').on('click', function(e) {
        // Let the default link behavior work (navigate to URL with tab param)
    });
    
    // Toggle pricing mode
    window.togglePricingMode = function(mode) {
        if (mode === 'custom') {
            $('#custom-pricing-section').show();
        } else {
            $('#custom-pricing-section').hide();
        }
    };
    
    // Show add pricing form
    $('#add-pricing-btn').on('click', function(e) {
        e.preventDefault();
        $('#pricing_id').val('');
        $('#pricing-form')[0].reset();
        $('#pricing_is_active').prop('checked', true);
        $('#pricing-form-title').text('Thêm Bảng Giá Mới');
        $('#pricing-form-container').slideDown();
        $('html, body').animate({
            scrollTop: $('#pricing-form-container').offset().top - 50
        }, 500);
    });
    
    // Cancel pricing form
    $('#cancel-pricing-btn').on('click', function(e) {
        e.preventDefault();
        $('#pricing-form-container').slideUp();
        $('#pricing-form')[0].reset();
    });
    
    // Handle form submission with loading spinner
    $('#pricing-form').on('submit', function() {
        var $btn = $('#save-pricing-btn');
        $btn.prop('disabled', true);
        $btn.find('.button-text').hide();
        $btn.find('.spinner-icon').show();
    });
    
    // Edit pricing
    $('.edit-pricing').on('click', function(e) {
        e.preventDefault();
        var pricingId = $(this).data('id');
        
        // Get pricing data from table row
        var row = $(this).closest('tr');
        var fromLocation = row.find('td:eq(0)').text().trim();
        var toLocation = row.find('td:eq(1)').text().trim();
        var carType = row.find('td:eq(2)').text();
        var tripType = row.find('td:eq(3)').text() === 'Sân bay' ? 'airport' : 'long_distance';
        var basePrice = row.find('td:eq(4)').text().replace(/[^\d]/g, '');
        var pricePerKm = row.find('td:eq(5)').text().replace(/[^\d]/g, '');
        var vatRate = parseFloat(row.find('td:eq(6)').text()) / 100;
        var isActive = row.find('td:eq(7)').text().includes('✅');
        
        // Handle "Tất cả" text
        if (fromLocation === 'Tất cả') fromLocation = '';
        if (toLocation === 'Tất cả') toLocation = '';
        
        // Fill form
        $('#pricing_id').val(pricingId);
        $('#pricing_from_location').val(fromLocation);
        $('#pricing_to_location').val(toLocation);
        $('#pricing_car_type').val(carType);
        $('#pricing_trip_type').val(tripType);
        $('#pricing_base_price').val(basePrice);
        $('#pricing_price_per_km').val(pricePerKm);
        $('#pricing_min_distance').val(0);
        $('#pricing_max_distance').val(0);
        $('#pricing_vat_rate').val(vatRate);
        $('#pricing_is_active').prop('checked', isActive);
        
        $('#pricing-form-title').text('Sửa Bảng Giá');
        $('#pricing-form-container').slideDown();
        $('html, body').animate({
            scrollTop: $('#pricing-form-container').offset().top - 50
        }, 500);
    });
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.spin-animation {
    display: inline-block;
    animation: spin 1s linear infinite;
}
.spinner-icon {
    color: #fff;
}
.wrap {
    max-width: 1200px;
}
.wrap code {
    background: #f0f0f0;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 14px;
}
.nav-tab-wrapper {
    margin-bottom: 20px;
}
.tab-content {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccc;
    border-top: none;
}
#custom-pricing-section {
    margin-top: 20px;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}
#pricing-form-container {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
#pricing-form-container h4 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}
#pricing-form .form-table th {
    width: 200px;
}
#pricing-form .description {
    margin-top: 5px;
    font-style: italic;
    color: #666;
}
</style>
