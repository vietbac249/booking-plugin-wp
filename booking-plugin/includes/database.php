<?php
/**
 * Database Schema for Phase 2
 * Tạo các bảng cần thiết cho hệ thống quản trị
 */

if (!defined('ABSPATH')) {
    exit;
}

class Booking_Database {
    
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Table: Bookings (Đơn hàng)
        $table_bookings = $wpdb->prefix . 'bookings';
        $sql_bookings = "CREATE TABLE $table_bookings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_code varchar(20) NOT NULL,
            customer_name varchar(100) NOT NULL,
            customer_phone varchar(20) NOT NULL,
            customer_email varchar(100),
            from_location text NOT NULL,
            to_location text NOT NULL,
            stops text,
            car_type varchar(50) NOT NULL,
            trip_type varchar(20) NOT NULL,
            is_round_trip tinyint(1) DEFAULT 0,
            has_vat tinyint(1) DEFAULT 0,
            trip_datetime datetime NOT NULL,
            distance decimal(10,2),
            price decimal(15,2) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            driver_id bigint(20),
            driver_accepted_via varchar(20),
            driver_accepted_at timestamp NULL,
            assigned_at timestamp NULL,
            assigned_by bigint(20),
            assignment_type varchar(20),
            group_id bigint(20),
            accept_token varchar(64),
            token_expires bigint(20),
            cancel_reason text,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY booking_code (booking_code),
            KEY driver_id (driver_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql_bookings);
        
