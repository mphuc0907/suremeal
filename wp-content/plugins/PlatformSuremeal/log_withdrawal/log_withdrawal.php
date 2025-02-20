<?php

global $wpdb;
require_once __DIR__ .'/../includes/function.php';

$myrows = $wpdb->get_results( "SELECT * FROM log_withdrawal" );


$module_path = 'admin.php?page=log_withdrawal_manager';
$sub = "";
if (isset($_GET['sub'])) {
    $sub = trim($_GET['sub']);
}


$module_short_url = str_replace('admin.php?page=','', $module_path);

$mess = '';
$mdlconf = array('title'=>'Withdrawal management');


if($sub==''){
    include_once __DIR__ .'/list.php';

}
?>


