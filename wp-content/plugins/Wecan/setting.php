<?php
/**
 * @package Subscribe Plugin by W.G
 * Auth:
 * @version 1.1.5
 */

/*
Plugin Name:  WG-Wecan-group
Plugin URI: https://wecan-group.com/
Description: Quản lý Wecan-Group
Author: Wecan Group
Version: 1.0
Author URI: https://wecan-group.com/
*/

$userRoles = '';
$userPer   = '';

add_action('admin_menu', 'show_menu_app1');

function show_menu_app1(){
    global $userRoles, $userPer;
    $userRoles = wp_get_current_user();
    $userPer = $userRoles->roles;

    if (in_array('administrator', $userPer)) {
        remove_menu_page('edit-comments.php');
        add_menu_page('Voucher', 'Voucher', 'manage_options', 'discount_price', 'magiamgia', 'dashicons-nametag', 40);
        add_submenu_page('discount_price', 'Add voucher', 'Add voucher', 'manage_options', 'add_discount_price', 'addmagiamgia');

        add_menu_page('Comment', 'Comment', 'manage_options', 'comment_manager', 'comment', 'dashicons-nametag', 40);
        add_menu_page('Customer', 'Customer', 'manage_options', 'customer_manager', 'customer', 'dashicons-nametag', 40);
        add_menu_page('Customer Dealers', 'Dealer management', 'manage_options', 'customer_dealers_manager', 'customer_dealers', 'dashicons-nametag', 40);
        add_menu_page('Orders', 'Orders', 'manage_options', 'orders_manager', 'orders', 'dashicons-nametag', 40);
        add_submenu_page('orders_manager', 'Customer Orders', 'Customer Orders', 'manage_options', 'customer_orders_manager', 'customerorder');
        add_submenu_page('orders_manager', 'Dealer Orders', 'Dealer Orders', 'manage_options', 'dealer_orders_manager', 'dealerorder');
        add_menu_page('Dealers', 'Dealer list', 'manage_options', 'dealer_manager', 'dealer', 'dashicons-nametag', 40);
    }

    if (in_array('editor', $userPer)) {
        add_menu_page('Voucher', 'Voucher', 'edit_pages', 'discount_price', 'magiamgia', 'dashicons-nametag', 40);
        add_submenu_page('discount_price', 'Add voucher', 'Add voucher', 'edit_pages', 'add_discount_price', 'addmagiamgia');
        add_menu_page('Comment', 'Comment', 'edit_pages', 'comment_manager', 'comment', 'dashicons-nametag', 40);
        add_menu_page('Customer', 'Customer', 'edit_pages', 'customer_manager', 'customer', 'dashicons-nametag', 40);
        add_menu_page('Customer Dealers', 'Dealer management', 'edit_pages', 'customer_dealers_manager', 'customer_dealers', 'dashicons-nametag', 40);

        add_menu_page('Orders', 'Orders', 'edit_pages', 'orders_manager', 'orders', 'dashicons-nametag', 40);
        add_submenu_page('orders_manager', 'Customer Orders', 'Customer Orders', 'edit_pages', 'customer_orders_manager', 'customerorder');
        add_submenu_page('orders_manager', 'Dealer Orders', 'Dealer Orders', 'edit_pages', 'dealer_orders_manager', 'dealerorder');
        add_menu_page('Dealers', 'Dealer list', 'edit_pages', 'dealer_manager', 'dealer', 'dashicons-nametag', 40);
    }
}

include plugin_dir_path( __FILE__ ) . '/includes/config.php';

function daily(){
    include plugin_dir_path( __FILE__ ) . '/daily/daily.php';
}
//function adddaily(){
//    include plugin_dir_path( __FILE__ ) . '/daily/adddaily.php';
//}

 function magiamgia(){
     include plugin_dir_path( __FILE__ ) . '/magiamgia/setting.php';
 }
 function addmagiamgia(){
     include plugin_dir_path( __FILE__ ) . '/magiamgia/add.php';
 }

function comment(){
    include plugin_dir_path( __FILE__ ) . '/comments/comment.php';
}

function customer(){
    include plugin_dir_path( __FILE__ ) . '/customers/customer.php';
}

function customer_dealers(){
    include plugin_dir_path( __FILE__ ) . '/customer-dealers/customer.php';
}


function dealer(){
    include plugin_dir_path( __FILE__ ) . '/dealers/dealer.php';
}

function application(){
    include plugin_dir_path( __FILE__ ) . '/application/order.php';
}

function orders(){
    include plugin_dir_path( __FILE__ ) . '/orders/order.php';
}

