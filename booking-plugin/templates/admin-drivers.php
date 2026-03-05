<?php
if (!defined('ABSPATH')) exit;
global $wpdb;

// Xử lý cập nhật trạng thái
if (isset($_POST['update_status']) && isset($_POST['driver_id']) && check_admin_referer('update_driver_status')) {
    $driver_id = intval($_POST['driver_id']);
    $new_status = sanitize_text_field($_POST['new_status']);
    
    $wpdb->update(
        $wpdb->prefix . 'drivers',
        array('status' => $new_status),
        array('id' => $driver_id)
    );
    
    echo '<div class="notice notice-success is-dismissible"><p>✅ Đã cập nhật trạng thái tài xế!</p></div>';
}

// Xử lý xóa tài xế
if (isset($_GET['delete']) && check_admin_referer('delete_driver_' . $_GET['delete'])) {
    $driver_id = intval($_GET['delete']);
    
    $wpdb->delete($wpdb->prefix . 'drivers', array('id' => $driver_id));
    
    echo '<div class="notice notice-success is-dismissible"><p>✅ Đã xóa tài xế!</p></div>';
}

$drivers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}drivers ORDER BY created_at DESC");
?>
<div class="wrap">
    <h1>Quản Lý Tài Xế</h1>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width:40px;">ID</th>
                <th>Họ Tên</th>
                <th>SĐT</th>
                <th>Loại Xe</th>
                <th>Biển Số</th>
                <th style="width:120px;">Trạng Thái</th>
                <th style="width:100px;">Giấy Tờ</th>
                <th style="width:200px;">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($drivers): ?>
                <?php foreach ($drivers as $driver): ?>
                    <tr>
                        <td><?php echo esc_html($driver->id); ?></td>
                        <td><strong><?php echo esc_html($driver->full_name); ?></strong></td>
                        <td><?php echo esc_html($driver->phone); ?></td>
                        <td><?php echo esc_html($driver->car_type); ?></td>
                        <td><?php echo esc_html($driver->car_plate); ?></td>
                        <td>
                            <?php
                            $status_colors = array(
                                'pending' => '#f0ad4e',
                                'verified' => '#5bc0de',
                                'active' => '#5cb85c',
                                'suspended' => '#d9534f',
                                'inactive' => '#999'
                            );
                            $status_labels = array(
                                'pending' => '⏳ Chờ xác minh',
                                'verified' => '✅ Đã xác minh',
                                'active' => '✅ Hoạt động',
                                'suspended' => '⏸️ Tạm dừng',
                                'inactive' => '❌ Ngừng'
                            );
                            $color = $status_colors[$driver->status] ?? '#999';
                            $label = $status_labels[$driver->status] ?? $driver->status;
                            ?>
                            <span style="color:<?php echo $color; ?>;"><?php echo $label; ?></span>
                        </td>
                        <td>
                            <?php if ($driver->id_card_front || $driver->id_card_back || $driver->ekyc_photo): ?>
                                <button type="button" class="button button-small view-docs" 
                                        data-name="<?php echo esc_attr($driver->full_name); ?>"
                                        data-front="<?php echo esc_url($driver->id_card_front); ?>"
                                        data-back="<?php echo esc_url($driver->id_card_back); ?>"
                                        data-ekyc="<?php echo esc_url($driver->ekyc_photo); ?>">
                                    📄 Xem
                                </button>
                            <?php else: ?>
                                <span style="color:#999;">Chưa có</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" style="display:inline-block; margin-right:5px;">
                                <?php wp_nonce_field('update_driver_status'); ?>
                                <input type="hidden" name="update_status" value="1">
                                <input type="hidden" name="driver_id" value="<?php echo $driver->id; ?>">
                                <select name="new_status" onchange="if(confirm('Bạn có chắc muốn thay đổi trạng thái?')) this.form.submit();" style="font-size:12px;">
                                    <option value="">-- Đổi trạng thái --</option>
                                    <option value="pending">⏳ Chờ xác minh</option>
                                    <option value="verified">✅ Đã xác minh</option>
                                    <option value="active">✅ Hoạt động</option>
                                    <option value="suspended">⏸️ Tạm dừng</option>
                                    <option value="inactive">❌ Ngừng</option>
                                </select>
                            </form>
                            <a href="?page=booking-drivers&delete=<?php echo $driver->id; ?>&_wpnonce=<?php echo wp_create_nonce('delete_driver_' . $driver->id); ?>" 
                               class="button button-small button-link-delete" 
                               onclick="return confirm('Bạn có chắc muốn xóa tài xế này? Hành động này không thể hoàn tác!');"
                               style="color:#a00;">
                                🗑️ Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">Chưa có tài xế nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="docs-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:100000;">
    <div style="max-width:1000px; margin:50px auto; background:#fff; border-radius:8px; padding:30px; position:relative;">
        <button type="button" id="close-modal" style="position:absolute; top:15px; right:15px; font-size:24px; background:none; border:none; cursor:pointer;">&times;</button>
        <h2 id="modal-title">Giấy Tờ</h2>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
            <div>
                <h3>CCCD Mặt Trước</h3>
                <div style="border:2px solid #ddd; border-radius:8px; padding:10px; min-height:250px; display:flex; align-items:center; justify-content:center;">
                    <img id="img-front" src="" style="max-width:100%; height:auto; display:none;">
                    <p id="no-front" style="color:#999;">Chưa có ảnh</p>
                </div>
            </div>
            <div>
                <h3>CCCD Mặt Sau</h3>
                <div style="border:2px solid #ddd; border-radius:8px; padding:10px; min-height:250px; display:flex; align-items:center; justify-content:center;">
                    <img id="img-back" src="" style="max-width:100%; height:auto; display:none;">
                    <p id="no-back" style="color:#999;">Chưa có ảnh</p>
                </div>
            </div>
        </div>
        <div style="margin-top:20px;">
            <h3>Ảnh eKYC</h3>
            <div style="border:2px solid #ddd; border-radius:8px; padding:10px; min-height:250px; max-width:400px; margin:0 auto; display:flex; align-items:center; justify-content:center;">
                <img id="img-ekyc" src="" style="max-width:100%; height:auto; display:none;">
                <p id="no-ekyc" style="color:#999;">Chưa có ảnh</p>
            </div>
        </div>
        <div style="text-align:center; margin-top:20px;">
            <button type="button" class="button button-primary" onclick="jQuery('#docs-modal').fadeOut();">Đóng</button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.view-docs').on('click', function() {
        var name = $(this).data('name');
        var front = $(this).data('front');
        var back = $(this).data('back');
        var ekyc = $(this).data('ekyc');
        
        $('#modal-title').text('Giấy Tờ: ' + name);
        
        if (front) {
            $('#img-front').attr('src', front).show();
            $('#no-front').hide();
        } else {
            $('#img-front').hide();
            $('#no-front').show();
        }
        
        if (back) {
            $('#img-back').attr('src', back).show();
            $('#no-back').hide();
        } else {
            $('#img-back').hide();
            $('#no-back').show();
        }
        
        if (ekyc) {
            $('#img-ekyc').attr('src', ekyc).show();
            $('#no-ekyc').hide();
        } else {
            $('#img-ekyc').hide();
            $('#no-ekyc').show();
        }
        
        $('#docs-modal').fadeIn();
    });
    
    $('#close-modal').on('click', function() {
        $('#docs-modal').fadeOut();
    });
});
</script>