        // Table: Drivers (Tài xế)
        $table_drivers = $wpdb->prefix . 'drivers';
        $sql_drivers = "CREATE TABLE $table_drivers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            full_name varchar(100) NOT NULL,
            id_card varchar(20) NOT NULL,
            phone varchar(20) NOT NULL,
            email varchar(100),
            address text,
            birth_date date,
            car_type varchar(50) NOT NULL,
            car_plate varchar(20) NOT NULL,
            car_brand varchar(50),
            car_color varchar(30),
            car_year int(4),
            license_number varchar(20) NOT NULL,
            id_card_front varchar(255),
            id_card_back varchar(255),
            ekyc_photo varchar(255),
            telegram_chat_id varchar(100),
            zalo_user_id varchar(100),
            status varchar(20) DEFAULT 'pending',
            rating decimal(3,2) DEFAULT 0,
            total_trips int(11) DEFAULT 0,
            completed_trips int(11) DEFAULT 0,
            cancelled_trips int(11) DEFAULT 0,
            points int(11) DEFAULT 0,
            rank varchar(20) DEFAULT 'new',
            joined_date date,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY phone (phone),
            KEY status (status),
            KEY rank (rank)
        ) $charset_collate;";
        
        dbDelta($sql_drivers);
        
        // Table: Driver Documents (Giấy tờ tài xế)
        $table_documents = $wpdb->prefix . 'driver_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            driver_id bigint(20) NOT NULL,
            document_type varchar(50) NOT NULL,
            file_path varchar(255) NOT NULL,
            verified tinyint(1) DEFAULT 0,
            verified_by bigint(20),
            verified_at timestamp NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY driver_id (driver_id)
        ) $charset_collate;";
        
        dbDelta($sql_documents);
        
        // Table: Contracts (Hợp đồng)
        $table_contracts = $wpdb->prefix . 'contracts';
        $sql_contracts = "CREATE TABLE $table_contracts (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            contract_code varchar(20) NOT NULL,
            driver_id bigint(20) NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            status varchar(20) DEFAULT 'active',
            signed_at timestamp NULL,
            signature_otp varchar(10),
            contract_file varchar(255),
            created_by bigint(20),
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY contract_code (contract_code),
            KEY driver_id (driver_id)
        ) $charset_collate;";
        
        dbDelta($sql_contracts);
        
        // Table: Reviews (Đánh giá)
        $table_reviews = $wpdb->prefix . 'reviews';
        $sql_reviews = "CREATE TABLE $table_reviews (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) NOT NULL,
            driver_id bigint(20) NOT NULL,
            customer_name varchar(100),
            rating int(1) NOT NULL,
            on_time_rating int(1),
            attitude_rating int(1),
            cleanliness_rating int(1),
            safety_rating int(1),
            comment text,
            images text,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY booking_id (booking_id),
            KEY driver_id (driver_id)
        ) $charset_collate;";
        
        dbDelta($sql_reviews);
        
        // Table: Driver Points (Điểm tài xế)
        $table_points = $wpdb->prefix . 'driver_points';
        $sql_points = "CREATE TABLE $table_points (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            driver_id bigint(20) NOT NULL,
            points int(11) NOT NULL,
            reason varchar(255),
            booking_id bigint(20),
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY driver_id (driver_id)
        ) $charset_collate;";
        
        dbDelta($sql_points);
        
        // Table: Booking Logs (Lịch sử đơn hàng)
        $table_logs = $wpdb->prefix . 'booking_logs';
        $sql_logs = "CREATE TABLE $table_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) NOT NULL,
            old_status varchar(20),
            new_status varchar(20) NOT NULL,
            changed_by bigint(20),
            note text,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY booking_id (booking_id)
        ) $charset_collate;";
        
        dbDelta($sql_logs);
        
        // Table: Custom Pricing (Bảng giá tùy chỉnh)
        $table_pricing = $wpdb->prefix . 'custom_pricing';
        $sql_pricing = "CREATE TABLE $table_pricing (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            from_location varchar(255),
            to_location varchar(255),
            car_type varchar(50) NOT NULL,
            trip_type varchar(20) NOT NULL,
            base_price decimal(10,2) NOT NULL,
            price_per_km decimal(10,2) NOT NULL,
            min_distance decimal(10,2) DEFAULT 0,
            max_distance decimal(10,2) DEFAULT 0,
            vat_rate decimal(5,2) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY car_type (car_type),
            KEY trip_type (trip_type),
            KEY from_location (from_location),
            KEY to_location (to_location)
        ) $charset_collate;";
        
        dbDelta($sql_pricing);
        
        // Table: Booking Assignment Logs (Lịch sử gán đơn hàng)
        $table_assignment_logs = $wpdb->prefix . 'booking_assignment_logs';
        $sql_assignment_logs = "CREATE TABLE $table_assignment_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) NOT NULL,
            driver_id bigint(20),
            group_id bigint(20),
            assignment_type varchar(20) NOT NULL,
            assigned_by bigint(20) NOT NULL,
            status varchar(20) NOT NULL,
            accepted_at timestamp NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY booking_id (booking_id),
            KEY driver_id (driver_id),
            KEY group_id (group_id)
        ) $charset_collate;";
        
        dbDelta($sql_assignment_logs);
        
        // Table: Notification Groups (Nhóm thông báo)
        $table_groups = $wpdb->prefix . 'booking_notification_groups';
        $sql_groups = "CREATE TABLE $table_groups (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            type varchar(20) NOT NULL,
            chat_id varchar(100),
            bot_token varchar(255),
            group_id varchar(100),
            access_token varchar(255),
            is_active tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY type (type),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        dbDelta($sql_groups);
        
        // Lưu version database
        update_option('booking_db_version', '2.0.0');
    }
    
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'booking_notification_groups',
            $wpdb->prefix . 'booking_assignment_logs',
            $wpdb->prefix . 'custom_pricing',
            $wpdb->prefix . 'booking_logs',
            $wpdb->prefix . 'driver_points',
            $wpdb->prefix . 'reviews',
            $wpdb->prefix . 'contracts',
            $wpdb->prefix . 'driver_documents',
            $wpdb->prefix . 'drivers',
            $wpdb->prefix . 'bookings'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
        
        delete_option('booking_db_version');
    }
}
