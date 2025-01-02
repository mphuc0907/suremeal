<?php
/**
 * Created by PhpStorm.
 * User: vodanh
 * Date: 09/10/2018
 * Time: 16:51
 */
global $wpdb;
require_once __DIR__ .'/../includes/function.php';
$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."ratetingstar " );


$module_path = 'admin.php?page=rate_star';
$sub = trim($_GET['sub']);

$module_short_url = str_replace('admin.php?page=','', $module_path);

$mess = '';
$mdlconf = array('title'=>'Đánh giá sao');
if($sub==''){
    include_once __DIR__ . '/rate_star_list.php';
}else if($sub=='edit'){
    include_once __DIR__ . '/rate_star_edit.php';
}else if($sub=='add'){
    include_once __DIR__ . '/rate_star_add.php';
}

?>

