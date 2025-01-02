<?php
global $wpdb;
require_once __DIR__ .'/../includes/function.php';

$myrows = $wpdb->get_results( "SELECT * FROM wp_discount_dealer" );


$module_path = '?page=discount_dealers_manager';
$sub = "";
if (isset($_GET['sub'])) {
    $sub = trim($_GET['sub']);
}


$module_short_url = str_replace('admin.php?page=','', $module_path);

$mess = '';
$mdlconf = array('title'=>'Discount dealers manager');


if($sub==''){
    include_once __DIR__ .'/list.php';
}else if($sub=='add'){
    include_once __DIR__ .'/add.php';
}else if($sub=='edit'){
    include_once __DIR__ .'/edit.php';
}
?>
