<?php
global $wpdb;
require_once __DIR__ .'/../includes/function.php';

$myrows = $wpdb->get_results( "SELECT * FROM np_report_parent" );


$module_path = 'admin.php?page=csdl';
$sub = trim($_GET['sub']);

$module_short_url = str_replace('admin.php?page=','', $module_path);

$mess = '';
$mdlconf = array('title'=>'Cơ sở dữ liệu M&E');


if($sub==''){
    include_once __DIR__ .'/list.php';

}else if($sub=='edit'){
    include_once __DIR__ .'/edit.php';

}else if($sub=='add'){

    include_once __DIR__ .'/add.php';

}

?>


