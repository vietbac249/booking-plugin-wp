<div class="booking-container">
    <h2 class="booking-title">ĐẶT XE</h2>
    
    <!-- Tabs -->
    <div class="booking-tabs">
        <button class="booking-tab-btn active" data-tab="airport">Sân bay</button>
        <button class="booking-tab-btn" data-tab="longdistance">Đường dài</button>
    </div>

    <!-- Tab 1: Sân bay Nội Bài -->
    <div class="booking-tab-content active" id="airport-tab">
        <div class="booking-form-group">
            <label>Bạn đi từ:</label>
            <div class="booking-input-wrapper">
                <span class="booking-location-icon origin">⊙</span>
                <input type="text" id="airport-from" class="booking-location-input" placeholder="Điểm đi">
                <button class="booking-add-stop-btn" id="add-airport-stop" title="Thêm điểm dừng">+</button>
            </div>
        </div>

        <div id="airport-stops-container"></div>

        <div class="booking-form-group">
            <label>Bạn muốn đến:</label>
            <div class="booking-input-wrapper">
                <span class="booking-location-icon destination">📍</span>
                <input type="text" id="airport-to" class="booking-location-input" value="Sân bay Nội Bài" readonly>
            </div>
        </div>

        <div class="booking-toggle-group">
            <div class="booking-toggle-wrapper">
                <span class="booking-toggle-label">2 chiều</span>
                <div class="booking-toggle" id="round-trip-toggle">
                    <div class="booking-toggle-slider"></div>
                </div>
            </div>

            <div class="booking-toggle-wrapper">
                <span class="booking-toggle-label">VAT</span>
                <div class="booking-toggle" id="vat-airport-toggle">
                    <div class="booking-toggle-slider"></div>
                </div>
            </div>

            <button class="booking-swap-direction-btn" id="swap-airport">
                🔄 Đảo chiều
            </button>
        </div>

        <div class="booking-row">
            <div class="booking-car-wrapper">
                <label>Loại xe</label>
                <div class="booking-car-select-custom" id="airport-car-select">
                    <div class="booking-car-selected">
                        <span class="booking-car-icon">🚗</span>
                        <span class="booking-car-name">4 chỗ cốp rộng</span>
                        <span class="booking-dropdown-arrow">▼</span>
                    </div>
                    <div class="booking-car-options">
                        <div class="booking-car-option active" data-value="4-seat">
                            <span class="car-emoji">🚗</span>
                            <span class="car-label">4 chỗ cốp rộng</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="7-seat">
                            <span class="car-emoji">🚙</span>
                            <span class="car-label">7 chỗ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="4-seat-small">
                            <span class="car-emoji">🚘</span>
                            <span class="car-label">4 chỗ cốp nhỏ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="16-seat">
                            <span class="car-emoji">🚐</span>
                            <span class="car-label">16 chỗ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="29-seat">
                            <span class="car-emoji">🚌</span>
                            <span class="car-label">29 chỗ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="45-seat">
                            <span class="car-emoji">🚍</span>
                            <span class="car-label">45 chỗ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                    </div>
                    <input type="hidden" id="airport-car-type" value="4-seat">
                </div>
            </div>

            <div class="booking-datetime-wrapper">
                <label>Thời gian đi</label>
                <div class="booking-datetime-input-wrapper">
                    <span class="booking-datetime-icon">📅</span>
                    <input type="text" id="airport-datetime" class="booking-datetime-picker" placeholder="Chọn ngày và giờ" readonly>
                </div>
            </div>
        </div>

        <button class="booking-calculate-btn" id="calc-airport">
            <span>Kiểm Tra Giá</span>
            <span>→</span>
        </button>

        <div class="booking-result-box" id="airport-result-box" style="display: none;">
            <div class="booking-result-item">
                <span class="booking-result-label">Khoảng cách</span>
                <span class="booking-result-value" id="airport-distance">-- km</span>
            </div>
            <div class="booking-result-item">
                <span class="booking-result-label">Cước phí</span>
                <span class="booking-result-value booking-price" id="airport-price">-- VNĐ</span>
            </div>
        </div>

        <div class="booking-contact-form" id="airport-contact-form" style="display: none;">
            <h3>Thông tin liên hệ</h3>
            
            <input type="tel" id="airport-phone" class="booking-contact-input" placeholder="Số điện thoại *" required>
            <input type="text" id="airport-name" class="booking-contact-input" placeholder="Họ và tên *" required>

            <button class="booking-submit-btn" id="submit-airport">
                <span>Đặt xe ngay</span>
                <span>→</span>
            </button>
        </div>
    </div>

    <!-- Tab 2: Đường dài -->
    <div class="booking-tab-content" id="longdistance-tab">
        <div class="booking-form-group">
            <label>Điểm đi:</label>
            <div class="booking-input-wrapper">
                <span class="booking-location-icon origin">⊙</span>
                <input type="text" id="long-from" class="booking-location-input" placeholder="Điểm đi">
                <button class="booking-add-stop-btn" id="add-long-stop" title="Thêm điểm dừng">+</button>
            </div>
        </div>

        <div id="long-stops-container"></div>

        <div class="booking-form-group">
            <label>Điểm đến:</label>
            <div class="booking-input-wrapper">
                <span class="booking-location-icon destination">📍</span>
                <input type="text" id="long-to" class="booking-location-input" placeholder="Điểm đến">
            </div>
        </div>

        <div class="booking-toggle-group">
            <div class="booking-toggle-wrapper">
                <span class="booking-toggle-label">2 chiều</span>
                <div class="booking-toggle" id="round-trip-long-toggle">
                    <div class="booking-toggle-slider"></div>
                </div>
            </div>

            <div class="booking-toggle-wrapper">
                <span class="booking-toggle-label">VAT</span>
                <div class="booking-toggle" id="vat-long-toggle">
                    <div class="booking-toggle-slider"></div>
                </div>
            </div>

            <button class="booking-swap-direction-btn" id="swap-long">
                🔄 Đảo chiều
            </button>
        </div>

        <div class="booking-row">
            <div class="booking-car-wrapper">
                <label>Loại xe</label>
                <div class="booking-car-select-custom" id="long-car-select">
                    <div class="booking-car-selected">
                        <span class="booking-car-icon">🚗</span>
                        <span class="booking-car-name">4 chỗ cốp rộng</span>
                        <span class="booking-dropdown-arrow">▼</span>
                    </div>
                    <div class="booking-car-options">
                        <div class="booking-car-option active" data-value="4-seat">
                            <span class="car-emoji">🚗</span>
                            <span class="car-label">4 chỗ cốp rộng</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="7-seat">
                            <span class="car-emoji">🚙</span>
                            <span class="car-label">7 chỗ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="4-seat-small">
                            <span class="car-emoji">🚘</span>
                            <span class="car-label">4 chỗ cốp nhỏ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="16-seat">
                            <span class="car-emoji">🚐</span>
                            <span class="car-label">16 chỗ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="29-seat">
                            <span class="car-emoji">🚌</span>
                            <span class="car-label">29 chỗ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                        <div class="booking-car-option" data-value="45-seat">
                            <span class="car-emoji">🚍</span>
                            <span class="car-label">45 chỗ</span>
                            <span class="car-checkmark">✓</span>
                        </div>
                    </div>
                    <input type="hidden" id="long-car-type" value="4-seat">
                </div>
            </div>

            <div class="booking-datetime-wrapper">
                <label>Thời gian đi</label>
                <div class="booking-datetime-input-wrapper">
                    <span class="booking-datetime-icon">📅</span>
                    <input type="text" id="long-datetime" class="booking-datetime-picker" placeholder="Chọn ngày và giờ" readonly>
                </div>
            </div>
        </div>

        <button class="booking-calculate-btn" id="calc-long">
            <span>Kiểm Tra Giá</span>
            <span>→</span>
        </button>

        <div class="booking-result-box" id="long-result-box" style="display: none;">
            <div class="booking-result-item">
                <span class="booking-result-label">Khoảng cách</span>
                <span class="booking-result-value" id="long-distance">-- km</span>
            </div>
            <div class="booking-result-item">
                <span class="booking-result-label">Cước phí</span>
                <span class="booking-result-value booking-price" id="long-price">-- VNĐ</span>
            </div>
        </div>

        <div class="booking-contact-form" id="long-contact-form" style="display: none;">
            <h3>Thông tin liên hệ</h3>
            
            <input type="tel" id="long-phone" class="booking-contact-input" placeholder="Số điện thoại *" required>
            <input type="text" id="long-name" class="booking-contact-input" placeholder="Họ và tên *" required>

            <button class="booking-submit-btn" id="submit-long">
                <span>Đặt xe ngay</span>
                <span>→</span>
            </button>
        </div>
    </div>
</div>
