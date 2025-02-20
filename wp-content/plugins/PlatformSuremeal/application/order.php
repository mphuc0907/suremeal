<?php
global $wpdb;
require_once __DIR__ .'/../includes/function.php';

$myrows = $wpdb->get_results( "SELECT * FROM tt_orders" );


$module_path = 'admin.php?page=order_manager';
$sub = "";
if (isset($_GET['sub'])) {
    $sub = trim($_GET['sub']);
}


$module_short_url = str_replace('admin.php?page=','', $module_path);

$mess = '';
$mdlconf = array('title'=>'Quản lý đơn hàng');


if($sub==''){
    include_once __DIR__ .'/list.php';

}else if($sub=='edit'){
    include_once __DIR__ .'/edit.php';

}else if($sub=='add'){

    include_once __DIR__ .'/add.php';

} else if($sub == 'voucher_info') {
    include_once __DIR__ . '/voucher-info.php';
}

?>


