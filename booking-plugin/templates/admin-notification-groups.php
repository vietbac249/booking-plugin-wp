<?php
if (!defined('ABSPATH')) exit;

global $wpdb;

// Xử lý thêm/sửa group
if (isset($_POST['save_group']) && check_admin_referer('save_group_action')) {
    $group_data = array(
        'name' => sanitize_text_field($_POST['group_name']),
        'type' => sanitize_text_field($_POST['group_type']),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    );
    
    if ($_POST['group_type'] === 'telegram') {
        $group_data['chat_id'] = sanitize_text_field($_POST['telegram_chat_id']);
        $group_data['bot_token'] = sanitize_text_field($_POST['telegram_bot_token']);
    } else {
        $group_data['group_id'] = sanitize_text_field($_POST['zalo_group_id']);
        $group_data['access_token'] = sanitize_text_field($_POST['zalo_access_token']);
    }
    
    if (!empty($_POST['group_id'])) {
        $wpdb->update(
            $wpdb->prefix . 'booking_notification_groups',
            $group_data,
            array('id' => intval($_POST['group_id']))
        );
        $message = 'updated';
    } else {
        $wpdb->insert($wpdb->prefix . 'booking_notification_groups', $group_data);
        $message = 'added';
    }
}

// Xử lý xóa group
if (isset($_GET['delete_group']) && check_admin_referer('delete_group_' . $_GET['delete_group'])) {
    $wpdb->delete($wpdb->prefix . 'booking_notification_groups', array('id' => intval($_GET['delete_group'])));
    $message = 'deleted';
}

// Lấy danh sách groups
$groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}booking_notification_groups ORDER BY created_at DESC");
?>

