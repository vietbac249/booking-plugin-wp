jQuery(document).ready(function($) {
    
    // Biến lưu số điểm dừng
    let airportStopsCount = 0;
    let longStopsCount = 0;
    const MAX_STOPS = 2;
    
    // ========================================
    // CUSTOM DROPDOWN FUNCTIONS (VER 3)
    // ========================================
    
    function initCustomDropdowns() {
        // Initialize airport car dropdown
        initCarDropdown('#airport-car-select', '#airport-car-type');
        
        // Initialize long distance car dropdown
        initCarDropdown('#long-car-select', '#long-car-type');
    }
    
    function initCarDropdown(dropdownSelector, hiddenInputSelector) {
        const $dropdown = $(dropdownSelector);
        if (!$dropdown.length) return;
        
        const $hiddenInput = $(hiddenInputSelector);
        const $selected = $dropdown.find('.booking-car-selected');
        const $options = $dropdown.find('.booking-car-options');
        const $carOptions = $dropdown.find('.booking-car-option');
        
        // Toggle dropdown
        $selected.on('click', function(e) {
            e.stopPropagation();
            
            // Close other dropdowns
            $('.booking-car-select-custom').not($dropdown).removeClass('open');
            
            // Toggle current dropdown
            $dropdown.toggleClass('open');
        });
        
        // Select option
        $carOptions.on('click', function(e) {
            e.stopPropagation();
            
            const value = $(this).data('value');
            const emoji = $(this).find('.car-emoji').text();
            const label = $(this).find('.car-label').text();
            
            // Update selected display
            $dropdown.find('.booking-car-icon').text(emoji);
            $dropdown.find('.booking-car-name').text(label);
            
            // Update hidden input
            $hiddenInput.val(value);
            
            // Update active state
            $carOptions.removeClass('active');
            $(this).addClass('active');
            
            // Close dropdown
            $dropdown.removeClass('open');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest(dropdownSelector).length) {
                $dropdown.removeClass('open');
            }
        });
    }
    
    // ========================================
    // FLATPICKR DATETIME PICKER (VER 3)
    // ========================================
    
    function initDateTimePickers() {
        if (typeof flatpickr === 'undefined') {
            console.warn('Flatpickr library not loaded');
            return;
        }
        
        // Set default date/time (1 hour from now)
        const now = new Date();
        now.setHours(now.getHours() + 1);
        now.setMinutes(0);
        
        // Time options (every hour from 0:00 to 23:00)
        const timeOptions = [];
        for (let h = 0; h < 24; h++) {
            timeOptions.push(`${h.toString().padStart(2, '0')}:00`);
        }
        
        // Function to add custom time grid
        function addTimeGrid(instance) {
            const calendarContainer = instance.calendarContainer;
            
            // Check if time grid already exists
            if (calendarContainer.querySelector('.flatpickr-time-grid')) {
                return;
            }
            
            // Create time grid container
            const timeGridContainer = document.createElement('div');
            timeGridContainer.className = 'flatpickr-time';
            timeGridContainer.style.display = 'none'; // Hidden initially
            
            const timeGrid = document.createElement('div');
            timeGrid.className = 'flatpickr-time-grid';
            
            // Add time options
            timeOptions.forEach(time => {
                const timeOption = document.createElement('div');
                timeOption.className = 'flatpickr-time-option';
                timeOption.textContent = time;
                timeOption.dataset.time = time;
                
                // Click handler for time selection
                timeOption.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Remove selected class from all options
                    timeGrid.querySelectorAll('.flatpickr-time-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    
                    // Get selected date
                    const selectedDate = instance.selectedDates[0];
                    if (selectedDate) {
                        // Parse time
                        const [hours, minutes] = time.split(':').map(Number);
                        selectedDate.setHours(hours);
                        selectedDate.setMinutes(minutes || 0);
                        
                        // Update input value
                        const day = selectedDate.getDate().toString().padStart(2, '0');
                        const month = (selectedDate.getMonth() + 1).toString().padStart(2, '0');
                        const year = selectedDate.getFullYear();
                        const formattedDateTime = `${day}/${month}/${year} ${time}`;
                        
                        instance.input.value = formattedDateTime;
                        
                        // Close picker after selection
                        setTimeout(() => {
                            instance.close();
                        }, 300);
                    }
                });
                
                timeGrid.appendChild(timeOption);
            });
            
            timeGridContainer.appendChild(timeGrid);
            calendarContainer.appendChild(timeGridContainer);
        }
        
        // Common Flatpickr config
        const flatpickrConfig = {
            enableTime: false, // Disable default time picker
            dateFormat: "d/m/Y H:i",
            minDate: "today",
            defaultDate: now,
            locale: "vn",
            disableMobile: true,
            onReady: function(selectedDates, dateStr, instance) {
                // Add custom time grid after calendar
                addTimeGrid(instance);
            },
            onOpen: function(selectedDates, dateStr, instance) {
                // Make sure time grid exists
                addTimeGrid(instance);
                
                // If date is already selected, show time grid
                if (selectedDates.length > 0) {
                    const timeGridContainer = instance.calendarContainer.querySelector('.flatpickr-time');
                    if (timeGridContainer) {
                        timeGridContainer.style.display = 'block';
                    }
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    // Show time grid when date is selected
                    const timeGridContainer = instance.calendarContainer.querySelector('.flatpickr-time');
                    if (timeGridContainer) {
                        timeGridContainer.style.display = 'block';
                    }
                }
            }
        };
        
        // Initialize airport datetime picker
        if ($('#airport-datetime').length) {
            const airportPicker = flatpickr('#airport-datetime', flatpickrConfig);
        }
        
        // Initialize long distance datetime picker
        if ($('#long-datetime').length) {
            const longPicker = flatpickr('#long-datetime', flatpickrConfig);
        }
    }
    
    // Khởi tạo
    initTabs();
    initAutocomplete();
    initCustomDropdowns();
    initDateTimePickers();
    initEventListeners();
    
    // Xử lý tabs
    function initTabs() {
        $('.booking-tab-btn').on('click', function() {
            const tabName = $(this).data('tab');
            
            $('.booking-tab-btn').removeClass('active');
            $('.booking-tab-content').removeClass('active');
            
            $(this).addClass('active');
            $('#' + tabName + '-tab').addClass('active');
        });
    }
    
    // Khởi tạo Google Maps Autocomplete
    function initAutocomplete() {
        if (typeof google !== 'undefined' && google.maps) {
            const options = {
                componentRestrictions: { country: 'vn' }
            };
            
            const airportFrom = document.getElementById('airport-from');
            const longFrom = document.getElementById('long-from');
            const longTo = document.getElementById('long-to');
            
            if (airportFrom) new google.maps.places.Autocomplete(airportFrom, options);
            if (longFrom) new google.maps.places.Autocomplete(longFrom, options);
            if (longTo) new google.maps.places.Autocomplete(longTo, options);
        }
    }
    
    // Khởi tạo event listeners
    function initEventListeners() {
        // Nút đảo chiều sân bay
        $('#swap-airport').on('click', function(e) {
            e.preventDefault();
            swapAirportLocations();
        });
        
        // Nút đảo chiều đường dài
        $('#swap-long').on('click', function(e) {
            e.preventDefault();
            swapLongLocations();
        });
        
        // Toggle switches
        $('#round-trip-toggle').on('click', function() {
            $(this).toggleClass('active');
        });
        
        $('#vat-airport-toggle').on('click', function() {
            $(this).toggleClass('active');
        });
        
        $('#round-trip-long-toggle').on('click', function() {
            $(this).toggleClass('active');
        });
        
        $('#vat-long-toggle').on('click', function() {
            $(this).toggleClass('active');
        });
        
        // Nút thêm điểm dừng
        $('#add-airport-stop').on('click', function(e) {
            e.preventDefault();
            addStop('airport');
        });
        
        $('#add-long-stop').on('click', function(e) {
            e.preventDefault();
            addStop('long');
        });
        
        // Nút tính giá
        $('#calc-airport').on('click', function(e) {
            e.preventDefault();
            calculatePrice('airport');
        });
        
        $('#calc-long').on('click', function(e) {
            e.preventDefault();
            calculatePrice('long');
        });
        
        // Cập nhật hiển thị loại xe
        $('#airport-car-type').on('change', function() {
            updateCarDisplay('airport');
        });
        
        $('#long-car-type').on('change', function() {
            updateCarDisplay('long');
        });
        
        // Set thời gian mặc định
        setDefaultDateTime();
        
        // Xử lý submit đặt xe
        $('#submit-airport').on('click', function(e) {
            e.preventDefault();
            submitBooking('airport');
        });
        
        $('#submit-long').on('click', function(e) {
            e.preventDefault();
            submitBooking('long');
        });
    }
    
    // Submit đặt xe
    function submitBooking(type) {
        const phone = $('#' + type + '-phone').val();
        const name = $('#' + type + '-name').val();
        
        if (!phone || !name) {
            alert('Vui lòng nhập đầy đủ thông tin liên hệ');
            return;
        }
        
        // Validate số điện thoại
        const phoneRegex = /^[0-9]{10,11}$/;
        if (!phoneRegex.test(phone)) {
            alert('Số điện thoại không hợp lệ');
            return;
        }
        
        const $btn = $('#submit-' + type);
        $btn.prop('disabled', true).html('Đang xử lý... <span class="booking-loading"></span>');
        
        // Gọi AJAX để lưu booking
        $.ajax({
            url: bookingAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'submit_booking',
                nonce: bookingAjax.nonce,
                type: type,
                phone: phone,
                name: name,
                from: $('#' + type + '-from').val(),
                to: $('#' + type + '-to').val(),
                car_type: $('#' + type + '-car-type').val(),
                datetime: $('#' + type + '-datetime').val(),
                price: $('#' + type + '-price').text()
            },
            success: function(response) {
                if (response.success) {
                    alert('Đặt xe thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.');
                    location.reload();
                } else {
                    alert(response.data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('Đặt xe <span>→</span>');
            }
        });
    }
    
    // Thêm điểm dừng
    function addStop(type) {
        const stopsCount = type === 'airport' ? airportStopsCount : longStopsCount;
        
        if (stopsCount >= MAX_STOPS) {
            alert('Chỉ được thêm tối đa ' + MAX_STOPS + ' điểm dừng');
            return;
        }
        
        const stopId = type + '-stop-' + (stopsCount + 1);
        const stopHtml = `
            <div class="booking-form-group booking-stop-item" id="${stopId}">
                <label>Điểm dừng ${stopsCount + 1}:</label>
                <div class="booking-input-wrapper">
                    <span class="booking-location-icon">🟢</span>
                    <input type="text" class="booking-location-input ${type}-stop-input" placeholder="Điểm dừng tiếp theo">
                    <button class="booking-remove-stop-btn" onclick="removeStop('${stopId}', '${type}')">−</button>
                </div>
            </div>
        `;
        
        $('#' + type + '-stops-container').append(stopHtml);
        
        // Khởi tạo autocomplete cho input mới
        const newInput = $('#' + stopId + ' input')[0];
        if (typeof google !== 'undefined' && google.maps) {
            new google.maps.places.Autocomplete(newInput, {
                componentRestrictions: { country: 'vn' }
            });
        }
        
        if (type === 'airport') {
            airportStopsCount++;
        } else {
            longStopsCount++;
        }
        
        // Ẩn nút thêm nếu đã đủ
        if ((type === 'airport' ? airportStopsCount : longStopsCount) >= MAX_STOPS) {
            $('#add-' + type + '-stop').hide();
        }
    }
    
    // Xóa điểm dừng (global function)
    window.removeStop = function(stopId, type) {
        $('#' + stopId).remove();
        
        if (type === 'airport') {
            airportStopsCount--;
        } else {
            longStopsCount--;
        }
        
        // Hiện lại nút thêm
        $('#add-' + type + '-stop').show();
        
        // Cập nhật lại số thứ tự
        $('#' + type + '-stops-container .booking-stop-item').each(function(index) {
            $(this).find('label').text('Điểm dừng ' + (index + 1) + ':');
        });
    };
    
    // Set thời gian mặc định (hiện tại + 1 giờ)
    function setDefaultDateTime() {
        const now = new Date();
        now.setHours(now.getHours() + 1);
        const dateTimeString = now.toISOString().slice(0, 16);
        $('#airport-datetime').val(dateTimeString);
        $('#long-datetime').val(dateTimeString);
    }
    
    // Cập nhật hiển thị loại xe
    function updateCarDisplay(type) {
        const carType = $('#' + type + '-car-type').val();
        const carNames = {
            '4-seat': '4 chỗ cốp rộng',
            '7-seat': '7 chỗ',
            '4-seat-small': '4 chỗ cốp nhỏ',
            '16-seat': '16 chỗ',
            '29-seat': '29 chỗ',
            '45-seat': '45 chỗ'
        };
        $('#' + type + '-car-display').text(carNames[carType]);
    }
    
    // Đảo chiều điểm đi - điểm đến (Sân bay)
    function swapAirportLocations() {
        const $fromInput = $('#airport-from');
        const $toInput = $('#airport-to');
        
        const tempValue = $fromInput.val();
        $fromInput.val($toInput.val());
        $toInput.val(tempValue);
        
        // Toggle readonly
        if ($fromInput.prop('readonly')) {
            $fromInput.prop('readonly', false);
            $toInput.prop('readonly', true);
            $toInput.val('Sân bay Nội Bài');
        } else {
            $fromInput.prop('readonly', true);
            $toInput.prop('readonly', false);
            $fromInput.val('Sân bay Nội Bài');
        }
    }
    
    // Đảo chiều điểm đi - điểm đến (Đường dài)
    function swapLongLocations() {
        const $fromInput = $('#long-from');
        const $toInput = $('#long-to');
        
        const tempValue = $fromInput.val();
        $fromInput.val($toInput.val());
        $toInput.val(tempValue);
    }
    
    // Tính giá
    function calculatePrice(type) {
        let from, to, roundTrip, needVAT, carType, datetime;
        
        if (type === 'airport') {
            from = $('#airport-from').val();
            to = $('#airport-to').val();
            roundTrip = $('#round-trip-toggle').hasClass('active');
            needVAT = $('#vat-airport-toggle').hasClass('active');
            carType = $('#airport-car-type').val(); // Hidden input
            datetime = $('#airport-datetime').val();
        } else {
            from = $('#long-from').val();
            to = $('#long-to').val();
            roundTrip = $('#round-trip-long-toggle').hasClass('active');
            needVAT = $('#vat-long-toggle').hasClass('active');
            carType = $('#long-car-type').val(); // Hidden input
            datetime = $('#long-datetime').val();
        }
        
        if (!from || !to) {
            alert('Vui lòng nhập đầy đủ thông tin điểm đi và điểm đến');
            return;
        }
        
        if (!datetime) {
            alert('Vui lòng chọn thời gian đi');
            return;
        }
        
        const $btn = $('#calc-' + type);
        $btn.prop('disabled', true).html('Đang tính... <span class="booking-loading"></span>');
        
        // Kiểm tra chế độ tính giá
        const pricingMode = bookingAjax.pricingMode || 'auto';
        
        if (pricingMode === 'custom') {
            // Sử dụng bảng giá tùy chỉnh
            calculateCustomPrice(type, carType, roundTrip, needVAT, $btn);
        } else {
            // Sử dụng Google Maps API
            calculateAutoPrice(type, from, to, carType, roundTrip, needVAT, datetime, $btn);
        }
    }
    
    // Tính giá tự động (Google Maps API)
    function calculateAutoPrice(type, from, to, carType, roundTrip, needVAT, datetime, $btn) {
        $.ajax({
            url: bookingAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'calculate_distance',
                nonce: bookingAjax.nonce,
                origin: from,
                destination: to,
                car_type: carType,
                datetime: datetime
            },
            success: function(response) {
                if (response.success) {
                    const distance = response.data.distance;
                    const pricing = type === 'airport' ? bookingAjax.pricing.airport : bookingAjax.pricing.longDistance;
                    
                    // Tính giá cơ bản theo loại xe
                    let basePrice = pricing.basePrice;
                    const carMultipliers = {
                        '4-seat': 1,
                        '7-seat': 1.3,
                        '4-seat-small': 0.9,
                        '16-seat': 2,
                        '29-seat': 3,
                        '45-seat': 4
                    };
                    
                    let price = distance * basePrice * carMultipliers[carType];
                    
                    if (roundTrip && type === 'airport') {
                        price = price * 2 * pricing.roundTripMultiplier;
                    }
                    
                    if (needVAT) {
                        price = price * (1 + pricing.vatRate);
                    }
                    
                    displayResult(type, distance, price, roundTrip);
                    
                    // Hiện form liên hệ
                    $('#' + type + '-result-box').slideDown(400);
                    $('#' + type + '-contact-form').slideDown(400);
                    $('#calc-' + type).hide();
                } else {
                    alert(response.data.message || 'Không thể tính khoảng cách');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Kiểm Tra Giá');
            }
        });
    }
    
    // Tính giá theo bảng tùy chỉnh (không cần Google API)
    function calculateCustomPrice(type, carType, roundTrip, needVAT, $btn) {
        // Map car type từ select value sang tên trong database
        const carTypeMap = {
            '4-seat': '4 chỗ cốp rộng',
            '7-seat': '7 chỗ',
            '4-seat-small': '4 chỗ cốp nhỏ',
            '16-seat': '16 chỗ',
            '29-seat': '29 chỗ',
            '45-seat': '45 chỗ'
        };
        
        const tripType = type === 'airport' ? 'airport' : 'long_distance';
        const from = $('#' + type + '-from').val();
        const to = $('#' + type + '-to').val();
        
        $.ajax({
            url: bookingAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'calculate_custom_price',
                nonce: bookingAjax.nonce,
                car_type: carTypeMap[carType],
                trip_type: tripType,
                is_round_trip: roundTrip,
                has_vat: needVAT,
                from: from,
                to: to
            },
            success: function(response) {
                if (response.success) {
                    const distance = response.data.distance;
                    const price = response.data.price;
                    const message = response.data.message;
                    
                    displayResult(type, distance, price, roundTrip);
                    
                    // Hiển thị thông báo chế độ test
                    if (message) {
                        $('#' + type + '-result-box').prepend(
                            '<div class="test-mode-notice" style="background:#fff3cd; padding:10px; border-radius:8px; margin-bottom:10px; color:#856404;">' +
                            '⚠️ ' + message +
                            '</div>'
                        );
                    }
                    
                    // Hiện form liên hệ
                    $('#' + type + '-result-box').slideDown(400);
                    $('#' + type + '-contact-form').slideDown(400);
                    $('#calc-' + type).hide();
                } else {
                    alert(response.data.message || 'Không thể tính giá. Vui lòng kiểm tra bảng giá trong Cài Đặt.');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Kiểm Tra Giá');
            }
        });
    }
    
    // Hiển thị kết quả
    function displayResult(type, distance, price, isRoundTrip) {
        let displayDistance = distance;
        if (isRoundTrip) {
            displayDistance = distance * 2;
        }
        
        $('#' + type + '-distance').text(displayDistance.toFixed(1) + ' km');
        $('#' + type + '-price').text(formatPrice(price));
    }
    
    // Format giá tiền
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }
});