function customerorder(){
    include plugin_dir_path( __FILE__ ) . '/orders/customer-order.php';
}

function dealerorder(){
    include plugin_dir_path( __FILE__ ) . '/orders/dealer-order.php';
}

// function affiliateorder(){
//     include plugin_dir_path( __FILE__ ) . '/orders/affiliate-order.php';
// }

function csdl(){
    include plugin_dir_path( __FILE__ ) . '/csdl/setting.php';
}

function danhgia() {
    include plugin_dir_path(__FILE__) . '/danhgia/danhgia.php';
}
// Thêm code check token và hiển thị overlay
function check_token_and_show_overlay() {
    // Lấy current screen
    $screen = get_current_screen();

    // Danh sách các page thuộc plugin cần check token
    $plugin_pages = array(
        'toplevel_page_discount_price',
        'giftcode_page_add_discount_price',
        'toplevel_page_list-complaints',
        'khiếu-nại_page_list-dispute',
        'toplevel_page_category_sp',
        'toplevel_page_order',
        'toplevel_page_kiot',
        'toplevel_page_statistic',
        'toplevel_page_customer',
        'người-dùng_page_report-user',
        'toplevel_page_share_post',
        'toplevel_page_top_key_word',
        'toplevel_page_manage_sale',
        'toplevel_page_tracking',
        'toplevel_page_transaction',
        'toplevel_page_auction',
        'toplevel_page_payment-error',
        'toplevel_page_system-bot-chat',
        'toplevel_page_message-management',
        'toplevel_page_manageSupport'
    );

    // Kiểm tra nếu đang ở trong một trang của plugin
//    if ($screen && in_array($screen->id, $plugin_pages)) {
//        $user_id = get_current_user_id();
//        $token = get_field('tokenApiAdmin', 'user_' . $user_id);
//
//        if (empty($token)) {
//            add_action('admin_footer', 'render_token_overlay');
//        }
//    }
}
add_action('current_screen', 'check_token_and_show_overlay');

// Hàm render overlay
//function render_token_overlay() {
//    ?>
<!--    <div id="wg-token-overlay" class="wg-token-overlay">-->
<!--        <div class="overlay-content">-->
<!--            <h3>Thông báo</h3>-->
<!--            <p>Bạn không có quyền truy cập vào trang này.</p>-->
<!--            <div class="overlay-actions">-->
<!--                <button onclick="closeWGTokenOverlay()" class="button button-secondary">Đóng</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <style>-->
<!--        .wg-token-overlay {-->
<!--            display: flex;-->
<!--            position: fixed;-->
<!--            top: 0;-->
<!--            left: 0;-->
<!--            right: 0;-->
<!--            bottom: 0;-->
<!--            background-color: rgba(0, 0, 0, 0.7);-->
<!--            z-index: 999999;-->
<!--            justify-content: center;-->
<!--            align-items: center;-->
<!--        }-->
<!---->
<!--        .wg-token-overlay .overlay-content {-->
<!--            background-color: #fff;-->
<!--            padding: 30px;-->
<!--            border-radius: 3px;-->
<!--            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);-->
<!--            max-width: 400px;-->
<!--            width: 90%;-->
<!--        }-->
<!---->
<!--        .wg-token-overlay h3 {-->
<!--            margin-top: 0;-->
<!--            margin-bottom: 15px;-->
<!--            color: #23282d;-->
<!--        }-->
<!---->
<!--        .wg-token-overlay p {-->
<!--            margin-bottom: 20px;-->
<!--            font-size: 14px;-->
<!--        }-->
<!---->
<!--        .wg-token-overlay .overlay-actions {-->
<!--            display: flex;-->
<!--            gap: 10px;-->
<!--            justify-content: flex-end;-->
<!--        }-->
<!---->
<!--        .wg-token-overlay .button {-->
<!--            min-width: 100px;-->
<!--            text-align: center;-->
<!--        }-->
<!--    </style>-->
<!---->
<!--    <script>-->
<!--        function closeWGTokenOverlay() {-->
<!--            window.location.href = '/wp-admin';-->
<!--        }-->
<!---->
<!--        document.addEventListener('DOMContentLoaded', function() {-->
<!--            var overlay = document.getElementById('wg-token-overlay');-->
<!--            var content = overlay.querySelector('.overlay-content');-->
<!---->
<!--            overlay.addEventListener('click', function(e) {-->
<!--                if (e.target === overlay) {-->
<!--                    e.preventDefault();-->
<!--                    e.stopPropagation();-->
<!--                }-->
<!--            });-->
<!--        });-->
<!--    </script>-->
<!--    --><?php
//}