<div class="wrap">
    <h1>📱 Quản Lý Groups Thông Báo</h1>
    
    <?php if (isset($message)): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php 
                if ($message === 'added') echo '✅ Đã thêm group mới thành công!';
                elseif ($message === 'updated') echo '✅ Đã cập nhật group thành công!';
                elseif ($message === 'deleted') echo '✅ Đã xóa group thành công!';
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <p>Quản lý các group Telegram và Zalo để gửi thông báo đơn hàng mới cho tài xế.</p>
    
    <button type="button" class="page-title-action" id="add-group-btn">➕ Thêm Group</button>
    
    <!-- Form thêm/sửa group -->
    <div id="group-form-container" style="display:none; margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ccc; border-radius: 4px;">
        <h2 id="group-form-title">Thêm Group Mới</h2>
        <form method="post" id="group-form">
            <?php wp_nonce_field('save_group_action'); ?>
            <input type="hidden" name="save_group" value="1">
            <input type="hidden" name="group_id" id="group_id">
            
            <table class="form-table">
                <tr>
                    <th><label>Tên Group *</label></th>
                    <td>
                        <input type="text" name="group_name" id="group_name" required class="regular-text" placeholder="VD: Nhóm Tài Xế Hà Nội">
                        <p class="description">Tên để nhận diện group</p>
                    </td>
                </tr>
                <tr>
                    <th><label>Loại Group *</label></th>
                    <td>
                        <select name="group_type" id="group_type" required onchange="toggleGroupType(this.value)">
                            <option value="">-- Chọn loại --</option>
                            <option value="telegram">📱 Telegram</option>
                            <option value="zalo">💬 Zalo</option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <!-- Telegram fields -->
            <div id="telegram-fields" style="display:none;">
                <h3>Cấu Hình Telegram</h3>
                <table class="form-table">
                    <tr>
                        <th><label>Bot Token *</label></th>
                        <td>
                            <input type="text" name="telegram_bot_token" id="telegram_bot_token" class="regular-text" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                            <p class="description">
                                Token của Telegram Bot. 
                                <a href="https://core.telegram.org/bots#6-botfather" target="_blank">Hướng dẫn tạo bot</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Chat ID *</label></th>
                        <td>
                            <input type="text" name="telegram_chat_id" id="telegram_chat_id" class="regular-text" placeholder="-1001234567890">
                            <p class="description">
                                Chat ID của group. Thêm bot vào group, sau đó dùng 
                                <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a> để lấy Chat ID
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Zalo fields -->
            <div id="zalo-fields" style="display:none;">
                <h3>Cấu Hình Zalo</h3>
                <table class="form-table">
                    <tr>
                        <th><label>Access Token *</label></th>
                        <td>
                            <input type="text" name="zalo_access_token" id="zalo_access_token" class="regular-text">
                            <p class="description">
                                Access Token của Zalo OA. 
                                <a href="https://developers.zalo.me/" target="_blank">Hướng dẫn lấy token</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Group ID *</label></th>
                        <td>
                            <input type="text" name="zalo_group_id" id="zalo_group_id" class="regular-text">
                            <p class="description">ID của Zalo Group (đang phát triển)</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <table class="form-table">
                <tr>
                    <th><label>Trạng Thái</label></th>
                    <td>
                        <label>
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                            Kích hoạt group này
                        </label>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">Lưu Group</button>
                <button type="button" class="button" id="cancel-group-btn">Hủy</button>
            </p>
        </form>
    </div>
    
    <!-- Danh sách groups -->
    <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Tên Group</th>
                <th style="width: 100px;">Loại</th>
                <th>Thông Tin</th>
                <th style="width: 100px;">Trạng Thái</th>
                <th style="width: 150px;">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($groups)): ?>
                <?php foreach ($groups as $group): ?>
                    <tr>
                        <td><?php echo $group->id; ?></td>
                        <td><strong><?php echo esc_html($group->name); ?></strong></td>
                        <td>
                            <?php if ($group->type === 'telegram'): ?>
                                <span style="color: #0088cc;">📱 Telegram</span>
                            <?php else: ?>
                                <span style="color: #0068ff;">💬 Zalo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($group->type === 'telegram'): ?>
                                <small>
                                    <strong>Chat ID:</strong> <?php echo esc_html($group->chat_id); ?><br>
                                    <strong>Bot Token:</strong> <?php echo esc_html(substr($group->bot_token, 0, 20)) . '...'; ?>
                                </small>
                            <?php else: ?>
                                <small>
                                    <strong>Group ID:</strong> <?php echo esc_html($group->group_id); ?><br>
                                    <strong>Token:</strong> <?php echo esc_html(substr($group->access_token, 0, 20)) . '...'; ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($group->is_active): ?>
                                <span style="color: green;">✅ Hoạt động</span>
                            <?php else: ?>
                                <span style="color: red;">❌ Tắt</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="#" class="button button-small edit-group" 
                               data-id="<?php echo $group->id; ?>"
                               data-name="<?php echo esc_attr($group->name); ?>"
                               data-type="<?php echo $group->type; ?>"
                               data-telegram-chat-id="<?php echo esc_attr($group->chat_id); ?>"
                               data-telegram-bot-token="<?php echo esc_attr($group->bot_token); ?>"
                               data-zalo-group-id="<?php echo esc_attr($group->group_id); ?>"
                               data-zalo-access-token="<?php echo esc_attr($group->access_token); ?>"
                               data-active="<?php echo $group->is_active; ?>">
                                Sửa
                            </a>
                            <a href="?page=booking-notification-groups&delete_group=<?php echo $group->id; ?>&_wpnonce=<?php echo wp_create_nonce('delete_group_' . $group->id); ?>" 
                               class="button button-small" 
                               onclick="return confirm('Bạn có chắc muốn xóa group này?')">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">
                        <p style="font-size: 16px; color: #666;">Chưa có group nào. Hãy thêm group mới.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; padding: 20px; background: #f0f0f1; border-radius: 4px;">
        <h3>📖 Hướng Dẫn Sử Dụng</h3>
        
        <h4>Telegram:</h4>
        <ol>
            <li>Tạo bot mới với <a href="https://t.me/BotFather" target="_blank">@BotFather</a></li>
            <li>Lấy Bot Token từ BotFather</li>
            <li>Tạo group Telegram và thêm bot vào group</li>
            <li>Gửi tin nhắn bất kỳ trong group</li>
            <li>Truy cập: <code>https://api.telegram.org/bot[BOT_TOKEN]/getUpdates</code></li>
            <li>Tìm "chat":{"id":-1001234567890} - đó là Chat ID của group</li>
            <li>Nhập Bot Token và Chat ID vào form trên</li>
        </ol>
        
        <h4>Zalo:</h4>
        <ol>
            <li>Đăng ký Zalo Official Account tại <a href="https://oa.zalo.me/" target="_blank">oa.zalo.me</a></li>
            <li>Vào <a href="https://developers.zalo.me/" target="_blank">Zalo Developers</a></li>
            <li>Tạo ứng dụng và lấy Access Token</li>
            <li>Lấy Group ID (đang phát triển)</li>
        </ol>
        
        <p><strong>Lưu ý:</strong> Zalo Group hiện chưa được hỗ trợ đầy đủ. Khuyến nghị sử dụng Telegram.</p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Show add group form
    $('#add-group-btn').on('click', function() {
        $('#group_id').val('');
        $('#group-form')[0].reset();
        $('#is_active').prop('checked', true);
        $('#group-form-title').text('Thêm Group Mới');
        $('#telegram-fields, #zalo-fields').hide();
        $('#group-form-container').slideDown();
    });
    
    // Cancel group form
    $('#cancel-group-btn').on('click', function() {
        $('#group-form-container').slideUp();
    });
    
    // Toggle group type fields
    window.toggleGroupType = function(type) {
        if (type === 'telegram') {
            $('#telegram-fields').show();
            $('#zalo-fields').hide();
            $('#telegram_bot_token, #telegram_chat_id').prop('required', true);
            $('#zalo_access_token, #zalo_group_id').prop('required', false);
        } else if (type === 'zalo') {
            $('#telegram-fields').hide();
            $('#zalo-fields').show();
            $('#telegram_bot_token, #telegram_chat_id').prop('required', false);
            $('#zalo_access_token, #zalo_group_id').prop('required', true);
        } else {
            $('#telegram-fields, #zalo-fields').hide();
        }
    };
    
    // Edit group
    $('.edit-group').on('click', function(e) {
        e.preventDefault();
        
        var id = $(this).data('id');
        var name = $(this).data('name');
        var type = $(this).data('type');
        var telegramChatId = $(this).data('telegram-chat-id');
        var telegramBotToken = $(this).data('telegram-bot-token');
        var zaloGroupId = $(this).data('zalo-group-id');
        var zaloAccessToken = $(this).data('zalo-access-token');
        var isActive = $(this).data('active');
        
        $('#group_id').val(id);
        $('#group_name').val(name);
        $('#group_type').val(type);
        $('#telegram_chat_id').val(telegramChatId);
        $('#telegram_bot_token').val(telegramBotToken);
        $('#zalo_group_id').val(zaloGroupId);
        $('#zalo_access_token').val(zaloAccessToken);
        $('#is_active').prop('checked', isActive == 1);
        
        toggleGroupType(type);
        
        $('#group-form-title').text('Sửa Group');
        $('#group-form-container').slideDown();
        $('html, body').animate({
            scrollTop: $('#group-form-container').offset().top - 50
        }, 500);
    });
});
</script>

<style>
.wrap code {
    background: #f0f0f0;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 13px;
}
#group-form-container {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
#group-form-container h2, #group-form-container h3 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}
</style>
