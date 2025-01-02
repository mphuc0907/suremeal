<?php
/**
 * Created by PhpStorm.
 * User: vodanh
 * Date: 09/10/2018
 * Time: 16:51
 */
global $wpdb;
require_once __DIR__ .'/../includes/function.php';
$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."order " );


$module_path = 'admin.php?page=order';
$sub = trim($_GET['sub']);

$module_short_url = str_replace('admin.php?page=','', $module_path);

$mess = '';
$mdlconf = array('title'=>'Đơn hàng');
if($sub==''){
    include_once __DIR__ . '/order_list.php';
}else if($sub=='edit'){
    include_once __DIR__ . '/order_edit.php';
}

?>

