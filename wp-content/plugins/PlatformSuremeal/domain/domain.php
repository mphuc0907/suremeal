<?php
global $wpdb;
require_once __DIR__ .'/../includes/function.php';

$myrows = $wpdb->get_results( "SELECT * FROM wp_account_users" );


$module_path = 'admin.php?page=domain_manager';
$sub = "";
if (isset($_GET['sub'])) {
    $sub = trim($_GET['sub']);
}


$module_short_url = str_replace('admin.php?page=','', $module_path);

$mess = '';
$mdlconf = array('title'=>'Domain manager');


if($sub==''){
    include_once __DIR__ .'/list.php';

}else if($sub=='detail'){
    include_once __DIR__ .'/detail.php';

}
?>


