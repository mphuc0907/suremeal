<?php
/**
 * @package Subscribe Plugin by Suremeal
 * Auth:
 * @version 1.1.5
 */

/*
Plugin Name:  Plastform Suremeal
Plugin URI: https://wecan-group.com/
Description: Quản lý Plastform Suremeal
Author: Wecan Group
Version: 1.0
Author URI: https://wecan-group.com/
*/

$userRoles = '';
$userPer   = '';

add_action('admin_menu', 'show_menu_app1');

function show_menu_app1() {
    // Hiển thị menu nếu người dùng có quyền
    if (current_user_can('manage_options')) {
        add_menu_page('Customer', 'Customer', 'manage_options', 'customer_manager', 'customer', 'dashicons-nametag', 40);
        add_menu_page('Customer Sale Partner', 'Sale Partner', 'manage_options', 'customer_dealers_manager', 'customer_dealers', 'dashicons-nametag', 40);
        add_menu_page('Log withdrawal', 'Log withdrawal', 'manage_options', 'log_withdrawal_manager', 'log_withdrawal', 'dashicons-nametag', 40);
//        add_menu_page('Domain', 'Domain', 'manage_options', 'domain_manager', 'domain', 'dashicons-nametag', 40);
        add_menu_page( 'Affiliate Orders', 'Affiliate Orders', 'manage_options', 'orders_manager', 'orders' , 'dashicons-nametag', 40);
    }

    // Xóa menu mặc định của WordPress
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'show_menu_app1');

// Hàm xử lý cho từng menu
function customer() {
    include plugin_dir_path(__FILE__) . '/customers/customer.php';
}

//function domain() {
//    include plugin_dir_path(__FILE__) . '/domain/domain.php';
//}

function customer_dealers() {
    include plugin_dir_path(__FILE__) . '/customer-dealers/customer.php';
}

function log_withdrawal() {
    include plugin_dir_path(__FILE__) . '/log_withdrawal/log_withdrawal.php';
}

function orders(){
    include plugin_dir_path( __FILE__ ) . '/orders/order.php';
}
